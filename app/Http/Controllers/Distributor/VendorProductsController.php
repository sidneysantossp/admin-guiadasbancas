<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DistributorVendorProduct;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class VendorProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    /**
     * Lista produtos habilitados pelos jornaleiros
     */
    public function index(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        
        $query = DistributorVendorProduct::with(['food', 'vendor'])
            ->forDistributor($distributor->id)
            ->active()
            ->whereHas('food', function($q) {
                $q->where('status', 1);
            });

        // Filtros
        if ($request->has('vendor_id') && $request->vendor_id != '') {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('food', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('low_stock') && $request->low_stock == '1') {
            $query->where('stock_quantity', '<=', 10);
        }

        // Ordenação alfabética pelo nome do produto
        $products = $query
            ->select('distributor_vendor_products.*')
            ->join('food', 'food.id', '=', 'distributor_vendor_products.food_id')
            ->orderBy('food.name', 'asc')
            ->paginate(15);
        $products->appends($request->query());
        
        // Lista de jornaleiros para o filtro
        $vendors = Vendor::where('distributor', 0)
            ->where('status', 1)
            ->whereHas('availableDistributorProducts', function($q) use ($distributor) {
                $q->where('distributor_id', $distributor->id)->where('status', 1);
            })
            ->select('id', 'f_name', 'l_name')
            ->orderBy('f_name', 'asc')
            ->get();

        return view('distributor-views.vendor-products.index', compact('products', 'vendors'));
    }

    /**
     * Atualiza o estoque de um produto
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:distributor_vendor_products,id',
            'stock_quantity' => 'required|integer|min:0'
        ]);

        $distributor = Auth::guard('distributor')->user();
        
        $product = DistributorVendorProduct::forDistributor($distributor->id)
            ->findOrFail($request->product_id);

        $product->stock_quantity = $request->stock_quantity;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Estoque atualizado com sucesso!'
        ]);
    }

    /**
     * Atualiza estoque em lote
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:distributor_vendor_products,id',
            'products.*.stock_quantity' => 'required|integer|min:0'
        ]);

        $distributor = Auth::guard('distributor')->user();
        
        try {
            foreach ($request->products as $productData) {
                $product = DistributorVendorProduct::forDistributor($distributor->id)
                    ->findOrFail($productData['id']);
                
                $product->stock_quantity = $productData['stock_quantity'];
                $product->save();
            }

            Toastr::success('Estoque atualizado em lote com sucesso!');
            return back();

        } catch (\Exception $e) {
            Toastr::error('Erro ao atualizar estoque: ' . $e->getMessage());
            return back();
        }
    }
}