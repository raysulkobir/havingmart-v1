<?php

namespace App\Http\Controllers;

use App\Utility\PayfastUtility;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\Area;
use App\Models\Carrier;
use App\Models\CombinedOrder;
use App\Models\Country;
use App\Models\District;
use App\Models\Product;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Http\Controllers\OrderController;
use App\Utility\PayhereUtility;
use App\Utility\NotificationUtility;
use Session;
use Auth;
use DB;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        // Minumum order amount check
        if(get_setting('minimum_order_amount_check') == 1){
            $subtotal = 0;
            foreach (Cart::where('user_id', Auth::user()->id)->get() as $key => $cartItem){ 
                $product = Product::find($cartItem['product_id']);
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            }
            if ($subtotal < get_setting('minimum_order_amount')) {
                flash(translate('You order amount is less then the minimum order amount'))->warning();
                return redirect()->route('home');
            }
        }
        // Minumum order amount check end
        
        if ($request->payment_option != null) {
            (new OrderController)->store($request);

            $request->session()->put('payment_type', 'cart_payment');
            
            $data['combined_order_id'] = $request->session()->get('combined_order_id');
            $request->session()->put('payment_data', $data);

            if ($request->session()->get('combined_order_id') != null) {

                // If block for Online payment, wallet and cash on delivery. Else block for Offline payment
                $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
                if (class_exists($decorator)) {
                    return (new $decorator)->pay($request);
                }
                else {
                    $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                    $manual_payment_data = array(
                        'name'   => $request->payment_option,
                        'amount' => $combined_order->grand_total,
                        'trx_id' => $request->trx_id,
                        'photo'  => $request->photo
                    );
                    foreach ($combined_order->orders as $order) {
                        $order->manual_payment = 1;
                        $order->manual_payment_data = json_encode($manual_payment_data);
                        $order->save();
                    }
                    flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                    return redirect()->route('order_confirmed');
                }
            }
        } else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order = Order::findOrFail($order->id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            calculateCommissionAffilationClubPoint($order);
        }
        Session::put('combined_order_id', $combined_order_id);
        return redirect()->route('order_confirmed');
    }

    public function get_shipping_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        $districts = State::where('status', 1)->orderBy('name', 'asc')->get();
        $areas = City::where('status', 1)->get();
