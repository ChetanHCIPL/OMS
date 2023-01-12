<!DOCTYPE html>
<html class="loading" lang="{{ App::getLocale() }}" data-textdirection="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/charts/jquery-jvectormap-2.0.3.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/charts/morris.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/core/colors/material-palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/plugins/loaders/loaders.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/core/colors/palette-loader.css')}}">
    <!-- END: Theme CSS-->
    
    <!-- Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/pages/css/admin/custom.css')}}">
    <!-- Custom CSS-->
    <link rel="shortcut icon" href="favicon.ico" />
            <!-- BEGIN PAGE LEVEL STYLES SECTION-->
            @yield('styles')
            <!-- END PAGE LEVEL STYLES SECTION-->
            
            <!-- END HEAD -->
            <!-- BEGIN: Vendor JS-->
            <script src="{{ asset('/assets/vendors/js/material-vendors.min.js')}}"></script>
            <script src="{{ asset('/assets/pages/scripts/custom.js')}}" type="text/javascript"></script>
            <!-- BEGIN Vendor JS-->
        </head>
        <body class="horizontal-layout horizontal-menu material-horizontal-layout material-horizontal-nav material-layout 2-columns blank-page bg_theme_gradient<?php if (!Auth::check()) { ?> fixed-navbar<?php } ?>" data-open="hover" data-menu="horizontal-menu" data-col="2-columns">

        <?php //if (!Auth::guard('admin')->check()) { ?>
            @include('admin/includes/header_login')
        <?php //} ?>

         
        <!-- BEGIN: Content-->
        <div class="app-content content">
            @yield('content')
        </div>
        <!-- END: Content-->


         <!-- BEGIN: Page Vendor JS-->
        <script src="{{ asset('/assets/vendors/js/ui/jquery.sticky.js')}}"></script>
        <script src="{{ asset('/assets/vendors/js/charts/jquery.sparkline.min.js')}}"></script>
        <script src="{{ asset('/assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
        <script src="{{ asset('/assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
        <!-- END: Page Vendor JS-->

        <!-- BEGIN: Theme JS-->
        <script src="{{ asset('/assets/js/core/app-menu.js')}}"></script>
        <script src="{{ asset('/assets/js/core/app.js')}}"></script>
        <!-- END: Theme JS-->

        <!-- BEGIN: Page JS-->
        <script src="{{ asset('/assets/js/core/libraries/jquery_ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/assets/js/scripts/pages/material-app.js')}}"></script>
        <script src="{{ asset('/assets/data/jvector/visitor-data.js')}}"></script>
        <script src="{{asset('/assets/bk/vendors/js/bootbox/bootbox.min.js')}}" type="text/javascript"></script>
        <script src="{{ asset('/assets/pages/scripts/general.js?ver=1.1')}}" type="text/javascript"></script>
        
    <!-- END: Page Vendor JS-->
        <!-- BEGIN PAGE LEVEL SCRIPTS SECTION-->
        @yield('scripts')
        <!-- END PAGE LEVEL SCRIPTS SECTION-->
    </body>
</html>