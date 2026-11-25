@php
    $product_url = route('product', $product->slug);
    if($product->auction_product == 1) { $product_url = route('auction-product', $product->slug); }
    $hm_discount  = discount_in_percentage($product);
    $hm_brand     = $product->brand;
    $hm_reviews   = $product->reviews()->count();
    $hm_in_stock  = ($product->digital == 1) || ($product->current_stock > 0);
@endphp
<div class="aiz-card-box border border-light hov-shadow-md mt-1 mb-2 has-transition bg-white">
    <div class="position-relative">
        {{-- New Arrival / Best Seller / discount badges --}}
        <div class="hm-card-badges">
            @if($hm_discount > 0)
                <span class="hm-badge hm-badge-off">-{{ $hm_discount }}%</span>
            @endif
            @if($product->best_selling == 1)
                <span class="hm-badge hm-badge-best">{{ translate('Best Seller') }}</span>
            @elseif($product->new_product == 1)
                <span class="hm-badge hm-badge-new">{{ translate('New') }}</span>
            @endif
        </div>

        <a href="{{ $product_url }}" class="d-block">
            <img
                class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                alt="{{ $product->getTranslation('name') }}"
                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
            >
        </a>

        @if ($product->wholesale_product)
            <span class="absolute-bottom-left fs-11 text-white fw-600 px-2 lh-1-8" style="background-color: #455a64">
                {{ translate('Wholesale') }}
            </span>
        @endif

        <div class="absolute-top-right aiz-p-hov-icon">
            <a href="javascript:void(0)" onclick="addToWishList({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to wishlist') }}" data-placement="left">
                <i class="la la-heart-o"></i>
            </a>
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Quick View') }}" data-placement="left">
                <i class="las la-eye"></i>
            </a>
            <a href="javascript:void(0)" onclick="addToCompare({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to compare') }}" data-placement="left">
                <i class="las la-sync"></i>
            </a>
        </div>
    </div>

    <div class="pt-2 text-center">
        <div class="p-md-2">
            @if($hm_brand)
                <a href="{{ route('products.brand', $hm_brand->slug) }}" class="hm-card-brand d-block text-reset">{{ $hm_brand->getTranslation('name') }}</a>
            @endif

            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                <a href="{{ $product_url }}" class="d-block text-reset">{{ $product->getTranslation('name') }}</a>
            </h3>

            <div class="rating rating-sm mt-1">
                {{ renderStarRating($product->rating) }}
                <span class="hm-review-count">({{ $hm_reviews }})</span>
            </div>

            <div class="fs-15 mt-1">
                @if(home_base_price($product) != home_discounted_base_price($product))
                    <del class="fw-600 mr-1">{{ home_base_price($product) }}</del>
                @endif
                <span class="fw-700 text-primary">{{ home_discounted_base_price($product) }}</span>
            </div>

            <div class="hm-stock {{ $hm_in_stock ? 'is-in' : 'is-out' }}">
                <i class="las {{ $hm_in_stock ? 'la-check-circle' : 'la-times-circle' }}"></i>
                {{ $hm_in_stock ? translate('In Stock') : translate('Out of Stock') }}
            </div>
        </div>

        <div class="product-actions">
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" data-placement="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" style="vertical-align: middle;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                {{ translate("Add to Cart") }}
            </a>
        </div>
        <div class="product-actions mt-1">
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }}, 'buy_now')" data-placement="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" style="vertical-align: middle;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                {{ translate("Buy Now") }}
            </a>
        </div>

        @if (addon_is_activated('club_point'))
            <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                {{ translate('Club Point') }}:
                <span class="fw-700 float-right">{{ $product->earn_point }}</span>
            </div>
        @endif
    </div>
</div>
