<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\Grade;
use App\Models\PaymentTerms;
use App\Models\SalesUsers;
use App\Models\Taluka;
use App\Traits\General\ApiFunction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\Auth\ClientAuth;
use Validator;
use Hash;
use Exception;
use Config;
use App\Models\State;  
use App\Models\Zone;  
use App\Events\SendOtpNotification;
use App\Events\ResendOtpNotification;
use App\Models\Admin;
use App\Models\ClientDeviceToken;
use App\Models\ClientLoginLog;
use App\Models\Country;
use App\Models\Districts;
use App\Models\Designation;
use App\Models\SalesStructure;

class ClientAuthController extends Controller {
    
    use ApiFunction, ClientAuth;
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->NOIMG_PATH = Config::get('path.noimg_path');
        $this->CLIENT_PATH = Config::get('path.client_path');
        $this->aws_bucket_array = config('constants.aws_bucket_array');
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->img_max_size = @(Config::get('constants.IMG_MAX_SIZE'));
        $this->size_array = Config::get('constants.user_image_size');
        $this->destinationPath = Config::get('path.client_path');
        //$this->allow_app_recording_mobiles = Config::get('constants.allow_app_recording_mobiles');
    }
    
    /**
     * Summary of Login
     * Client Login 
     * Request URL : client-login
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function login(Request $request){ 
        ## Variable Declaration
        $data = array();
        $created_at = date_getSystemDateTime();
        $is_profile_updated = 1;
        $data["is_upgrade"]     = 0;
        $data["can_access_app"] = 1; 
        $data["allow_screenshot"]     = 1;

        try{
            ## Validate the request
            $validator = $this->validateLoginRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = $validator->errors()->all();            
            }else{

                $where_check_arr = array();
                
                $where_check_arr['username']=trim($request->input('username'));
                $request_app_version = $request->input('app_version');
                $device_type = $request->input('device_type');

				if($device_type==1){
					$this->android_current_app = config('api_constants.ANDROID_CURRENT_APP');
					$this->allow_login_android_app_ver_arr = config('api_constants.ALLOW_LOGIN_ANDROID_APP_VER_ARR');
				}else if($device_type==2){
					$this->android_current_app = config('api_constants.IOS_CURRENT_APP');
					$this->allow_login_android_app_ver_arr = config('api_constants.ALLOW_LOGIN_IOS_APP_VER_ARR');
				}

                $check = Clients::getClientDetailsforAppLogin($where_check_arr);

                if(!empty($check)){

                    $client_id       = $check[0]['id'];
                    $client_name     = $check[0]['client_name'];
                    $mobile_number   = $check[0]['mobile_number'];
                    $whatsapp_number = $check[0]['whatsapp_number'];
                    $password        = $check[0]['password'];
                    $status          = $check[0]['status'];                    

                    if(!empty($request->input('password')) && !Hash::check(trim($request->input('password')), $password)){
                            ## Invalid credentials
                            $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                            $message[] =  __('message.msg_credentials_not_match');
                            ## Return Response
                            return $this->prepareResult($message, $data);
                    }else {
                        if($status == Clients::ACTIVE || $status == Clients::VERIFIED){
                            
                            // ## Generate Token to Authenticate member
                            $token = app_generate_login_device_token(50);
                            
                            ## Insert Generated Login Token
                            $insert_token_array = array(
                                'client_id'         => $client_id,
                                'device_type'       => $request->input('device_type'),
                                'device_uuid'       => $request->input('device_uuid'),
                                'device_no'         => $request->input('device_no'),
                                'device_name'       => $request->input('device_name'),
                                'device_platform'   => $request->input('device_platform'),
                                'device_model'      => $request->input('device_model'),
                                'device_version'    => $request->input('device_version'),
                                'app_version'       => $request->input('app_version'),
                                'token'             => $token,
                                'created_at'        => $created_at,
                                'expire_at'         => date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=config('api_constants.USER_AUTH_TOKEN_EXPIRATION_HOURS'),$ia=0,$sa=0)
                            );
                            $insert_token = ClientDeviceToken::addClientDeviceToken($insert_token_array);

                            if(!empty($insert_token) && isset($insert_token['id']) && $insert_token['id'] > 0){
                                $insert_history_array = array( 
                                    'client_id'           => $client_id,
                                    'ip'                => getIP(),
                                    'device_type'       => $request->input('device_type'),
                                    'device_uuid'       => $request->input('device_uuid'),
                                    'device_no'         => $request->input('device_no'),
                                    'device_name'       => $request->input('device_name'),
                                    'device_platform'   => $request->input('device_platform'),
                                    'device_model'      => $request->input('device_model'),
                                    'device_version'    => $request->input('device_version'),
                                    'app_version'       => $request->input('app_version'),
                                    'token'             => $token,
                                    'created_at'        => $created_at,
                                    'expire_at'         => date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=config('api_constants.USER_AUTH_TOKEN_EXPIRATION_HOURS'),$ia=0,$sa=0),
                                    'login_date'        => date_getSystemDateTime()
                                );


                                $insert_history = ClientLoginLog::addClientLoginLog($insert_history_array);
                                
                                if(!empty($insert_history) && isset($insert_history['id']) && $insert_history['id'] > 0){
                                    
                                    $client_data = Clients::getClientDataFromIdForAppLogin($client_id);
                                    
                                    $client_data[0]['login_log_id'] =  $insert_history['id'];
                                    if(!empty($client_data)){

                                        ##Defined empty variables
                                        $client_data[0]['district_name'] = $client_data[0]['state_name'] =  $client_data[0]['country_name'] = $client_data[0]['zone_name'] =  $client_data[0]['taluka_name'] = $client_data[0]['grade_name'] = $client_data[0]['payment_term_name'] = $client_data[0]['sales_user_name'] ='';

                                        $client_data[0]['full_name'] = ucwords($client_data[0]['client_name']);
                                            
                                        if(empty($client_data[0]['image'])){
                                            $img_arr = getImageUrl('male_user.jpg', $this->NOIMG_PATH, $size = '','','','',$this->aws_bucket_array);    
                                            $client_data[0]['image'] = $img_arr['small_image_url'];
                                        }else{
                                            $img_arr = getImageUrl($client_data[0]['image'], $this->CLIENT_PATH, $size = '2',$sizecnt = '', '','_','80x80.jpg');
                                            $client_data[0]['image'] = $img_arr['small_image_url'];
                                        }

                                        if($client_data[0]['country_id'] > 0){
                                            $country = Country::getCountryDataFromId($client_data[0]['country_id']);
                                            $client_data[0]['country_name'] = $country[0]['country_name'] ? $country[0]['country_name'] : '';
                                        }

                                        if ($client_data[0]['state_id'] > 0){
                                            $state = State::getStateDataFromId($client_data[0]['state_id']);
                                            $client_data[0]['state_name'] = $state[0]['state_name'] ? $state[0]['state_name'] : '';
                                        }

                                        if ($client_data[0]['district_id'] > 0){
                                            $district = Districts::getDistrictsDataFromId($client_data[0]['district_id']);
                                                $client_data[0]['district_name'] = $district[0]['district_name'] ? $district[0]['district_name'] : '';
                                        }

                                        if ($client_data[0]['zone_id'] > 0){
                                            $zone = Zone::getZoneDataFromId($client_data[0]['zone_id']);
                                            $client_data[0]['zone_name'] = $zone[0]['zone_name'] ? $zone[0]['zone_name'] : '';
                                        }

                                        if ($client_data[0]['taluka_id'] > 0){
                                            $taluka = Taluka::getTalukaDataFromId($client_data[0]['zone_id']);
                                            $client_data[0]['taluka_name'] = $taluka[0]['taluka_name'] ? $taluka[0]['taluka_name'] : '';
                                        }

                                        if ($client_data[0]['grade_id'] > 0){
                                            $grade = Grade::getGradeDataFromId($client_data[0]['grade_id']);
                                            $client_data[0]['grade_name'] = $grade[0]['name'] ? $grade[0]['name'] : '';
                                        }

                                        if ($client_data[0]['payment_term_id'] > 0){
                                            $term = PaymentTerms::getPaymentTermsDataFromId($client_data[0]['payment_term_id']);
                                            $client_data[0]['payment_term_name'] = $term[0]['term'] ? $term[0]['term'] : '';
                                        }
                                        
                                        if ($client_data[0]['sales_user_id'] > 0){
                                            $sales_user = Admin::getAdminNameFromId($client_data[0]['sales_user_id']);
                                            $client_data[0]['sales_user_name'] = $sales_user[0]['name'] ? $sales_user[0]['name'] : '';
                                        }

                                        // Set flag for User logged in Type
                                        $client_data[0]['logged_user'] = Config::get('constants.logged_user_client');

                                        ## Success
                                        $this->setStatusCode(Response::HTTP_OK);
                                        
                                        if(($device_type == 1 || $device_type == 2) && !in_array($request_app_version, $this->allow_login_android_app_ver_arr)){
                                            ## Unregister Login device
                                            $message_str = "Please upgrade this app. Your app current version is %s and latest version is %s. Please click on Upgrade button to upgrade app";
                                            $message[] = sprintf($message_str, $request_app_version, "3.1");
                                            $data["is_upgrade"] = 1;
                                            $data["can_access_app"] = 0;
                                        }else{
                                            $message[] = __('message.msg_success');
                                            
                                        }
                                        ## Response Array
                                        $data['token'] = $token; 
                                        $data['client_data'] = isset($client_data[0])?$client_data[0]:array();
                                    }else{
                                        ## Something went wrong with your request
                                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                        $message = __('message.msg_wrong_request');
                                    }
                                }else{
                                    ## Something went wrong with your request
                                    $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                                    $message[] = __('message.msg_wrong_request'); 
                                }
                            }else{
                                ## Something went wrong with your request
                                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                $message = __('message.msg_wrong_request');
                            }
                        }else if($status == Admin::INACTIVE){
                            ## Inactive profile
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message[] = __('message.msg_account_inactivated');
                        }else if($status == Admin::BLOCKED){
                            ## Blocked profile
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message[] = __('message.msg_profile_blocked');
                        }else{
                            ## Invalid status
                            $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                            $message[] = __('message.msg_invalid_status');
                        }
                    }
                }else{
                    $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $message[] = __('message.msg_email_not_exist');
                }
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message[] = $e->getMessage();
        }

        ## Return Response
        return $this->prepareResult($message, $data);
    } 
}
