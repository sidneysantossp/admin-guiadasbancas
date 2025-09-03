<?php

namespace App\Http\Controllers\Distributor;

use App\Models\Food;
use App\Models\OrderDetail;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Category;
use App\Models\ItemCampaign;
use App\Models\Zone;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\OrderTransaction;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }

    public function index(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        // Estatísticas básicas do distribuidor
        $total_orders = Order::whereHas('details', function($query) use ($distributor) {
            $query->whereHas('food', function($q) use ($distributor) {
                $q->where('vendor_id', $distributor->id);
            });
        })->count();
        
        $total_revenue = OrderDetail::whereHas('food', function($query) use ($distributor) {
            $query->where('vendor_id', $distributor->id);
        })->sum('price');
        
        $total_products = Food::where('vendor_id', $distributor->id)->count();
        
        return view('distributor-views.report.index', compact(
            'total_orders',
            'total_revenue', 
            'total_products'
        ));
    }

    public function expense_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if($filter == 'custom'){
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }

        $distributor = Auth::guard('distributor')->user();
        
        // Implementar lógica de relatórios específica para distribuidores
        $expenses = collect(); // Por enquanto retorna coleção vazia
        
        return view('distributor-views.report.expense-report', compact('expenses', 'from', 'to', 'filter'));
    }

    public function order_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if($filter == 'custom'){
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }

        $distributor = Auth::guard('distributor')->user();
        
        // Implementar lógica de relatórios de pedidos específica para distribuidores
        $orders = collect(); // Por enquanto retorna coleção vazia
        
        return view('distributor-views.report.order-report', compact('orders', 'from', 'to', 'filter'));
    }

    public function food_wise_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if($filter == 'custom'){
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }

        $distributor = Auth::guard('distributor')->user();
        
        // Implementar lógica de relatórios de produtos específica para distribuidores
        $foods = collect(); // Por enquanto retorna coleção vazia
        
        return view('distributor-views.report.food-wise-report', compact('foods', 'from', 'to', 'filter'));
    }

    public function day_wise_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if($filter == 'custom'){
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }

        $distributor = Auth::guard('distributor')->user();
        
        // Implementar lógica de relatórios diários específica para distribuidores
        $transactions = collect(); // Por enquanto retorna coleção vazia
        
        return view('distributor-views.report.day-wise-report', compact('transactions', 'from', 'to', 'filter'));
    }
}