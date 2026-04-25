<!DOCTYPE html>
@if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
<head>
    <title>@yield('meta_title', get_setting('website_name').' | '.get_setting('site_motto'))</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description') )" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords') )">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    @yield('meta')
    @if(!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ uploaded_asset(get_setting('meta_image')) }}">
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ uploaded_asset(get_setting('meta_image')) }}">
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ uploaded_asset(get_setting('meta_image')) }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">

    {{-- start slider  --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
     {{-- end slider  --}}

    @if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/style.css?v=1') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
    </script>

    <style>
        body{
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
        }
        :root{
            --primary: {{ get_setting('base_color', '#e62d04') }};
            --hov-primary: {{ get_setting('base_hov_color', '#c52907') }};
            --soft-primary: {{ hex2rgba(get_setting('base_color','#e62d04'),.15) }};
        }

        #map{
            width: 100%;
            height: 250px;
        }
        #edit_map{
            width: 100%;
            height: 250px;
        }
        .pac-container { z-index: 100000; }
    </style>
    
@if (get_setting('google_analytics') == 1)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('TRACKING_ID') }}');
    </script>
@endif

@if (get_setting('facebook_pixel') == 1)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
    </noscript>
@endif

@php
    echo get_setting('header_script');
@endphp

</head>
<body>
    <div class="aiz-main-wrapper d-flex flex-column">
        @include('frontend.inc.nav')

        @yield('content')

        @include('frontend.inc.footer')
    </div>

    @if (get_setting('show_cookies_agreement') == 'on')
        <div class="aiz-cookie-alert shadow-xl">
            <div class="p-3 bg-dark rounded">
                <div class="text-white mb-3">
                    @php
                        echo get_setting('cookies_agreement_text');
                    @endphp
                </div>
                <button class="btn btn-primary aiz-cookie-accept">
                    {{ translate('Ok. I Understood') }}
                </button>
            </div>
        </div>
    @endif

    @if (get_setting('show_website_popup') == 'on')
        <div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
            <div class="absolute-full bg-black opacity-60"></div>
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md">
                <div class="modal-content position-relative border-0 rounded-0">
                    <div class="aiz-editor-data">
                        {!! get_setting('website_popup_content') !!}
                    </div>
                    @if (get_setting('show_subscribe_form') == 'on')
                        <div class="pb-5 pt-4 px-5">
                            <form class="" method="POST" action="{{ route('subscribers.store') }}">
                                @csrf
                                <div class="form-group mb-0">
                                    <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    {{ translate('Subscribe Now') }}
                                </button>
                            </form>
                        </div>
                    @endif
                    <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                        <i class="la la-close fs-20"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @include('frontend.partials.modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la-2x">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.mobile-category-drawer')

    @yield('modal')
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/scripts.js') }}"></script>
    @if (get_setting('facebook_chat') == 1)
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                  xfbml            : true,
                  version          : 'v3.3'
                });
              };

              (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="fb-root"></div>
        <div class="fb-customerchat"
          attribution=setup_tool
          page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif

    <script>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

    <script>

        $(document).ready(function() {
            $('.category-nav-element').each(function(i, el) {
                $(el).on('mouseover', function(){
                    if(!$(el).find('.sub-cat-menu').hasClass('loaded')){
                        $.post('{{ route('category.elements') }}', {_token: AIZ.data.csrf, id:$(el).data('id')}, function(data){
                            $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                        });
                    }
                });
            });
            if ($('#lang-change').length > 0) {
                $('#lang-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var locale = $this.data('flag');
                        $.post('{{ route('language.change') }}',{_token: AIZ.data.csrf, locale:locale}, function(data){
                            location.reload();
                        });

                    });
                });
            }

            if ($('#currency-change').length > 0) {
                $('#currency-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var currency_code = $this.data('currency');
                        $.post('{{ route('currency.change') }}',{_token: AIZ.data.csrf, currency_code:currency_code}, function(data){
                            location.reload();
                        });

                    });
                });
            }
        });

        $('#search').on('keyup', function(){
            search();
        });

        $('#search').on('focus', function(){
            search();
        });

        function search(){
            var searchKey = $('#search').val();
            if(searchKey.length > 0){
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route('search.ajax') }}', { _token: AIZ.data.csrf, search:searchKey}, function(data){
                    if(data == '0'){
                        // $('.typed-search-box').addClass('d-none');
                        $('#search-content').html(null);
                        $('.typed-search-box .search-nothing').removeClass('d-none').html('{{ translate('Sorry, nothing found for') }} <strong>"'+searchKey+'"</strong>');
                        $('.search-preloader').addClass('d-none');

                    }
                    else{
                        $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                        $('#search-content').html(data);
                        $('.search-preloader').addClass('d-none');
                    }
                });
            }
            else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        function updateNavCart(view,count){
            $('.cart-count').html(count);
            $('#cart_items').html(view);
        }

        function removeFromCart(key){
            $.post('{{ route('cart.removeFromCart') }}', {
                _token  : AIZ.data.csrf,
                id      :  key
            }, function(data){
                updateNavCart(data.nav_cart_view,data.cart_count);
                $('#cart-summary').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())-1);
            });
        }

        function addToCompare(id){
            $.post('{{ route('compare.addToCompare') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#compare').html(data);
                AIZ.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html())+1);
            });
        }

        function addToWishList(id){
            @if (Auth::check() && Auth::user()->user_type == 'customer')
                $.post('{{ route('wishlists.store') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                    if(data != 0){
                        $('#wishlist').html(data);
                        AIZ.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    }
                    else{
                        AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
            @elseif(Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the WishList.') }}");
            @else
                AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
            @endif
        }

        function showAddToCartModal(id, type=''){
            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route('cart.showCartModal') }}', {_token: AIZ.data.csrf, id:id, type}, function(data){
                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }

        $('#option-choice-form input').on('change', function(){
            getVariantPrice();
        });

        function getVariantPrice(){
            if($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()){
                $.ajax({
                   type:"POST",
                   url: '{{ route('products.variant_price') }}',
                   data: $('#option-choice-form').serializeArray(),
                   success: function(data){

                        $('.product-gallery-thumb .carousel-box').each(function (i) {
                            if($(this).data('variation') && data.variation == $(this).data('variation')){
                                $('.product-gallery-thumb').slick('slickGoTo', i);
                            }
                        })

                        $('#option-choice-form #chosen_price_div').removeClass('d-none');
                        $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
                        $('#available-quantity').html(data.quantity);
                        $('.input-number').prop('max', data.max_limit);
                        if(parseInt(data.in_stock) == 0 && data.digital  == 0){
                           $('.buy-now').addClass('d-none');
                           $('.add-to-cart').addClass('d-none');
                           $('.out-of-stock').removeClass('d-none');
                        }
                        else{
                           $('.buy-now').removeClass('d-none');
                           $('.add-to-cart').removeClass('d-none');
                           $('.out-of-stock').addClass('d-none');
                        }

                        AIZ.extra.plusMinus();
                   }
               });
            }
        }

        function checkAddToCartValidity(){
            var names = {};
            $('#option-choice-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });

            if($('#option-choice-form input:radio:checked').length == count){
                return true;
            }

            return false;
        }

        function addToCart(buyNow = ''){
            @if(Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif

            if(checkAddToCartValidity()) {
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data){

                       $('#addToCart-modal-body').html(null);
                       $('.c-preloader').hide();
                       $('#modal-size').removeClass('modal-lg');
                       $('#addToCart-modal-body').html(data.modal_view);
                       AIZ.extra.plusMinus();
                       AIZ.plugins.slickCarousel();
                       updateNavCart(data.nav_cart_view,data.cart_count);

                          if(buyNow){
                            window.location.replace("{{ route('cart') }}");
                          }
                    }
                });
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }

        function buyNow(){
            @if(Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the Cart.') }}");
                return false;
            @endif
            
            if(checkAddToCartValidity()) {
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                   type:"POST",
                   url: '{{ route('cart.addToCart') }}',
                   data: $('#option-choice-form').serializeArray(),
                   success: function(data){
                       if(data.status == 1){

                            $('#addToCart-modal-body').html(data.modal_view);
                            updateNavCart(data.nav_cart_view,data.cart_count);

                            window.location.replace("{{ route('cart') }}");
                       }
                       else{
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.modal_view);
                       }
                   }
               });
            }
            else{
                AIZ.plugins.notify('warning', "{{ translate('Please choose all the options') }}");
            }
        }
    </script>

    @yield('script')

    @php
        echo get_setting('footer_script');
    @endphp

    <script>
        var width = $(window).width();
        if(width <= 991){
            $("#nav_home").removeClass("py-15px");
            $("#nav_home").addClass("py-10px");
            $("#footer_bottom").removeClass("pt-2");
            $("#footer_bottom").addClass("pt-4");
        }
    </script>
    {{-- slider  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


    <div class="social-float-container">
        <a href="https://wa.me/8801308664173" target="_blank" class="social-icon whatsapp">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path></svg>
        </a>
        <a href="https://m.me/103810778317437" target="_blank" class="social-icon messenger">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M256.55 8C116.52 8 8 110.34 8 248.57c0 72.3 29.71 134.78 78.07 177.94 8.35 7.51 6.63 11.86 8.05 58.23A19.92 19.92 0 0 0 122 502.31c52.91-23.3 53.59-25.14 62.56-22.7C337.85 521.8 504 423.7 504 248.57 504 110.34 396.59 8 256.55 8zm149.24 185.13l-73 115.57a37.37 37.37 0 0 1-53.91 9.93l-58.08-43.47a15 15 0 0 0-18 0l-78.37 59.44c-10.46 7.93-24.16-4.6-17.11-15.67l73-115.57a37.36 37.36 0 0 1 53.91-9.93l58.06 43.46a15 15 0 0 0 18 0l78.41-59.38c10.44-7.98 24.14 4.54 17.09 15.62z"></path></svg>
        </a>
    </div>

    <!--Start of Tawk.to Script-->
    {{-- <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/697660f3737dce198497a9a0/1jfr6legj';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
    </script> --}}
    <!--End of Tawk.to Script-->
</body>
</html>
