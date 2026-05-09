
@php
if (auth()->user() != null) {
    $user_id = Auth::user()->id;
    $cart = \App\Models\Cart::where('user_id', $user_id)->get();
} else {
    $temp_user_id = Session()->get('temp_user_id');
    if ($temp_user_id) {
        $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
    }
}
@endphp

<!-- Cart Drawer Trigger Button -->
<div class="cart-drawer-trigger" onclick="openCartDrawer()">
    <div class="cart-icon-wrapper">
       
        <span class="cart-badge cart-count" id="drawer-cart-count">
            @if (isset($cart) && count($cart) > 0)
                {{ count($cart) }}
            @else
                0
            @endif
        </span>
        <div class="cart-preview">
             <i class="la la-shopping-bag"></i>
            <div class="cart-preview-count" id="drawer-preview-count">
                @if (isset($cart) && count($cart) > 0)
                    {{ count($cart) }}
                @else
                    0
                @endif
            </div>
            <div class="cart-preview-text">ITEMS</div>
            <div class="cart-preview-price" id="drawer-preview-price">
                @php
                    $total = 0;
                    if (isset($cart) && count($cart) > 0) {
                        foreach ($cart as $cartItem) {
                            $product = \App\Models\Product::find($cartItem['product_id']);
                            if ($product) {
                                $total += ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                            }
                        }
                    }
                @endphp
                {{ single_price($total) }}
            </div>
        </div>
    </div>
</div>

<!-- Cart Drawer Overlay -->
<div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="closeCartDrawer()"></div>

