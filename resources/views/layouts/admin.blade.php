<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{Config::get('constants.SITE_TITLE')}}</title> 
         <!-- Font! -->
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/pages/css/font.css')}}">
      <!-- Font -->
    <!-- BEGIN: Vendor CSS! -->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/material-vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/icheck/icheck.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/icheck/custom.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS !-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/material.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/material-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/material-colors.css')}}">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/core/menu/menu-types/material-horizontal-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/core/colors/material-palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/plugins/loaders/loaders.min.css')}}">
    <link href="{{ asset('/assets/vendors/css/forms/selects/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/vendors/css/forms/selects/selectize.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/vendors/css/forms/selects/selectize.default.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins/forms/selectize/selectize.css')}}" rel="stylesheet" type="text/css" />
    <!--End Selectize-->
    <!--Start Bootstrap Switch-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/bootstrap-switch.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/forms/toggle/switchery.min.css')}}">
    <link href="{{ asset('assets/css/core/colors/palette-switch.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/assets/vendors/css/fancybox/dist/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- END: Theme CSS-->
    
    <!-- Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/pages/css/admin/custom.css?ver=1.5')}}">
    <!-- Custom CSS-->
    <link rel="shortcut icon" href="favicon.ico" />

    @yield('styles')
        <!-- BEGIN: Vendor JS-->
        
        <script src="{{ asset('/assets/vendors/js/material-vendors.min.js')}}"></script>
        <script src="{{ asset('/assets/pages/scripts/custom.js')}}" type="text/javascript"></script>
        <script src="{{ asset('/assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
        <!-- BEGIN Vendor JS-->     
        <!-- global css end -->

        
    <!-- END HEAD -->
    </head>
    @php 
        $currentroute = Route::currentRouteName(); 
        $route_prefix = trim(Request::route()->getPrefix(), '/');
    @endphp 
    @if($currentroute == 'password.resets' || $currentroute == 'password.reset.token' || $currentroute == 'sales.password.resets' || $currentroute == 'client.password.resets' || $currentroute == 'client.password.reset.token' || $currentroute == 'sales.password.reset.token')
     @php $cls = 'blank-page bg_theme_gradient' @endphp
    @else
     @php $cls = '' @endphp
    @endif
    <body class="horizontal-layout horizontal-menu 2-columns {{$cls}}" data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
        <div class="pace  pace-inactive">
            <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
                <div class="pace-progress-inner"></div>
            </div>
            <div class="pace-activity"></div>
        </div>
           
            @if(Auth::guard('admin')->check() && $route_prefix == 'admin')  
                @include('admin/includes/header')
            @elseif(Auth::guard('sales_user')->check() && $route_prefix == 'sales_user')  
                @include('sales/includes/header')
            @elseif(Auth::guard('client')->check() && $route_prefix == 'client')  
                @include('client/includes/header')
            @endif

            </div>
            <!-- BEGIN: Content-->
             <div class="app-content content">
             <div class="content-wrapper">
                <div class="content-body">
                    @yield('content')
                </div>
            </div>

            <!-- END: Content-->
            @if(Auth::guard('admin')->check() || Auth::guard('client')->check() || Auth::guard('sales_user')->check())  
                @include('admin/includes/footer')
            @endif

            <!-- Scripts -->
            @include('admin/general_includes/javascript_settings')
            <!-- script start -->

           


              <!-- BEGIN: Page Vendor JS-->
        <script src="{{ asset('/assets/vendors/js/ui/jquery.sticky.js')}}"></script>
        <script src="{{ asset('/assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
        <script src="{{ asset('/assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
        <!-- END: Page Vendor JS-->

        <!-- BEGIN: Theme JS-->
        <script src="{{ asset('/assets/js/core/app-menu.js')}}"></script>
        <script src="{{ asset('/assets/js/core/app.js')}}"></script>
        <!-- END: Theme JS-->

        <!-- BEGIN: Page JS-->
        <script src="{{ asset('/assets/js/core/libraries/jquery_ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{asset('/assets/vendors/js/bootbox/bootbox.min.js')}}" type="text/javascript"></script>
        <script src="{{ asset('/assets/pages/scripts/general.js')}}" type="text/javascript"></script>
        <!-- END: Page Vendor JS-->
        <!-- script end-->  
        <!-- script end-->  
        <script type="text/javascript">
           var assets = '<?php echo url("/"); ?>';
           var csrf_token = "{{ csrf_token() }}";
        </script>
        
        <!--ckeditor-->
        @include('admin/general_includes/global_ckeditor')
        <!---ckeditor-->

        @yield('scripts')
    </body>
</html>