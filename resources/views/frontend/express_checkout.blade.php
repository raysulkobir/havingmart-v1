@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('Checkout') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 gry-bg">
    <div class="container">
        <div class="row cols-xs-space cols-sm-space cols-md-space">
            <div class="col-xxl-8 col-xl-10 mx-auto">
                <form class="form-default" data-toggle="validator" action="{{ route('checkout.express') }}" role="form" method="POST" id="express-checkout-form">
                    @csrf
                    
                    <!-- Cart Summary -->
                    <div class="shadow-sm bg-white p-4 rounded mb-4">
                        <h4 class="mb-3">{{ translate('Order Summary') }}</h4>
                        <div class="table-responsive">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ translate('Product') }}</th>
                                        <th>{{ translate('Price') }}</th>
                                        <th>{{ translate('Quantity') }}</th>
                                        <th>{{ translate('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}" class="img-fit size-50px rounded mr-2" alt="{{ $product->getTranslation('name') }}">
                                                        <div>
                                                            <div class="fw-600">{{ $product->getTranslation('name') }}</div>
                                                            @if($cartItem['variation'] != null)
                                                                <small class="text-muted">{{ $cartItem['variation'] }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ format_price($cart_product_price) }}</td>
                                                <td>{{ $cartItem['quantity'] }}</td>
                                                <td>{{ format_price($cart_product_price * $cartItem['quantity']) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="fw-600">{{ translate('Subtotal') }}</td>
                                        <td class="fw-600">{{ format_price($subtotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-600">{{ translate('Tax') }}</td>
                                        <td class="fw-600">{{ format_price(get_setting('tax')) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-600">{{ translate('Shipping Cost') }}</td>
                                        <td class="fw-600" id="shipping-cost">{{ format_price(0) }}</td>
                                    </tr>
                                    <tr class="fs-16 fw-600">
                                        <td colspan="3">{{ translate('Grand Total') }}</td>
                                        <td id="grand-total">{{ format_price($subtotal + get_setting('tax')) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="shadow-sm bg-white p-4 rounded mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">{{ translate('Shipping Information') }}</h4>
                            @if(!Auth::check())
                                <div class="text-right">
                                    <span class="opacity-60">{{ translate('Returning customer?') }}</span>
                                    <a href="{{ route('user.login') }}" class="ml-2 fw-700 text-primary">{{ translate('Login here') }}</a>
                                </div>
                            @endif
                        </div>
                        @if(Auth::check())
                            <div class="row gutters-5">
                                @foreach (Auth::user()->addresses as $key => $address)
                                    <div class="col-md-6 mb-3">
                                        <label class="aiz-megabox d-block bg-white mb-0">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default) checked @endif required>
                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-3 text-left">
                                                    <div>
                                                        <span class="opacity-60">{{ translate('Name') }}:</span>
                                                        <span class="fw-600 ml-2">{{ $address->name }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="opacity-60">{{ translate('Address') }}:</span>
                                                        <span class="fw-600 ml-2">{{ $address->address }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="opacity-60">{{ translate('Phone') }}:</span>
                                                        <span class="fw-600 ml-2">{{ $address->phone }}</span>
                                                    </div>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="col-md-6 mx-auto mb-3">
                                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center" onclick="add_new_address()">
                                        <i class="las la-plus la-2x mb-3"></i>
                                        <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ translate('Name') }} *</label>
                                        <input type="text" name="guest_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ translate('Email') }} *</label>
                                        <input type="email" name="guest_email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ translate('Phone') }} *</label>
                                        <input type="text" name="guest_phone" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ translate('Address') }} *</label>
                                        <textarea name="guest_address" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Hidden Shipping Method - Auto Home Delivery -->
                    <input type="hidden" name="shipping_method" value="home_delivery">

                    <!-- Payment Information -->
                    <div class="shadow-sm bg-white p-4 rounded mb-4">
                        <h4 class="mb-3">{{ translate('Payment Method') }}</h4>
                        <div class="row">
                            <div class="col-xxl-8 col-xl-10 mx-auto">
                                <div class="row gutters-10">
                                    <!-- Always show Cash on Delivery as fallback -->
                                    <div class="col-6 col-md-4">
                                        <label class="aiz-megabox d-block mb-3">
                                            <input value="cash_on_delivery" type="radio" name="payment_option" @if(get_setting('cash_on_delivery') != 1) checked @endif>
                                            <span class="d-block aiz-megabox-elem p-3">
                                                <img src="{{ static_asset('assets/img/cards/cod.png') }}" class="img-fluid mb-2">
                                                <span class="d-block text-center">
                                                    <span class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                    
                                    @if (get_setting('paypal_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paypal" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/paypal.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Paypal') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('stripe_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="stripe" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/stripe.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Stripe') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('mercadopago_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="mercadopago" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/mercadopago.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Mercadopago') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('sslcommerz_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="sslcommerz" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('SSLCommerz') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('instamojo_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="instamojo" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/instamojo.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Instamojo') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('razorpay') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="razorpay" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/rozarpay.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Razorpay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('paystack') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paystack" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/paystack.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Paystack') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('voguepay') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="voguepay" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/vogue.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('VoguePay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    
                                    @if (get_setting('wallet_payment_system') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="wallet" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/wallet.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Wallet') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="shadow-sm bg-white p-4 rounded mb-4">
                        <h4 class="mb-3">{{ translate('Additional Information (Optional)') }}</h4>
                        <div class="form-group">
                            <textarea name="additional_info" rows="3" class="form-control" placeholder="{{ translate('Type any additional notes for your order') }}"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                            <a href="{{ route('cart') }}" class="btn btn-link">
                                <i class="las la-arrow-left"></i>
                                {{ translate('Return to Cart')}}
                            </a>
                        </div>
                        <div class="col-md-6 text-center text-md-right">
                            <button type="submit" class="btn btn-primary fw-600 btn-lg">
                                <i class="las la-lock mr-2"></i>
                                {{ translate('Place Order') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function add_new_address() {
    window.location.href = '{{ route("addresses.index") }}';
}

// Form validation before submission
document.getElementById('express-checkout-form').addEventListener('submit', function(e) {
    var paymentOption = document.querySelector('input[name="payment_option"]:checked');
    
    if (!paymentOption) {
        e.preventDefault();
        alert('Please select a payment method');
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
    var guestEmail = document.querySelector('input[name="guest_email"]');
    var guestPhone = document.querySelector('input[name="guest_phone"]');
    var guestAddress = document.querySelector('textarea[name="guest_address"]');
    
    if (!guestName.value.trim()) {
        e.preventDefault();
        guestName.focus();
        alert('Please enter your name');
        return false;
    }

    if (!guestEmail.value.trim() || !guestEmail.value.includes('@')) {
        e.preventDefault();
        guestEmail.focus();
        alert('Please enter a valid email address');
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
    submitBtn.innerHTML = '<i class="la la-spinner la-spin mr-2"></i>Processing...';
});
</script>
@endsection

@section('modal')
    @include('frontend.partials.address_modal')
@endsection
