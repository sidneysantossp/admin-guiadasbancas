<?php

namespace App\Http\Controllers\Distributor;

use App\Models\Allergy;
use App\Models\Nutrition;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Food;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
            // Em caso de erro, usar valores padrão
            $productWiseTax = false;
            $taxVats = [];
        }
        
        return view('distributor-views.food.index', compact('categories', 'productWiseTax', 'taxVats'));
    }

    public function list(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $key = explode(' ', $request['search']);
        
        $foods = Food::where('vendor_id', $distributor->id)
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
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
            
            // Criar produto simples
            $food = new Food();
            $food->name = $request->name[0];
            $food->category_id = $request->category_id;
            $food->price = (float) str_replace(',', '.', str_replace('.', '', $request->price));
            $food->description = $request->description[0] ?? '';
            $food->vendor_id = $distributor->id;
            $food->restaurant_id = $distributor->id;
            $food->veg = 0;
            $food->status = 1;
            $food->discount = 0;
            $food->discount_type = 'amount';
            $food->stock_type = 'unlimited';
            $food->item_stock = 0;
            $food->maximum_cart_quantity = $request->maximum_cart_quantity ?? 1;
            $food->is_halal = 0;
            $food->category_ids = json_encode([['id' => $request->category_id, 'position' => 1]]);
            $food->choice_options = json_encode([]);
            $food->variations = json_encode([]);
            $food->attributes = json_encode([]);
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
        
        try {
            $taxData = Helpers::getTaxSystemType();
            $productWiseTax = $taxData['productWiseTax'];
            $taxVats = $taxData['taxVats'];
        } catch (\Exception $e) {
            $productWiseTax = false;
            $taxVats = [];
        }
        
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
        $food->price = (float) str_replace(',', '.', str_replace('.', '', $request->price));
        $food->description = $request->description[0] ?? '';
        
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
        
        return back()->with('success', 'Status atualizado com sucesso!');
    }

    public function get_categories(Request $request)
    {
        $categories = Category::where(['parent_id' => $request->parent_id])->get();
        return response()->json($categories);
    }

}
        
        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $discount;
        } else {
            $dis = $discount;
        }

        if ($discount > 0 && $request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if (($discount > 0 && $request['price'] <= $dis) || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        
        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids, $tag->id);
            }
        }

        $nutrition_ids = [];
        if ($request->nutritions != null) {
            $nutritions = $request->nutritions;
        }
        if (isset($nutritions)) {
            foreach ($nutritions as $key => $value) {
                $nutrition = Nutrition::firstOrNew(
                    ['nutrition' => $value]
                );
                $nutrition->save();
                array_push($nutrition_ids, $nutrition->id);
            }
        }
        
        $allergy_ids = [];
        if ($request->allergies != null) {
            $allergies = $request->allergies;
        }
        if (isset($allergies)) {
            foreach ($allergies as $key => $value) {
                $allergy = Allergy::firstOrNew(
                    ['allergy' => $value]
                );
                $allergy->save();
                array_push($allergy_ids, $allergy->id);
            }
        }

        $food = new Food;
        $food->name = $request->name[array_search('default', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $food->category_ids = json_encode($category);
        $food->category_id = $request?->sub_category_id ?? $request?->category_id;
        $food->description = $request->description[array_search('default', $request->lang)];
        $food->choice_options = json_encode([]);
        $food->variations = json_encode([]);
        $food->price = $request->price;
        $food->image = Helpers::upload(dir: 'product/', format: 'png', image: $request->file('image'));
        $food->available_time_starts = $request->available_time_starts;
        $food->available_time_ends = $request->available_time_ends;
        $food->discount = $request->discount ?? 0;
        $food->discount_type = $request->discount_type;
        $food->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $food->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $food->vendor_id = $distributor->id; // Associa ao distribuidor logado
        $food->restaurant_id = $distributor->id; // Compatibilidade com sistema existente
        $food->veg = $request->veg;
        $food->item_stock = $request?->item_stock ?? 0;
        $food->stock_type = $request->stock_type;
        $food->maximum_cart_quantity = $request->maximum_cart_quantity;
        $food->is_halal = $request->is_halal ?? 0;
        $food->status = 1; // Produto ativo por padrão

        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {
                if ($option['min'] > 0 && $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
            }

            $food->save();

            foreach (array_values($request->options) as $key => $option) {
                $variation = New Variation();
                $variation->food_id = $food->id;
                $variation->name = $option['name'];
                $variation->type = $option['type'];
                $variation->min = $option['min'] ?? 0;
                $variation->max = $option['max'] ?? 0;
                $variation->is_required = data_get($option, 'required') == 'on' ? true : false;
                $variation->save();

                foreach (array_values($option['values']) as $value) {
                    $VariationOption = New VariationOption();
                    $VariationOption->food_id = $food->id;
                    $VariationOption->variation_id = $variation->id;
                    $VariationOption->option_name = $value['label'];
                    $VariationOption->option_price = $value['optionPrice'];
                    $VariationOption->stock_type = $request?->stock_type ?? 'unlimited';
                    $VariationOption->stock = $value['current_stock'] ?? 0;
                    $VariationOption->save();
                }
            }
        } else {
            $food->save();
        }

        // Garantir que o food foi salvo e tem ID
        if (!$food->id) {
            $food->save();
        }

        // Salvar tags, nutrições e alergias
        if (count($tag_ids) > 0) {
            $food->tags()->sync($tag_ids);
        }
        if (count($nutrition_ids) > 0) {
            $food->nutritions()->sync($nutrition_ids);
        }
        if (count($allergy_ids) > 0) {
            $food->allergies()->sync($allergy_ids);
        }

        // Salvar traduções
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if ($default_lang == $key && !($request->name[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Food',
                            'translationable_id' => $food->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $food->name]
                    );
                }
            } else {
                if ($request->name[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Food',
                            'translationable_id' => $food->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $request->name[$index]]
                    );
                }
            }
            if ($default_lang == $key && !($request->description[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Food',
                            'translationable_id' => $food->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $food->description]
                    );
                }
            } else {
                if ($request->description[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Food',
                            'translationable_id' => $food->id,
                            'locale' => $key,
                            'key' => 'description'
                        ],
                        ['value' => $request->description[$index]]
                    );
                }
            }
        }

            // Código antigo removido
        }
    }

    public function update(Request $request, $id)
    {
        $distributor = Auth::guard('distributor')->user();
        $product = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        $product_category = json_decode($product->category_ids);
        $categories = Category::where(['position' => 0])->get();
        
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        
        return view('distributor-views.food.edit', compact('product', 'product_category', 'categories', 'productWiseTax', 'taxVats'));
    }

    public function update(Request $request, $id)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'image' => 'nullable|max:2048',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'discount' => 'nullable|numeric|min:0',
            'description.*' => 'max:1000',
            'veg' => 'required'
        ]);

        $discount = $request['discount'] ?? 0;
        
        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $discount;
        } else {
            $dis = $discount;
        }

        if ($discount > 0 && $request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if (($discount > 0 && $request['price'] <= $dis) || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $food->name = $request->name[array_search('default', $request->lang)];
        $food->category_id = $request?->sub_category_id ?? $request?->category_id;
        $food->description = $request->description[array_search('default', $request->lang)];
        $food->price = $request->price;
        
        if ($request->has('image')) {
            $food->image = Helpers::update(dir: 'product/', old_image: $food->image, format: 'png', image: $request->file('image'));
        }
        
        $food->available_time_starts = $request->available_time_starts;
        $food->available_time_ends = $request->available_time_ends;
        $food->discount = $request->discount ?? 0;
        $food->discount_type = $request->discount_type;
        $food->veg = $request->veg;
        $food->item_stock = $request?->item_stock ?? 0;
        $food->stock_type = $request->stock_type;
        $food->maximum_cart_quantity = $request->maximum_cart_quantity;
        $food->is_halal = $request->is_halal ?? 0;
        $food->save();

        Toastr::success(translate('messages.product_updated_successfully'));
        return response()->json(['message' => translate('messages.product_updated_successfully')]);
    }

    public function delete($id)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($id);
        
        if ($food->image) {
            Helpers::delete('product/' . $food->image);
        }
        
        $food->delete();
        
        Toastr::success(translate('messages.product_deleted_successfully'));
        return back();
    }

    public function status(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $food = Food::where('vendor_id', $distributor->id)->findOrFail($request->id);
        $food->status = $request->status;
        $food->save();
        
        Toastr::success(translate('messages.status_updated_successfully'));
        return back();
    }

    public function get_categories(Request $request)
    {
        $categories = Category::where(['parent_id' => $request->parent_id])->get();
        return response()->json($categories);
    }
}