<?php ## Created by Mayur as on 01 July 2022

namespace App\Http\Controllers\API\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\Auth\Auth;
use App\Traits\General\ApiFunction;
use App\Traits\Auth\Register;
use App\Models\Plan;
use App\Models\Plan_Course;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\Member;
use App\Models\MemberDeviceChangeLog;
use App\Models\SMSTemplate;
use App\Models\TempMemberRegistration;
use App\Models\EmailTemplate;
use Redirect;
use Mail;
use Config;
use Validator;
use Exception;

class ChangeDeviceController extends Controller
{

    use ApiFunction, Auth;

    public function __construct(){
        $this->member_type_array = Config::get('constants.member_type_array');        
        $this->free_registration_plan_id = Config::get('constants.FREE_REGISTRATION_PLAN_ID');        
        $this->free_registration_days = Config::get('constants.FREE_REGISTRATION_DAYS');  
        $this->admission_code_array = Config::get('constants.admission_type_array');        
    }

    /**
    * Function: Generate OTP
    *
    * @param    array $request
    * @return   json    
    */
    public function CheckMemberAccountAssociateSendOTP(Request $request){
        $message = '';
        try{
            ## Variable Declaration
            $data = array();
            $created_at = date_getSystemDateTime();
            $mobile_number = $request->mobile;
            $langCode = $request->lang_code;
            $where_arr['mobile_number'] = $mobile_number;
            $post = $request->all();
            $validator = $this->validateDeviceOTPRequest($post);
            if ($validator->fails()) { 
                ## Invalid request parameters
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $validator_message = $validator->errors()->all();
                    $message = $validator_message[0];           
            }else{
                    //Check device entry in changes device table log
                    $data['is_allow_change_device']   = 0;
                    $checkMemberDataExistOrNot = Member::getMemberDetails($where_arr)->select('id')->get()->toArray();
                     
                    if(count($checkMemberDataExistOrNot) > 0)
                    {
                        $member_id = $checkMemberDataExistOrNot[0]['id'];
                        //Get memeber device change attempt count from setting panel 
                        $allow_attempt_cnt = config('settings.MEMBER_DEVICE_CHANGE_ATTEMPT');
                        $TotalRequstCnt = MemberDeviceChangeLog::getCountOfDeviceChangeRequest($member_id);
                        if($TotalRequstCnt >= $allow_attempt_cnt ){ // Do not allow to change device 
                            $this->setStatusCode(Response::HTTP_OK);
                            $message = 'You cannot change your device. For further assistance please contact XXX-XXX-XXXX';
                        }
                        else
                        {
                            $otp = gen_generateRandomDigits(4);
                            $data['is_allow_change_device']   = 1;
                            $expiration_duration  = config('constants.OTP_EXPIRATION_TIME');
                            $expired_date         = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                            $section_type         = array_search('Member', config('constants.sms_template_section'));
                            $type                 = 'SendOtpForChangeDevice';
                            $lang_code            = isset($langCode) && !empty($langCode) ? $langCode : 'en';
                            
                            // Get SMS notification data for Send registration OTP
                            $sms_template = SMSTemplate::getSmsTemplateDataByLangCode($type, $section_type, $lang_code);
                            if(isset($sms_template[0]['content']) && $sms_template[0]['content'] != "" ){
                                
                                ## SMS Notification Data
                                $body_msg = $sms_template[0]['content'];

                                ## Replace Meassage data
                                $array_search_msg  = array("{#var#}");
                                $array_replace_msg = array($otp);
                                $sms_data_meassage = addslashes(str_replace($array_search_msg, $array_replace_msg, $body_msg));
                                $msg_reponse =  sendSMS($sms_data_meassage,$mobile_number);

                                if(isset($msg_reponse['isError']) && $msg_reponse['isError'] == 0){
                                        $id = $checkMemberDataExistOrNot[0]['id'];
                                        $update_array = array(
                                                                'otp'           => $otp,
                                                                'expired_date'  => $expired_date,
                                                             ); 
                                        $update = Member::updateMember($id,$update_array);
                                        if(isset($update))
                                        {
                                            ## Success
                                            $this->setStatusCode(Response::HTTP_OK);
                                            $message = __('message.msg_otp_sent_successfully');
                                            $data['otp'] = $otp; 
                                            $data['mobile'] = $mobile_number; 
                                            $data['expired_date']  = $expired_date; 
                                            $data['expire_timer']  = ($expiration_duration*60);
                                            $data['msg_reponse']   = $sms_data_meassage;
                                        }                              
                                }else{
                                        ## Error in send OTP
                                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                        $message = __('message.msg_otp_send_failed'); 
                                }

                            }else{
                                ## Template not found any notification
                                $message = __('message.msg_unable_to_send_SMS');
                            }
                        }
                    }
                    else
                    {
                           $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                           $message =  __('message.msg_account_not_associate', ['attribute' => $mobile_number]);
                    } 
                }  
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        if(empty($data)){
            $data =(object) $data;
        }
        return $this->prepareResult($message, $data);
    }

    /**
     * Function: Check Member Account Associate Verify OTP
     *
     * @param    array  $request
     * @return   json    
     */
    public function CheckMemberAccountAssociateVerifyOTP(Request $request){ 
        $message = '';
        try{
            $data = array();
            $created_at = date_getSystemDateTime();
            ## Request parameters
            $post = $request->all(); 
            $validator = $this->validateChangeDeviceVerifyOTPRequest($post);
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message[0];           
            }else{
                $phone = $post['mobile'];
                if(isset($phone) || $phone != "" || $phone != NULL){
                    $mobile_otp = $post['otp'];
                    $Member = Member::where('mobile_number', '=', $phone)->first();
                    if(!empty($Member)){

                        $otp_expired_at = isset($Member->expired_date)?strtotime($Member->expired_date):"";
                        $otp_db = isset($Member->otp)?$Member->otp:"";
                        $member_id = isset($Member->id)?$Member->id:"";
                        $current_time = strtotime($created_at);
                        $id = isset($Member->id)?$Member->id:0;
                        $status = isset($Member->status)?$Member->status:0;
                        if($otp_db != $mobile_otp){
                            ## Invalid OTP 
                            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                            $message = __('message.msg_otp_not_match'); 
                        }
                        else if($current_time > $otp_expired_at){
                            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                            $message = __('message.msg_otp_expired'); 
                        }else{
                            if($status == Member::ACTIVE){
                                    ## Response Array 
                                    $data['page_title']     = 'Confirmation';
                                    $data['page_message']   = 'You have 1 attempt to change your device. Click on Submit for final approval ';
                                    $data['popup_message']  = 'Are you sure you want to change your device?';
                                    $data['phone_number'] = $phone; 
                                    $data['member_id'] = $id;
                                    $this->setStatusCode(Response::HTTP_OK);
                                    $message = __('message.msg_otp_verified'); 
                                    
                                }else if($status == Member::INACTIVE){
                                    ## Inactive account
                                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                    $message = __('message.msg_account_inactivated');
                                
                                }else{
                                    ## Something went wrong with your request
                                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                                    $message = __('message.msg_something_went_wrong');
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
     * Function: Member forgot password resend api (individual)
     *
     * @param    array  $request
     * @return   json    
     */
    public function CheckMemberAccountAssociateResendOTP(Request $request){

        try{

            $data = array();
            $created_at = date_getSystemDateTime();
            $message = '';        
                ## Request parameters
                $post = $request->all();                  
                $mobile_number = $post['mobile'];
                
                $check = array();
                if(isset($mobile_number) || $mobile_number != "" || $mobile_number != NULL){
                    
                    $check = Member::where('mobile_number', '=', $mobile_number)->get()->toArray();
                                    
                    if(!empty($check)){

                        $otp = gen_generateRandomDigits(4);

                        ## OTP Expiration Time
                        $expiration_duration = config('constants.OTP_EXPIRATION_TIME');
                        $expired_date = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                        $id = isset($check[0]['id'])?$check[0]['id']:0;

                            ## Update Member
                            $update_array = array(
                                'expired_date'     => $expired_date,
                                'otp'              => $otp
                            );
                            //echo "<pre>";print_r($update_array);exit;
                            $member_upd_data = Member::updateMember($id, $update_array);
                            if($member_upd_data != ''){
                                //send data on sms template
                                $section_type   = array_search('Member', config('constants.sms_template_section'));
                                $type           = 'ReSendOtpForChangeDevice';
                                $lang_code      = isset($langCode) && !empty($langCode) ? $langCode : 'en';
                                $sms_template   = SMSTemplate::getSmsTemplateDataByLangCode($type, $section_type, $lang_code);
                                if(isset($sms_template[0]['content']) && $sms_template[0]['content'] != "" ){
                        
                                    ## SMS Notification Data
                                    $body_msg = $sms_template[0]['content'];
                                    ## Replace Meassage data
                                    $array_search_msg  = array("{#var#}");
                                    $array_replace_msg = array($otp);
                                    $sms_data_meassage = addslashes(str_replace($array_search_msg, $array_replace_msg, $body_msg));

                                    //Send SMS to member
                                    $msg_reponse       = sendSMS($sms_data_meassage,$mobile_number);

                                    if(isset($msg_reponse['isError']) && $msg_reponse['isError'] == 0)
                                    {
                                        ## Update Member OTP 
                                        $update_array = array(
                                            'expired_date'     => $expired_date,
                                            'otp'              => $otp,
                                            'updated_at'       => date_getSystemDateTime(),
                                        );
                                        $member_upd_data = TempMemberRegistration::updateTempMemberRegistration($id, $update_array);
                                        $data['otp']            = $otp;
                                        $data['mobile']         = $mobile_number;
                                        $data['expire_timer']   = ($expiration_duration*60);
                                        $data['expire_date']    = $expired_date;    
                                        $data['msg_reponse']    = $sms_data_meassage; 
                                        $this->setStatusCode(Response::HTTP_OK);                      
                                        $message                = __('message.msg_otp_resend');   
                                    }
                                    else
                                    {
                                         ## Invalid OTP      
                                        $message = __('message.msg_otp_send_failed');       
                                    }
                                }
                                else
                                {
                                    ## SMS Template not found any notification
                                    $message = __('message.msg_unable_to_send_SMS');
                                }
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
     * Function: Check Member Device Change Request
     *
     * @param    array  $request
     * @return   json    
     */
    public function checkMemberDeviceChangeRequest(Request $request){

        try{

            $data = array();
            $created_at = date_getSystemDateTime();
            $message = '';
            // $data['is_allow_change_device']   = 0;
            $data['member_id']   = 0;
            ## Validate the request
        
                ## Request parameters
                $post = $request->all();
                $validator = $this->validateChangeDeviceRequest($post);
                if ($validator->fails()) { 
                    ## Invalid request parameters
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $validator_message = $validator->errors()->all();
                    $message = $validator_message[0];           
                }else{
                    $member_id = $post['member_id'];
                    if($member_id > 0){
                        $member_details = Member::getMemberDataFromId($member_id);
                        if(!empty($member_details)){
                            $mobile_number = $member_details[0]['mobile_number'];
                            $member_id = $member_details[0]['id'];
                            $data['member_id']   = $member_id;
                            // $TotalRequstCnt = MemberDeviceChangeLog::getCountOfDeviceChangeRequest($member_id);
                            // if($TotalRequstCnt > 0 ){ // Do not allow to change device 
                            //     $this->setStatusCode(Response::HTTP_OK);
                            //     $message = 'You cannot change your device. For further assistance please contact XXX-XXX-XXXX';
                            // }else{
                            //     $data['is_allow_change_device']   = 1;
                                // $data['page_message'] = 'You have 1 attempt to change your device. Click on Submit for final approval ';
                                // $data['popup_message'] = 'Are you sure you want to change your device?';

                                $old_device_detail_arr = array(
                                    "from_ip"=> $member_details[0]['from_ip'],
                                    "device_type" => $member_details[0]['device_type'],
                                    "device_uuid" => $member_details[0]['device_uuid'],
                                    "device_no" => $member_details[0]['device_no'],
                                    "device_name" => $member_details[0]['device_name'],
                                    "device_platform" => $member_details[0]['device_platform'],
                                    "device_model" => $member_details[0]['device_model'],
                                    "device_version" => $member_details[0]['device_version'],
                                    "app_version" => $member_details[0]['app_version']         
                                );

                                $new_device_detail_arr = array(
                                    "from_ip"=> isset($post['from_ip']) && !empty($post['from_ip']) ? $post['from_ip'] : '',
                                    "device_type" => $post['device_type'],
                                    "device_uuid" => $post['device_uuid'],
                                    "device_no" => $post['device_no'],
                                    "device_name" => $post['device_name'],
                                    "device_platform" => $post['device_platform'],
                                    "device_model" => $post['device_model'],
                                    "device_version" => isset($post['device_version']) && !empty($post['device_version']) ? $post['device_version'] : '',
                                    "app_version" => isset($post['app_version']) && !empty($post['app_version']) ? $post['app_version'] : ''
                                );

                                $update_dev_arr = array("from_ip"=>"","device_type"=>"1","device_uuid"=>"","device_no"=>"","device_name"=>"","device_platform"=>"","device_version"=>"","device_model"=>"","app_version"=>"","token"=>"","token_expire_at"=>"","token_created_at"=>"");

                                $update_dev = Member::updateMember($member_id,$update_dev_arr);
                                ##Insert in log table
                                $old_device_info = serialize($old_device_detail_arr);
                                $new_device_info = serialize($new_device_detail_arr);

                                $insert_device_log = array(
                                                        'member_id'=>$member_id,
                                                        'mobile_number'=>$mobile_number,
                                                        'old_device_info'=>$old_device_info,
                                                        'new_device_info'=>$new_device_info,
                                                        'created_at'=>date_getSystemDateTime()
                                                    );
                                MemberDeviceChangeLog::addMemberDeviceChangeLog($insert_device_log);

                                // $data['page_title'] = '';
                                $data['pop_up_message'] = 'Your device has been successfully changed.';

                                $this->setStatusCode(Response::HTTP_OK);
                                $message = __('message.msg_device_changed');
                            // }
                        }else{
                            ## Something went wrong with your request
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message = __('message.msg_wrong_request');
                        }
                    }else{
                        ## Something went wrong with your request
                        $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                        $message = __('message.msg_wrong_request'); 
                    }
                }

        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        ## Return Response
        return $this->prepareResult($message, $data);
    }
}
