<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\DistributorVendorProduct;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\Vendor;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistributorProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:vendor');
    }

    /**
     * Lista produtos disponíveis dos distribuidores
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $query = DistributorVendorProduct::with(['food', 'distributor'])
            ->forVendor($vendor->id)
            ->active()
            ->whereHas('food', function($q) {
                $q->where('status', 1);
            });

        // Filtros
        if ($request->has('distributor_id') && $request->distributor_id != '') {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('food', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('in_stock') && $request->in_stock == '1') {
            $query->inStock();
        }

        // Ordenação alfabética pelo nome do produto (tabela food)
        $products = $query
            ->select('distributor_vendor_products.*')
            ->join('food', 'food.id', '=', 'distributor_vendor_products.food_id')
            ->orderBy('food.name', 'asc')
            ->paginate(12);
        $products->appends($request->query());
        
        // Lista de distribuidores para o filtro
        $distributors = Vendor::where('distributor', 1)
            ->where('status', 1)
            ->whereHas('distributorProducts', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->where('status', 1);
            })
            ->select('id', 'f_name', 'l_name')
            ->orderBy('f_name', 'asc')
            ->get();

        return view('vendor-views.distributor-products.index', compact('products', 'distributors'));
    }

    /**
     * Mostra detalhes de um produto do distribuidor
     */
    public function show($id)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $product = DistributorVendorProduct::with(['food', 'distributor'])
            ->forVendor($vendor->id)
            ->active()
            ->findOrFail($id);

        return view('vendor-views.distributor-products.show', compact('product'));
    }

    /**
     * Adiciona produto ao carrinho (sessão)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:distributor_vendor_products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $vendor = Auth::guard('vendor')->user();
        
        $product = DistributorVendorProduct::with('food')
            ->forVendor($vendor->id)
            ->active()
            ->findOrFail($request->product_id);

        // Verifica disponibilidade
        if (!$product->isAvailableForOrder($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não disponível para a quantidade solicitada.'
            ]);
        }

        // Adiciona ao carrinho na sessão
        $cart = session()->get('distributor_cart', []);
        $cartKey = $product->distributor_id . '_' . $product->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'distributor_id' => $product->distributor_id,
                'product_id' => $product->id,
                'food_id' => $product->food_id,
                'product_name' => $product->food->name,
                'unit_price' => $product->final_price,
                'quantity' => $request->quantity,
                'min_quantity' => $product->min_quantity,
                'stock_quantity' => $product->getAvailableStock(),
                'image' => $product->food->image_full_url
            ];
        }

        // Verifica se não excede o estoque
        if ($cart[$cartKey]['quantity'] > $product->getAvailableStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Quantidade solicitada excede o estoque disponível.'
            ]);
        }

        session()->put('distributor_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Mostra o carrinho de compras
     */
    public function cart()
    {
        $cart = session()->get('distributor_cart', []);
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $key => $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $cartItems[] = array_merge($item, [
                'cart_key' => $key,
                'total_price' => $itemTotal
            ]);
            $totalAmount += $itemTotal;
        }

        // Agrupa por distribuidor
        $cartByDistributor = [];
        foreach ($cartItems as $item) {
            $distributorId = $item['distributor_id'];
            if (!isset($cartByDistributor[$distributorId])) {
                $distributor = Vendor::find($distributorId);
                $cartByDistributor[$distributorId] = [
                    'distributor' => $distributor,
                    'items' => [],
                    'total' => 0
                ];
            }
            $cartByDistributor[$distributorId]['items'][] = $item;
            $cartByDistributor[$distributorId]['total'] += $item['total_price'];
        }

        return view('vendor-views.distributor-products.cart', compact('cartByDistributor', 'totalAmount'));
    }

    /**
     * Atualiza quantidade no carrinho
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('distributor_cart', []);
        
        if (!isset($cart[$request->cart_key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item não encontrado no carrinho.'
            ]);
        }

        // Verifica estoque
        $product = DistributorVendorProduct::find($cart[$request->cart_key]['product_id']);
        if (!$product || $request->quantity > $product->getAvailableStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Quantidade não disponível em estoque.'
            ]);
        }

        $cart[$request->cart_key]['quantity'] = $request->quantity;
        session()->put('distributor_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Carrinho atualizado!',
            'new_total' => $request->quantity * $cart[$request->cart_key]['unit_price']
        ]);
    }

    /**
     * Remove item do carrinho
     */
    public function removeFromCart(Request $request)
    {
        $cart = session()->get('distributor_cart', []);
        
        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('distributor_cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removido do carrinho!',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Finaliza pedido
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:vendors,id',
            'delivery_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'delivery_date' => 'nullable|date|after:today'
        ]);

        $vendor = Auth::guard('vendor')->user();
        $cart = session()->get('distributor_cart', []);
        
        // Filtra itens do distribuidor específico
        $distributorItems = array_filter($cart, function($item) use ($request) {
            return $item['distributor_id'] == $request->distributor_id;
        });

        if (empty($distributorItems)) {
            return back()->withErrors(['error' => 'Nenhum item encontrado para este distribuidor.']);
        }

        try {
            DB::beginTransaction();

            // Cria o pedido
            $order = DistributorOrder::create([
                'order_number' => DistributorOrder::generateOrderNumber(),
                'distributor_id' => $request->distributor_id,
                'vendor_id' => $vendor->id,
                'total_amount' => 0,
                'total_items' => 0,
                'status' => 'pending',
                'notes' => $request->notes,
                'delivery_date' => $request->delivery_date,
                'delivery_address' => $request->delivery_address,
                'payment_status' => 'pending',
                'payment_method' => 'cash'
            ]);

            // Adiciona os itens
            foreach ($distributorItems as $cartKey => $item) {
                $order->addItem(
                    $item['food_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['product_name']
                );

                // Reduz estoque
                $product = DistributorVendorProduct::find($item['product_id']);
                $product->reduceStock($item['quantity']);

                // Remove do carrinho
                unset($cart[$cartKey]);
            }

            session()->put('distributor_cart', $cart);
            DB::commit();

            return redirect()->route('vendor.distributor-orders.show', $order->id)
                ->with('success', 'Pedido realizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao processar pedido: ' . $e->getMessage()]);
        }
    }

    /**
     * Lista pedidos feitos aos distribuidores
     */
    public function orders(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $query = DistributorOrder::with(['distributor', 'items'])
            ->forVendor($vendor->id);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return view('vendor-views.distributor-products.orders', compact('orders'));
    }

    /**
     * Mostra detalhes de um pedido
     */
    public function showOrder($id)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $order = DistributorOrder::with(['distributor', 'items.food'])
            ->forVendor($vendor->id)
            ->findOrFail($id);

        return view('vendor-views.distributor-products.order-details', compact('order'));
    }

    /**
     * Cancela um pedido
     */
    public function cancelOrder(Request $request, $id)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $order = DistributorOrder::forVendor($vendor->id)->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return back()->withErrors(['error' => 'Este pedido não pode ser cancelado.']);
        }

        try {
            DB::beginTransaction();

            // Restaura estoque
            foreach ($order->items as $item) {
                $product = DistributorVendorProduct::where([
                    'distributor_id' => $order->distributor_id,
                    'vendor_id' => $vendor->id,
                    'food_id' => $item->food_id
                ])->first();
                
                if ($product) {
                    $product->addStock($item->quantity);
                }
            }

            $order->updateStatus('cancelled', $request->reason);
            DB::commit();

            return back()->with('success', 'Pedido cancelado com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao cancelar pedido: ' . $e->getMessage()]);
        }
    }
    public function cartCount(Request $request)
    {
        $cart = session()->get('distributor_cart', []);
        $cart_count = array_sum(array_column($cart, 'quantity'));
        return response()->json(['cart_count' => $cart_count]);
    }
}
