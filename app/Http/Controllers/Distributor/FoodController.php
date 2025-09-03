<?php

namespace App\Http\Controllers\Distributor;

use App\Models\Food;
use App\Models\Category;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderDetail; // added
use App\Models\Order; // added

class FoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    public function index()
    {
        $distributor = Auth::guard('distributor')->user();
        $categories = Category::where(['position' => 0])->get();
        
        try {
            $taxData = Helpers::getTaxSystemType();
            $productWiseTax = $taxData['productWiseTax'];
            $taxVats = $taxData['taxVats'];
        } catch (\Exception $e) {
            $productWiseTax = false;
            $taxVats = [];
        }
        
        return view('distributor-views.food.index', compact('categories', 'productWiseTax', 'taxVats'));
    }

    public function list(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $key = explode(' ', $request['search']);
        
        $foodsQuery = Food::where('vendor_id', $distributor->id)
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            });

        // Add derived column with the last sale datetime (last delivered order that includes the product)
        $foodsQuery->addSelect([
            'last_sale_at' => OrderDetail::selectRaw('MAX(order_details.created_at)')
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->whereColumn('order_details.food_id', 'food.id')
                ->where('orders.order_status', 'delivered')
        ]);

        $foods = $foodsQuery
            ->latest()
            ->paginate(config('default_pagination'));
            
        return view('distributor-views.food.list', compact('foods'));
    }

    public function store(Request $request)
    {
        try {
            $distributor = Auth::guard('distributor')->user();
            
            // Validação básica
            if (!$request->has('name') || !$request->name[0]) {
                return response()->json(['errors' => [['message' => 'Nome do produto é obrigatório']]]);
            }
            
            if (!$request->category_id) {
                return response()->json(['errors' => [['message' => 'Categoria é obrigatória']]]);
            }
            
            if (!$request->price) {
                return response()->json(['errors' => [['message' => 'Preço é obrigatório']]]);
            }
            
            // Campos essenciais adicionais
            if (!$request->product_type) {
                return response()->json(['errors' => [['message' => 'Tipo de produto é obrigatório']]]);
            }
            if (!$request->quantity || (int) $request->quantity < 1) {
                return response()->json(['errors' => [['message' => 'Quantidade é obrigatória']]]);
            }
            
            // Criar produto simples
            $food = new Food();
            $food->name = $request->name[0];
            $food->category_id = $request->category_id;
            // Normaliza preço: aceita "7,90" (BR) ou "7.90" (normalizado)
            $food->price = (float) (strpos($request->price, ',') !== false
                ? str_replace(',', '.', str_replace('.', '', $request->price))
                : str_replace(',', '', $request->price));
            $food->description = $request->description[0] ?? '';
            $food->vendor_id = $distributor->id;
            $food->restaurant_id = $distributor->id;
            $food->veg = 0;
            $food->status = 1;
            $food->discount = 0;
            $food->discount_type = 'amount';
            // Estoque principal
            $food->stock_type = 'fixed';
            $food->item_stock = (int) $request->quantity;
            $food->maximum_cart_quantity = $request->maximum_cart_quantity ?? 1;
            $food->is_halal = 0;
            // Category ids (inclui subcategoria se enviada)
            $categoryIds = [ ['id' => (int) $request->category_id, 'position' => 1] ];
            if ($request->filled('sub_category_id')) {
                $categoryIds[] = ['id' => (int) $request->sub_category_id, 'position' => 2];
            }
            $food->category_ids = json_encode($categoryIds);

            // choice_options e variations (se vierem do formulário)
            $choiceOptions = [];
            if ($request->filled('choice_options')) {
                try {
                    $parsed = json_decode($request->input('choice_options'), true);
                    if (is_array($parsed)) {
                        $choiceOptions = $parsed;
                    }
                } catch (\Exception $e) {
                }
            }
            $variations = [];
            if ($request->filled('variations')) {
                try {
                    $parsedVar = json_decode($request->input('variations'), true);
                    if (is_array($parsedVar)) {
                        $variations = $parsedVar;
                    }
                } catch (\Exception $e) {
                }
            }
            $food->choice_options = json_encode($choiceOptions);
            $food->variations = json_encode($variations);

            // Guardar o tipo de unidade nos atributos
            $food->attributes = json_encode(['product_type' => $request->product_type]);
            $food->add_ons = json_encode([]);
            
            if ($request->hasFile('image')) {
                $food->image = Helpers::upload(dir: 'product/', format: 'png', image: $request->file('image'));
            }
            
            $food->save();
            
            return response()->json(['message' => 'Produto cadastrado com sucesso!']);
            
        } catch (\Exception $e) {
            return response()->json(['errors' => [['message' => 'Erro: ' . $e->getMessage()]]]);
        }
    }

    public function edit($id)
    {
        $distributor = Auth::guard('distributor')->user();
        $product = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        $product_category = json_decode($product->category_ids);
        $categories = Category::where(['position' => 0])->get();
        
        // Simplificando tax system para evitar erros
        $productWiseTax = false;
        $taxVats = [];
        
        return view('distributor-views.food.edit', compact('product', 'product_category', 'categories', 'productWiseTax', 'taxVats'));
    }

    public function update(Request $request, $id)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        
        // Validação básica
        if (!$request->has('name') || !$request->name[0]) {
            return response()->json(['errors' => [['message' => 'Nome do produto é obrigatório']]]);
        }
        
        $food->name = $request->name[0];
        $food->category_id = $request->category_id;
        // Normaliza preço: aceita "7,90" (BR) ou "7.90" (normalizado)
        $food->price = (float) (strpos($request->price, ',') !== false
            ? str_replace(',', '.', str_replace('.', '', $request->price))
            : str_replace(',', '', $request->price));
        $food->description = $request->description[0] ?? '';
        
        // Atualizar category_ids com possível subcategoria
        $categoryIds = [ ['id' => (int) $request->category_id, 'position' => 1] ];
        if ($request->filled('sub_category_id')) {
            $categoryIds[] = ['id' => (int) $request->sub_category_id, 'position' => 2];
        }
        $food->category_ids = json_encode($categoryIds);

        // Se enviado, atualizar estoque e tipo de unidade
        if ($request->filled('quantity')) {
            $food->stock_type = 'fixed';
            $food->item_stock = (int) $request->quantity;
        }
        if ($request->filled('product_type')) {
            $attr = [];
            if (!empty($food->attributes)) {
                $decoded = json_decode($food->attributes, true);
                if (is_array($decoded)) {
                    $attr = $decoded;
                }
            }
            $attr['product_type'] = $request->product_type;
            $food->attributes = json_encode($attr);
        }
        if ($request->filled('maximum_cart_quantity')) {
            $food->maximum_cart_quantity = (int) $request->maximum_cart_quantity;
        }

        // Atualiza choice_options e variations, se enviados
        if ($request->filled('choice_options')) {
            try {
                $parsed = json_decode($request->input('choice_options'), true);
                if (is_array($parsed)) {
                    $food->choice_options = json_encode($parsed);
                }
            } catch (\Exception $e) {}
        }
        if ($request->filled('variations')) {
            try {
                $parsedVar = json_decode($request->input('variations'), true);
                if (is_array($parsedVar)) {
                    $food->variations = json_encode($parsedVar);
                }
            } catch (\Exception $e) {}
        }
        
        if ($request->hasFile('image')) {
            $food->image = Helpers::update(dir: 'product/', old_image: $food->image, format: 'png', image: $request->file('image'));
        }
        
        $food->save();
        
        return response()->json(['message' => 'Produto atualizado com sucesso!']);
    }

    public function delete($id)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        
        if ($food->image) {
            Helpers::delete('product/' . $food->image);
        }
        
        $food->delete();
        
        return back()->with('success', 'Produto deletado com sucesso!');
    }

    public function status(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($request->id);
        $food->status = $request->status;
        $food->save();
        
        return back()->with('success', 'Disponibilidade atualizada com sucesso!');
    }

    public function get_categories(Request $request)
    {
        $categories = Category::where(['parent_id' => $request->parent_id])->get();
        return response()->json($categories);
    }
}
