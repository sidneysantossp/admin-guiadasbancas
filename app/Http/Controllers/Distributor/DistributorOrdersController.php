<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DistributorOrder;
use App\Models\DistributorVendorProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistributorOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    /**
     * Lista todos os pedidos recebidos
     */
    public function index(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $query = DistributorOrder::with(['vendor', 'items'])
            ->forDistributor($distributor->id);

        // Filtros
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('vendor_id') && $request->vendor_id != '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                      $vendorQuery->where('f_name', 'like', "%{$search}%")
                                  ->orWhere('l_name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        // Estatísticas
        $stats = [
            'total' => DistributorOrder::forDistributor($distributor->id)->count(),
            'pending' => DistributorOrder::forDistributor($distributor->id)->pending()->count(),
            'confirmed' => DistributorOrder::forDistributor($distributor->id)->confirmed()->count(),
            'delivered' => DistributorOrder::forDistributor($distributor->id)->delivered()->count(),
        ];

        return view('distributor-views.orders.index', compact('orders', 'stats'));
    }

    /**
     * Mostra detalhes de um pedido
     */
    public function show($id)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $order = DistributorOrder::with(['vendor', 'items.food'])
            ->forDistributor($distributor->id)
            ->findOrFail($id);

        return view('distributor-views.orders.show', compact('order'));
    }

    /**
     * Atualiza o status de um pedido
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $distributor = Auth::guard('distributor')->user();
        
        $order = DistributorOrder::forDistributor($distributor->id)->findOrFail($id);

        try {
            DB::beginTransaction();

            // Se estiver cancelando, restaura o estoque
            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = DistributorVendorProduct::where([
                        'distributor_id' => $distributor->id,
                        'vendor_id' => $order->vendor_id,
                        'food_id' => $item->food_id
                    ])->first();
                    
                    if ($product) {
                        $product->addStock($item->quantity);
                    }
                }
            }

            $order->updateStatus($request->status, $request->notes);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status do pedido atualizado com sucesso!',
                'status_label' => $order->status_label
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lista pedidos pendentes
     */
    public function pending(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $orders = DistributorOrder::with(['vendor', 'items'])
            ->forDistributor($distributor->id)
            ->pending()
            ->latest()
            ->paginate(15);

        return view('distributor-views.orders.pending', compact('orders'));
    }

    /**
     * Lista pedidos confirmados
     */
    public function confirmed(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $orders = DistributorOrder::with(['vendor', 'items'])
            ->forDistributor($distributor->id)
            ->confirmed()
            ->latest()
            ->paginate(15);

        return view('distributor-views.orders.confirmed', compact('orders'));
    }

    /**
     * Lista pedidos entregues
     */
    public function delivered(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $orders = DistributorOrder::with(['vendor', 'items'])
            ->forDistributor($distributor->id)
            ->delivered()
            ->latest()
            ->paginate(15);

        return view('distributor-views.orders.delivered', compact('orders'));
    }

    /**
     * Aprova múltiplos pedidos de uma vez
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:distributor_orders,id'
        ]);

        $distributor = Auth::guard('distributor')->user();
        $approved = 0;

        try {
            DB::beginTransaction();

            foreach ($request->order_ids as $orderId) {
                $order = DistributorOrder::forDistributor($distributor->id)
                    ->where('id', $orderId)
                    ->where('status', 'pending')
                    ->first();

                if ($order) {
                    $order->updateStatus('confirmed', 'Aprovado em lote');
                    $approved++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$approved} pedidos aprovados com sucesso!"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar pedidos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exporta pedidos para Excel
     */
    public function export(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $query = DistributorOrder::with(['vendor', 'items.food'])
            ->forDistributor($distributor->id);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        // Aqui você pode implementar a exportação usando FastExcel ou similar
        // Por enquanto, retorna os dados em JSON para teste
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Dados preparados para exportação'
        ]);
    }

    /**
     * Dashboard com métricas dos pedidos
     */
    public function dashboard()
    {
        $distributor = Auth::guard('distributor')->user();
        
        $stats = [
            'today_orders' => DistributorOrder::forDistributor($distributor->id)
                ->whereDate('created_at', today())->count(),
            'week_orders' => DistributorOrder::forDistributor($distributor->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_orders' => DistributorOrder::forDistributor($distributor->id)
                ->whereMonth('created_at', now()->month)->count(),
            'total_revenue' => DistributorOrder::forDistributor($distributor->id)
                ->where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => DistributorOrder::forDistributor($distributor->id)
                ->pending()->count(),
        ];

        // Pedidos recentes
        $recentOrders = DistributorOrder::with(['vendor'])
            ->forDistributor($distributor->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('distributor-views.orders.dashboard', compact('stats', 'recentOrders'));
    }
    
    /**
     * Gera QR Code PIX para pagamento
     */
    public function generatePixQR($orderId)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $order = DistributorOrder::with(['vendor'])
            ->forDistributor($distributor->id)
            ->findOrFail($orderId);
            
        if ($order->payment_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido já foi pago ou não está pendente.'
            ]);
        }
        
        // Dados para o PIX
        $pixData = [
            'key' => $distributor->pix_key ?? $distributor->phone ?? $distributor->email,
            'merchant_name' => $distributor->f_name . ' ' . $distributor->l_name,
            'merchant_city' => $distributor->city ?? 'Cidade',
            'amount' => $order->total_amount,
            'order_number' => $order->order_number,
            'description' => 'Pedido ' . $order->order_number
        ];
        
        return response()->json([
            'success' => true,
            'pix_data' => $pixData
        ]);
    }
}
