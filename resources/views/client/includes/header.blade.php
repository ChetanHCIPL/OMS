 <!-- BEGIN: Header--> 
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center">
    <div class="navbar-wrapper custom_inner_wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto">
                    <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a>
                </li>
                <li class="nav-item">
                    <a class="navbar-brand p-0" href="{{route('client.dashboard')}}">
                        <img  class="card-title text-center logo" src="{{asset('/images/logo/logo.png')}}" alt="OMS" title="" >
                    </a>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand" href="#"><i class="ficon ft-maximize"></i></a></li>
                </ul>
                <ul class="nav navbar-nav float-right">
                <?php
                    $name = isset(Auth::guard('client')->user()->client_name) ? Auth::guard('client')->user()->client_name: "" ;
                    $checkImgArr = array();
                    if(Auth::guard('client')->user()->image != ""){
                      $checkImgArr = checkImageExistInFolder(Config::get('path.AWS_CLIENT_PATH'),Config::get('path.client_path'),'client','','_', Auth::guard('client')->user()->image,count(Config::get('constants.client_image_size'))); 
                    }
                ?>
                    <li class="dropdown dropdown-user nav-item">
                            <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <span class="mr-1 user-name text-bold-700">{{$name}}</span><span class="avatar avatar-online">
                                    <img src="{{ !empty($checkImgArr['img_url'])? $checkImgArr['img_url']:'' }}" onerror="isImageExist(this)"  noimage="50x50.jpg" alt=""><i></i>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('client.user.edit-profile') }}"><i class="ft-user"></i> Edit Profile </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();"><i class="ft-power"></i>
                                    Logout 
                                </a>
                            <form id="logout-form" action="{{ route('client.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->
<!-- BEGIN: Main Menu-->
<div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-dark navbar-without-dd-arrow navbar-shadow" role="navigation" data-menu="menu-wrapper">
    <div class="navbar-container main-menu-content" data-menu="menu-container">
        <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
            <?php
            $dashboard_open = "";
            if (in_array(Request::route()->getName(), array("client.dashboard"))) {
                $dashboard_open = ' active';
            }?>
            <li class="dropdown nav-item {{ $dashboard_open }}" >
                <a class="nav-link" href="{{route('client.dashboard')}}"><i class="material-icons">settings_input_svideo</i><span>Dashboard</span></a>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu