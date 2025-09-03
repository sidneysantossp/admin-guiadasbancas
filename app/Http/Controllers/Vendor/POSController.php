<?php

namespace App\Http\Controllers\Vendor;

use App\Scopes\RestaurantScope;
use App\Traits\PlaceNewOrder;
use Carbon\Carbon;
use App\Models\Food;
use App\Models\User;
use App\Models\Order;
use App\Mail\PlaceOrder;
use App\Models\Category;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class POSController extends Controller
{
    use PlaceNewOrder;
    public function index(Request $request)
    {
        $time = Carbon::now()->toTimeString();
        $category = $request->query('category_id', 0);
        $categories = Category::active()->get();
        $keyword = $request->query('keyword', false);
        $key = explode(' ', $keyword);
        $products = Food::active()->
        when($category, function($query)use($category){
            $query->whereHas('category',function($q)use($category){
                return $q->whereId($category)->orWhere('parent_id', $category);
            });
        })
        ->when($keyword, function($query)use($key){
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })->available($time)
        ->latest()->paginate(10);
        return view('vendor-views.pos.index', compact('categories', 'products','category', 'keyword'));
    }

    public function quick_view(Request $request)
    {
        $product = Food::findOrFail($request->product_id);

        return response()->json([
            'success' => 1,
            'view' => view('vendor-views.pos._quick-view-data', compact('product'))->render(),
        ]);
    }

    public function quick_view_card_item(Request $request)
    {
        $product = Food::findOrFail($request->product_id);
        $item_key = $request->item_key;
        $cart_item = session()->get('cart')[$item_key];

        return response()->json([
            'success' => 1,
            'view' => view('vendor-views.pos._quick-view-cart-item', compact('product', 'cart_item', 'item_key'))->render(),
        ]);
    }

    public function variant_price(Request $request)
    {
        $product = Food::find($request->id);
        $price = $product->price;
        $addon_price = 0;
        $add_on_ids=[];
        $add_on_qtys=[];
        if ($request['addon_id']) {
            foreach ($request['addon_id'] as $id) {
                $add_on_ids[]= $id;
                $add_on_qtys[]= $request['addon-quantity' . $id];
                $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
            }
        }
        $addonAndVariationStock= Helpers::addonAndVariationStockCheck(product:$product, quantity: $request->quantity,add_on_qtys:$add_on_qtys, variation_options:explode(',',$request?->option_ids),add_on_ids:$add_on_ids );
        if(data_get($addonAndVariationStock, 'out_of_stock') != null) {
            return response()->json([
                'error' => 'stock_out',  'message' => data_get($addonAndVariationStock, 'out_of_stock'),
                'current_stock' => data_get($addonAndVariationStock, 'current_stock'),
                'id'=> data_get($addonAndVariationStock, 'id'),
                'type'=> data_get($addonAndVariationStock, 'type'),
            ],203);
        }

        $product_variations = json_decode($product->variations, true);
        if ($request->variations && count($product_variations)) {
            $price_total =  $price + Helpers::variation_price(product:$product_variations,variations: $request->variations);
            $price= $price_total - Helpers::product_discount_calculate(product:$product, price:$price_total, restaurant:Helpers::get_restaurant_data());
        } else {
            $price = $product->price - Helpers::product_discount_calculate(product:$product, price:$product->price, restaurant:Helpers::get_restaurant_data());
        }
        return array('price' => Helpers::format_currency(($price * $request->quantity) + $addon_price));
    }

    public function addDeliveryInfo(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'floor' => 'required',
            'road' => 'required',
            'house' => 'required',
            'delivery_fee' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'delivery',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'delivery_fee' => $request->delivery_fee,
            'distance' => $request->distance,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('vendor-views.pos._address', compact('address'))->render(),
        ]);
    }

    public function addToCart(Request $request)
    {
        $product = Food::find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $addon_price = 0;
        $variation_price=0;
        $add_on_ids=[];
        $add_on_qtys=[];

        $product_variations = json_decode($product->variations, true);
        if ($request->variations && count($product_variations)) {
            foreach($request->variations  as $key=> $value ){

                if($value['required'] == 'on' &&  isset($value['values']) == false){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select items from') . ' ' . $value['name'],
                    ]);
                }
                if(isset($value['values'])  && $value['min'] != 0 && $value['min'] > count($value['values']['label'])){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select minimum ').$value['min'].translate(' For ').$value['name'].'.',
                    ]);
                }
                if(isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select maximum ').$value['max'].translate(' For ').$value['name'].'.',
                    ]);
                }
            }
            $variation_data = Helpers::get_varient(product_variations:$product_variations,variations: $request->variations);
            $variation_price = $variation_data['price'];
            $variations = $request->variations;
        }

        $data['variations'] = $variations;
        $data['variant'] = $str;

        $price = $product->price + $variation_price;
        $data['variation_price'] = $variation_price;

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $product_discount =  Helpers::food_discount_calculate($product, $price, $product->restaurant, false);

        $data['discount'] = $product_discount['discount_amount'];
        $data['image'] = $product->image;
        $data['image_full_url'] = $product->image_full_url;
        $data['add_ons'] = [];
        $data['add_on_qtys'] = [];
        $data['maximum_cart_quantity'] = $product->maximum_cart_quantity;
        $data['variation_option_ids'] = $request?->option_ids ?? null;
        if($request['addon_id'])
        {
            foreach($request['addon_id'] as $id)
            {
                $add_on_ids[]= $id;
                $add_on_qtys[]= $request['addon-quantity' . $id];
                $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                $data['add_on_qtys'][]=$request['addon-quantity'.$id];
            }
            $data['add_ons'] = $request['addon_id'];
        }
        $addonAndVariationStock= Helpers::addonAndVariationStockCheck(product:$product,quantity: $request->quantity,add_on_qtys:$add_on_qtys, variation_options:explode(',',$request?->option_ids),add_on_ids:$add_on_ids );
        if(data_get($addonAndVariationStock, 'out_of_stock') != null) {
            return response()->json([
                'data' => 'stock_out',
                'message' => data_get($addonAndVariationStock, 'out_of_stock'),
                'current_stock' => data_get($addonAndVariationStock, 'current_stock'),
                'id'=> data_get($addonAndVariationStock, 'id'),
                'type'=> data_get($addonAndVariationStock, 'type'),
            ],203);
        }

        $data['addon_price'] = $addon_price;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            if(isset($request->cart_item_key))
            {
                $cart[$request->cart_item_key] = $data;
                $data = 2;
            }
            else
            {
                $cart->push($data);
            }

        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        $this->setPosCalculatedTax($product->restaurant);

        return response()->json([
            'data' => $data
        ]);
    }

    public function cart_items()
    {
        return view('vendor-views.pos._cart');
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));

            $item_id = $cart[$request->key]['id'];
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);

            $product = Food::withoutGlobalScope(RestaurantScope::class)->find($item_id);
            if ($product && $product->restaurant) {
                $this->setPosCalculatedTax($product->restaurant);
            }
        }

        return response()->json([],200);
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $product= Food::find($request->food_id);
        if($request->option_ids){
            $addonAndVariationStock= Helpers::addonAndVariationStockCheck(product:$product,quantity: $request->quantity, variation_options:explode(',',$request?->option_ids));
            if(data_get($addonAndVariationStock, 'out_of_stock') != null) {
                return response()->json([
                    'data' => 'stock_out',
                    'message' => data_get($addonAndVariationStock, 'out_of_stock'),
                    'current_stock' => data_get($addonAndVariationStock, 'current_stock'),
                    'id'=> data_get($addonAndVariationStock, 'id'),
                    'type'=> data_get($addonAndVariationStock, 'type'),
                ],203);
            }

        }

        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);

        try {
            $product_id = $cart[$request->key]['id'];
            $product = Food::withoutGlobalScope(RestaurantScope::class)->with('restaurant')->find($product_id);
            if ($product && $product->restaurant) {
                $this->setPosCalculatedTax($product->restaurant);
            }
        } catch (\Exception $exception) {
            info((string)[$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
        }

        return response()->json([],200);
    }

    //empty Cart
    public function emptyCart(Request $request)
    {
        session()->forget('cart');
        session()->forget('tax_amount');
        session()->forget('tax_included');
        session()->forget('address');
        return response()->json([],200);
    }

    public function update_tax(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_discount(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['discount'] = $request->discount;
        $cart['discount_type'] = $request->type;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_paid(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['paid'] = $request->paid;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = User::
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                ->orWhere('l_name', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
            }
        })
        ->limit(8)
        ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        $data[]=(object)['id'=>false, 'text'=>translate('messages.walk_in_customer')];

        $reversed = $data->toArray();

        $data = array_reverse($reversed);


        return response()->json($data);
    }

    public function place_order(Request $request)
    {
        if(!$request->type){
            Toastr::error(translate('No payment method selected'));
            return back();
        }

        if($request->session()->has('cart'))
        {
            if(count($request->session()->get('cart')) < 1)
            {
                Toastr::error(translate('messages.cart_empty_warning'));
                return back();
            }
        }
        else
        {
            Toastr::error(translate('messages.cart_empty_warning'));
            return back();
        }
        if ($request->session()->has('address')) {
            if(!$request->user_id){
                Toastr::error(translate('messages.no_customer_selected'));
                return back();
            }
            $address = $request->session()->get('address');
        }

        $restaurant = Helpers::get_restaurant_data();
        $self_delivery_status = $restaurant->self_delivery_system;

        $rest_sub=$restaurant?->restaurant_sub;
        if ( $restaurant->restaurant_model == 'subscription' && isset($rest_sub)) {
            $self_delivery_status = $rest_sub->self_delivery;
            if($rest_sub->max_order != "unlimited" && $rest_sub->max_order <= 0){
                Toastr::error(translate('messages.You_have_reached_the_maximum_number_of_orders'));
                return back();
            }
        } elseif( $restaurant->restaurant_model == 'unsubscribed'){
            Toastr::error(translate('messages.You_are_not_subscribed_or_your_subscription_has_expired'));
            return back();
        }


        $cart = $request->session()->get('cart');

        $total_addon_price = 0;
        $product_price = 0;
        $restaurant_discount_amount = 0;

        $order_details = [];
        $order = new Order();
        $order->id = 100000 + Order::count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::latest()->first()->id + 1;
        }
        $order->payment_status = isset($address)?'unpaid':'paid';
        if($request->user_id){

            $order->order_status = isset($address)?'confirmed':'delivered';
            $order->order_type = isset($address)?'delivery':'take_away';
        }else{
            $order->order_status = 'delivered';
            $order->order_type = 'take_away';
        }
        $order->delivered = $order->order_status ==  'delivered' ?  now() : null ;
        $order->distance = isset($address) ? $address['distance'] : 0;

        $distance_data = $order->distance ?? 1;
        $extra_charges = 0;
        if($self_delivery_status != 1){
            $data = Helpers::vehicle_extra_charge(distance_data:$distance_data);
            $vehicle_id= (isset($data) ? $data['vehicle_id']  : null);
            $extra_charges = (float) (isset($data) ? $data['extra_charge']  : 0);
        }
        $additional_charge_status = BusinessSetting::where('key', 'additional_charge_status')->first()?->value;
        $additional_charge = BusinessSetting::where('key', 'additional_charge')->first()?->value;
        if ($additional_charge_status == 1) {
            $order->additional_charge = $additional_charge ?? 0;
        } else {
            $order->additional_charge = 0;
        }


        $order->vehicle_id =  $vehicle_id ?? null;
        $order->restaurant_id = $restaurant->id;
        $order->user_id = $request?->user_id;
        $order->zone_id = $restaurant->zone_id;
        $order->delivery_charge = isset($address)?$address['delivery_fee']:0;
        $order->original_delivery_charge = isset($address)?$address['delivery_fee']:0;
        $order->delivery_address = isset($address)?json_encode($address):null;
        $order->checked = 1;
        $order->created_at = now();
        $order->schedule_at = now();
        $order->updated_at = now();
        $order->otp = rand(1000, 9999);

        DB::beginTransaction();

        $additionalCharges = [];
        // $settings = BusinessSetting::whereIn('key', [
        //     'additional_charge_status',
        //     'additional_charge',
        //     'extra_packaging_data',
        // ])->pluck('value', 'key');

        $additional_charge_status  = $settings['additional_charge_status'] ?? null;
        $additional_charge         = $settings['additional_charge'] ?? null;

        // if ($additional_charge_status == 1) {
        //     $additionalCharges['tax_on_additional_charge'] = $additional_charge ?? 0;
        // }

        $order_details = $this->makePosOrderDetails($cart, $restaurant);

        if (data_get($order_details, 'status_code') === 403) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                ]
            ], data_get($order_details, 'status_code'));
        }

        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $restaurant_discount_amount = $order_details['restaurant_discount_amount'];
        $product_data = $order_details['product_data'];
        $order_details = $order_details['order_details'];

        $total_price = $product_price + $total_addon_price - $restaurant_discount_amount;
        $totalDiscount = $restaurant_discount_amount;
        $finalCalculatedTax =  Helpers::getFinalCalculatedTax($order_details, $additionalCharges, $totalDiscount, $total_price, $restaurant->id);


        $tax_amount = $finalCalculatedTax['tax_amount'];
        $tax_status = $finalCalculatedTax['tax_status'];
        $taxMap = $finalCalculatedTax['taxMap'];
        $orderTaxIds = data_get($finalCalculatedTax ,'taxData.orderTaxIds',[] );
        $taxType=  data_get($finalCalculatedTax ,'taxType');
        $order->tax_type = $taxType;

        $order->tax_status = $tax_status;

        try {
            $order->restaurant_discount_amount= $restaurant_discount_amount;
            $order->total_tax_amount= $tax_amount;

            $order->order_amount = $total_price + $tax_amount + $order->delivery_charge  + $order->additional_charge;
            $order->adjusment = $request?->amount ?? $order->order_amount;
            $order->payment_method = $request->type;


            $max_cod_order_amount_value=  BusinessSetting::where('key', 'max_cod_order_amount')->first()?->value ?? 0;
            if($max_cod_order_amount_value > 0 && $order->payment_method == 'cash_on_delivery' && $order->order_amount > $max_cod_order_amount_value){
            Toastr::error(translate('messages.You can not Order more then ').$max_cod_order_amount_value .Helpers::currency_symbol().' '. translate('messages.on COD order.')  );
            return back();
            }

            $order->save();
            $taxMapCollection = collect($taxMap);
            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;

                if ($item['food_id']) {
                    $item_id = $item['food_id'];
                } else {
                    $item_id = $item['item_campaign_id'];
                }
                $index = $taxMapCollection->search(function ($tax) use ($item_id) {
                    return $tax['product_id'] == $item_id;
                });
                if ($index !== false) {
                    $matchedTax = $taxMapCollection->pull($index);
                    $order_details[$key]['tax_status'] = $matchedTax['include'] == 1 ? 'included' : 'excluded';
                    $order_details[$key]['tax_amount'] = $matchedTax['totalTaxamount'];
                }
            }

            OrderDetail::insert($order_details);

            if (count($orderTaxIds)) {
                \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                    orderId: $order->id,
                    orderTaxIds: $orderTaxIds,
                );
            }

            session()->forget('cart');
            session()->forget('address');
            session()->forget('tax_amount');
            session()->forget('tax_included');
            session(['last_order' => $order->id]);

            if ( $restaurant->restaurant_model == 'subscription' && isset($rest_sub)) {
                if ($rest_sub->max_order != "unlimited" && $rest_sub->max_order > 0 ) {
                    $rest_sub->decrement('max_order' , 1);
                    }
            }

            DB::commit();
            //PlaceOrderMail
            try{
                $notification_status= Helpers::getNotificationStatusData('customer','customer_order_notification');

                if($notification_status?->mail_status == 'active' && $order->order_status == 'pending' && config('mail.status') &&  Helpers::get_mail_status('place_order_mail_status_user')== '1' && $order?->customer?->email)
                {
                    Mail::to($order->customer->email)->send(new PlaceOrder($order->id));
                }
                }catch (\Exception $exception) {
                    info([$exception->getFile(),$exception->getLine(),$exception->getMessage()]);
                }
                //PlaceOrderMail end

            Toastr::success(translate('messages.order_placed_successfully'));
            return back();
        } catch (\Exception $exception) {
            DB::rollBack();
            info([$exception->getFile(),$exception->getLine(),$exception->getMessage()]);
        }
        Toastr::warning(translate('messages.failed_to_place_order'));
        return back();
    }


    public function customer_store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
        ]);
        User::create([
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => bcrypt('password')
        ]);
        try {
            $notification_status= Helpers::getNotificationStatusData('customer','customer_pos_registration');

            if ($notification_status?->mail_status == 'active' && config('mail.status') && $request->email && Helpers::get_mail_status('pos_registration_mail_status_user') == '1') {
                Mail::to($request->email)->send(new \App\Mail\CustomerRegistrationPOS($request->f_name . ' ' . $request->l_name,$request['email'],'password'));
                Toastr::success(translate('mail_sent_to_the_user'));
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }
        Toastr::success(translate('customer_added_successfully'));
        return back();
    }
    public function extra_charge(Request $request)
    {
        $distance_data = $request->distancMileResult ?? 1;
        $self_delivery_status = $request->self_delivery_status;
        $extra_charges = 0;
        if($self_delivery_status != 1){
            $data = Helpers::vehicle_extra_charge(distance_data:$distance_data);
            $vehicle_id= (isset($data) ? $data['vehicle_id']  : null);
            $extra_charges = (float) (isset($data) ? $data['extra_charge']  : 0);
        }
            return response()->json($extra_charges,200);
    }
}
