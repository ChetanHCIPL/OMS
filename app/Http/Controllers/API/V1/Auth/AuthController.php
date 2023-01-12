<?php

namespace App\Http\Controllers\API\V1\Auth;
//ini_set('memory_limit', '-1');

use App\Http\Controllers\Controller;
use App\Models\SalesUser;
use App\Traits\General\ApiFunction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\Auth\Auth;
use PhpParser\Node\Stmt\Class_;
use Validator;
use Hash;
use Exception;
use Mail;
use DB;
use Config;
use App\Models\State;  
use App\Models\Settings;
use App\Events\SendOtpNotification;
use App\Events\ResendOtpNotification;
use App\Models\UserDeviceToken;
use App\Models\UserLoginLog;
use App\Models\Country;
use App\Models\Districts;
use App\Models\Designation;
use App\Models\SalesStructure;

class AuthController extends Controller {
    
    use ApiFunction, Auth;
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->NOIMG_PATH = Config::get('path.noimg_path');
        $this->USER_PATH = Config::get('path.user_path');
        $this->aws_bucket_array = config('constants.aws_bucket_array');
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->img_max_size = @(Config::get('constants.IMG_MAX_SIZE'));
        $this->size_array = Config::get('constants.user_image_size');
        $this->destinationPath = Config::get('path.user_path');
        //$this->allow_app_recording_mobiles = Config::get('constants.allow_app_recording_mobiles');
    }
    /**
     * Function: Login API
     *
     * @param    string  $request
     * @return   json    
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
                /*$registartion_token = $request->input('registration_token');
                if(!empty($registartion_token))
                {
                    $registartion_token_decode  = base64_decode($request->input('registration_token'));
                    $registartion_token_explode = explode('-', $registartion_token_decode);

                    $user_id = (isset($registartion_token_explode[0]) && !empty($registartion_token_explode[0]) ? $registartion_token_explode[0] : "");
                    
                    $expire_time_limit = (isset($registartion_token_explode[1]) && !empty($registartion_token_explode[1]) ? $registartion_token_explode[1] : "");
                    
                    $current_system_time    = date_getUnixSystemTime();
              
                    if($expire_time_limit <= $current_system_time){
                        ## Invalid credentials
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message[] =  "your registration token has expired";
                        ## Return Response
                        return $this->prepareResult($message, $data);
                    }
                    $where_check_arr['id'] = trim($user_id);
                }
                else{
                    $where_check_arr['mobile']=trim($request->input('email'));
                    $where_check_arr['email']=trim($request->input('email'));
                }*/ 
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
                $check = SalesUser::getSalesUserDetailsforAppLogin($where_check_arr);

                if(!empty($check)){

                    ## Details of requested member
                    $sales_user_id        = 0;
                    if($check[0]['user_type'] == SalesUser::USER_TYPE_SALES_USER){
                        $sales_user_id        = $check[0]['id'];
                    }if($check[0]['user_type'] == SalesUser::USER_TYPE_DEALER){
                        $sales_user_id        = $check[0]['id']; //Change when we will provide login for dealer
                    }
                    
                    $user_id        = $check[0]['id'];
                    $first_name     = $check[0]['first_name'];
                    $last_name      = $check[0]['last_name'];
                    $mobile_number  = $check[0]['mobile'];
                    $email          = $check[0]['email'];
                    $password       = $check[0]['password'];
                    $status         = $check[0]['status'];                    

                    if(!empty($request->input('password')) && !Hash::check(trim($request->input('password')), $password)){
                            ## Invalid credentials
                            $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                            $message[] =  __('message.msg_credentials_not_match');
                            ## Return Response
                            return $this->prepareResult($message, $data);
                    }else {
                        
                        if($status == SalesUser::ACTIVE){
                            
                            // else if(($device_type == 1 || $device_type == 2) && !in_array($request_app_version, $this->allow_login_android_app_ver_arr)){
                            //     ## Unregister Login device
                            //     $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                            //     // $message[] = __('message.msg_not_allow_multiple_device_login'); 
                            //     $message_str =__('message.msg_not_allow_multiple_device_login'); 

                            //     $message[] = sprintf($message_str, $request_app_version,  $this->android_current_app);
                            //     $data["is_upgrade"]         = 1;
                            //     $data["can_access_app"]     = 0;

                            //     ## Return Response
                            //     return $this->prepareResult($message, $data);
                            // }
                            //else{
                                // ## Generate Token to Authenticate member
                                $token = app_generate_login_device_token(50);

                                ## Insert Generated Login Token
                                $insert_token_array = array(
                                    //'entity_type_id'    => $request->input('entity_type_id'),
                                    'user_id'           => $user_id,
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
                                $insert_token = UserDeviceToken::addUserDeviceToken($insert_token_array);


                                if(!empty($insert_token) && isset($insert_token['id']) && $insert_token['id'] > 0){
                                    $insert_history_array = array( 
                                        'user_id'           => $user_id,
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


                                    $insert_history = UserLoginLog::addUserLoginLog($insert_history_array);
                                    
                                    if(!empty($insert_history) && isset($insert_history['id']) && $insert_history['id'] > 0){
                                        
                                        $user_data = SalesUser::getUserDataFromIdForAppLogin($user_id);
                                        $user_data[0]['login_log_id'] =  $insert_history['id'];
                                        if(!empty($user_data)){

                                            ##Defined empty variables
                                            $user_data[0]['district_name'] = $user_data[0]['state_name'] =  $user_data[0]['country_name'] = $user_data[0]['designation_name'] =  $user_data[0]['sales_strucutre_name'] = $user_data[0]['sales_strucutre_short_name'] ='';

                                            $user_data[0]['full_name'] = ucwords($user_data[0]['first_name'].' '.$user_data[0]['last_name']);
                                             
                                            if(empty($user_data[0]['image'])){
                                                $img_arr = getImageUrl('male_user.jpg', $this->NOIMG_PATH, $size = '','','','',$this->aws_bucket_array);    
                                                $user_data[0]['image'] = $img_arr['small_image_url'];
                                            }else{
                                                $img_arr = getImageUrl($user_data[0]['image'], $this->USER_PATH, $size = '2',$sizecnt = '', '','_','80x80.jpg');
                                                $user_data[0]['image'] = $img_arr['small_image_url'];
                                            }

                                            if($user_data[0]['country_id'] > 0){
                                                $country = Country::getCountryDataFromId($user_data[0]['country_id']);
                                                $user_data[0]['country_name'] = $country[0]['country_name'] ? $country[0]['country_name'] : '';
                                            }

                                            if ($user_data[0]['state_id'] > 0){
                                                $state = State::getStateDataFromId($user_data[0]['state_id']);
                                                $user_data[0]['state_name'] = $state[0]['state_name'] ? $state[0]['state_name'] : '';
                                            }

                                            if ($user_data[0]['district_id'] > 0){
                                                $district = Districts::getDistrictsDataFromId($user_data[0]['district_id']);
                                                    $user_data[0]['district_name'] = $district[0]['district_name'] ? $district[0]['district_name'] : '';
                                            }

                                            if ($user_data[0]['designation_id'] > 0){
                                                $designation = Designation::getDesignationDataFromId($user_data[0]['designation_id']);
                                                $user_data[0]['designation_name'] = $designation[0]['name'] ? $designation[0]['name'] : '';
                                            }

                                            if ($user_data[0]['sales_structure_id'] > 0){
                                                $sales_structure = SalesStructure::getSalesStructureDataFromId($user_data[0]['sales_structure_id']);
                                                $user_data[0]['sales_strucutre_short_name'] = $sales_structure[0]['short_name'] ? $sales_structure[0]['short_name'] : '';
                                                $user_data[0]['sales_strucutre_name'] = $sales_structure[0]['full_name'] ? $sales_structure[0]['full_name'] : '';
                                            }

                                            $user_type = Config::get('constants.user_type.'.$check[0]['user_type']);
                                            $user_data[0]['user_type_title'] = $user_type ? $user_type : '';
                                            $user_data[0]['sales_user_id'] = $sales_user_id;
                                            $user_data[0]['id'] = $user_data[0]['id'];
                                            $user_data[0]['logged_user'] = Config::get('constants.logged_user_sales_user');
                                            
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
                                            $data['user_data'] = isset($user_data[0])?$user_data[0]:array();
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
                            //}
                        }else if($status == SalesUser::INACTIVE){
                            ## Inactive profile
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message[] = __('message.msg_account_inactivated');
                        }else if($status == SalesUser::BLOCKED){
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
                    $message[] = __('message.msg_credentials_not_match');
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

    /**
     * Summary of logout
     * Function: Lgout API
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function logout(Request $request){ 
        
         ## Variable Declaration
        $data = array(); 
        try{
            ## Validate the request
            $validator = $this->validateLogoutRequest($request->all());
            if ($validator->fails()) { 
                 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = $validator->errors()->all();            
            }else{
                ## Check customer exist or not with requested email
                //$check = Customer::getCustomerDetails(['id' => trim($request->input('buyer_id'))])->get()->toArray();
                if($request->has('user_id')){ //Email Or Mobile
                    $where_check_arr['id']=trim($request->input('user_id'));
                }

                $check = SalesUser::getUser($where_check_arr);
                
                if(!empty($check)){ 
                    
                    ## check login log is exits
                    $logData = UserLoginLog::getUserLoginData(['id' => trim($request->input('login_log_id'))])->get()->toArray();
                    
                    if(empty($logData))
                    {
                        ## Invalid Login Log id
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message[] = __('message.msg_invalid_field', ['attribute' => strtolower(__('label.lbl_login_id'))]);
                    }else{
                        ## UPDATE logout time 
                        $updateArry = array('logout_date'=>date_getSystemDateTime());
                        UserLoginLog::updateUserLoginLog(trim($request->input('login_log_id')),$updateArry);
                                    
                        ## Success
                        $this->setStatusCode(Response::HTTP_OK);
                        $message[] = __('message.msg_success');
                    }
                }else{
                    ## Email not exist
                    $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $message[] = __('message.msg_field_not_exist', ['attribute' => strtolower(__('label.lbl_user'))]);            
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

    /**
     * Summary of getMemberProfileDetails
     * get Sales User Profile Data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getUserProfileDetails(Request $request){

        $data = array();
        try{
            $post = $request->all();

            $user_id      = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            if($user_id > 0){
                $data = $gender_array  = array();
                
                ## Get member Details
                $user_profile_data = SalesUser::getUserDataFromId($user_id);
                if(!empty($user_profile_data[0])){

                    // Get Designation Name from ID
                    $designation = Designation::getDesignationDataFromId($user_profile_data[0]['designation_id']);

                    // Get Sales Structure Name & Short Name By ID
                    $sales_structure = SalesStructure::getSalesStructureDataFromId($user_profile_data[0]['sales_structure_id']);

                    // Concate First & Last Name
                    $full_name = $user_profile_data[0]['first_name'] . ' ' . $user_profile_data[0]['last_name'];

                    // Get Image and Prepare Path For Image
                    $checkImgArr = '';
                    if($user_profile_data[0]['image'] != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$user_profile_data[0]['image'],count($this->size_array));
                    }

                    // Prepare Data For Response
                    $data['profile_data'] = array(
                        'id' => $user_id,
                        'first_name' => $user_profile_data[0]['first_name'] ? $user_profile_data[0]['first_name'] : '',
                        'last_name' => $user_profile_data[0]['last_name'] ? $user_profile_data[0]['last_name'] : '',
                        'email' => $user_profile_data[0]['email'] ? $user_profile_data[0]['email'] : '',
                        'profile_image' => $user_profile_data[0]['image'] ? $checkImgArr['img_url'] : '',
                        'dob' => $user_profile_data[0]['dob'] ? $user_profile_data[0]['dob'] : '',
                        'mobile' => $user_profile_data[0]['mobile'] ? $user_profile_data[0]['mobile'] : '',
                        'whatsapp_number' => $user_profile_data[0]['whatsapp_number'] ? $user_profile_data[0]['whatsapp_number'] : '',
                        'sales_structure_id' => $user_profile_data[0]['sales_structure_id'] ? $user_profile_data[0]['sales_structure_id'] : '',
                        'sales_structure_name' => $sales_structure[0]['full_name'] ? $sales_structure[0]['full_name'] : '',
                        'sales_strucutre_short_name' => $sales_structure[0]['short_name'] ? $sales_structure[0]['short_name'] : '',
                        'designation_id' => $user_profile_data[0]['designation_id'] ? $user_profile_data[0]['designation_id'] : '',
                        'designation_name' => $designation[0]['name'] ? $designation[0]['name'] : '',
                        'aadhar_no' => $user_profile_data[0]['adhar_no'] ? $user_profile_data[0]['adhar_no'] : '',
                        'full_name' => $full_name ? $full_name : '',
                    );

                }else{
                    ## Success
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_user_details_not_found');
                }

                ## Success
                $this->setStatusCode(Response::HTTP_OK);
                $message = __('label.lbl_sucess');
            }else{
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_user_id_missing');
            }
            
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }

        if(empty($data)){
            $data = (Object)$data;
        }
        
        ## Return Response
        return $this->prepareResult($message, $data);
    }

    public function updateUserProfileDetails(Request $request){
        try{
            
            $is_error = 0;
            $post = $request->all();
            $message = $data = array();

            $user_id            = (isset($request->user_id) && $request->user_id > 0  ? $request->user_id : 0);
            $first_name         = (isset($request->first_name) && $request->first_name != "" ? $request->first_name : '');
            $last_name          = (isset($request->last_name) && $request->last_name != "" ? $request->last_name : '');
            $email              = (isset($request->email) && $request->email != "" ? $request->email : '');
            $dob                = (isset($request->dob) && $request->dob != "" ? date('Y-m-d',strtotime($request->dob)) : '');
            $mobile             = (isset($request->mobile) && $request->mobile != "" ? $request->mobile : '');
            $whatsapp_number    = (isset($request->whatsapp_number) && $request->whatsapp_number != "" ? $request->whatsapp_number : '');
            $designation_id     = (isset($request->designation_id) && $request->designation_id > 0  ? $request->designation_id : 0);
            $device_type        = (isset($request->device_type) && $request->device_type > 0  ? $request->device_type : '');
            $sales_structure_id = (isset($request->sales_structure_id) && $request->sales_structure_id > 0  ? $request->sales_structure_id : 0);
            $adhar_no           = (isset($request->aadhar_no) && $request->aadhar_no > 0) ? $request->aadhar_no : 0;

            $old_image = '';
            if($user_id > 0){
                $userData = SalesUser::getUserDataFromId($user_id);

                $old_image = isset($userData[0]['image'])?$userData[0]['image']:"";
            }
            
            if($request->has('image') && $request->filled('image')){
                $base64String = $request->input('image');
                $image      = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$base64String));
                $fileObj    = getimagesize($base64String);
                $fileExt    = explode("/", $fileObj['mime'])[1];
                $fileSize   = (int) (strlen(rtrim($base64String, '=')) * 3 / 4);
                
                if (!in_array($fileExt, $this->img_ext_array)){
                    ## Invalid image extension
                    $is_error = 1;
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message[] = config('messages.msg.MSG_IMG_INVALID_EXTENSION');
                } else {
                    if ($fileSize > $this->img_max_size || $fileSize == 0){
                        $is_error = 1;
                        ## Invalid image size
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message[] = config('messages.msg.MSG_IMG_INVALID_SIZE');
                    }else {
                        
                        ## Store User Image in aws s3 server with the height and width locked in the right proportions
                        if($request->has('photo_name') && $request->filled('photo_name')){
                            $photo = time().'_'.gen_remove_spacial_char($request->input('photo_name'));
                            $photo = isset($photo)?$photo:'';
                        }else{
                            $photo = time()."_".'.'.$fileExt;
                            $photo = isset($photo)?$photo:'';
                        }
                         
                        ## Store image in folder in multiple size according to select value for SITE IMAGES STORAGE
                        if($image != '')
                            ## Store image to local storage
                            storeImageinFolder($image, $this->destinationPath, $photo, $this->size_array);
                        }
                        ## Remove Existing Image (If Any)
                        if($old_image != ""){
                            ## Remove Existing Image from local storage
                            deleteImageFromFolder($old_image, $this->destinationPath, $this->size_array);
                        }
                    }
            }else{
                $photo = $old_image;
            }
            // echo 'd';exit;
            $validation = $this->validateEdit($post); 

            if ($validation->fails()) {
                $validate_msg = setValidationErrorMessageForPopup($validation); 
                if(!empty($validate_msg)){
                   $validate_msg_arr = explode('</br>', $validate_msg);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = $validate_msg_arr;
                    } 
                }
            }else if($is_error == 0){
                //echo '<pre>'; print_r($user_id); echo '</pre>'; exit();
                if($user_id > 0) {
                    $update_array = array(
                        'first_name' => $first_name,
                        'last_name' =>  $last_name,
                        'email' => $email,
                        'dob' => $dob,
                        'mobile' => $mobile,
                        'image' => $photo,
                        'whatsapp_number' => $whatsapp_number,
                        'designation_id' => $designation_id,
                        'sales_structure_id' => $sales_structure_id,
                        'adhar_no' => $adhar_no,
                        'from_ip' => getIP(),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    $update_db = SalesUser::updateSalesUser($user_id, $update_array);
                    $this->setStatusCode(Response::HTTP_OK);
                    $message[] = __('message.msg_profile_edited_successfully');
                }else{
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message[] = __('message.msg_user_id_missing');
                }
            }
                         
        }catch(\Exception $e){
            // Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message[] = $e->getMessage();
        }        
        return $this->prepareResult($message, $data);
    }

    /**
     * Summary of validateEdit
     * Validate User Profile Edit
     * 
     * @param mixed $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateEdit($data = array()) {
        $rules = array(
                'user_id' => 'required',
                'first_name' => 'required|min:3|max:50',
                'last_name' => 'required|min:3|max:50',
                'dob' => 'required',
                'email' => 'required|email|unique:admin,email,'.$data['user_id'].',id',
                'mobile' => 'required|min:10|max:10|unique:admin,mobile,'.$data['user_id'].',id|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
                'whatsapp_number' => 'required|min:10|max:10|unique:admin,whatsapp_number,'.$data['user_id'].',id|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
                'designation_id' => 'required',
                'sales_structure_id' => 'required',
                'aadhar_no' => 'required',
            );

        $messages = array(
            'user_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'User id'),
            'first_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'First Name'),
            'first_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'First Name'),
            'first_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'First Name'),
            'last_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Last Name'),
            'last_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Last Name'),
            'last_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Last Name'),
            'email.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'email.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'email.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email'),
            'email.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Email'),
            'mobile.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Mobile Number'),
            'mobile.max' => sprintf(Config::get('messages.validation_msg.maxmobilelength'), '10', 'Mobile Number'),
            'mobile.min' => sprintf(Config::get('messages.validation_msg.minmobilelength'), '10', 'Mobile Number'),
            'mobile.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Mobile Number'),
            'mobile.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Mobile number'),
            'whatsapp_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'WhatsApp Number'),
            'whatsapp_number.max' => sprintf(Config::get('messages.validation_msg.maxmobilelength'), '10', 'WhatsApp Number'),
            'whatsapp_number.min' => sprintf(Config::get('messages.validation_msg.minmobilelength'), '10', 'WhatsApp Number'),
            'whatsapp_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'WhatsApp Number'),
            'whatsapp_number.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'WhatsApp Number'),
            'dob.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Date of Birth'),
            'designation_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Designation'),
            'sales_structure_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Sales Structure'),
            'aadhar_no.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Aadhar Number'),
            
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    * Function: Generate OTP for Forgot Password
    *
    * @param    array $request
    * @return   json    
    */
    public function generateOTPForForgotPassword(Request $request){
        try{
            ## Variable Declaration
            $data = array();
            $created_at = date_getSystemDateTime();
            $username = $request->username;
            
            $message = '';
            if(isset($username) || $username != "" || $username != NULL){
                // Get User
                $user = SalesUser::getUserByUsername($username);
                
                // Check If User is Exist Then Proceed Further
                if(!empty($user)){
                    $user_id = $user[0]['id'];
                    $mobile = $user[0]['mobile'];

                    if(isset($mobile) && !empty($mobile) && strlen($mobile) == 10 ){
                        // Generate Valid OTP
                        $otp = gen_generateRandomDigits(4);

                        // generate Expired Time Period For OTP
                        $expiration_duration = config('constants.OTP_EXPIRATION_TIME');
                        $expired_date = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                        $data['section_type'] = array_search('Sales', config('constants.sms_template_section'));
                        $data['type']         = 'ForgotPasswordOTP';
                        $data['otp']          = $otp;

                        // Get SMS Template For Forfot Password OTP
                        $message_str = event(new SendOtpNotification($data));
                        if(isset($message_str[0]['meassge']) && !empty($message_str[0]['meassge']))
                        {
                            $msg_reponse =  sendSMS($message_str[0]['meassge'],$mobile);
                            $msg = $message_str[0]['meassge']; 
                        } 
                       
                        if(isset($msg_reponse['isError']) && $msg_reponse['isError'] == 0){

                            // Prepare OTP Related Data To updated In User Data
                            $update_array = array(
                                'otp'           => $otp,
                                'expired_date'  => $expired_date,
                            );
                            $user_data = SalesUser::updateSalesUser($user_id, $update_array);
                            
                            // If All Updation Done Then prepare for Response
                            if(isset($user_data)){
                                ## Success
                                $this->setStatusCode(Response::HTTP_OK);
                                $message = __('message.msg_otp_sent_successfully');
                                $data['otp'] = $otp; 
                                $data['email'] = $username; 
                                $data['expired_date']  = $expired_date; 
                                $data['expire_timer']  = ($expiration_duration*60);
                                $data['msg_reponse']   = $msg;     
                            }
                        }else{
                            ## Error in editing profile
                            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                            $message = __('message.msg_otp_send_failed'); 
                        } 

                        //$ret = sendSMS();
                    }else{
                        ## Error in editing profile
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = __('message.msg_otp_send_failed'); 
                    } 
                }else{
                    ## Email not exist
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    ##Need to change message
                    $message = __('message.msg_record_not_found');
                } 
            } else{
                ## Email not exist
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                ##Need to change message
                $message = __('message.msg_require_email_phone_username');
            } 
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        return $this->prepareResult($message, $data);
    }

      /**
     * Function: Client forgot password resend api (individual)
     *
     * @param    array  $request
     * @return   json    
     */
    public function resendForgotPasswordOtp(Request $request){

        try{

            $data = array();
            $created_at = date_getSystemDateTime();
            $message = '';
            ## Validate the request
        
                ## Request parameters
                $post = $request->all(); 
                // Get username
                $username = $post['username'];

                $check = array();
                if(isset($username) || $username != "" || $username != NULL){
                    ## Check If User Is Exist Or not  
                    if($username != ''){
                        $check = SalesUser::getUserByUsername($username);                        
                    }
                    
                    // If Exist Then Proceed Further
                    if(!empty($check)){

                        // Generate OTP
                        $otp = gen_generateRandomDigits(4);

                        ## Generate OTP Time Period For Expire
                        $expiration_duration = config('constants.OTP_EXPIRATION_TIME');
                        $expired_date = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);

                        
                        $id = isset($check[0]['id'])?$check[0]['id']:0;

                        ## Prepate Data To Update User With OTP & expire date
                        $update_array = array(
                            'otp'     => $otp,
                            'expired_date'     => $expired_date
                        );
                            
                        // Update User Data with OTP & Expire Date
                        $user_upd_data = SalesUser::updateSalesUser($id, $update_array);
                        if($user_upd_data != ''){
                            $data['otp'] = $otp;
                            $data['username'] = $username;
                            $data['expire_timer']  = ($expiration_duration*60);
                            //send data on sms template
                            $template_data['section_type'] = array_search('Sales', config('constants.sms_template_section'));
                            $template_data['type']         = 'ResendForgotPasswordOtp';
                            $template_data['otp']          = $otp; 
                            $message_str = event(new ResendOtpNotification($template_data));
                            $sms_message = isset($message_str[0]['meassge'])?$message_str[0]['meassge']:'';

                            // TO DO Remaining Send OTP Functionality
                            $res = "OK";
                            if($res == "OK"){
                                //$this->setStatusCode(Response::HTTP_OK);
                                $message = __('message.msg_otp_resend'); 
                                
                            }else{
                                //$this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                $message = __('message.msg_otp_send_failed'); 
                                
                                }
                            $data['send_message'] = $sms_message; 
                            $this->setStatusCode(Response::HTTP_OK);
                        }

                    }else{
                        ## Invalid OTP      
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message = __('message.msg_record_not_found'); 
                    }
                }else{
                    ## Email not exist
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    ##Need to change message
                    $message = __('message.msg_require_email_phone'); 
                }  

        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        ## Return Response
        return $this->prepareResult($message, $data);
    }   
    
    /**
     * Function: Verify forgot password otp
     *
     * @param    array  $request
     * @return   json    
     */
    public function verifyForgotPasswordOtp(Request $request){ 
        $message = '';
        try{
            $data = array();
            $created_at = date_getSystemDateTime();
            ## Get Request parameters
            $post = $request->all(); 

            $username = $request->username;
            if(isset($username) || $username != "" || $username != NULL){
                // Get OTP
                $OTP = $post['otp'];

                // Get user By username
                $user = SalesUser::getUserByUsername($username);
                
                // Check If User is Exist
                if(!empty($user)){
                    // Validate OTP, If its valid or not Expire.
                    $otp_expired_at = isset($user[0]['expired_date'])?strtotime($user[0]['expired_date']):"";

                    $otp_db = isset($user[0]['otp'])?$user[0]['otp']:"";
                    $member_id = isset($user[0]['id'])?$user[0]['id']:"";
                    $current_time = strtotime($created_at);
                    $id = isset($user[0]['id'])?$user[0]['id']:0;
                    $status = isset($user[0]['status'])?$user[0]['status']:0;
                    if($otp_db != $OTP){
                        ## Invalid OTP 
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = __('message.msg_otp_not_match'); 
                    }
                    else if($current_time > $otp_expired_at){
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = __('message.msg_otp_expired'); 
                    }else{
                        if($status == SalesUser::ACTIVE){

                            ## Generate Token to Authenticate user
                            $token = app_generate_login_device_token(50);                           

                            ## Delete Previous Login Token for this user
                            $delete = UserDeviceToken::deleteUserDeviceToken($id);

                            ## Insert Generated Login Token
                            $insert_token_array = array(
                                'user_id'           => $id,
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

                            $insert_token = UserDeviceToken::addUserDeviceToken($insert_token_array);
                             
                            if(!empty($insert_token) && isset($insert_token['id']) && $insert_token['id'] > 0){

                                ## Insert User Login History
                                $insert_history_array = array(
                                    'user_id'           => $id,
                                    'ip'                => getIP(),
                                    'device_type'       => $request->input('device_type'),
                                    'device_uuid'       => $request->input('device_uuid'),
                                    'device_no'         => $request->input('device_no'),
                                    'device_name'       => $request->input('device_name'),
                                    'device_platform'   => $request->input('device_platform'),
                                    'token'             => $token,
                                    'device_model'      => $request->input('device_model'),
                                    'device_version'    => $request->input('device_version'),
                                    'app_version'       => $request->input('app_version'),
                                    'login_date'        => $created_at,
                                    'created_at'        => $created_at,
                                    'expire_at'         => date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=config('api_constants.USER_AUTH_TOKEN_EXPIRATION_HOURS'),$ia=0,$sa=0)
                                );
                                $insert_history = UserLoginLog::addUserLoginLog($insert_history_array);
                                
                                ## Success
                                //$message = "Success";
                                $image = '';
                                $sales_structure_name = '';
                                $sales_strucutre_short_name = '';
                                $country_name = '';
                                $state_name = '';
                                $designation_name = '';
                                $district_name = '';
                                $user_type_title = "";
                                if(empty($user[0]['image'])){
                                    $img_arr = getImageUrl('male_user.jpg', $this->NOIMG_PATH, $size = '','','','',$this->aws_bucket_array);    
                                    $image = $img_arr['small_image_url'];
                                }else{
                                    $img_arr = getImageUrl($user[0]['image'], $this->USER_PATH, $size = '2',$sizecnt = '', '','_','80x80.jpg');
                                    $image = $img_arr['small_image_url'];
                                }
                                // Prepare Sales Structure Name
                                if ($user[0]['sales_structure_id'] > 0){
                                    $sales_structure = SalesStructure::getSalesStructureDataFromId($user[0]['sales_structure_id']);
                                    $sales_structure_name = $sales_structure[0]['full_name'] ? $sales_structure[0]['full_name'] : '';
                                    $sales_strucutre_short_name = $sales_structure[0]['short_name'] ? $sales_structure[0]['short_name'] : '';
                                }
                                
                                if($user[0]['country_id'] > 0){
                                    $country = Country::getCountryDataFromId($user[0]['country_id']);
                                    $country_name = $country[0]['country_name'] ? $country[0]['country_name'] : '';
                                }
                                
                                if ($user[0]['state_id'] > 0){
                                    $state = State::getStateDataFromId($user[0]['state_id']);
                                    $state_name = $state[0]['state_name'] ? $state[0]['state_name'] : '';
                                }

                                if ($user[0]['district_id'] > 0){
                                    $district = Districts::getDistrictsDataFromId($user[0]['district_id']);
                                    $district_name = $district[0]['district_name'] ? $district[0]['district_name'] : '';
                                }

                                if ($user[0]['designation_id'] > 0){
                                    $designation = Designation::getDesignationDataFromId($user[0]['designation_id']);
                                    $designation_name = $designation[0]['name'] ? $designation[0]['name'] : '';
                                }

                                $user_type = Config::get('constants.user_type.'.$user[0]['user_type']);
                                $user_type_title = $user_type ? $user_type : '';

                                ## Response Array
                                $data["is_upgrade"] = 0;
                                $data["can_access_app"] = 1;
                                $data["token"] = $token;
                                
                                $data['user_data'] = array(
                                    'id'        => $id,
                                    'user_type' => $user[0]['user_type'] ,
                                    'access_group_id' => $user[0]['access_group_id'],
                                    'first_name' => $user[0]['first_name'],
                                    'last_name' => $user[0]['last_name'],
                                    'email' => $user[0]['email'],
                                    'mobile' => $user[0]['mobile'],
                                    'sales_structure_id' => $user[0]['sales_structure_id'],
                                    'image' => $image,
                                    'country_id'    => $user[0]['country_id'],
                                    'state_id'      => $user[0]['state_id'],
                                    'tot_login'     => $user[0]['tot_login'],
                                    'last_access'     => $user[0]['last_access'],
                                    'status'     => $user[0]['status'],
                                    'district_id'     => $user[0]['district_id'],
                                    'designation_id'     => $user[0]['designation_id'],
                                    'login_log_id'     => $insert_history['id'],
                                    'sales_strucutre_short_name'     => $sales_strucutre_short_name,
                                    'sales_strucutre_name'     => $sales_structure_name,
                                    'designation_name'     => $designation_name,
                                    'country_name'     => $country_name,
                                    'state_name'     => $state_name,
                                    'district_name'     => $district_name,
                                    'full_name'     => $user[0]['first_name'] . " " . $user[0]['first_name'],
                                    'user_type_title'     => $user_type_title,
                                    'sales_user_id'     => $id,
                                );

                                $this->setStatusCode(Response::HTTP_OK);
                                $message = __('message.msg_otp_verified'); 
                                
                            }else{
                                ## Something went wrong with your request
                                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                $message = __('message.msg_something_went_wrong');
                            }    
                        }else if($status == SalesUser::INACTIVE){
                            ## Inactive account
                            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                            $message = __('message.msg_account_inactivated');
                        }
                        
                    }
                }else{
                    ## Email not exist
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    ##Need to change message
                    $message = __('message.msg_record_not_found');
                } 
            } else{
                ## Email not exist
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                ##Need to change message
                $message = __('message.msg_require_email_phone');
            }  

        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        ## Return Response
        return $this->prepareResult($message, $data);
    }

    /**
     * Function: User forgot reset password 
     *
     * @param    array  $request
     * @return   json    
     */
    public function passwordReset(Request $request){

        // Get Post Request Data
        $post = $request->all(); 

        ## Variable Declaration
        $data = array();
        $created_at = date_getSystemDateTime(); 
        try{
            ## Validate the request
            $validator = $this->validateChangePasswordRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = $validator->errors()->all();            
            }else{
                // Get User data By Id
                $user_id = 0;
                if($request->has('user_id')){ // User id 
                    $user_id =trim($request->input('user_id'));
                }
                if($user_id > 0){

                    $user_obj = SalesUser::where('id', '=', $user_id)->first();
                    // Prepare Valid array To Update Password
                    if(!empty($user_obj)){
                        
                        $update_array = array(
                            'password' => bcrypt($request->input('confirm_password')),
                            'otp'           => '',
                            'expired_date'  => NULL,
                            'updated_at'  => $created_at
                        );

                        // Update With new Password
                        $update = SalesUser::updateSalesUser($user_id, $update_array);
                        if(isset($update)){
                            ## Success
                            $this->setStatusCode(Response::HTTP_OK);
                            $message = __('message.msg_reset_password');
                         }else{
                            ## Error in editing profile
                            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                            $message = __('message.msg_wrong_request');
                        }
                    }
                    else{
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message = __('message.msg_record_not_found'); 
                    }
                }else{
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message = __('message.msg_request_parameter_missing.'); 
                }
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        ## Return Response
        return $this->prepareResult($message, $data = []);
    }
}
