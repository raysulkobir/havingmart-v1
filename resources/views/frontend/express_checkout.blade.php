@extends('frontend.layouts.app')

@section('content')
<style>
    .minimal-checkout-container {
        max-width: 900px;
        width: 100%;
        margin: 10px auto 30px auto;
        background: #ffffff;
        border: 2px solid var(--primary);
        border-radius: 24px;
        padding: 32px 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        position: relative;
    }
    .minimal-input-group {
        display: flex;
        align-items: stretch;
        border: 1.5px solid var(--soft-primary);
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 16px;
        transition: all 0.2s;
    }
    .minimal-input-group:focus-within {
        box-shadow: 0 0 0 3px var(--soft-primary);
        border-color: var(--primary);
    }
    .minimal-input-icon {
        background-color: var(--soft-primary);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        border-right: 1.5px solid var(--soft-primary);
        flex-shrink: 0;
    }
    .minimal-input-icon i {
        font-size: 22px;
    }
    .minimal-input-field {
        border: none !important;
        outline: none !important;
        flex-grow: 1;
        padding: 14px 16px;
        font-size: 15px;
        color: #333;
        background: transparent;
    }
    .minimal-input-field::placeholder {
        color: #9ca3af;
    }
    /* Product row style */
    .minimal-product-item {
        display: flex;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1.5px solid var(--soft-primary);
        width: 100%;
        overflow: hidden;
    }
    .minimal-product-item:last-child {
        border-bottom: none;
    }
    .minimal-product-img-wrapper {
        position: relative;
        flex-shrink: 0;
        margin-right: 16px;
    }
    .minimal-product-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background-color: #6b7280;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .minimal-product-title {
        color: var(--primary);
        font-weight: 600;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 0;
        white-space: normal;
        word-break: break-word;
    }
    .minimal-product-price-wrapper {
        display: flex;
        align-items: center;
        margin-left: auto;
        padding-left: 12px;
        flex-shrink: 0;
    }
    .minimal-product-price {
        font-weight: 700;
        color: #1f2937;
        font-size: 15px;
        margin-right: 12px;
    }
    .minimal-product-remove {
        color: #9ca3af;
        cursor: pointer;
        transition: color 0.2s;
        background: none;
        border: none;
        padding: 4px;
        font-size: 16px;
    }
    .minimal-product-remove:hover {
        color: #ef4444;
    }
    /* Delivery selection style */
    .minimal-section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--primary);
        margin: 24px 0 12px 0;
    }
    .minimal-delivery-box {
        border: 1.5px solid var(--soft-primary);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .minimal-delivery-option {
        display: flex;
        align-items: center;
        padding: 16px;
        cursor: pointer;
        background: #fff;
        transition: background-color 0.2s;
    }
    .minimal-delivery-option:not(:last-child) {
        border-bottom: 1.5px solid var(--soft-primary);
    }
    .minimal-delivery-option:hover {
        background-color: var(--soft-primary);
    }
    .minimal-delivery-option input[type="radio"] {
        display: none;
    }
    .minimal-delivery-radio-custom {
        width: 22px;
        height: 22px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        margin-right: 12px;
        position: relative;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .minimal-delivery-option input[type="radio"]:checked + .minimal-delivery-radio-custom {
        border-color: var(--primary);
    }
    .minimal-delivery-option input[type="radio"]:checked + .minimal-delivery-radio-custom::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        background-color: var(--primary);
        border-radius: 50%;
        top: 3px;
        left: 3px;
    }
    .minimal-delivery-text {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }
    .minimal-delivery-price {
        margin-left: auto;
        font-weight: 700;
        color: #111827;
        font-size: 14px;
    }
    /* Summary box style */
    .minimal-summary-box {
        background-color: var(--soft-primary);
        border: 1.5px solid var(--soft-primary);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .minimal-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 15px;
        color: #4b5563;
        margin-bottom: 10px;
    }
    .minimal-summary-row.total-row {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1.5px solid var(--primary);
    }
    /* Timer box style */
    .minimal-timer-box {
        border: 1.5px solid #fee2e2;
        background-color: #fef2f2;
        border-radius: 20px;
        padding: 16px;
        text-align: center;
        margin-bottom: 20px;
    }
    .minimal-timer-text {
        color: #dc2626;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .minimal-timer-clock {
        color: #991b1b;
        font-size: 28px;
        font-weight: 800;
    }
    .btn-checkout-submit {
        background: var(--primary);
        color: #fff !important;
        border: none;
        transition: all 0.3s;
    }
    .btn-checkout-submit:hover {
        background: var(--hov-primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    @media (max-width: 768px) {
        .minimal-checkout-container {
            padding: 20px 16px;
            border-radius: 16px;
            margin: 10px auto 20px auto;
        }
        .minimal-input-field {
            padding: 12px 10px;
            font-size: 14px;
        }
        .minimal-input-icon {
            width: 44px;
        }
        .minimal-input-icon i {
            font-size: 18px;
        }
        .minimal-product-item {
            padding: 12px 0;
        }
        .minimal-product-title {
            font-size: 13px;
        }
        .minimal-product-price {
            font-size: 14px;
            margin-right: 8px;
        }
    }
</style>

<section class="py-4 gry-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9 col-sm-12">
                <div class="minimal-checkout-container">


                    <form class="form-default" data-toggle="validator" action="{{ route('checkout.express') }}" role="form" method="POST" id="express-checkout-form">
                        @csrf
                        @foreach ($carts as $cartItem)
                            <input type="hidden" name="cart_ids[]" value="{{ $cartItem['id'] }}">
                        @endforeach

                        <!-- Address/Shipping Info -->
                        @if(Auth::check() && count(Auth::user()->addresses) > 0)
                            <div class="minimal-section-title">ডেলিভারি ঠিকানা নির্বাচন করুন</div>
                            <div class="minimal-delivery-box">
                                @foreach (Auth::user()->addresses as $key => $address)
                                    <label class="minimal-delivery-option mb-0">
                                        <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default) checked @endif required>
                                        <span class="minimal-delivery-radio-custom"></span>
                                        <span class="minimal-delivery-text">
                                            <strong>{{ $address->name }}</strong> - {{ $address->address }}, {{ $address->phone }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mb-3 text-right">
                                <a href="javascript:void(0)" onclick="add_new_address()" class="text-primary small fw-700">
                                    <i class="las la-plus mr-1"></i>নতুন ঠিকানা যোগ করুন
                                </a>
                            </div>
                        @else
                            <div class="minimal-input-group">
                                <div class="minimal-input-icon">
                                    <i class="las la-user"></i>
                                </div>
                                <input type="text" name="guest_name" class="minimal-input-field" placeholder="আপনার নাম" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                            </div>

                            <div class="minimal-input-group">
                                <div class="minimal-input-icon">
                                    <i class="las la-phone"></i>
                                </div>
                                <input type="text" name="guest_phone" class="minimal-input-field" placeholder="ফোন নাম্বার" value="{{ Auth::check() ? Auth::user()->phone : '' }}" required>
                            </div>

                            <div class="minimal-input-group">
                                <div class="minimal-input-icon">
                                    <i class="las la-map-marker-alt"></i>
                                </div>
                                <textarea name="guest_address" class="minimal-input-field" rows="2" placeholder="বাড়ি/ফ্ল্যাট নম্বর, রোড, এলাকা, উপজেলা, জেলা" required></textarea>
                            </div>
                        @endif

                        <hr style="border-top: 1.5px solid var(--soft-primary); margin: 20px 0;">

                        <!-- Product List -->
                        <div class="mb-3">
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
                                    <div class="minimal-product-item">
                                        <div class="minimal-product-img-wrapper">
                                            <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="size-50px rounded border img-fit" alt="{{ $product->getTranslation('name') }}">
                                            <span class="minimal-product-badge">{{ $cartItem['quantity'] }}</span>
                                        </div>
                                        <div class="flex-grow-1 pr-2" style="min-width: 0; overflow: hidden;">
                                            <div class="minimal-product-title">{{ $product->getTranslation('name') }}</div>
                                            @if($cartItem['variation'] != null)
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ $cartItem['variation'] }}</small>
                                            @endif
                                        </div>
                                        <div class="minimal-product-price-wrapper">
                                            <span class="minimal-product-price">{{ format_price($cart_product_price * $cartItem['quantity']) }}</span>
                                            <button type="button" class="minimal-product-remove" onclick="removeExpressItem({{ $cartItem['id'] }})" title="বাদ দিন">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Delivery area selection -->
                        <div class="minimal-section-title">ডেলিভারি চার্জ সিলেক্ট করুন..</div>
                        <div class="minimal-delivery-box">
                            <label class="minimal-delivery-option mb-0">
                                <input type="radio" name="shipping_charge" value="inside_dhaka" checked required>
                                <span class="minimal-delivery-radio-custom"></span>
                                <span class="minimal-delivery-text">ঢাকা সিটির মধ্যে</span>
                                <span class="minimal-delivery-price">৳৮০</span>
                            </label>
                            <label class="minimal-delivery-option mb-0">
                                <input type="radio" name="shipping_charge" value="outside_dhaka" required>
                                <span class="minimal-delivery-radio-custom"></span>
                                <span class="minimal-delivery-text">ঢাকা সিটির বাহিরে</span>
                                <span class="minimal-delivery-price">৳১২০</span>
                            </label>
                        </div>

                        <!-- Pricing Details Summary Box -->
                        <div class="minimal-summary-box">
                            <div class="minimal-summary-row">
                                <span>সাব টোটাল</span>
                                <span id="summary-subtotal">{{ format_price($subtotal) }}</span>
                            </div>
                            <div class="minimal-summary-row">
                                <span>ডেলিভারি চার্জ</span>
                                <span id="shipping-cost">{{ format_price(80) }}</span>
                            </div>
                            <span class="d-none" id="summary-tax">{{ format_price(get_setting('tax', 0)) }}</span>
                            <div class="minimal-summary-row total-row">
                                <span>সর্বমোট</span>
                                <span id="grand-total">{{ format_price($subtotal + 80 + get_setting('tax', 0)) }}</span>
                            </div>
                        </div>

                        <!-- Hidden Shipping Method -->
                        <input type="hidden" name="shipping_method" value="home_delivery">

                        <!-- Payment Information -->
                        <div class="minimal-section-title">অর্থপ্রদানের পদ্ধতি</div>
                        <div class="minimal-payment-box d-flex flex-wrap rounded-lg p-2 bg-white mb-4" style="border: 1.5px solid var(--soft-primary); gap: 8px;">
                            <!-- Cash on Delivery -->
                            <label class="minimal-payment-option-label mb-0 flex-grow-1 cursor-pointer">
                                <input type="radio" name="payment_option" value="cash_on_delivery" checked required style="display:none;">
                                <div class="payment-inner p-3 border rounded text-center d-flex align-items-center justify-content-center" style="border-color: var(--primary); background: var(--soft-primary); transition: all 0.2s;">
                                    <img src="{{ static_asset('assets/img/cards/cod.png') }}" class="mr-2" style="max-height: 20px;">
                                    <span class="fw-700 text-dark" style="font-size: 13px;">ক্যাশ অন ডেলিভারি</span>
                                </div>
                            </label>
                            
                            @if (get_setting('sslcommerz_payment') == 1)
                                <label class="minimal-payment-option-label mb-0 flex-grow-1 cursor-pointer">
                                    <input type="radio" name="payment_option" value="sslcommerz" style="display:none;">
                                    <div class="payment-inner p-3 border rounded text-center d-flex align-items-center justify-content-center" style="border-color: #e2e8f0; background: #fff; transition: all 0.2s;">
                                        <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}" class="mr-2" style="max-height: 20px;">
                                        <span class="fw-700 text-dark" style="font-size: 13px;">SSLCommerz</span>
                                    </div>
                                </label>
                            @endif
                            
                            @if (get_setting('bkash') == 1)
                                <label class="minimal-payment-option-label mb-0 flex-grow-1 cursor-pointer">
                                    <input type="radio" name="payment_option" value="bkash" style="display:none;">
                                    <div class="payment-inner p-3 border rounded text-center d-flex align-items-center justify-content-center" style="border-color: #e2e8f0; background: #fff; transition: all 0.2s;">
                                        <img src="{{ static_asset('assets/img/cards/bkash.png') }}" class="mr-2" style="max-height: 20px;">
                                        <span class="fw-700 text-dark" style="font-size: 13px;">bKash</span>
                                    </div>
                                </label>
                            @endif
                        </div>

                        <!-- Comments Section -->
                        <div class="minimal-input-group">
                            <div class="minimal-input-icon">
                                <i class="las la-edit"></i>
                            </div>
                            <input type="text" name="additional_info" class="minimal-input-field" placeholder="কোনো মন্তব্য থাকলে লিখুন...">
                        </div>

                        <!-- Timer Countdown -->
                        <div class="minimal-timer-box">
                            <div class="minimal-timer-text">⌛ আর অল্প সময়! অফার শেষ হবে কিছুক্ষণের মধ্যেই</div>
                            <div class="minimal-timer-clock" id="countdown-timer">15:00</div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-checkout-submit btn-block btn-lg fw-700 py-3 rounded-lg">
                            <i class="las la-lock mr-2"></i>অর্ডার করুন (<span id="btn-grand-total">{{ format_price($subtotal + 80 + get_setting('tax', 0)) }}</span>)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function add_new_address() {
    window.location.href = '{{ route("addresses.index") }}';
}

function removeExpressItem(id) {
    if(confirm("আপনি কি এই পণ্যটি বাদ দিতে চান?")) {
        $.post('{{ route('cart.removeFromCart') }}', {
            _token  : AIZ.data.csrf,
            id      : id
        }, function(data){
            AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
            location.reload();
        });
    }
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

// Payment option selection styling
document.querySelectorAll('input[name="payment_option"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.minimal-payment-option-label .payment-inner').forEach(function(el) {
            el.style.borderColor = '#e2e8f0';
            el.style.background = '#fff';
        });
        if(this.checked) {
            var inner = this.closest('.minimal-payment-option-label').querySelector('.payment-inner');
            inner.style.borderColor = 'var(--primary)';
            inner.style.background = 'var(--soft-primary)';
        }
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

// Countdown Timer
var duration = 15 * 60; // 15 minutes
var timerDisplay = document.getElementById('countdown-timer');
if (timerDisplay) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        timerDisplay.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
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
    
    @if(Auth::check() && count(Auth::user()->addresses) > 0)
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
