<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

## Prefix: Latest versionV1 for api - set in global constants
// $versionV1 = config('api_constants.API_LATEST_VERSION_PREFIX');
// Route::group(['prefix' => strtolower($versionV1)], function() use($versionV1){
//   ##=========================
//   ## START API: Before Login
//   ##=========================
  
//   ## Middleware: json_request - Check format of Request Parameters
//   ## Middleware: authorization_token - Check Authorization Token in Request Header for API
//   ## Middleware: setlocale - Set locale in session for current request
//   Route::get('get-browse-plan-data', "API\\$versionV1\Plan\PlanController@getPlanList");
//   Route::post('save-purchase-plan-data', "API\\$versionV1\Plan\PlanController@savePurchasePlan");
//   Route::post('updare-purchase-plan-data', "API\\$versionV1\Plan\PlanController@updatePurchasePlan");

//   Route::group(['middleware' => ['json_request', 'authorization_token']], function() use($versionV1){  
    
//     ##Phase 2 APIs Start##
//     ## Member - Send OTP for Registration 
//     Route::post('send-registration-otp', "API\\$versionV1\Auth\RegisterController@generateRegistrationOtp");
    
//         ## Generate OTP
//         Route::post('verify-registration-otp', "API\\$versionV1\Auth\RegisterController@verifyRegistrationOtp");

//         ## Re Generate OTP
//         Route::post('resend-registration-otp', "API\\$versionV1\Auth\RegisterController@resendRegistrationOtp");

//         ## Create Member Registration 
//         Route::post('member-submit-registration', "API\\$versionV1\Auth\RegisterController@submitRegistration");
        
//         ##Phase 2 APIs End##
        
        
//         ## Member - Login
//         //sRoute::post('login', "API\\$versionV1\Auth\AuthController@login");
//         Route::post('login', "API\\$versionV1\Auth\AuthController@login");
        
//         ## Member - forgot password
//         Route::post('forgot-password', "API\\$versionV1\Auth\AuthController@generateMobileOtpForgotPassword");
        
//         ## Generate OTP
//         Route::post('verify-mobile-forgot-password-otp', "API\\$versionV1\Auth\AuthController@verifyMobileForgotPasswordOtp");
        
//         ## Member: Change Password
//         Route::post('change-password', "API\\$versionV1\Auth\AuthController@passwordReset");
        
//         ## resend otp for forgot password
//         Route::post('resend-forgot-password-mobile-otp',"API\\$versionV1\Auth\AuthController@resendForgotPasswordOtp");
        
        
        
//         ##-----Start General API-------------##
//         ## Get Master Data based on requested data type
//         Route::post('get-master-data', "API\\$versionV1\General\GeneralApiController@getMasterdata");
//         ## Get language variables
//         Route::post('get-language-variables', "API\\$versionV1\General\VariableController@getLanguageVariables");
        
//         // Get All Language Variables
//         // Route::post('get-master-data', "API\\$versionV1\General\GeneralApiController@getMasterdata");
        
//         ## Get Browse Course Data
//         Route::post('get-browse-course-data', "API\\$versionV1\Course\CourseController@getBrowseCourseData");
        
//         ## Get Board Of Directors Data
//         Route::post('get-broad-of-director-data', "API\\$versionV1\General\GeneralApiController@GetBroadOfDirectorData");
        
//         ## Get Contact Us data
//         Route::post('get-contact-us-data', "API\\$versionV1\General\GeneralApiController@getContactUsData");
//         ##-----End General API-------------##
        
        
//         ##--------Start Change Device APIs--------##
        
//         Route::post('check-member-device-send-otp',"API\\$versionV1\Auth\ChangeDeviceController@CheckMemberAccountAssociateSendOTP");
        
//         Route::post('check-member-device-verify-otp',"API\\$versionV1\Auth\ChangeDeviceController@CheckMemberAccountAssociateVerifyOTP");
        
//         Route::post('check-member-device-resend-otp',"API\\$versionV1\Auth\ChangeDeviceController@CheckMemberAccountAssociateResendOTP");

//         Route::post('check-member-device-change-request',"API\\$versionV1\Auth\ChangeDeviceController@checkMemberDeviceChangeRequest");


//         ##--------End Change Device APIs--------##
        
//        ##--------Get Start city, state and country APIs--------##   
       
//        Route::get('countries', "API\\$versionV1\Country\CountryController@index");
//        Route::get('states/{country_code}', "API\\$versionV1\State\StateController@index");
//        Route::get('cities/{state_code}', "API\\$versionV1\City\CityController@index");
       
//        ##--------End Get Start city, state and country API--------## 
       
//       });

//       ##=========================
//       ## START API: After Login
//       ##=========================
//       Route::group(['prefix' => config('api_constants.MEMBER_API_PREFIX'), 'middleware' => ['json_request', 'authorization_token', 'member_auth_token']], function() use($versionV1){
        
//         ##Edit member details start
//         Route::post('get-member-profile-details', "API\\$versionV1\Auth\AuthController@getMemberProfileDetails");
        
//         Route::post('update-member-profile-details', "API\\$versionV1\Auth\AuthController@updateMemberProfileDetails");
        
//         Route::post('get-member-referrer-code', "API\\$versionV1\Member\MemberController@getMemberReferrerCode");
        
