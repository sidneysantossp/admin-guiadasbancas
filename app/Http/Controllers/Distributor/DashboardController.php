<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\Vendor;
use App\Models\Food;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    public function dashboard()
    {
        $distributor = Auth::guard('distributor')->user();
        
        // Estatísticas básicas para o dashboard do distribuidor
        $total_orders = Order::count();
        $pending_orders = Order::where('order_status', 'pending')->count();
        $completed_orders = Order::where('order_status', 'delivered')->count();
        $total_earnings = Order::where('order_status', 'delivered')->sum('order_amount');
        
        $data = [
            'distributor' => $distributor,
            'total_orders' => $total_orders,
            'pending_orders' => $pending_orders,
            'completed_orders' => $completed_orders,
            'total_earnings' => $total_earnings,
        ];
        
        return view('distributor-views.dashboard', compact('data'));
    }

    // Dados para gráfico de barras: pedidos realizados por período
    public function ordersChart(Request $request)
    {
        $range = $request->query('range', 'last_7_days');
        $allowed = ['today','yesterday','last_7_days','current_month','last_90_days'];
        if (!in_array($range, $allowed, true)) {
            $range = 'last_7_days';
        }

        $end = Carbon::today()->endOfDay();
        switch ($range) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case 'current_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfDay();
                break;
            case 'last_90_days':
                $start = Carbon::today()->subDays(89)->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
            case 'last_7_days':
            default:
                $start = Carbon::today()->subDays(6)->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
        }

        // Agrupar por dia (YYYY-mm-dd)
        $rows = Order::select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->pluck('c', 'd');

        $labels = [];
        $data = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[] = (int) ($rows[$key] ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // Top 10 últimos pedidos por jornaleiro (vendor)
    public function latestOrdersByVendor(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();

        // Para cada jornaleiro, pega a data do último pedido e ordena
        $sub = DistributorOrder::select('vendor_id', DB::raw('MAX(created_at) as last_order_at'))
            ->forDistributor($distributor->id)
            ->groupBy('vendor_id');

        $pairs = $sub->orderByDesc('last_order_at')->limit(10)->get();
        $vendorIds = $pairs->pluck('vendor_id')->filter()->values();
        $vendors = Vendor::with('storage')->whereIn('id', $vendorIds)->get()->keyBy('id');

        $data = $pairs->map(function($row) use ($vendors){
            $v = $vendors[$row->vendor_id] ?? null;
            $name = $v ? trim(($v->f_name ?? '').' '.($v->l_name ?? '')) : 'Jornaleiro';
            return [
                'vendor_id' => $row->vendor_id,
                'name' => $name,
                'image' => $v?->image_full_url,
                'last_order_at' => Carbon::parse($row->last_order_at)->diffForHumans(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    // Top 10 produtos mais pedidos (por quantidade de itens)
    public function topProducts(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();

        $rows = DistributorOrderItem::select('food_id', DB::raw('SUM(quantity) as qty'))
            ->join('distributor_orders as o', 'o.id', '=', 'distributor_order_items.distributor_order_id')
            ->where('o.distributor_id', $distributor->id)
            ->groupBy('food_id')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        // Carregar dados de produto
        $foods = Food::whereIn('id', $rows->pluck('food_id')->filter())->get()->keyBy('id');

        $data = $rows->map(function($r) use ($foods) {
            $food = $foods[$r->food_id] ?? null;
            return [
                'food_id' => $r->food_id,
                'name' => $food?->name ?? ($food?->title ?? 'Produto'),
                'image' => $food?->image_full_url ?? null,
                'qty' => (int) $r->qty,
            ];
        });

        return response()->json(['data' => $data]);
    }
}