<!-- Cart Drawer -->
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
        <h3 class="cart-drawer-title" id="drawer-title">
            <span class="cart-count">
                @if (isset($cart) && count($cart) > 0)
                    {{ count($cart) }}
                @else
                    0
                @endif
            </span> ITEMS
        </h3>
        <button class="cart-drawer-close" onclick="closeCartDrawer()">
            <i class="las la-times"></i>
        </button>
    </div>

    <div class="cart-drawer-body" id="drawer-cart-items">
        @if (isset($cart) && count($cart) > 0)
            @php
                $total = 0;
            @endphp
            
            @foreach ($cart as $key => $cartItem)
                @php
                    $product = \App\Models\Product::find($cartItem['product_id']);
                    if ($product) {
                        $total += ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                        $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                        $max_qty = $product_stock ? $product_stock->qty : 999;
                        $product_name_with_choice = $product->getTranslation('name');
                        if ($cartItem['variation'] != null) {
                            $product_name_with_choice = $product->getTranslation('name').' - '.$cartItem['variation'];
                        }
                    }
                @endphp
                
                @if ($product != null)
                    <div class="cart-drawer-item" id="cart-item-{{ $cartItem['id'] }}">
                        <div class="cart-item-image">
                            <img src="{{ uploaded_asset($product->thumbnail_img) }}" 
                                 alt="{{ $product->getTranslation('name') }}">
                        </div>
                        <div class="cart-item-details">
                            <h4 class="cart-item-name">{{ $product_name_with_choice }}</h4>
                            <p class="cart-item-price" id="item-price-{{ $cartItem['id'] }}">
                                {{ single_price(($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity']) }}
                            </p>
                            
                            @if($cartItem['digital'] != 1)
                                <div class="cart-item-quantity">
                                    <button class="qty-btn qty-minus" 
                                            data-cart-id="{{ $cartItem['id'] }}"
                                            data-min-qty="{{ $product->min_qty }}"
                                            onclick="updateDrawerQuantity({{ $cartItem['id'] }}, 'minus', {{ $product->min_qty }}, {{ $max_qty }})">
                                        <i class="las la-minus"></i>
                                    </button>
                                    <input type="text" 
                                           value="{{ $cartItem['quantity'] }}" 
                                           readonly 
                                           class="qty-input" 
                                           id="qty-{{ $cartItem['id'] }}">
                                    <button class="qty-btn qty-plus" 
                                            data-cart-id="{{ $cartItem['id'] }}"
                                            data-max-qty="{{ $max_qty }}"
                                            onclick="updateDrawerQuantity({{ $cartItem['id'] }}, 'plus', {{ $product->min_qty }}, {{ $max_qty }})">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button class="cart-item-remove" onclick="removeFromDrawer({{ $cartItem['id'] }})">
                            <i class="las la-trash"></i>
                        </button>
                    </div>
                @endif
            @endforeach
        @else
            <div class="cart-drawer-empty">
                <i class="las la-shopping-bag la-5x"></i>
                <p>{{ translate('Your Cart is empty') }}</p>
            </div>
        @endif
    </div>

    @if (isset($cart) && count($cart) > 0)
        <div class="cart-drawer-footer" id="drawer-footer">
            <div class="cart-total">
                <span>Cart Total:</span>
                <span class="cart-total-price" id="drawer-total">{{ single_price($total) }}</span>
            </div>
            <a href="{{ route('checkout.express.show') }}" class="cart-proceed-btn">
                PROCEED <i class="las la-arrow-right"></i>
            </a>
        </div>
    @endif
</div>

<script>
function openCartDrawer() {
    document.getElementById('cartDrawer').classList.add('active');
    document.getElementById('cartDrawerOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCartDrawer() {
    document.getElementById('cartDrawer').classList.remove('active');
    document.getElementById('cartDrawerOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Update quantity in drawer
function updateDrawerQuantity(cartId, action, minQty, maxQty) {
    const qtyInput = document.getElementById('qty-' + cartId);
    let currentQty = parseInt(qtyInput.value);
    let newQty = currentQty;
    
    if (action === 'plus') {
        if (currentQty >= maxQty) {
            AIZ.plugins.notify('warning', '{{ translate("Maximum quantity reached") }}');
            return;
        }
        newQty = currentQty + 1;
    } else if (action === 'minus') {
        if (currentQty <= minQty) {
            AIZ.plugins.notify('warning', '{{ translate("Minimum quantity is") }} ' + minQty);
            return;
        }
        newQty = currentQty - 1;
    }
    
    // Show loading state
    const cartItem = document.getElementById('cart-item-' + cartId);
    cartItem.classList.add('cart-item-loading');
    
    // AJAX call to update quantity
    $.ajax({
        type: "POST",
        url: '{{ route('cart.updateQuantity') }}',
        data: {
            _token: '{{ csrf_token() }}',
            id: cartId,
            quantity: newQty
        },
        success: function(data) {
            cartItem.classList.remove('cart-item-loading');
            
            if (data.status == 1) {
                // Update quantity input
                qtyInput.value = newQty;
                
                // Update item price
                if (data.item_total) {
                    document.getElementById('item-price-' + cartId).textContent = data.item_total;
                }
                
                // Update cart total
                if (data.cart_total) {
                    document.getElementById('drawer-total').textContent = data.cart_total;
                    document.getElementById('drawer-preview-price').textContent = data.cart_total;
                }
                
                // Update cart count
                if (data.cart_count !== undefined) {
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cart_count;
                    });
                }
                
                // Update nav cart view if exists
                if (data.nav_cart_view && document.getElementById('cart_items')) {
                    document.getElementById('cart_items').innerHTML = data.nav_cart_view;
                }
                
                AIZ.plugins.notify('success', '{{ translate("Cart updated successfully") }}');
            } else {
                AIZ.plugins.notify('danger', data.message || '{{ translate("Something went wrong") }}');
            }
        },
        error: function(xhr) {
            cartItem.classList.remove('cart-item-loading');
            AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
        }
    });
}

// Remove item from drawer
function removeFromDrawer(cartId) {
    if (!confirm('{{ translate("Are you sure you want to remove this item?") }}')) {
        return;
    }
    
    const cartItem = document.getElementById('cart-item-' + cartId);
    cartItem.classList.add('cart-item-loading');
    
    $.ajax({
        type: "POST",
        url: '{{ route('cart.removeFromCart') }}',
        data: {
            _token: '{{ csrf_token() }}',
            id: cartId
        },
        success: function(data) {
            if (data.status == 1) {
                // Remove item with animation
                $(cartItem).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Update cart count
                    if (data.cart_count !== undefined) {
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = data.cart_count;
                        });
                        
                        // Update title
                        document.getElementById('drawer-title').innerHTML = '<span class="cart-count">' + data.cart_count + '</span> ITEMS';
                    }
                    
                    // Update total
                    if (data.cart_total) {
                        document.getElementById('drawer-total').textContent = data.cart_total;
                        document.getElementById('drawer-preview-price').textContent = data.cart_total;
                    }
                    
                    // Update nav cart
                    if (data.nav_cart_view && document.getElementById('cart_items')) {
                        document.getElementById('cart_items').innerHTML = data.nav_cart_view;
                    }
                    
                    // If cart is empty, show empty state
                    if (data.cart_count == 0) {
                        const drawerBody = document.getElementById('drawer-cart-items');
                        drawerBody.innerHTML = `
                            <div class="cart-drawer-empty">
                                <i class="las la-shopping-bag la-5x"></i>
                                <p>{{ translate('Your Cart is empty') }}</p>
                            </div>
                        `;
                        
                        // Hide footer
                        const footer = document.getElementById('drawer-footer');
                        if (footer) {
                            footer.style.display = 'none';
                        }
                    }
                });
                
                AIZ.plugins.notify('success', '{{ translate("Item removed from cart") }}');
            } else {
                cartItem.classList.remove('cart-item-loading');
                AIZ.plugins.notify('danger', data.message || '{{ translate("Something went wrong") }}');
            }
        },
        error: function() {
            cartItem.classList.remove('cart-item-loading');
            AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
        }
    });
}

// Close drawer on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCartDrawer();
    }
});
</script>