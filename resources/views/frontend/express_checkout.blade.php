@extends('frontend.layouts.app')

@section('content')
<style>
    .checkout-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        background: #fff;
    }
    .checkout-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 16px 24px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .checkout-card-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #1a202c;
    }
    .checkout-card-body {
        padding: 24px;
    }
    .express-product-item {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .express-product-item:last-child {
        border-bottom: none;
    }
    .sticky-sidebar {
        position: sticky;
        top: 120px;
        z-index: 10;
    }
    .aiz-megabox-elem {
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out;
        border: 1px solid #e2e8f0;
    }
    .aiz-megabox input:checked ~ .aiz-megabox-elem {
        border-color: var(--primary) !important;
        background: rgba(226, 33, 50, 0.02);
        box-shadow: 0 0 0 1px var(--primary);
    }
    .btn-checkout-submit {
        background: linear-gradient(135deg, #E22132, #260A54);
        color: #fff !important;
        border: none;
        transition: all 0.3s;
    }
    .btn-checkout-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(226, 33, 50, 0.4);
    }
</style>

<section class="py-4 gry-bg">
    <div class="container-fluid px-lg-5">
        <!-- Compact Delivery Alert Banner -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info py-2 px-3 border-0 rounded-lg d-flex flex-wrap align-items-center justify-content-between mb-4 shadow-sm">
                    <div class="small mb-0 text-dark">
                        <i class="las la-truck mr-1 fs-18 text-info"></i>
                        <strong>ঢাকার ভিতরে:</strong> ৳৮০ (১-২ দিন) | <strong>ঢাকার বাইরে:</strong> ৳১২০ (২-৪ দিন) | <strong>৳৩০০০+ অর্ডারে ফ্রি ডেলিভারি</strong>
                    </div>
                </div>
            </div>
        </div>

        <form class="form-default" data-toggle="validator" action="{{ route('checkout.express') }}" role="form" method="POST" id="express-checkout-form">
            @csrf
            @foreach ($carts as $cartItem)
                <input type="hidden" name="cart_ids[]" value="{{ $cartItem['id'] }}">
            @endforeach

            <div class="row">
                <!-- Left Side: Shipping & Payment Info -->
                <div class="col-lg-7 col-xl-8">
                    <!-- Shipping Information -->
                    <div class="checkout-card">
                        <div class="checkout-card-header d-flex justify-content-between align-items-center">
                            <h4><i class="las la-map-marker-alt mr-2 text-primary"></i>{{ translate('Shipping Information') }}</h4>
                            @if(!Auth::check())
                                <div>
                                    <span class="opacity-60 small">{{ translate('Returning customer?') }}</span>
                                    <a href="{{ route('user.login') }}" class="ml-2 fw-700 text-primary small">{{ translate('Login here') }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="checkout-card-body">
                            @if(Auth::check())
                                <div class="row gutters-5">
                                    @foreach (Auth::user()->addresses as $key => $address)
                                        <div class="col-md-6 mb-3">
                                            <label class="aiz-megabox d-block mb-0 cursor-pointer">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default) checked @endif required>
                                                <span class="d-flex p-3 aiz-megabox-elem rounded border">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-2 text-left">
                                                        <div class="fw-700 text-dark mb-1">{{ $address->name }}</div>
                                                        <div class="small text-secondary mb-1"><i class="las la-map-marker-alt"></i> {{ $address->address }}</div>
                                                        <div class="small text-secondary"><i class="las la-phone"></i> {{ $address->phone }}</div>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                    <div class="col-md-6 mb-3">
                                        <div class="border p-3 rounded mb-0 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center" onclick="add_new_address()" style="border-style: dashed !important; min-height: 110px;">
                                            <i class="las la-plus la-2x mb-2 text-muted"></i>
                                            <div class="alpha-7 small fw-600 text-secondary">{{ translate('Add New Address') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-secondary fw-600 small">আপনার নাম <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="las la-user"></i></span>
                                            </div>
                                            <input type="text" name="guest_name" class="form-control border-left-0 pl-1" placeholder="e.g. John Doe" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-secondary fw-600 small">ফোন নম্বর <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i class="las la-phone"></i></span>
                                            </div>
                                            <input type="text" name="guest_phone" class="form-control border-left-0 pl-1" placeholder="e.g. 017XXXXXXXX" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-secondary fw-600 small">সম্পূর্ণ ঠিকানা <span class="text-danger">*</span></label>
                                        <textarea name="guest_address" class="form-control" rows="2" placeholder="e.g. House #12, Road #4, Dhanmondi, Dhaka" required></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Charge Selection -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h4><i class="las la-truck mr-2 text-primary"></i>ডেলিভারি এলাকা নির্বাচন করুন</h4>
                        </div>
                        <div class="checkout-card-body">
                            <div class="row">
                                <div class="col-6">
                                    <label class="aiz-megabox d-block mb-0 cursor-pointer">
                                        <input type="radio" name="shipping_charge" value="inside_dhaka" required>
                                        <span class="d-block p-3 aiz-megabox-elem rounded border text-center">
                                            <div class="fw-700 fs-14 mb-1 text-dark">ঢাকার ভিতরে</div>
                                            <div class="text-primary font-weight-bold fs-18 mb-1">৳৮০</div>
                                            <div class="small text-muted">{{ translate('1-2 Days Delivery') }}</div>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="aiz-megabox d-block mb-0 cursor-pointer">
                                        <input type="radio" name="shipping_charge" value="outside_dhaka" required>
                                        <span class="d-block p-3 aiz-megabox-elem rounded border text-center">
                                            <div class="fw-700 fs-14 mb-1 text-dark">ঢাকার বাইরে</div>
                                            <div class="text-primary font-weight-bold fs-18 mb-1">৳১২০</div>
                                            <div class="small text-muted">{{ translate('2-4 Days Delivery') }}</div>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Shipping Method - Auto Home Delivery -->
                    <input type="hidden" name="shipping_method" value="home_delivery">

                    <!-- Payment Information -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h4><i class="las la-credit-card mr-2 text-primary"></i>অর্থপ্রদানের পদ্ধতি</h4>
                        </div>
                        <div class="checkout-card-body">
                            <div class="row gutters-10">
                                <!-- Cash on Delivery -->
                                <div class="col-6 col-md-4 mb-3">
                                    <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                        <input value="cash_on_delivery" type="radio" name="payment_option" @if(get_setting('cash_on_delivery') != 1) checked @endif required>
                                        <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                            <img src="{{ static_asset('assets/img/cards/cod.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                            <span class="d-block fw-700 fs-13 text-dark">{{ translate('Cash on Delivery') }}</span>
                                        </span>
                                    </label>
                                </div>
                                
                                @if (get_setting('paypal_payment') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="paypal" class="online_payment" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/paypal.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('Paypal') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                
                                @if (get_setting('stripe_payment') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="stripe" class="online_payment" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/stripe.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('Stripe') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                
                                @if (get_setting('sslcommerz_payment') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="sslcommerz" class="online_payment" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('SSLCommerz') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                @if (get_setting('razorpay') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="razorpay" class="online_payment" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/rozarpay.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('Razorpay') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                @if (get_setting('paystack') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="paystack" class="online_payment" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/paystack.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('Paystack') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                                
                                @if (get_setting('wallet_payment_system') == 1)
                                    <div class="col-6 col-md-4 mb-3">
                                        <label class="aiz-megabox d-block mb-0 cursor-pointer h-100">
                                            <input value="wallet" type="radio" name="payment_option" required>
                                            <span class="d-flex flex-column align-items-center justify-content-center p-3 aiz-megabox-elem rounded border text-center h-100">
                                                <img src="{{ static_asset('assets/img/cards/wallet.png') }}" class="img-fluid mb-2" style="max-height: 28px; object-fit: contain;">
                                                <span class="d-block fw-700 fs-13 text-dark">{{ translate('Wallet') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <h4><i class="las la-edit mr-2 text-primary"></i>{{ translate('Additional Information (Optional)') }}</h4>
                        </div>
                        <div class="checkout-card-body">
                            <div class="form-group mb-0">
                                <textarea name="additional_info" rows="2" class="form-control" placeholder="{{ translate('Type any additional notes for your order') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Order Summary & Checkout Action -->
                <div class="col-lg-5 col-xl-4">
                    <div class="checkout-card sticky-sidebar">
                        <div class="checkout-card-header">
                            <h4><i class="las la-shopping-basket mr-2 text-primary"></i>{{ translate('Order Summary') }}</h4>
                        </div>
                        <div class="checkout-card-body p-0">
                            <!-- Product List -->
                            <div class="px-4 py-2" style="max-height: 220px; overflow-y: auto;">
                                @php
                                    $subtotal = 0;
                                @endphp
                                @foreach ($carts as $key => $cartItem)
                                    @php
                                        $product = \App\Models\Product::find($cartItem['product_id']);
                                        if($product) {
                                            $cart_product_price = cart_product_price($cartItem, $product, false, false);
                                            $subtotal += $cart_product_price * $cartItem['quantity'];
                                        }
                                    @endphp
                                    @if($product)
                                        <div class="d-flex align-items-center express-product-item">
                                            <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="img-fit size-50px rounded mr-3 border" alt="{{ $product->getTranslation('name') }}">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-700 text-dark text-truncate small">{{ $product->getTranslation('name') }}</div>
                                                <div class="small text-muted">
                                                    Qty: {{ $cartItem['quantity'] }} × {{ format_price($cart_product_price) }}
                                                </div>
                                                @if($cartItem['variation'] != null)
                                                    <small class="text-muted d-block" style="font-size: 10px;">{{ $cartItem['variation'] }}</small>
                                                @endif
                                            </div>
                                            <div class="text-right pl-2 fw-700 text-dark small">
                                                {{ format_price($cart_product_price * $cartItem['quantity']) }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Pricing Details -->
                            <div class="bg-light p-4 rounded-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">{{ translate('Subtotal') }}</span>
                                    <span class="fw-700 text-dark" id="summary-subtotal">{{ format_price($subtotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">{{ translate('Tax') }}</span>
                                    <span class="fw-700 text-dark" id="summary-tax">{{ format_price(get_setting('tax', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span class="text-secondary">{{ translate('Shipping Cost') }}</span>
                                    <span class="fw-700 text-dark" id="shipping-cost">{{ format_price(0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="h6 mb-0 text-dark fw-700">{{ translate('Grand Total') }}</span>
                                    <span class="h5 mb-0 text-primary fw-800" id="grand-total">{{ format_price($subtotal + get_setting('tax', 0)) }}</span>
                                </div>

                                <!-- Action Buttons -->
                                <button type="submit" class="btn btn-checkout-submit btn-block btn-lg fw-700 mb-3 py-3">
                                    <i class="las la-lock mr-2"></i>
                                    অর্ডার করুন (<span id="btn-grand-total">{{ format_price($subtotal + get_setting('tax', 0)) }}</span>)
                                </button>

                                <div class="text-center text-muted small">
                                    <i class="las la-shield-alt text-success mr-1 fs-14"></i>
                                    {{ translate('Secure Checkout — 100% Encrypted Connection') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function add_new_address() {
    window.location.href = '{{ route("addresses.index") }}';
}

// Shipping charge calculation on radio change
document.querySelectorAll('input[name="shipping_charge"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var shippingCharge = 0;
        var selectedOption = this.value;
        
        if (selectedOption === 'inside_dhaka') {
            shippingCharge = 80;
        } else if (selectedOption === 'outside_dhaka') {
            shippingCharge = 120;
        }
        
        // Update shipping cost display
        document.getElementById('shipping-cost').textContent = formatPrice(shippingCharge);
        
        // Calculate and update grand total
        updateGrandTotal();
    });
});

// Format price function
function formatPrice(amount) {
    return '৳' + amount.toFixed(2);
}

// Update grand total
function updateGrandTotal() {
    var subtotalElement = document.getElementById('summary-subtotal');
    var taxElement = document.getElementById('summary-tax');
    var shippingCostElement = document.getElementById('shipping-cost');
    var grandTotalElement = document.getElementById('grand-total');
    var buttonTotalElement = document.getElementById('btn-grand-total');
    
    if (subtotalElement && taxElement && shippingCostElement && grandTotalElement) {
        var subtotal = parseFloat(subtotalElement.textContent.replace(/[৳,]/g, ''));
        var tax = parseFloat(taxElement.textContent.replace(/[৳,]/g, ''));
        var shippingCost = parseFloat(shippingCostElement.textContent.replace(/[৳,]/g, ''));
        
        var grandTotal = subtotal + tax + shippingCost;
        var formatted = formatPrice(grandTotal);
        
        grandTotalElement.textContent = formatted;
        if (buttonTotalElement) {
            buttonTotalElement.textContent = formatted;
        }
    }
}

// Form validation before submission
document.getElementById('express-checkout-form').addEventListener('submit', function(e) {
    var paymentOption = document.querySelector('input[name="payment_option"]:checked');
    var shippingCharge = document.querySelector('input[name="shipping_charge"]:checked');
    
    if (!paymentOption) {
        e.preventDefault();
        alert('Please select a payment method');
        return false;
    }
    
    if (!shippingCharge) {
        e.preventDefault();
        alert('Please select a delivery area');
        return false;
    }
    
    @if(Auth::check())
    var addressId = document.querySelector('input[name="address_id"]:checked');
    if (!addressId) {
        e.preventDefault();
        alert('Please select a shipping address');
        return false;
    }
    @else
    var guestName = document.querySelector('input[name="guest_name"]');
    var guestPhone = document.querySelector('input[name="guest_phone"]');
    var guestAddress = document.querySelector('textarea[name="guest_address"]');
    
    if (!guestName.value.trim()) {
        e.preventDefault();
        guestName.focus();
        alert('Please enter your name');
        return false;
    }

    if (!guestPhone.value.trim()) {
        e.preventDefault();
        guestPhone.focus();
        alert('Please enter your phone number');
        return false;
    }
    
    if (!guestAddress.value.trim()) {
        e.preventDefault();
        guestAddress.focus();
        alert('Please enter your address');
        return false;
    }
    @endif
    
    // Show loading state
    var submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="las la-spinner la-spin mr-2"></i>Processing...';
});

@php
    $ga_currency = Session::has('currency_code') ? Session::get('currency_code') : get_system_default_currency()->code;
    $ga_affiliation = get_setting('website_name') ?: get_setting('meta_title');
    $ga_checkout_value = 0;
    $ga_checkout_items = [];

    foreach ($carts as $cartItem) {
        $product = \App\Models\Product::with('category')->find($cartItem['product_id']);
        if (!$product) {
            continue;
        }

        $unit_price = round(convert_price(cart_product_price($cartItem, $product, false, true)), 2);
        $qty = (int) $cartItem['quantity'];
        $ga_checkout_value += $unit_price * $qty;

        $item_name = $product->getTranslation('name');
        if (!empty($cartItem['variation'])) {
            $item_name .= ' - ' . $cartItem['variation'];
        }

        $ga_checkout_items[] = [
            'item_id' => (string) $product->id,
            'item_name' => $item_name,
            'item_category' => optional($product->category)->getTranslation('name') ?? '',
            'affiliation' => $ga_affiliation,
            'price' => $unit_price,
            'quantity' => $qty,
            'variant' => $cartItem['variation'] ?? '',
            'short_description' => \Illuminate\Support\Str::limit(strip_tags($product->meta_description ?? ''), 150),
        ];
    }
    $ga_checkout_value = round($ga_checkout_value, 2);
@endphp

window.dataLayer = window.dataLayer || [];
dataLayer.push({
    event: 'begin_checkout',
    ecommerce: {
        currency: @json($ga_currency),
        value: {{ $ga_checkout_value }},
        items: @json($ga_checkout_items)
    }
});
</script>
@endsection

@section('modal')
    @include('frontend.partials.address_modal')
@endsection