//         Route::post('account-security-change-password', "API\\$versionV1\Auth\AuthController@changeCurrentPassword");
        
//         ## Member Logout
//         Route::post('logout', "API\\$versionV1\Auth\AuthController@logout");
        
//         ## Get Dashboard Data
//         Route::post('get-dashboard-data', "API\\$versionV1\Dashboard\DashboardController@getData");
//         Route::post('get-dashboard-course-data', "API\\$versionV1\Dashboard\DashboardController@getDataByCourse");
        
//         #######################################################################################  
        
//         ###################### Phase 2 API ######################
//       });    
// });

$versionV1 = config('api_constants.API_LATEST_VERSION_PREFIX');
Route::group(['prefix' => strtolower($versionV1)], function() use($versionV1){
  ##=========================
  ## START API: Before Login
  ##=========================
  
  ## Middleware: json_request - Check format of Request Parameters
  ## Middleware: authorization_token - Check Authorization Token in Request Header for API
  ## Middleware: setlocale - Set locale in session for current request

  Route::group(['middleware' => ['json_request', 'authorization_token']], function() use($versionV1){  

    ## Member - Login
    //sRoute::post('login', "API\\$versionV1\Auth\AuthController@login");
    Route::post('login', "API\\$versionV1\Auth\AuthController@login");

    // Forgot Password API
    Route::post('forgot-password-otp', "API\\$versionV1\Auth\AuthController@generateOTPForForgotPassword");
    Route::post('resend-forgot-password-otp',"API\\$versionV1\Auth\AuthController@resendForgotPasswordOtp");

    // Verify Forgot Password OTP
    Route::post('verify-forgot-password-otp', "API\\$versionV1\Auth\AuthController@verifyForgotPasswordOtp");

    // Reset Password
    Route::post('change-password', "API\\$versionV1\Auth\AuthController@passwordReset");

    Route::post('client-login', "API\\$versionV1\Auth\ClientAuthController@login");

    ##-----Start General API-------------##
      ## Get Master Data based on requested data type
      Route::post('get-master-data', "API\\$versionV1\General\GeneralApiController@getMasterdata");

      ## Contact Us!
      Route::post('get-contact-us-data', "API\\$versionV1\General\GeneralApiController@getContactUsData");

    ##-----End General API-------------##
  });


  ##============================================
  ## START API: After Login For Sales user Login
  ##============================================
  Route::group(['prefix' => config('api_constants.USER_API_PREFIX'), 'middleware' => ['json_request', 'authorization_token', 'user_auth_token']], function() use($versionV1){
    ## Edit Sales User Profile
    Route::post('get-user-profile-details', "API\\$versionV1\Auth\AuthController@getUserProfileDetails");
    Route::post('update-user-profile-details', "API\\$versionV1\Auth\AuthController@updateUserProfileDetails");

    ## Get Sales Users List
    Route::post('get-sales-users-list', "API\\$versionV1\Sales\SalesUserController@getSalesUsersList");


    ## Client's APIs Endpoint
    Route::post('get-client-list-with-detail', "API\\$versionV1\Client\ClientController@getAllClients"); // rename [get-client-list-with-detail]
    Route::post('get-client-details', "API\\$versionV1\Client\ClientController@getClientsDetailsById");
    Route::post('add-client-data', "API\\$versionV1\Client\ClientController@addClientData");
    Route::post('add-client-address-data', "API\\$versionV1\Client\ClientController@addClientAddressData");
    Route::post('add-client-contact-data', "API\\$versionV1\Client\ClientController@addClientContactData"); 

    ## Cliend's Edit Details
    Route::post('edit-client-address-data', "API\\$versionV1\Client\ClientController@editClientAddressData");
    Route::post('edit-client-contact-data', "API\\$versionV1\Client\ClientController@editClientContactData");
    
    ## Client's Detele Address & COntacts
    Route::post('delete-client-address', "API\\$versionV1\Client\ClientController@deleteClientAddress");
    Route::post('delete-client-contact', "API\\$versionV1\Client\ClientController@deleteClientContact");

    ## Client's Get || Update Address & Contact
    Route::post('get-client-address', "API\\$versionV1\Client\ClientController@getClientAddressByAddressId");

    ## Contact us 
    Route::post('insert-contact-us-data', "API\\$versionV1\Client\ClientController@insertContactUsData");
    

    Route::post('logout', "API\\$versionV1\Auth\AuthController@logout");

    ## Order's APIs Endpoint
    Route::post('get-order-list', "API\\$versionV1\Order\OrderController@getAllOrders"); 

    Route::post('get-client-addresses', "API\\$versionV1\Client\ClientController@getClientAddressesByClientId"); 
    Route::post('get-client-contacts', "API\\$versionV1\Client\ClientController@getClientContactsByClientId"); 
    Route::post('get-client-list', "API\\$versionV1\Client\ClientController@getClientsBasicData"); 

    Route::post('get-filtered-products-list', "API\\$versionV1\Product\ProductController@getFilteredProductsData"); 
    
  });

  ##============================================
  ## START API: After Login For Clients user Login
  ##============================================
  Route::group(['prefix' => config('api_constants.CLIENT_API_PREFIX'), 'middleware' => ['json_request', 'authorization_token', 'client_auth_token']], function() use($versionV1){
        
  });

});


