@extends('frontend.layouts.app')

@section('content')
    {{-- Hero Banner Slider — Full Container Width --}}
    <div class="hm-hero-section mb-4">
        <div class="container">
            @if (get_setting('home_slider_images') != null)
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true" data-autoplay="true">
                    @php $slider_images = json_decode(get_setting('home_slider_images'), true); @endphp
                    @foreach ($slider_images as $key => $value)
                        <div class="carousel-box">
                            <a href="{{ json_decode(get_setting('home_slider_links'), true)[$key] }}">
                                <img
                                    class="d-block mw-100 img-fit overflow-hidden"
                                    src="{{ uploaded_asset($slider_images[$key]) }}"
                                    alt="{{ env('APP_NAME') }} promo"
                                    height="420"
                                    style="border-radius: 12px; width: 100%;"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';"
                                >
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Today's Deal — Horizontal Strip --}}
    @if(count($todays_deal_products) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">
                        {{ translate("Today's Deal") }}
                        <span class="hm-deal-badge ml-2">{{ translate('Hot') }}</span>
                    </h2>
                </div>
                <div class="hm-product-grid">
                    @foreach ($todays_deal_products as $product)
                        @if ($product != null)
                            <div class="hm-product-col">
                                @include('frontend.partials.product_box_1', ['product' => $product])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Category Thumbnails Row --}}
    @if(count($popularCategories) > 0)
    <section class="hm-categories-strip mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Shop by Category') }}</h2>
                    <a href="{{ route('categories.all') }}" class="hm-view-all-btn">{{ translate('View All') }}</a>
                </div>
                <div class="hm-cat-carousel owl-carousel owl-theme">
                    @foreach ($popularCategories as $category)
                        @if ($category != null)
                            <div class="item">
                                <a href="{{ route('products.category', $category->slug) }}" class="hm-cat-item d-block">
                                    <div class="hm-cat-img-wrap">
                                        <img
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($category->banner) }}"
                                            alt="{{ $category->getTranslation('name') }}"
                                            class="lazyload"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                    <span class="hm-cat-name text-truncate d-block text-center" style="max-width: 100%;">{{ $category->getTranslation('name') }}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Flash Deal --}}
    @if(@$flash_deal != null && strtotime(date('Y-m-d H:i:s')) >= $flash_deal->start_date && strtotime(date('Y-m-d H:i:s')) <= $flash_deal->end_date)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Flash Sale') }}</h2>
                    <div class="d-flex align-items-center">
                        <div class="aiz-count-down mr-3" data-date="{{ date('Y/m/d H:i:s', $flash_deal->end_date) }}"></div>
                        <a href="{{ route('flash-deal-details', $flash_deal->slug) }}" class="hm-view-all-btn">{{ translate('View More') }}</a>
                    </div>
                </div>
                <div class="hm-product-grid">
                    @foreach ($flash_deal->flash_deal_products->take(12) as $flash_deal_product)
                        @php
                            $product = $flash_deal_product->product;
                        @endphp
                        @if ($product != null && $product->published != 0)
                            <div class="hm-product-col">
                                @include('frontend.partials.product_box_1', ['product' => $product])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- New Products --}}
    @if (count(@$new_products) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('New Products') }}</h2>
                </div>
                <div class="hm-product-grid">
                    @foreach ($new_products as $product)
                        <div class="hm-product-col">
                            @include('frontend.partials.product_box_1', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Banner Section 1 --}}
    @if (get_setting('home_banner1_images') != null)
        @php $banner_1_imags = json_decode(get_setting('home_banner1_images'), true) ?? []; @endphp
        @if(count($banner_1_imags) > 0)
        <div class="mb-4">
            <div class="container">
                <div class="row gutters-10">
                    @foreach ($banner_1_imags as $key => $value)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}" class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($banner_1_imags[$key]) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100 rounded-lg">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @endif

    {{-- Featured Products --}}
    @if (count(@$featured_products) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Featured Products') }}</h2>
                </div>
                <div class="hm-product-grid">
                    @foreach ($featured_products as $product)
                        <div class="hm-product-col">
                            @include('frontend.partials.product_box_1', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Best Selling --}}
    @if (get_setting('best_selling') == 1 && count(@$best_selling_products) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Best Selling') }}</h2>
                </div>
                <div class="hm-product-grid">
                    @foreach ($best_selling_products->take(12) as $product)
                        <div class="hm-product-col">
                            @include('frontend.partials.product_box_1', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Banner Section 2 --}}
    @if (get_setting('home_banner2_images') != null)
    <div class="mb-4">
        <div class="container">
            <div class="row gutters-10">
                @php $banner_2_imags = json_decode(get_setting('home_banner2_images')); @endphp
                @foreach ($banner_2_imags as $key => $value)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="mb-3 mb-lg-0">
                            <a href="{{ json_decode(get_setting('home_banner2_links'), true)[$key] }}" class="d-block text-reset">
                                <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($banner_2_imags[$key]) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100 rounded-lg">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Category-wise Products --}}
    @if(!empty($home_categories))
        @foreach ($home_categories as $cat_id)
            @php $category = $home_category_models[$cat_id] ?? null; @endphp
            @if($category != null)
                <section class="mb-4">
                    <div class="container">
                        <div class="hm-section-card">
                            <div class="hm-section-header">
                                <h2 class="hm-section-title">{{ $category->getTranslation('name') }}</h2>
                                <a href="{{ route('products.category', $category->slug) }}" class="hm-view-all-btn">{{ translate('View More') }}</a>
                            </div>
                            <div class="hm-product-grid">
                                @foreach (($home_category_products[$cat_id] ?? collect()) as $product)
                                    <div class="hm-product-col">
                                        @include('frontend.partials.product_box_1', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

    {{-- Banner Section 3 --}}
    @if (get_setting('home_banner3_images') != null)
    <div class="mb-4">
        <div class="container">
            <div class="row gutters-10">
                @php $banner_3_imags = json_decode(get_setting('home_banner3_images')); @endphp
                @foreach ($banner_3_imags as $key => $value)
                    <div class="col-xl col-md-6">
                        <div class="mb-3 mb-lg-0">
                            <a href="{{ json_decode(get_setting('home_banner3_links'), true)[$key] }}" class="d-block text-reset">
                                <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($banner_3_imags[$key]) }}" alt="{{ env('APP_NAME') }} promo" class="img-fluid lazyload w-100 rounded-lg">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Best Sellers --}}
    @if (get_setting('vendor_system_activation') == 1 && count($best_selers) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card hm-dark-section">
                <div class="hm-section-header">
                    <h2 class="hm-section-title text-white">{{ translate('Best Sellers') }}</h2>
                    <a href="{{ route('sellers') }}" class="hm-view-all-btn hm-view-all-light">{{ translate('View All Sellers') }}</a>
                </div>
                <div class="hm-seller-grid">
                    @foreach ($best_selers as $seller)
                        @if ($seller->user != null)
                            <div class="hm-seller-card">
                                <a href="{{ route('shop.visit', $seller->slug) }}" class="d-block">
                                    <div class="hm-seller-logo">
                                        <img
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="@if ($seller->logo !== null) {{ uploaded_asset($seller->logo) }} @else {{ static_asset('assets/img/placeholder.jpg') }} @endif"
                                            alt="{{ $seller->name }}"
                                            class="lazyload"
                                        >
                                    </div>
                                    <div class="hm-seller-info">
                                        <h3 class="hm-seller-name">{{ $seller->name }}</h3>
                                        <div class="rating rating-sm">{{ renderStarRating($seller->rating) }}</div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Trending Products --}}
    @if (count(@$trending_products) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Trending Products') }}</h2>
                </div>
                <div class="hm-product-grid" id="trending-products-grid">
                    @foreach ($trending_products as $product)
                        <div class="hm-product-col">
                            @include('frontend.partials.product_box_1', ['product' => $product])
                        </div>
                    @endforeach
                </div>
                <div id="trending-loader" class="text-center mt-4 py-3 d-none" data-page="2" data-loading="false" data-has-more="{{ $has_more_trending ? 'true' : 'false' }}">
                    <div class="d-inline-flex align-items-center justify-content-center text-primary">
                        <i class="las la-spinner la-spin fs-24 mr-2"></i>
                        <span class="fw-600">{{ translate('Loading more products...') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Shop By Brands --}}
    @if (get_setting('top10_brands') != null && count($topBrands) > 0)
    <section class="mb-4">
        <div class="container">
            <div class="hm-section-card">
                <div class="hm-section-header">
                    <h2 class="hm-section-title">{{ translate('Shop By Brands') }}</h2>
                    <a href="{{ route('brands.all') }}" class="hm-view-all-btn">{{ translate('View All Brands') }}</a>
                </div>
                <div class="hm-brand-grid">
                    @foreach ($topBrands as $brand)
                        @if ($brand != null)
                            <a href="{{ route('products.brand', $brand->slug) }}" class="hm-brand-card">
                                <img
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($brand->logo) }}"
                                    alt="{{ $brand->getTranslation('name') }}"
                                    class="lazyload hm-brand-img"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                                <span class="hm-brand-name">{{ $brand->getTranslation('name') }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Category Slider
            $('.hm-cat-carousel').owlCarousel({
                loop: true,
                margin: 15,
                nav: false,
                dots: false,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                smartSpeed: 600,
                responsive: {
                    0: { items: 2, margin: 10 },
                    480: { items: 3, margin: 10 },
                    768: { items: 5, margin: 12 },
                    992: { items: 6, margin: 15 },
                    1200: { items: 8, margin: 15 }
                }
            });

            // Infinite Scroll for Trending Products
            var isTrendingLoading = false;
            $(window).on('scroll', function() {
                var loader = $('#trending-loader');
                var grid = $('#trending-products-grid');
                if (loader.length === 0 || grid.length === 0 || loader.attr('data-has-more') === 'false' || isTrendingLoading) {
                    return;
                }
                
                var bottomOfGrid = grid.offset().top + grid.outerHeight();
                var bottomOfWindow = $(window).scrollTop() + $(window).height();
                
                if (bottomOfWindow > bottomOfGrid - 100) {
                    isTrendingLoading = true;
                    loadMoreTrending();
                }
            });

            function loadMoreTrending() {
                var loader = $('#trending-loader');
                var page = parseInt(loader.attr('data-page'));
                
                loader.removeClass('d-none');
                
                $.ajax({
                    url: "{{ route('home.section.trending') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        page: page
                    },
                    success: function(response) {
                        if (response.html) {
                            $('#trending-products-grid').append(response.html);
                            
                            // Initialize lazy loading for new images
                            if (typeof AIZ === 'object' && typeof AIZ.plugins === 'object' && typeof AIZ.plugins.lazyLoader === 'function') {
                                AIZ.plugins.lazyLoader();
                            }
                            
                            loader.attr('data-page', page + 1);
                        }
                        
                        isTrendingLoading = false;
                        if (!response.has_more) {
                            loader.attr('data-has-more', 'false');
                        }
                        loader.addClass('d-none');
                    },
                    error: function() {
                        isTrendingLoading = false;
                        loader.addClass('d-none');
                    }
                });
            }
        });
    </script>
@endsection
