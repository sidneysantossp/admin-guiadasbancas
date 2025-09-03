<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\Vendor;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class OrderController extends Controller
{
    /**
     * Lista pedidos com status específico
     */
    public function list(Request $request, $status = 'all')
    {
        $distributor = Auth::guard('distributor')->user();
        $view = $request->get('view', 'list'); // default para list
        
        $query = DistributorOrder::with(['vendor','items'])
            ->where('distributor_id', $distributor->id);
            
        if ($status != 'all') {
            $query = $query->where('status', $status);
        }
        
        // Search by order id (exact match)
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            if (is_numeric($search)) {
                $query->where('id', (int) $search);
            }
        }
        
        $orders = $query->latest()->get(); // Use get() instead of paginate for kanban
        $paginatedOrders = $query->latest()->paginate(15)->appends($request->query()); // Keep for list view

        // Aggregated counts by status for tabs
        $rawCounts = DistributorOrder::select('status', DB::raw('COUNT(*) as total'))
            ->where('distributor_id', $distributor->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'    => (int) ($rawCounts['pending'] ?? 0),
            'confirmed'  => (int) ($rawCounts['confirmed'] ?? 0),
            'processing' => (int) ($rawCounts['processing'] ?? 0),
            'delivering' => (int) ($rawCounts['delivering'] ?? 0),
            'delivered'  => (int) ($rawCounts['delivered'] ?? 0),
            'canceled'   => (int) ($rawCounts['canceled'] ?? 0),
        ];
        $count_all = array_sum($counts);

        // Kanban-specific data
        $kanbanColumns = [
            'pending' => 'Pendentes',
            'confirmed' => 'Autorizados', 
            'processing' => 'Separação',
            'delivering' => 'Conferência',
            'delivered' => 'Faturamento',
            'canceled' => 'Triagem',
            'transport' => 'Transporte'
        ];

        // Group orders by status for Kanban
        $ordersByStatus = [];
        foreach ($kanbanColumns as $statusKey => $statusLabel) {
            $ordersByStatus[$statusKey] = $orders->where('status', $statusKey)->values();
        }
            
        return view('distributor.orders.index', compact(
            'orders', 
            'paginatedOrders', 
            'status', 
            'counts', 
            'count_all', 
            'view', 
            'kanbanColumns', 
            'ordersByStatus'
        ));
    }
    
    /**
     * Detalhes do pedido
     */
    public function details($id)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $order = DistributorOrder::with(['items.food', 'vendor'])
            ->where('id', $id)
            ->where('distributor_id', $distributor->id)
            ->first();
            
        if (!$order) {
            Toastr::error('Pedido não encontrado');
            return back();
        }
        
        return view('distributor-views.order.details', compact('order'));
    }
    
    /**
     * Atualização de status do pedido
     */
    public function status(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:pending,confirmed,processing,delivering,delivered,canceled',
        ]);
        
        $order = DistributorOrder::where('id', $request->id)
            ->where('distributor_id', $distributor->id)
            ->first();
            
        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'Pedido não encontrado!']
                ]
            ], 404);
        }
        
        $order->status = $request->status;
        $order->save();
        
        // Notificar jornaleiro sobre atualização
        $vendor = Vendor::find($order->vendor_id);
        $fcm_token = $vendor ? $vendor->cm_firebase_token : null;
        
        try {
            if ($fcm_token) {
                $data = [
                    'title' => 'Atualização de Pedido',
                    'description' => 'O status do seu pedido #'.$order->id.' foi atualizado para '.$request->status,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            // Falha na notificação, apenas logamos
        }
        
        Toastr::success('Status do pedido atualizado!');
        return back();
    }

    /**
     * Exibir pedidos no formato Kanban ou Lista
     */
    public function kanban(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $view = $request->get('view', 'kanban'); // default para kanban
        
        // Definir os status das colunas do Kanban
        $kanbanColumns = [
            'pending' => 'Pendentes',
            'confirmed' => 'Autorizados', 
            'processing' => 'Separação',
            'delivering' => 'Conferência',
            'delivered' => 'Faturamento',
            'canceled' => 'Triagem',
            'transport' => 'Transporte'
        ];

        // Buscar pedidos do distribuidor
        $orders = DistributorOrder::with(['vendor','items'])
            ->where('distributor_id', $distributor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupar pedidos por status para Kanban
        $ordersByStatus = [];
        foreach ($kanbanColumns as $statusKey => $statusLabel) {
            $ordersByStatus[$statusKey] = $orders->where('status', $statusKey)->values();
        }

        // Contadores por status
        $statusCounts = [];
        foreach ($kanbanColumns as $statusKey => $statusLabel) {
            $statusCounts[$statusKey] = $orders->where('status', $statusKey)->count();
        }

        return view('distributor.orders.index', compact('ordersByStatus', 'kanbanColumns', 'orders', 'view', 'statusCounts'));
    }

    /**
     * Atualizar status via Kanban (drag & drop)
     */
    public function updateKanbanStatus(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $order = DistributorOrder::where('id', $request->order_id)
            ->where('distributor_id', $distributor->id)
            ->first();
            
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido não encontrado'], 404);
        }
        
        $order->status = $request->status;
        $order->save();
        
        // Notificar jornaleiro sobre atualização
        $vendor = Vendor::find($order->vendor_id);
        $fcm_token = $vendor ? $vendor->cm_firebase_token : null;
        
        try {
            if ($fcm_token) {
                $data = [
                    'title' => 'Atualização de Pedido',
                    'description' => 'O status do seu pedido #'.$order->id.' foi atualizado',
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            // Falha na notificação, apenas logamos
        }
        
        return response()->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
    }
}