//        if (Session::has('cart') && count(Session::get('cart')) > 0) {
        if ($carts && count($carts) > 0) {
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories', 'carts', 'districts', 'areas'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->address_id == null) {
            flash(translate("Please add shipping address"))->warning();
            return back();
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();
        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        foreach ($carts as $key => $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->save();
        }

        $carrier_list = array();
        if(get_setting('shipping_type') == 'carrier_wise_shipping'){
            $zone = \App\Models\Country::where('id',$carts[0]['address']['country_id'])->first()->zone_id;

            $carrier_query = Carrier::query();
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();
        }
        
        return view('frontend.delivery_info', compact('carts','carrier_list'));
    }

    public function store_delivery_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();

        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $tax += cart_product_tax($cartItem, $product,false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];

                if(get_setting('shipping_type') != 'carrier_wise_shipping' || $request['shipping_type_' . $product->user_id] == 'pickup_point'){
                    if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                        $cartItem['shipping_type'] = 'pickup_point';
                        $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                    } else {
                        $cartItem['shipping_type'] = 'home_delivery';
                    }
                    $cartItem['shipping_cost'] = 0;
                    if ($cartItem['shipping_type'] == 'home_delivery') {
                        $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                    }
                }
                else{
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['carrier_id'] = $request['carrier_id_' . $product->user_id];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $cartItem['carrier_id']);
                }

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;

            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));

        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        $response_message = array();

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    $carts = Cart::where('user_id', Auth::user()->id)
                                    ->where('owner_id', $coupon->user_id)
                                    ->get();

                    $coupon_discount = 0;
                    
                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($carts as $key => $cartItem) { 
                            $product = Product::find($cartItem['product_id']);
                            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                            $tax += cart_product_tax($cartItem, $product,false) * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;
                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }

                        }
                    } elseif ($coupon->type == 'product_base') {
                        foreach ($carts as $key => $cartItem) { 
                            $product = Product::find($cartItem['product_id']);
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += (cart_product_price($cartItem, $product, false, false) * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                    if($coupon_discount > 0){
                        Cart::where('user_id', Auth::user()->id)
                            ->where('owner_id', $coupon->user_id)
                            ->update(
                                [
                                    'discount' => $coupon_discount / count($carts),
                                    'coupon_code' => $request->code,
                                    'coupon_applied' => 1
                                ]
                            );
                        $response_message['response'] = 'success';
                        $response_message['message'] = translate('Coupon has been applied');
                    }
                    else{
                        $response_message['response'] = 'warning';
                        $response_message['message'] = translate('This coupon is not applicable to your cart products!');
                    }
                    
                } else {
                    $response_message['response'] = 'warning';
                    $response_message['message'] = translate('You already used this coupon!');
                }
            } else {
                $response_message['response'] = 'warning';
                $response_message['message'] = translate('Coupon expired!');
            }
        } else {
            $response_message['response'] = 'danger';
            $response_message['message'] = translate('Invalid coupon!');
        }

        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();
        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $returnHTML = view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'))->render();
        return response()->json(array('response_message' => $response_message, 'html'=>$returnHTML));
    }

    public function remove_coupon_code(Request $request)
    {
        Cart::where('user_id', Auth::user()->id)
                ->update(
                        [
                            'discount' => 0.00,
                            'coupon_code' => '',
                            'coupon_applied' => 0
                        ]
        );

        $coupon = Coupon::where('code', $request->code)->first();
        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        return view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'));
    }

    public function apply_club_point(Request $request) {
        if (addon_is_activated('club_point')){

            $point = $request->point;

            if(Auth::user()->point_balance >= $point) {
                $request->session()->put('club_point', $point);
                flash(translate('Point has been redeemed'))->success();
            }
            else {
                flash(translate('Invalid point!'))->warning();
            }
        }
        return back();
    }

    public function remove_club_point(Request $request) {
        $request->session()->forget('club_point');
        return back();
    }

    public function express_checkout(Request $request)
    {
        // Handle guest checkout
        if (!Auth::check()) {
            return $this->process_guest_checkout($request);
        } else {
            return $this->process_logged_in_checkout($request);
        }
    }

    private function process_guest_checkout(Request $request)
    {
        // Validate guest information
        $request->validate([
            'payment_option' => 'required|string',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_address' => 'required|string|max:500',
            'shipping_charge' => 'required|in:inside_dhaka,outside_dhaka',
        ]);

        // Get guest cart items
        $temp_user_id = $request->session()->get('temp_user_id');
        $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        
        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        // Create guest order data
        $guest_info = [
            'name' => $request->guest_name,
            'email' => $request->guest_email,
            'phone' => $request->guest_phone,
            'address' => $request->guest_address,
        ];

        // Calculate shipping charge
        $shipping_charge = $request->shipping_charge == 'inside_dhaka' ? 60 : 120;
        $request->session()->put('shipping_charge', $shipping_charge);
        $request->session()->put('guest_checkout', true);

        // Use temporary user creation approach
        return $this->process_payment($request, $carts, $guest_info);
    }

    private function process_logged_in_checkout(Request $request)
    {
        // Validate logged in user information
        $request->validate([
            'payment_option' => 'required|string',
            'address_id' => 'required|exists:addresses,id',
            'shipping_charge' => 'required|in:inside_dhaka,outside_dhaka',
        ]);

        // Check if cart is empty
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        // Update cart items with selected address and default shipping type
        foreach ($carts as $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->shipping_type = 'home_delivery';
            $cartItem->shipping_cost = $request->session()->get('shipping_charge', 0);
            $cartItem->save();
        }

        // Calculate shipping charge
        $shipping_charge = $request->shipping_charge == 'inside_dhaka' ? 60 : 120;
        $request->session()->put('shipping_charge', $shipping_charge);

        // Auto-set shipping method to home delivery
        $request->session()->put('shipping_method', 'home_delivery');

        // Process the checkout using existing logic
        return $this->checkout($request);
    }

    public function order_confirmed()
    {
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

        // Handle cart cleanup for both guest and logged-in users
        if ($combined_order->user_id == 0) {
            // Guest user - clear cart by temp_user_id from session
            $temp_user_id = Session::get('temp_user_id');
            if ($temp_user_id) {
                Cart::where('temp_user_id', $temp_user_id)->delete();
            }
        } else {
            // Logged-in user - clear cart by user_id
            Cart::where('user_id', $combined_order->user_id)->delete();
        }

        // Logout guests if they were auto-logged in during checkout
        if (Session::has('guest_checkout')) {
            $combined_order->user_id = 0;
            $combined_order->save();
            foreach ($combined_order->orders as $order) {
                $order->user_id = 0;
                $order->save();
            }

            Auth::logout();
            Session::forget([
                'guest_checkout',
                'temp_user_id',
                'shipping_charge',
                'shipping_method',
                'payment_type',
                'payment_data',
                'club_point',
            ]);
        }

        foreach($combined_order->orders as $order){
            NotificationUtility::sendOrderPlacedNotification($order);
        }

        if ($combined_order->user_id == 0) {
            Session::forget('combined_order_id');
        }

        return view('frontend.order_confirmed', compact('combined_order'));
    }

    private function process_payment($request, $carts, $guest_info = null)
    {
        // Set payment type
        $request->session()->put('payment_type', 'cart_payment');
        
        if ($guest_info) {
            // For guest checkout, create a temporary user and use normal order process
            try {
                // Create a temporary user for guest checkout
                $temp_user = new User;
                $temp_user->name = $guest_info['name'];
                $temp_user->phone = $guest_info['phone'];
                
                // Generate unique email with more specificity to avoid conflicts
                $timestamp = time() . '_' . rand(1000, 9999);
                $temp_user->email = 'guest_' . $timestamp . '@temp.com';
                $temp_user->user_type = 'customer';
                $temp_user->password = bcrypt('temp_password');
                
                // Add debugging
                \Log::info('Creating temporary user:', [
                    'name' => $temp_user->name,
                    'email' => $temp_user->email,
                    'phone' => $temp_user->phone,
                    'timestamp' => $timestamp,
                ]);
                
                $temp_user->save();
                
                // Verify user was created - check if save was successful
                try {
                    $saved_user = User::find($temp_user->id);
                    if (!$saved_user) {
                        throw new \Exception('Failed to create temporary user - user not found after save');
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Failed to verify temporary user creation: ' . $e->getMessage());
                }
                
                \Log::info('Temporary user created successfully:', ['user_id' => $temp_user->id]);
                
                // Log in the temporary user
                Auth::login($temp_user);
                
                // Verify user is logged in
                if (!Auth::check() || Auth::user()->id != $temp_user->id) {
                    throw new \Exception('Failed to login temporary user');
                }
                
                \Log::info('User logged in successfully:', ['user_id' => Auth::user()->id]);
                
                // Create a temporary address for the user
                $address = new Address;
                $address->user_id = $temp_user->id;
                $address->name = $guest_info['name'];
                $address->phone = $guest_info['phone'];
                $address->address = $guest_info['address'];
                
                // Get a valid state_id as it is NOT NULL in the database
                $state = State::where('status', 1)->first();
                $address->state_id = $state ? $state->id : 1;
                
                $address->city_id = null;
                $address->postal_code = null;
                $address->country_id = 18; // Default country (Bangladesh)
                $address->set_default = 1;
                $address->save();
                
                // Verify address was created
                if (!$address->id) {
                    throw new \Exception('Failed to create temporary address');
                }
                
                // Update cart items with the new user ID, address and default shipping type
                foreach ($carts as $cartItem) {
                    $cartItem->user_id = $temp_user->id;
                    $cartItem->address_id = $address->id;
                    $cartItem->shipping_type = 'home_delivery';
                    $cartItem->shipping_cost = $request->session()->get('shipping_charge', 0);
                    $cartItem->temp_user_id = null;
                    $cartItem->save();
                }
                
                // Log in the temporary user AFTER moving cart items so OrderController can use it
                Auth::login($temp_user);
                
                // Set the address ID in request for normal checkout
                $request->merge(['address_id' => $address->id]);
                
                // Use the normal checkout process
                return $this->checkout($request);
                
            } catch (\Exception $e) {
                flash(translate('Order creation failed: ') . $e->getMessage())->error();
                return back();
            }
            
        } else {
            // Use existing order creation logic for logged-in users
            (new OrderController)->store($request);
        }
        
        $combined_order_id = $request->session()->get('combined_order_id');
        $data['combined_order_id'] = $combined_order_id;
        $request->session()->put('payment_data', $data);

        if ($combined_order_id != null) {
            // Handle payment gateway redirect
            $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
            if (class_exists($decorator)) {
                return (new $decorator)->pay($request);
            } else {
                // Handle manual payment and cash on delivery
                $combined_order = CombinedOrder::findOrFail($combined_order_id);
                $manual_payment_data = array(
                    'name'   => $request->payment_option,
                    'amount' => $combined_order->grand_total,
                    'trx_id' => $request->trx_id,
                    'photo'  => $request->photo
                );
                foreach ($combined_order->orders as $order) {
                    $order->manual_payment = 1;
                    $order->manual_payment_data = json_encode($manual_payment_data);
                    $order->payment_status = 'pending';
                    $order->save();
                }
                flash(translate('Your order has been placed successfully.'))->success();
                return redirect()->route('order_confirmed');
            }
        }
        
        flash(translate('Select Payment Option.'))->warning();
        return back();
    }

    public function show_express_checkout(Request $request)
    {
        // Handle guest or logged in user cart
        if (Auth::check()) {
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }
        
        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $districts = State::where('status', 1)->orderBy('name', 'asc')->get();
        $areas = City::where('status', 1)->get();
        
        // Get carrier list if carrier shipping is enabled
        $carrier_list = array();
        if(get_setting('shipping_type') == 'carrier_wise_shipping' && Auth::check()){
            if(!empty($carts[0]['address_id'])) {
                $address = Address::find($carts[0]['address_id']);
                if($address && $address->country_id) {
                    $zone = Country::where('id',$address->country_id)->first()->zone_id;
                    $carrier_query = Carrier::query();
                    $carrier_query->whereIn('id',function ($query) use ($zone) {
                        $query->select('carrier_id')->from('carrier_range_prices')
                        ->where('zone_id', $zone);
                    })->orWhere('free_shipping', 1);
                    $carrier_list = $carrier_query->get();
                }
            }
        }

        $categories = Category::all();
        return view('frontend.express_checkout', compact('categories', 'carts', 'districts', 'areas', 'carrier_list'));
    }
}
