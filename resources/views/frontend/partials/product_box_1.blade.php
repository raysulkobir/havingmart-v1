<div class="aiz-card-box border border-light hov-shadow-md mt-1 mb-2 has-transition bg-white">
    @if(discount_in_percentage($product) > 0)
        <span class="badge-custom">{{ translate('OFF') }}<span class="box ml-1 mr-0">&nbsp;{{discount_in_percentage($product)}}%</span></span>
    @endif
    <div class="position-relative">
        @php
            $product_url = route('product', $product->slug);
            if($product->auction_product == 1) {
                $product_url = route('auction-product', $product->slug);
            }
        @endphp
        <a href="{{ $product_url }}" class="d-block">
            <img
                class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                alt="{{  $product->getTranslation('name')  }}"
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
            <a href="javascript:void(0)" onclick="addToCompare({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to compare') }}" data-placement="left">
                <i class="las la-sync"></i>
            </a>
            {{-- <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to cart') }}" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a> --}}
        </div>
    </div>
    <div class="pt-2 text-center">
        <div class="p-md-2">
    
            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                <a href="{{ $product_url }}" class="d-block text-reset">{{  $product->getTranslation('name')  }}</a>
            </h3>
            <div class="fs-15">
                @if(home_base_price($product) != home_discounted_base_price($product))
                    <del class="fw-600 mr-1">{{ home_base_price($product) }}</del>
                @endif
                <span class="fw-700 text-primary">{{ home_discounted_base_price($product) }}</span>
            </div>

            
            <div class="rating rating-sm mt-1">
                {{ renderStarRating($product->rating) }}
            </div>
            {{-- <span class="fw-700 text-block">
                {{ (int) $product->weight }} {{ $product->unit }}
            </span> --}}
        </div>


        <div class="product-actions">
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})"  data-placement="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" style="vertical-align: middle;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                ব্যাগ এ রাখুন
            </a>
        </div>
        <div class="product-actions mt-1">
           <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }}, 'buy_now')"  data-placement="left">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" style="vertical-align: middle;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                এখনি কিনুন
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
