<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'OMS') }}</title> 
        <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> -->
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
		<link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendors/css/charts/morris.css')}}">
		<link rel="stylesheet" type="text/css" href="{{ asset('/assets/fonts/simple-line-icons/style.css')}}">
		<link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/core/colors/material-palette-gradient.css')}}">
		<link rel="stylesheet" type="text/css" href="{{asset('assets/css/plugins/loaders/loaders.min.css')}}">
		<link rel="stylesheet" type="text/css" href="{{asset('assets/css/core/colors/palette-loader.css')}}">
		<!-- <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/style.css')}}"> -->
		<!-- END: Theme CSS-->
		
		<!-- Custom CSS-->
		<link rel="stylesheet" type="text/css" href="{{ asset('/assets/pages/css/admin/custom.css')}}">
		<link href="{{ asset('/assets/pages/css/admin/report.css')}}" rel="stylesheet" type="text/css"/>
		<!-- Custom CSS-->
		<link rel="shortcut icon" href="favicon.ico" />

		@yield('styles')

		<!-- BEGIN: Vendor JS-->
		<script src="{{ asset('/assets/vendors/js/material-vendors.min.js')}}"></script>
	   <!--  <script src="{{ asset('/assets/vendors/js/vendors.min.js')}}"></script> -->
		<script src="{{ asset('/assets/pages/scripts/custom.js')}}" type="text/javascript"></script>
		<!-- BEGIN Vendor JS-->     
		<!-- global css end -->

			
		<!-- END HEAD -->
	</head>
    <body class="page-container-bg-solid">
        @yield('content')
        <!-- Scripts -->
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
        <script src="{{asset('/assets/vendors/js/bootbox/bootbox.min.js')}}" type="text/javascript"></script>
        <script src="{{ asset('/assets/pages/scripts/general.js')}}" type="text/javascript"></script>
		<script type="text/javascript">
           var assets = '<?php echo url("/"); ?>';
           var panel_text = 'admin';
           var csrf_token = "{{ csrf_token() }}";
		   var DATE_PICKER_FORMAT = "<?php echo Config::get('constants.DATETIME_PICKER_FORMAT');?>";
		   var toYear = "{{ date('Y', strtotime('+10 years')) }}";
		   var currentYear = "{{ date('Y') }}";
        </script>
        @yield('scripts')
    </body>
</html>
