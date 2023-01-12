<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('pinfo', function(){
    echo phpinfo(); 
});

###############################################################################################
##  START ADMIN PANEL ROUTES
###############################################################################################
## Load Language Variables
Route::get('/load-language-variables', 'General\VariableController@LoadLanguageVariable');
Route::prefix(config('constants.ADMIN_PANEL_PREFIX'))->group(function () {

    ## Authentication
    Route::get('login', ['as' => 'admin.login', 'uses' => 'Admin\Auth\AuthController@showLoginForm']);
    Route::post('login', ['as' => 'admin.auth', 'uses' => 'Admin\Auth\AuthController@adminAuth']);

    ## Sales User login
    Route::get('sales-login', ['as' => 'sales.login', 'uses' => 'Admin\Auth\AuthController@showSalesUsersLoginForm']);
    Route::post('sales-login', ['as' => 'sales.auth', 'uses' => 'Admin\Auth\AuthController@salesAuth']);

    ## Password Reset
    Route::get('password/resets', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.resets');
    Route::post('password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('password.reset.token');
    Route::post('password/reset', 'Admin\Auth\ResetPasswordController@reset')->name('password.request');
    
      ## ============================================ ##
        Route::get('/receipt/grid', ['as' => 'receipt/grid', 'uses' => 'Admin\Receipt\ReceiptController@index']);
        Route::get('/receipt/data', ['as' => 'receipt/data', 'uses' => 'Admin\Receipt\ReceiptController@ajaxData']);
        Route::post('/receipt/data', ['as' => 'receipt/data', 'uses' => 'Admin\Receipt\ReceiptController@PostAjaxData']);
        Route::get('/receipt/{mode}/{id?}', ['as' => 'receipt', 'uses' => 'Admin\Receipt\ReceiptController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);

        ## ============================================ ##
        ##       End  Receipt Mgmt                       ##
        ## ============================================ ##
    
    Route::group(['middleware' => ['admin']], function () {

        ##Unauthorised Access
        Route::get('/unauthorised-access', ['as' => 'unauthorised-access', 'uses' => function(){
            return view('admin/other/unauthorised_access');
        }]);

        Route::post('logout', ['as' => 'admin.logout', 'uses' => 'Admin\Auth\AuthController@logout']);
        Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\HomeController@index']);

        ## User Module
        Route::get('/user/grid/{acess_groupid?}', ['as' => 'admin.user', 'uses' => 'Admin\User\UserController@grid']);
        Route::post('userajaxlist', ['as' => 'admin.userajaxlist', 'uses' => 'Admin\User\UserController@userajaxlist']);
        Route::post('/user/data', ['as' => 'user/data', 'uses' => 'Admin\User\UserController@PostAjaxData']);
        Route::get('/user/{mode}/{id?}', ['as' => 'user', 'uses' => 'Admin\User\UserController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']); 
        Route::post('save', ['as' => 'admin.usersave', 'uses' => 'Admin\User\UserController@save']);  
        //Route::post('/user/{mode}/{id?}', ['as' => 'admin.usersave', 'uses' => 'Admin\User\UserController@save'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        
        Route::post('user/multipledelete', ['as' => 'admin.user.muldelete', 'uses' => 'Admin\User\UserController@multipledelete']);
        Route::post('user/updatestatus', ['as' => 'admin.user.updatestatus', 'uses' => 'Admin\User\UserController@updatestatus']);
        Route::post('user/removeimage', ['as' => 'admin.user.removeimage', 'uses' => 'Admin\User\UserController@removeimage']);

        ## Edit profile
        
        Route::get('user/edit-profile', ['as' => 'admin.user.edit-profile', 'uses' => 'Admin\User\EditProfileController@editProfile']);
        Route::post('user/save-edit-profile', 'Admin\User\EditProfileController@saveProfile');

        ## Sales User START
        Route::post('/salesuser/saleslist', ['as' => 'sales.saleslist', 'uses' => 'Admin\Sales\SalesUserController@SalesUsersListBySid']);
        Route::get('/salesuser/grid/{acess_groupid?}', ['as' => 'sales.user', 'uses' => 'Admin\Sales\SalesUserController@grid']);
        Route::post('salesuserajaxlist', ['as' => 'sales.userajaxlist', 'uses' => 'Admin\Sales\SalesUserController@userajaxlist']);
        Route::post('/salesuser/data', ['as' => 'salesuser/data', 'uses' => 'Admin\Sales\SalesUserController@PostAjaxData']);
        Route::get('/salesuser/{mode}/{id?}', ['as' => 'salesuser', 'uses' => 'Admin\Sales\SalesUserController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']); 
        Route::post('/salesusersave', ['as' => 'sales.usersave', 'uses' => 'Admin\Sales\SalesUserController@save']);  

        Route::post('salesuser/multipledelete', ['as' => 'salesuser.muldelete', 'uses' => 'Admin\Sales\SalesUserController@multipledelete']);
        Route::post('salesuser/updatestatus', ['as' => 'salesuser.updatestatus', 'uses' => 'Admin\Sales\SalesUserController@updatestatus']);
        Route::post('salesuser/removeimage', ['as' => 'salesuser.removeimage', 'uses' => 'Admin\User\SalesUserController@removeimage']);
        
        // Route::get('/salesuser/grid/{acess_groupid?}', ['as' => 'sales.user', 'uses' => 'Admin\User\UserController@grid']);
        ## Sales User END



        ## Access Group
        Route::get('access-role/grid', ['as' => 'access-role/grid', 'uses' => 'Admin\AccessGroup\AccessGroupController@grid']);
        Route::get('access-role/data', ['as' => 'access-role/data', 'uses' => 'Admin\AccessGroup\AccessGroupController@ajaxData']);
        Route::get('/access-role/{mode}/{id?}', ['as' => 'access-role', 'uses' => 'Admin\AccessGroup\AccessGroupController@add'])->where(['mode' => '[a-z]+']);
        Route::post('/access-role/data', ['as' => 'access-role/data', 'uses' => 'Admin\AccessGroup\AccessGroupController@postAjaxData']);
        Route::match(['GET', 'POST'],'/access-group/role/{iAGroupId?}' , ['as' => 'access-group/role' , 'uses' => 'Admin\AccessGroup\AccessGroupController@acessGroupRoleData'])->where(['iAGroupId' => '[a-zA-Z0-9]+']);
        Route::post('/access-group/save',[ 'as'=> 'access-group/save', 'uses'=> 'Admin\AccessGroup\AccessGroupController@saveAccessGroupRole']);
        Route::match(['GET', 'POST'], '/access-group/role-view/{iAGroupId?}', ['as' => 'access-group/role-view', 'uses' => 'Admin\AccessGroup\AccessGroupController@accessGroupRolesView'])->where(['iAGroupId' => '[a-zA-Z0-9]+']);


        ## Activity Log 
        Route::get('/activity-log', ['as' => 'activity-log', 'uses' => 'Admin\ActivityLog\ActivityLogController@index']);
        Route::post('/get-activity-log', ['as' => 'get-activity-log', 'uses' => 'Admin\ActivityLog\ActivityLogController@getActivityLog']);
        Route::post('/get-activity-log-detail', ['as' => 'get-activity-log-detail', 'uses' => 'Admin\ActivityLog\ActivityLogController@getActivityLogDetail']);

        ## Login History Log
        Route::get('/login-history/grid', ['as' => 'login-history/grid', 'uses' => 'Admin\User\LoginLogController@index']);
        Route::get('/login-history/data', ['as' => 'login-history/data', 'uses' => 'Admin\User\LoginLogController@ajaxData']);
       Route::post('/login-history/data', ['as' => 'login-history/data', 'uses' => 'Admin\User\LoginLogController@PostAjaxData']);
    
        ## IP
        Route::get('/ip/list', ['as' => 'ip/list', 'uses' => 'Admin\Ip\IpController@index']);
        Route::get('/ip/data', ['as' => 'ip/data', 'uses' => 'Admin\Ip\IpController@ajaxData']);
        Route::post('/ip/data', ['as' => 'ip/data', 'uses' => 'Admin\Ip\IpController@PostAjaxData']);




        ## ============================================ ##
        ##       Start  Master  Mgmt                    ##
        ## ============================================ ##
        Route::post('country/zonelist', ['as' => 'admin/master/zonelist', 'uses' => 'Admin\Master\CountryController@zonelist']);
        Route::post('country/statelist', ['as' => 'admin/master/statelist', 'uses' => 'Admin\Master\CountryController@statelist']);
        Route::post('country/countrylist', ['as' => 'admin/master/countrylist', 'uses' => 'Admin\Master\CountryController@countrylist']);
        Route::post('country/districtslist', ['as' => 'admin/master/districtslist', 'uses' => 'Admin\Master\CountryController@districtslistData']);
        

        Route::get('/country/grid', ['as' => 'country/grid', 'uses' => 'Admin\Master\CountryController@index']);
        Route::get('/country/data', ['as' => 'country/data', 'uses' => 'Admin\Master\CountryController@ajaxData']);
        Route::post('/country/data', ['as' => 'country/data', 'uses' => 'Admin\Master\CountryController@PostAjaxData']);
        Route::get('/country/{mode}/{id?}', ['as' => 'country', 'uses' => 'Admin\Master\CountryController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);

        ## districts master start ##
        Route::get('/districts/grid', ['as' => 'districts/grid', 'uses' => 'Admin\Master\DistrictController@index']);
        Route::get('/districts/data', ['as' => 'districts/data', 'uses' => 'Admin\Master\DistrictController@ajaxData']);
        Route::post('/districts/data', ['as' => 'districts/data', 'uses' => 'Admin\Master\DistrictController@PostAjaxData']);
        Route::get('/districts/{mode}/{id?}', ['as' => 'districts', 'uses' => 'Admin\Master\DistrictController@add'])->where(['mode' => '[a-z]+', 'id' =>  '.*']);
        Route::post('districts/districtslist', ['as' => 'admin/master/districts/districtslist', 'uses' => 'Admin\Master\DistrictController@districtslist']);
        ## districts master end ##

        ## Taluka master start ##
        Route::get('/taluka/grid', ['as' => 'taluka/grid', 'uses' => 'Admin\Master\TalukaController@index']);
        Route::get('/taluka/data', ['as' => 'taluka/data', 'uses' => 'Admin\Master\TalukaController@ajaxData']);
        Route::post('/taluka/data', ['as' => 'taluka/data', 'uses' => 'Admin\Master\TalukaController@PostAjaxData']);
        Route::post('/taluka/talukalist', ['as' =>'taluka/talukalist', 'uses' => 'Admin\Master\TalukaController@talukalist']);
        Route::get('/taluka/{mode}/{id?}', ['as' => 'taluka', 'uses' => 'Admin\Master\TalukaController@add'])->where(['mode' => '[a-z]+', 'id' =>  '.*']);
        
        ## Taluka master end ##

        ## state master start ##
        Route::get('/state/grid', ['as' => 'state/grid', 'uses' => 'Admin\Master\StateController@index']);
        Route::get('/state/data', ['as' => 'state/data', 'uses' => 'Admin\Master\StateController@ajaxData']);
        Route::post('/state/data', ['as' => 'state/data', 'uses' => 'Admin\Master\StateController@PostAjaxData']);
        Route::get('/state/{mode}/{id?}', ['as' => 'state', 'uses' => 'Admin\Master\StateController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## state master end ##

        ## state master start ##
        Route::post('/zone/zoneajax', ['as' => 'zone/zoneajax', 'uses' => 'Admin\Master\ZoneController@zoneListAjax']);
        Route::get('/zone/grid', ['as' => 'zone/grid', 'uses' => 'Admin\Master\ZoneController@index']);
        Route::get('/zone/data', ['as' => 'zone/data', 'uses' => 'Admin\Master\ZoneController@ajaxData']);
        Route::post('/zone/data', ['as' => 'zone/data', 'uses' => 'Admin\Master\ZoneController@PostAjaxData']);
        Route::get('/zone/{mode}/{id?}', ['as' => 'zone', 'uses' => 'Admin\Master\ZoneController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## state master end ##

        ## Board Manager START ##
        Route::get('/board/list', ['as' => 'board/list', 'uses' => 'Admin\Master\BoardController@index']);
        Route::get('/board/data', ['as' => 'board/data', 'uses' => 'Admin\Master\BoardController@ajaxData']);
        Route::post('/board/data', ['as' => 'board/data', 'uses' => 'Admin\Master\BoardController@PostAjaxData']);
        Route::post('board/mediumlist', ['as' => 'admin/master/mediumlist', 'uses' => 'Admin\Master\BoardController@mediumlist']);
        ## Board Manager END ##

        ## Designation START ##
        Route::get('/designation/list', ['as' => 'designation/list', 'uses' => 'Admin\Master\DesignationController@index']);
        Route::get('/designation/data', ['as' => 'designation/data', 'uses' => 'Admin\Master\DesignationController@ajaxData']);
        Route::post('/designation/data', ['as' => 'designation/data', 'uses' => 'Admin\Master\DesignationController@PostAjaxData']);
        ## Designation END ##

        ## Audit Stock Reason START ##
        Route::get('/auditstockreason/list', ['as' => 'auditstockreason/list', 'uses' => 'Admin\Master\AuditStockReasonController@index']);
        Route::get('/auditstockreason/data', ['as' => 'auditstockreason/data', 'uses' => 'Admin\Master\AuditStockReasonController@ajaxData']);
        Route::post('/auditstockreason/data', ['as' => 'auditstockreason/data', 'uses' => 'Admin\Master\AuditStockReasonController@PostAjaxData']);
        ## Audit Stock Reason END ##

        ## Payment Terms START ##
        Route::get('/paymentterms/list', ['as' => 'paymentterms/list', 'uses' => 'Admin\Master\PaymentTermsController@index']);
        Route::get('/paymentterms/data', ['as' => 'paymentterms/data', 'uses' => 'Admin\Master\PaymentTermsController@ajaxData']);
        Route::post('/paymentterms/data', ['as' => 'paymentterms/data', 'uses' => 'Admin\Master\PaymentTermsController@PostAjaxData']);
        ## Payment Terms END ##

        ## Medium Manager START ##
        Route::get('/medium/list{board_id?}', ['as' => 'medium/list', 'uses' => 'Admin\Master\MediumController@index']);
        Route::get('/medium/data', ['as' => 'medium/data', 'uses' => 'Admin\Master\MediumController@ajaxData']);
        Route::post('/medium/data', ['as' => 'medium/data', 'uses' => 'Admin\Master\MediumController@PostAjaxData']);
        ## Medium Manager END ##

        
        ## Product Head Manager START ##
        Route::get('/producthead/grid', ['as' => 'producthead/grid', 'uses' => 'Admin\Master\ProductHeadController@index']);
        Route::get('/producthead/data', ['as' => 'producthead/data', 'uses' => 'Admin\Master\ProductHeadController@ajaxData']);
        Route::post('/producthead/data', ['as' => 'producthead/data', 'uses' => 'Admin\Master\ProductHeadController@PostAjaxData']);
        Route::get('/producthead/{mode}/{id?}', ['as' => 'producthead', 'uses' => 'Admin\Master\ProductHeadController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## Product Head Manager END ##

        

        ## Product Head Manager START ##
        Route::get('/segment/grid', ['as' => 'segment/grid', 'uses' => 'Admin\Master\SegmentController@index']);
        Route::get('/segment/data', ['as' => 'segment/data', 'uses' => 'Admin\Master\SegmentController@ajaxData']);
        Route::post('/segment/data', ['as' => 'segment/data', 'uses' => 'Admin\Master\SegmentController@PostAjaxData']);
        Route::get('/segment/{mode}/{id?}', ['as' => 'segment', 'uses' => 'Admin\Master\SegmentController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## Product Head Manager END ##

        ## Product Manager START ##
        Route::get('/products/grid', ['as' => 'products/grid', 'uses' => 'Admin\Products\ProductsController@index']);
        Route::get('/products/data', ['as' => 'products/data', 'uses' => 'Admin\Products\ProductsController@ajaxData']);
        Route::post('/products/data', ['as' => 'products/data', 'uses' => 'Admin\Products\ProductsController@PostAjaxData']);
        Route::get('/products/{mode}/{id?}', ['as' => 'products', 'uses' => 'Admin\Products\ProductsController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## Product Manager END ##

        ## Product Manager START ##
        Route::get('/kit/grid', ['as' => 'kit/grid', 'uses' => 'Admin\Products\ProductsController@index']);
        Route::get('/kit/data', ['as' => 'kit/data', 'uses' => 'Admin\Products\ProductsController@ajaxData']);
        Route::post('/kit/data', ['as' => 'kit/data', 'uses' => 'Admin\Products\ProductsController@PostAjaxData']);
        Route::get('/kit/{mode}/{id?}', ['as' => 'kit', 'uses' => 'Admin\Products\ProductsController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## Product Manager END ##

        ## Product Manager START ##
        Route::get('/orders/godown', ['as' => 'orders/godown', 'uses' => 'Admin\Orders\OrdersController@godownpage']);
        Route::get('/orders/godown/data', ['as' => 'orders/godown/data', 'uses' => 'Admin\Orders\OrdersController@godownajax']);
        Route::post('/orders/godown/clientdata', ['as' => 'orders/godown/clientdata', 'uses' => 'Admin\Orders\OrdersController@godownajaxDetails']);
        Route::get('/orders/challan/grid', ['as' => 'orders/createChallan', 'uses' => 'Admin\Orders\OrdersControllerChallan1@index']);
        Route::get('/orders/challan/data', ['as' => 'orders/challan/data', 'uses' => 'Admin\Orders\OrdersControllerChallan1@ajaxData']);
        Route::post('/orders/challan/add', ['as' => 'orders/challan/add', 'uses' => 'Admin\Orders\OrdersControllerChallan@updateOrderStatus']);
        Route::post('/orders/clientajax', ['as' => 'orders/clientdata', 'uses' => 'Admin\Orders\OrdersController@PostAjaxDataClientsSearch']);
        Route::get('/orders/list', ['as' => 'orders/list', 'uses' => 'Admin\Orders\OrdersController@index']);
        Route::get('/orders/data', ['as' => 'orders/data', 'uses' => 'Admin\Orders\OrdersController@ajaxData']);
        Route::post('/orders/data', ['as' => 'orders/data', 'uses' => 'Admin\Orders\OrdersController@PostAjaxData']);
        Route::get('/orders/{mode}/{id?}', ['as' => 'ordersm', 'uses' => 'Admin\Orders\OrdersController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        Route::get('/order/challan/{mode}/{id?}', ['as' => 'order/challan', 'uses' => 'Admin\Orders\OrdersControllerChallan@addChallan'])->where(['mode' => '[a-z]+', 'id' => '.*']);        
        Route::post('/orders/filterproducts', ['as' => 'orders/filterProducts', 'uses' => 'Admin\Orders\OrdersController@filteredProductsList']);
        Route::post('/orders/productsdata', ['as' => 'orders/productsData', 'uses' => 'Admin\Orders\OrdersController@ProductsDataByIds']);
        ## Product Manager END ##

        ## Orders Manager START ##
        Route::get('/orders', ['as' => 'orders', 'uses' => 'Admin\Catalog\OrderController@index']);
        Route::get('/orders/option', ['as' => 'orders/option', 'uses' => 'Admin\Catalog\OrderController@option']);
        ## Orders Manager END ##

        ## Semester Manager START ##
        Route::get('/semester/list', ['as' => 'semester/list', 'uses' => 'Admin\Master\SemesterController@index']);
        Route::get('/semester/data', ['as' => 'semester/data', 'uses' => 'Admin\Master\SemesterController@ajaxData']);
        Route::post('/semester/data', ['as' => 'semester/data', 'uses' => 'Admin\Master\SemesterController@PostAjaxData']);
        ## Semester Manager END ##

        ## Series Manager START ##
        Route::get('/series/list', ['as' => 'series/list', 'uses' => 'Admin\Master\SeriesController@index']);
        Route::get('/series/data', ['as' => 'series/data', 'uses' => 'Admin\Master\SeriesController@ajaxData']);
        Route::post('/series/data', ['as' => 'series/data', 'uses' => 'Admin\Master\SeriesController@PostAjaxData']);
        ## Series Manager END ##

        ## Section Manager START ##
        Route::get('/section/list', ['as' => 'section/list', 'uses' => 'Admin\Master\SectionController@index']);
        Route::get('/section/data', ['as' => 'section/data', 'uses' => 'Admin\Master\SectionController@ajaxData']);
        Route::post('/section/data', ['as' => 'section/data', 'uses' => 'Admin\Master\SectionController@PostAjaxData']);
        ## Section Manager END ## 

        ### Client 
        //Route::get('/client/add', ['as' => 'client/add', 'uses' => 'Admin\Client\ClientController@index']);
        //Route::get('/client/{mode}/{id?}', ['as' => 'client/add', 'uses' => 'Admin\Client\ClientController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        Route::post('/client/addresslist', ['as' => 'client/addresslist', 'uses' => 'Admin\Client\ClientController@clientAddressList']);
        Route::post('/client/addressDetail', ['as' => 'client/addressDetail', 'uses' => 'Admin\Client\ClientController@clientAddressDetail']);
        Route::post('/client/clientContacts', ['as' => 'client/clientContacts', 'uses' => 'Admin\Client\ClientController@clientContactsList']);
        Route::get('/client/grid', ['as' => 'client/grid', 'uses' => 'Admin\Client\ClientController@index']);
        Route::get('/client/data', ['as' => 'client/data', 'uses' => 'Admin\Client\ClientController@ajaxData']);
        Route::post('/client/data', ['as' => 'client/data', 'uses' => 'Admin\Client\ClientController@PostAjaxData']);
        Route::get('/client/{mode}/{id?}', ['as' => 'client', 'uses' => 'Admin\Client\ClientController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
      ### Client ##
           
        ### End Client 
        ## Print Job Vendor Manager START ##
        Route::get('/print_job_vendor/list', ['as' => 'print_job_vendor/list', 'uses' => 'Admin\Master\PrintJobVendorController@index']);
        Route::get('/print_job_vendor/data', ['as' => 'print_job_vendor/data', 'uses' => 'Admin\Master\PrintJobVendorController@ajaxData']);
        Route::post('/print_job_vendor/data', ['as' => 'print_job_vendor/data', 'uses' => 'Admin\Master\PrintJobVendorController@PostAjaxData']);
        ## Print Job Vendor Manager END ##
        
        ## Binding Job Vendor Manager START ##
        Route::get('/binding_job_vendor/list', ['as' => 'binding_job_vendor/list', 'uses' => 'Admin\Master\BindingJobVendorController@index']);
        Route::get('/binding_job_vendor/data', ['as' => 'binding_job_vendor/data', 'uses' => 'Admin\Master\BindingJobVendorController@ajaxData']);
        Route::post('/binding_job_vendor/data', ['as' => 'binding_job_vendor/data', 'uses' => 'Admin\Master\BindingJobVendorController@PostAjaxData']);
        ## Binding Job Vendor Manager END ##

        ## User Discount Category Manager START ##
        Route::get('/user_discount_category/list', ['as' => 'user_discount_category/list', 'uses' => 'Admin\Master\UserDiscountCategoryController@index']);
        Route::get('/user_discount_category/data', ['as' => 'user_discount_category/data', 'uses' => 'Admin\Master\UserDiscountCategoryController@ajaxData']);
        Route::post('/user_discount_category/data', ['as' => 'user_discount_category/data', 'uses' => 'Admin\Master\UserDiscountCategoryController@PostAjaxData']);
        ## User Disocunt Category Manager END ## 

        
        ## Grade Manager START ##
        Route::get('/grade/list', ['as' => 'grade/list', 'uses' => 'Admin\Master\GradeController@index']);
        Route::get('/grade/data', ['as' => 'grade/data', 'uses' => 'Admin\Master\GradeController@ajaxData']);
        Route::post('/grade/data', ['as' => 'grade/data', 'uses' => 'Admin\Master\GradeController@PostAjaxData']);
        ## Grade Manager END ##       
        
        ## Product Head Manager START ##
        Route::get('/transporter/grid', ['as' => 'transporter/grid', 'uses' => 'Admin\Master\TransporterController@index']);
        Route::get('/transporter/data', ['as' => 'transporter/data', 'uses' => 'Admin\Master\TransporterController@ajaxData']);
        Route::post('/transporter/data', ['as' => 'transporter/data', 'uses' => 'Admin\Master\TransporterController@PostAjaxData']);
        Route::get('/transporter/{mode}/{id?}', ['as' => 'transporter', 'uses' => 'Admin\Master\TransporterController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);
        ## Product Head Manager END ##

        ## Grade Manager START ##
        Route::get('/accountyear/list', ['as' => 'accountyear/list', 'uses' => 'Admin\Master\AccountYearController@index']);
        Route::get('/accountyear/data', ['as' => 'accountyear/data', 'uses' => 'Admin\Master\AccountYearController@ajaxData']);
        Route::post('/accountyear/data', ['as' => 'accountyear/data', 'uses' => 'Admin\Master\AccountYearController@PostAjaxData']);
        ## Grade Manager END ##      
        
        Route::get('/contact-us/grid', ['as' => 'contact-us/grid', 'uses' => 'Admin\ContactUs\ContactUsController@index']);
        Route::get('/contact-us/data', ['as' => 'contact-us/data', 'uses' => 'Admin\ContactUs\ContactUsController@ajaxData']);
        Route::get('/contact-mail-logs/data', ['as' => 'contact-mail-logs/data', 'uses' => 'Admin\ContactUs\ContactUsController@contactMailLogs']);
        Route::post('/contact-us/data', ['as' => 'contact-us/data', 'uses' => 'Admin\ContactUs\ContactUsController@PostAjaxData']);
        Route::get('/contact-us/{mode}/{id?}', ['as' => 'contact-us', 'uses' => 'Admin\ContactUs\ContactUsController@add'])->where(['mode' => '[a-z]+', 'id' => '.*']);


        ## Document START ##
        Route::get('/documents/list', ['as' => 'documents/list', 'uses' => 'Admin\Master\DocumentsController@index']);
        Route::get('/documents/data', ['as' => 'documents/data', 'uses' => 'Admin\Master\DocumentsController@ajaxData']);
        Route::post('/documents/data', ['as' => 'documents/data', 'uses' => 'Admin\Master\DocumentsController@PostAjaxData']);
        ## Document END ##   

        ## ============================================ ##
        ##       End  Master  Mgmt                      ##
        ## ============================================ ##

        # ============================================ ##
        ##       Start  Tools  Mgmt                     ##
        ## ============================================ ##
        ## Settings
        Route::get('/setting', ['as' => 'setting', 'uses' => 'Admin\Tools\SettingsController@siteSetting']);
        Route::post('/setting/data',['as' => 'setting/data', 'uses' =>  'Admin\Tools\SettingsController@saveSiteSetting']);

        ## Email template
        Route::get('/email-template/grid', ['as' => 'email-template/grid', 'uses' => 'Admin\Tools\EmailTemplateController@index']);

        Route::get('/email-template/data', ['as' => 'email-template/data', 'uses' => 'Admin\Tools\EmailTemplateController@ajaxData']);

        Route::post('/email-template/data', ['as' => 'email-template/data', 'uses' => 'Admin\Tools\EmailTemplateController@PostAjaxData']); 

        Route::get('/email-template/{mode}/{id?}', ['as' => 'email-template', 'uses' => 'Admin\Tools\EmailTemplateController@add'])->where(['mode' => '[a-z]+']);

       
        ## SMS template
        Route::get('/sms-template/grid', ['as' => 'sms-template/grid', 'uses' => 'Admin\Tools\SMSTemplateController@index']);

        Route::get('/sms-template/data', ['as' => 'sms-template/data', 'uses' => 'Admin\Tools\SMSTemplateController@ajaxData']);

        Route::post('/sms-template/data', ['as' => 'sms-template/data', 'uses' => 'Admin\Tools\SMSTemplateController@PostAjaxData']); 

        Route::get('/sms-template/{mode}/{id?}', ['as' => 'sms-template', 'uses' => 'Admin\Tools\SMSTemplateController@add'])->where(['mode' => '[a-z]+']);

          ## WhatsApp template
        Route::get('/whatsapp-template/grid', ['as' => 'whatsapp-template/grid', 'uses' => 'Admin\Tools\WhatsAppTemplateController@index']);

        Route::get('/whatsapp-template/data', ['as' => 'whatsapp-template/data', 'uses' => 'Admin\Tools\WhatsAppTemplateController@ajaxData']);

        Route::post('/whatsapp-template/data', ['as' => 'whatsapp-template/data', 'uses' => 'Admin\Tools\WhatsAppTemplateController@PostAjaxData']); 

        Route::get('/whatsapp-template/{mode}/{id?}', ['as' => 'whatsapp-template', 'uses' => 'Admin\Tools\WhatsAppTemplateController@add'])->where(['mode' => '[a-z]+']);

        
        ## ============================================ ##
        ##       End  Tools  Mgmt                       ##
        ## ============================================ ##

    });
    
});

Route::prefix(config('constants.SALES_PANEL_PREFIX'))->group(function () {

    ## Authentication
    Route::get('login', ['as' => 'sales.login', 'uses' => 'Sales\Auth\AuthController@showLoginForm']);
    Route::post('login', ['as' => 'sales.auth', 'uses' => 'Sales\Auth\AuthController@salesAuth']);

    ## Password Reset
    Route::get('password/resets', 'Sales\Auth\ForgotPasswordController@showLinkRequestForm')->name('sales.password.resets');
    Route::post('password/email', 'Sales\Auth\ForgotPasswordController@sendResetLinkEmail')->name('sales.password.email');
    Route::get('password/reset/{token}', 'Sales\Auth\ResetPasswordController@showResetForm')->name('sales.password.reset.token');
    Route::post('password/reset', 'Sales\Auth\ResetPasswordController@reset')->name('sales.password.request');

    Route::group(['middleware' => ['sales_user']], function () {

        Route::post('logout', ['as' => 'sales.logout', 'uses' => 'Sales\Auth\AuthController@logout']);
        Route::get('dashboard', ['as' => 'sales.dashboard', 'uses' => 'Sales\HomeController@index']);
        
        ## Edit profile
        Route::get('user/edit-profile', ['as' => 'sales.user.edit-profile', 'uses' => 'Sales\User\EditProfileController@editProfile']);
        Route::post('user/save-edit-profile', ['as' => 'sales.user.save-profile', 'uses' => 'Sales\User\EditProfileController@saveProfile']);

        Route::post('user/removeimage', ['as' => 'sales.user.removeimage', 'uses' => 'Sales\User\EditProfileController@removeimage']);
    });    
});

Route::prefix(config('constants.CLIENT_PANEL_PREFIX'))->group(function () {

    ## Authentication
    Route::get('login', ['as' => 'client.login', 'uses' => 'Client\Auth\AuthController@showLoginForm']);
    Route::post('login', ['as' => 'client.auth', 'uses' => 'Client\Auth\AuthController@clientsAuth']);

    ## Password Reset
    Route::get('password/resets', 'Client\Auth\ForgotPasswordController@showLinkRequestForm')->name('client.password.resets');
    Route::post('password/email', 'Client\Auth\ForgotPasswordController@sendResetLinkEmail')->name('client.password.email');
    Route::get('password/reset/{token}', 'Client\Auth\ResetPasswordController@showResetForm')->name('client.password.reset.token');
    Route::post('password/reset', 'Client\Auth\ResetPasswordController@clientReset')->name('client.password.request');

    Route::group(['middleware' => ['client']], function () {

        Route::get('dashboard', ['as' => 'client.dashboard', 'uses' => 'Client\HomeController@index']);
        Route::post('logout', ['as' => 'client.logout', 'uses' => 'Client\Auth\AuthController@logout']);
        
        ## Edit profile
        Route::get('user/edit-profile', ['as' => 'client.user.edit-profile', 'uses' => 'Client\User\EditProfileController@editProfile']);
        Route::post('user/save-edit-profile', ['as' => 'client.user.save-profile', 'uses' => 'Client\User\EditProfileController@saveProfile']);

        Route::post('user/removeimage', ['as' => 'client.user.removeimage', 'uses' => 'Client\User\EditProfileController@removeimage']);
    });
});

Auth::routes();
Route::get('course-to-batch-cron', ['as' => 'course-to-batch-cron', 'uses' => 'Cron\CourseToBatchDataCopyController@copyCourseDataIntoBatch']);
Route::get('send-push-notifications', ['as' => 'send-push-notifications', 'uses' => 'Cron\PushNotificationController@sendPushNotifications']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('insert-member-notification', ['as' => 'insert-member-notification', 'uses' => 'Cron\MemberNotificationsController@insertMemberNotification']);
Route::get('send-member-remining-topic-notification', ['as' => 'send-member-remining-topic-notification', 'uses' => 'Cron\SendTopicNotificationController@sendMemberNotification']);
Route::get('send-birthaday-notification-to-member', ['as' => 'send-birthaday-notification-to-member', 'uses' => 'Cron\SendBirthadayNotificationTomemberController@sendBirthdayNotification']);
##This is for testing 
Route::get('topic-reminder-test-cron', ['as' => 'topic-reminder-test-cron', 'uses' => 'Cron\TopicReminderNotificationTestCron@sendMemberNotification']);

Route::get('app-inactivity-reminder', ['as' => 'app-inactivity-reminder', 'uses' => 'Cron\AppInActivityReminderTestCron@sendMemberNotification']);

Route::get('remove-member-notification', ['as' => 'remove-member-notification', 'uses' => 'Cron\removeMemberNotificationCron@removeMemberNotification']);

Route::get('trail-expiry-reminder-notification', ['as' => 'topic-reminder-test-cron', 'uses' => 'Cron\TrailExpiryReminderNotification@sendMemberNotification']);

Route::get('trail-expiry-reminder-email', ['as' => 'topic-reminder-test-cron', 'uses' => 'Cron\TrailExpiryReminderSendEmail@sendMemberEmailNotification']);

Route::get('update-admission-status-to-expired', ['as' => 'update-admission-status-to-expired', 'uses' => 'Cron\UpdateTrialMemberAdmissionStatusCron@UpdateStatusOfAdmission']);

Route::get('member-module-certificate-eligibility', ['as' => 'member-module-certificate-eligibility', 'uses' => 'Cron\MemberModuleCertificateEligibilityController@memberModuleCertificateEligibility']);

Route::get('member-module-certificate-expired', ['as' => 'member-module-certificate-expired', 'uses' => 'Cron\MemberModuleCertificateExpiredController@memberModuleCertificateExpired']);

Route::get('member-module-certificate-generate', ['as' => 'member-module-certificate-generate', 'uses' => 'Cron\MemberModuleCertificateGenerateController@memberModuleCertificateGenerate']);

Route::get('member-module-certificate-password-procted', ['as' => 'member-module-certificate-password-procted', 'uses' => 'Cron\MemberModuleCertificatePasswordProctedController@memberModuleCertificatePasswordProcted']);

Route::get('member-module-certificate-email-send', ['as' => 'member-module-certificate-email-send', 'uses' => 'Cron\ModuleCertificateEmailSendToMember@moduleCertificateEmailSendToMember']);

Route::get('update-app-search-keyword', ['as' => 'update-app-search-keyword', 'uses' => 'Cron\UpdateSearchKeywordController@updateSearchKey']);
Route::get('auto-assign-member-to-group', ['as' => 'update-app-search-keyword', 'uses' => 'Cron\AutoAssignMemberToGroupController@assignMemberToGroup']);


//Route::get('update-admission-end-date', ['as' => 'update-admission-end-date', 'uses' => 'Cron\SetAdmissionEndDate@updateAdmissionEndDate']);

