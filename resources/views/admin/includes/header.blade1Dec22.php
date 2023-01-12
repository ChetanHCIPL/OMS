 <!-- BEGIN: Header--> 
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center">
    <div class="navbar-wrapper custom_inner_wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item"><a class="navbar-brand p-0" href="{{route('admin.dashboard')}}">
                    <img  class="card-title text-center logo" src="{{asset('/images/logo/logo.png')}}" alt="OMS" title="" >
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
                    $name = ((isset(Auth::guard('admin')->user()->first_name) && isset(Auth::guard('admin')->user()->last_name))?Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name:"");
                    $checkImgArr = array();
                    if(Auth::guard('admin')->user()->image!=""){
                      $checkImgArr = checkImageExistInFolder(Config::get('path.AWS_ADMIN_USER'),Config::get('path.user_path'),'user','','_', Auth::guard('admin')->user()->image,count(Config::get('constants.admin_user_image_size'))); 
                    }
                    
                ?>
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 user-name text-bold-700">{{$name}}</span><span class="avatar avatar-online"><img src="{{isset($checkImgArr['img_url'])?$checkImgArr['img_url']:''}}" onerror="isImageExist(this)"  noimage="50x50.jpg" alt=""><i></i></span></a>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="{{ route('admin.user.edit-profile') }}"><i class="ft-user"></i> Edit Profile </a>
                            <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();"><i class="ft-power"></i>
                                Logout 
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
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
            if (in_array(Request::route()->getName(), array("admin.dashboard"))) {
                $dashboard_open = ' active';
            }?>
            <li class="dropdown nav-item {{ $dashboard_open }}" >
                <a class="nav-link" href="{{route('admin.dashboard')}}"><i class="material-icons">settings_input_svideo</i><span>Dashboard</span></a>
            </li>
            <?php
            if (gen_checkAllAccessRights('Users') || gen_checkAllAccessRights('Roles')  || gen_checkAllAccessRights('ActivityLog')||gen_checkAllAccessRights('LoginLog')) {
                $nav_open = "";
                if (in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate","access-role/grid" , "access-role", "access-group","login-history/grid","activity-log","ip/list","ip/data"))) {
                    $nav_open = ' active';
                }
                ?>
            <li class="dropdown nav-item  {{ $nav_open }}" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown" title="Admin"><i class="material-icons">account_circle</i><span>Admin</span></a>
                <ul class="dropdown-menu">
                    <li class="dropdown dropdown-submenu <?php echo ((in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate")))) ? 'active' : "" ?> " data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i class="material-icons">person_outline</i><span>User Mgmt</span></a>
                        <ul class="dropdown-menu">
                            <?php 
                            if (gen_checkAllAccessRights('Users')) { ?>
                            <li  data-menu=""   class="<?php echo ((in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate")))) ? 'active' : "" ?>  "  ><a class="dropdown-item" href="{{ route('admin.user') }}" data-toggle=""><span>Users</span></a></li>
                            <?php } 
                             if (gen_checkAllAccessRights('Roles')) { ?>
                            <li  data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("access-role/grid" , "access-role", "access-group")))) ? 'active' : "" ?>  "> <a class="dropdown-item" href="{{ route('access-role/grid')}}" data-toggle=""><span>Roles</span></a></li>
                            <?php } ?>
                            <?php if (gen_checkAllAccessRights('LoginLog')) { ?>
                                <li <?php echo (Request::route()->getName() == "login-history/grid") ? 'class="active"' : "" ?> data-menu=""><a class="dropdown-item" href="{{route('login-history/grid')}}" data-toggle=""><span>Login History</span></a></li>
                            <?php }?>
                            <?php if (gen_checkAllAccessRights('Ip')) { ?>
                                <li <?php echo (Request::route()->getName() == "ip/list") ? 'class="active"' : "" ?> data-menu=""><a class="dropdown-item" href="{{route('ip/list')}}" data-toggle=""><span>Ip Mgmt</span></a></li>
                            <?php }?>
                        </ul>
                    </li>
                     <?php if (gen_checkAllAccessRights('ActivityLog')) { ?>
                    <li class="<?php echo ((in_array(Request::route()->getName(), array("activity-log")))) ? 'active' : "" ?> "><a class="dropdown-item" href="{{route('activity-log')}}"><i class="material-icons">swap_horiz</i><span>Activity Log</span></a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <?php
            }?>

            
            <?php
            if(gen_checkAllAccessRights('Country') || gen_checkAllAccessRights('States') || gen_checkAllAccessRights('Districts') || gen_checkAllAccessRights('Boards') || gen_checkAllAccessRights('Languages') || gen_checkAllAccessRights('Variables') ) {
                    $nav_open = "";
                    if (in_array(Request::route()->getName(), array("country/grid",  "country/data", "country","districts/grid" , "districts/data", "districts", "state/grid", "state", "state/data", "language/grid", "language/data", "language", "variable/grid","variable","variable/data"))) 
                     {
                        $nav_open = ' active';
                    }
            ?>
            <li class="dropdown nav-item  {{ $nav_open }}" data-menu="dropdown"><a class="dropdown-toggle nav-link" data-toggle="dropdown" title="Master Mgmt" href="#" ><i class="material-icons">apps</i><span>Master Mgmt</span></a>
                <ul class="dropdown-menu">
                    <?php
                    if(gen_checkAllAccessRights('Country')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("country/grid",  "country/data", "country")))) ? 'active' : "" ?>  ">
                        <a class="dropdown-item" href="{{ route('country/grid') }}" data-toggle="">
                            <i class="material-icons">location_on</i>
                            <span>Country</span>
                        </a>
                    </li>
                    <?php
                    }
                    if(gen_checkAllAccessRights('States')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array( "state/grid", "state", "state/data")))) ? 'active' : "" ?>  ">
                        <a class="dropdown-item" href="{{ route('state/grid') }}" data-toggle="">
                            <i class="material-icons">navigation</i>
                            <span>State</span>
                        </a>
                    </li>
                    <?php
                    }
                    if(gen_checkAllAccessRights('Districts')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("districts/grid" , "districts/data", "districts", )))) ? 'active' : "" ?> ">
                        <a class="dropdown-item" href="{{ route('districts/grid') }}" data-toggle="">
                            <i class="material-icons">location_city</i>
                            <span>{{-- Dsitrict --}}District</span>
                        </a>
                    </li>
                    <?php }  
                    if(gen_checkAllAccessRights('Taluka')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("taluka/grid" , "taluka/data", "taluka", )))) ? 'active' : "" ?> ">
                        <a class="dropdown-item" href="{{ route('taluka/grid') }}" data-toggle="">
                            <i class="material-icons">my_location</i>
                            <span>{{-- Taluka --}}Taluka</span>
                        </a>
                    </li>
                    <?php }  ?>
                    <li data-menu="" class="dropdown-divider"></li>
                    <?php
                    if(gen_checkAllAccessRights('ProductHead')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("producthead/grid" , "ProductHead/data", "producthead", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('producthead/grid') }}" data-toggle="">
                            <i class="material-icons">category</i>
                                <span>{{-- Product Head --}}Product Head</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('UserDiscountCategory')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("user_discount_category/list" , "ProductHead/data", "user_discount_category", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('user_discount_category/list') }}" data-toggle="">
                            <i class="material-icons">assignment_ind</i>
                                <span>{{-- User Discount Category --}}User Discount Category</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('Grade')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("grade/list" , "Grade/data", "grade", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('grade/list') }}" data-toggle="">
                            <i class="material-icons">bar_chart</i>
                                <span>{{-- Grade --}}Grade</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('AccountYear')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("accountyear/list" , "accountyear/data", "accountyear", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('accountyear/list') }}" data-toggle="">
                            <i class="material-icons">history</i>
                                <span>{{-- Account Year --}}Account Year</span>
                            </a>
                        </li>
                    <?php } ?>
                    <li data-menu="" class="dropdown-divider"></li>
                    <?php
                    if(gen_checkAllAccessRights('Boards')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("board/list" , "Board/data", "board", )))) ? 'active' : "" ?> ">
                        <a class="dropdown-item" href="{{ route('board/list') }}" data-toggle="">
                        <i class="material-icons">list_alt</i>
                            <span>{{-- Board --}}Board</span>
                        </a>
                    </li>
                    <?php }
                    if(gen_checkAllAccessRights('Medium')) { ?> 
                    <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("medium/list" , "Medium/data", "medium", )))) ? 'active' : "" ?> ">
                        <a class="dropdown-item" href="{{ route('medium/list') }}" data-toggle="">
                        <i class="material-icons">language</i>
                            <span>{{-- Medium --}}Medium</span>
                        </a>
                    </li>
                    <?php }
                    if(gen_checkAllAccessRights('Segment')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("segment/grid" , "Segment/data", "segment", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('segment/grid') }}" data-toggle="">
                            <i class="material-icons">supervisor_account</i>
                                <span>{{-- Segment --}}Segment</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('Semester')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("board/list" , "Semester/data", "semester", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('semester/list') }}" data-toggle="">
                            <i class="material-icons">local_library</i>
                                <span>{{-- Semester --}}Semester</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('Series')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("series/list" , "Series/data", "series", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('series/list') }}" data-toggle="">
                            <i class="material-icons">confirmation_number</i>
                                <span>{{-- Series --}}Series</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('Section')) { ?> 
                        <li data-menu="" class="<?php echo ((in_array(Request::route()->getName(), array("section/list" , "Section/data", "section", )))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('section/list') }}" data-toggle="">
                            <i class="material-icons">event_seat</i>
                                <span>{{-- Section --}}Section</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('PrintJobVendor')) {?>
                        <li data-menu="" class="<?php echo((in_array(Request::route()->getName(), array("print_job_vendor/list", "PrintJobVendor/data", "print_job_vendor")))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('print_job_vendor/list') }}" data-toggle="">
                                <i class="material-icons">library_books</i>
                                    <span>{{-- Print Job Vendor --}}Print Job Vendor</span>
                            </a>
                        </li>
                    <?php }
                    if(gen_checkAllAccessRights('BindingJobVendor')) {?>
                        <li data-menu="" class="<?php echo((in_array(Request::route()->getName(), array("binding_job_vendor/list", "BindingJobVendor/data", "binding_job_vendor")))) ? 'active' : "" ?> ">
                            <a class="dropdown-item" href="{{ route('binding_job_vendor/list') }}" data-toggle="">
                                <i class="material-icons">filter_none</i>
                                    <span>{{-- Binding Job Vendor --}}Binding job Vendor</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?> 
            
            <?php
            if (gen_checkAllAccessRights('Users') || gen_checkAllAccessRights('Roles')  || gen_checkAllAccessRights('ActivityLog')||gen_checkAllAccessRights('LoginLog')) {
                $nav_open = "";
                if (in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate","access-role/grid" , "access-role", "access-group","login-history/grid","activity-log","ip/list","ip/data"))) {
                    $nav_open = ' active';
                }
                ?>
            <li class="dropdown nav-item  {{ $nav_open }}" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown" title="Admin"><i class="material-icons">local_mall</i><span>Catalogs Mgmt</span></a>
                <ul class="dropdown-menu">
                    <?php if (gen_checkAllAccessRights('Products')) { ?>
                        <li class="<?php echo ((in_array(Request::route()->getName(), array("products")))) ? 'active' : "" ?> "><a class="dropdown-item" href="{{route('products/grid')}}"><i class="material-icons">shopping_cart</i><span>Products</span></a>
                        </li>
                    <?php } ?>
                    <li class="dropdown dropdown-submenu <?php echo ((in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate")))) ? 'active' : "" ?> " data-menu="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown"><i class="material-icons">shopping_basket</i><span>Orders Mgmt</span></a>
                        <ul class="dropdown-menu">
                            <?php 
                            if (gen_checkAllAccessRights('Users')) { ?>
                            <li  data-menu=""   class="<?php echo ((in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate")))) ? 'active' : "" ?>  "  ><a class="dropdown-item" href="{{ route('orders') }}" data-toggle=""><span>Add Order</span></a></li>
                            <?php } 
                            if (gen_checkAllAccessRights('Users')) { ?>
                            <li  data-menu=""   class="<?php echo ((in_array(Request::route()->getName(), array("admin.user",  "user/data", "admin.usercreate")))) ? 'active' : "" ?>  "  ><a class="dropdown-item" href="{{ route('orders/option') }}" data-toggle=""><span>Add Order Option</span></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    
                </ul>
            </li>
            <?php
            }?>
            
            <li class="dropdown nav-item {{ $nav_open  }}" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown" title="Tools"><i class="material-icons">settings</i><span>Tools</span></a>
                <ul class="dropdown-menu">
                    <?php 
                        if (gen_checkAllAccessRights('Setting')) { ?>
                    <li data-menu="" <?php echo ((in_array(Request::route()->getName(), array("setting")))) ? 'class="active"' : "" ?> ><a class="dropdown-item" href="{{ route('setting')}}" data-toggle=""><i class="material-icons">settings_brightness</i><span>Settings</span></a></li> 
                    <?php } if (gen_checkAllAccessRights('EmailTemplate')) { ?>  
                    <li data-menu="" <?php echo ((in_array(Request::route()->getName(), array("email-template/grid",  "email-template/data", "email-template")))) ? 'class="active"' : "" ?> ><a class="dropdown-item" href="{{ route('email-template/grid')}}" data-toggle=""><i class="material-icons">email</i><span>Email Templates</span></a></li> 
                    <?php } if (gen_checkAllAccessRights('SMSTemplate')) { ?>
                        <li data-menu="" <?php echo ((in_array(Request::route()->getName(), array("sms-template/grid",  "sms-template/data", "sms-template")))) ? 'class="active"' : "" ?> ><a class="dropdown-item" href="{{ route('sms-template/grid')}}" data-toggle=""><i class="material-icons">sms</i><span>SMS Templates</span></a></li>
                     <?php }   
                    ?>                       
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu