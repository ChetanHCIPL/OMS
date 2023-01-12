<?php ## Created by Mayur as on 01 July 2022

namespace App\Http\Controllers\API\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction;
use App\Traits\Auth\Register;
use App\Models\Plan;
use App\Models\Plan_Course;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\Member;
use App\Models\SMSTemplate;
use App\Models\TempMemberRegistration;
use App\Models\EmailTemplate;
use App\Models\Education;
use App\Models\Profession;
use Redirect;
use Mail;
use Config;
use Validator;
use Exception;

class RegisterController extends Controller
{

    use ApiFunction, Register;

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
    public function generateRegistrationOtp(Request $request){

        $message = '';
        try{
            ## Variable Declaration
            $data = array();
            $message_str['meassge'] = '';
            $validator = $this->validateRegistrationOTPRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message[0];
            }
            else
            {
                $created_at = date_getSystemDateTime();
                $mobile_number = $request->mobile;
                $langCode = $request->lang_code;
                //Delete entry from temp member
                TempMemberRegistration::deleteTempMemberData($mobile_number);
                $otp = gen_generateRandomDigits(4);

                $expiration_duration  = config('constants.OTP_EXPIRATION_TIME');
                $expired_date         = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                $section_type         = array_search('Member', config('constants.sms_template_section'));
                $type                 = 'SendRegistrationOTP';
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

                        $insert_array = array(
                                    'mobile_number' => $mobile_number,
                                    'otp'           => $otp,
                                    'expired_date'  => $expired_date,
                                    'created_at'    => date_getSystemDateTime(),
                                );
                        $member_data = TempMemberRegistration::addTempMemberRegistration($insert_array);

                        if(isset($member_data)){
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
     * Function: Verify Registration otp
     *
     * @param    array  $request
     * @return   json    
     */
    public function verifyRegistrationOtp(Request $request){ 
        $message = '';
        try{
            $data = array();
            $created_at = date_getSystemDateTime();
            ## Request parameters
            $post = $request->all(); 
            //$mobile_number = $request->mobile;
            $mobile_number      = (isset($post['mobile']) && $post['mobile'] != "" ? $post['mobile'] : "");
            $mobile_otp         = (isset($post['otp']) && $post['otp'] != "" ? $post['otp'] : "");
            
            //if(isset($mobile_number) || $mobile_number != "" || $mobile_number != NULL){
            if($mobile_number != "" && $mobile_otp > 0){      
                $TempMemberData = array();          
                // Get temp member data from temp member table
                $TempMemberData = TempMemberRegistration::getTempMemberDataFrommobilenumber($mobile_number);
                
                if(!empty($TempMemberData)){
                    $otp_expired_at = isset($TempMemberData->expired_date)?strtotime($TempMemberData->expired_date):"";
                    $otp_db         = isset($TempMemberData->otp)?$TempMemberData->otp:"";
                    
                    $current_time   = strtotime($created_at);

                    //Check otp verify condition
                    if($otp_db != $mobile_otp){
                        ## Invalid OTP 
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = __('message.msg_otp_not_match'); 
                    }
                    else if($current_time > $otp_expired_at){
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message = __('message.msg_otp_expired'); 
                    }else{                        
                            ## Success
                            $this->setStatusCode(Response::HTTP_OK);
                            ## Response Array
                            $data['mobile_number']   = $mobile_number; 
                            $data['otp']             = $otp_db;  
                            $data['otp_expired_at']  = $TempMemberData->expired_date;
                            $message                 = __('message.msg_otp_verified');             
                        }                       
                } else{
                    ##Not found records in DB
                    $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                    $message = __('message.msg_record_not_found'); 
                }
            }                 
            else{
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_parameter_missing'); 
            } 
        }
        catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        if(empty($data)){
            $data =(object) $data;
        }
        ## Return Response
        return $this->prepareResult($message, $data);
    }

     /**
     * Function: Resend Registration otp
     *
     * @param    array  $request
     * @return   json    
     */
    public function resendRegistrationOtp(Request $request){
        $message = '';
        try{

            $data = array();
            $created_at = date_getSystemDateTime();
            ## Validate the request       
            ## Request parameters
            $post = $request->all();                  
            $mobile_number = $post['mobile'];
            $validator = $this->validateRegistrationOTPRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message[0];    
            }
            else
            {                
                $check = array();                
                $check = TempMemberRegistration::getTempMemberDataFrommobilenumber($mobile_number);

                if(!empty($check))
                {
                    $otp = gen_generateRandomDigits(4);
                    ## OTP Expiration Time
                    $expiration_duration = config('constants.OTP_EXPIRATION_TIME');
                    $expired_date = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                    $id = isset($check->id)?$check->id:0;
                    

                    //send data on sms template
                    $section_type   = array_search('Member', config('constants.sms_template_section'));
                    $type           = 'ReSendRegistrationOTP';
                    $lang_code      = isset($langCode) && !empty($langCode) ? $langCode : 'en';
                    
                    // Get SMS notification data for Send registration OTP
                    $sms_template = SMSTemplate::getSmsTemplateDataByLangCode($type, $section_type, $lang_code);
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
                }else{
                        ##Not found records in DB
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message = __('message.msg_record_not_found'); 
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
        ## Return Response
        return $this->prepareResult($message, $data);
    }

     /**
     * Function: Submit Registration
     *
     * @param    array  $request
     * @return   json    
     */
    public function submitRegistration(Request $request){
        $message = $member_type = $member_code = $admission_code = '';
        $member_id = 0;
        try{
            $post = $request->all();                  
            $parent_referral_code = (isset($post['referral_code']) && $post['referral_code'] != "" ? $post['referral_code'] : "");
            $parent_referral_id = '';
            $data = array();
            $created_at = date_getSystemDateTime();
            ## Validate the request       
            ## Request parameters
            $validator = $this->validateRegistrationRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message[0];           
            }
            else
            {    
                $registartion_token = '';
                $registered_from = Member::RWEB;
                if(!empty($post['device_type']))
                {                   
                    if($post['device_type'] == Member::DANDROID){
                        $registered_from = Member::RANDROID; //Android REGISTERED FROM
                    }elseif($post['device_type'] == Member::DIOS){
                        $registered_from =Member::RIOS; //IOS REGISTERED FROM
                    }elseif($post['device_type'] == Member::DWEB){
                        $registered_from = Member::RWEB;//WEB REGISTERED FROM
                    }else{
                        $registered_from = Member::RADMIN;//ADMIN REGISTERED FROM
                    }
                }                  
                if(is_array($this->member_type_array))
                {
                   $member_type = array_search('Member',$this->member_type_array,true);
                }

                $mobile                 = (isset($post['mobile']) ? $post['mobile'] : "");
                $first_name             = (isset($post['first_name']) ? $post['first_name'] : "");
                $last_name              = (isset($post['last_name']) ? $post['last_name'] : "");
                $email                  = (isset($post['email']) ? $post['email'] : "");
                $address                = (isset($post['address']) ? $post['address'] : "");
                $profession_id          = (isset($post['profession']) ? $post['profession'] : "");
                $password               = (isset($post['password']) ? Hash::make($post['password']) : "");
                $lang_code              = (isset($post['lang_code']) ? $post['lang_code'] : "");
                $gender                 = (isset($post['gender']) ? $post['gender'] : NULL);
                $device_type            = (isset($post['device_type']) ? $post['device_type'] : Member::DANDROID);
                $is_important_update    = (isset($post['is_important_update']) ? $post['is_important_update'] :0);
                $whatsapp_number        = (isset($is_important_update) && $is_important_update == '1' ? $mobile : "");

                //Get referral Code
                if(!empty($parent_referral_code)){
                    $parent_referral_code_arr = Member::getreferralCode($parent_referral_code);
                    if(!empty($parent_referral_code_arr)){
                        if(isset($parent_referral_code_arr[0]['referral_code'])){
                            $parent_referral_code = $parent_referral_code_arr[0]['referral_code'];
                            $parent_referral_id = $parent_referral_code_arr[0]['id'];
                        }
                    }
                }
               //
                if(empty($parent_referral_id) || $parent_referral_id ==''){
                    $parent_referral_code = '';
                }
                $insert_array = array(
                    'mobile_number'         => $mobile,
                    'first_name'            => $first_name,
                    'last_name'             => $last_name,
                    'email'                 => $email,
                    'address'               => $address,
                    'profession_id'         => $profession_id,
                    'password'              => $password,
                    'lang_code'             => $lang_code,
                    'from_ip'               => getIP(),
                    'registered_from'       => $registered_from,
                    'user_type'             => $member_type,// Default add member as user type.
                    'gender'                => $gender,                    
                    'device_type'           => $device_type,// Default add Android as Device type.
                    'parent_member_id'      => $parent_referral_id,//Add main member id(who has same refrral code) as parent member id 
                    'parent_referral_code'  => $parent_referral_code,
                    'is_important_update'   => $is_important_update,
                    'whatsapp_number'       => $whatsapp_number,
                    'created_at'            => $created_at,
                );  
                // echo "<pre>";print_r($this->admission_code_array);exit;
                // //Insert member data into main member table
                // $code = in_array('TF',$this->admission_code_array); 
                // echo $code;exit;
                $insert = Member::addMember($insert_array);
                
                //After successfully inserted into main table delete entry from temp member registration table                
                if (isset($insert)) {
                    $member_id = $insert->id;
                    //Genrate Random Referral code String
                    //$referral_code_gen = genrateRandomStringGenerator($first_name,$last_name,$mobile,$member_id);
                    //As per Kuldeep's team 
                    $referral_code_gen = strtoupper($first_name[0]) . strtoupper($last_name[0]) . rand(00000000, 99999999);
                    //Check Refferal code is present on table or not
                    $checkReferralCode = Member::getreferralCode($referral_code_gen);

                    if(count($checkReferralCode) > 0){
                        //$referral_code_gen = genrateRandomStringGenerator($first_name,$last_name,$mobile,$member_id);
                        $referral_code_gen = strtoupper($first_name[0]) . strtoupper($last_name[0]) . rand(00000000, 99999999);
                    }
                    $update_array = array('referral_code' => $referral_code_gen);
                    //Update refrral code
                    $updateRefrralCode = Member::updateMember($member_id,$update_array);

                    // Update member code
                    $updateMemberCode = Member::updateMemberCode($member_id);

                    //Insert data in admisstion and addmisstion course table based on plan
                    if($this->free_registration_plan_id > 0){
                        $plan_data = Plan::getPlanDataFromId($this->free_registration_plan_id);
                        if(!empty($plan_data)){
                            /*Comment below code for Fees, Tax Amount and Total fees should be 0 in free trail registration Riddhi [ Assigned By TL 13/06/2022 ]*/

                            // $fees       = $plan_data[0]['fees'];
                            // $tax_amount = $plan_data[0]['tax_amount'];
                            // $total_fees = $plan_data[0]['total_fees'];
                             
                             $fees       = 0.00;
                             $tax_amount = 0.00;
                             $total_fees = 0.00;

                            $admission_date     =  $created_at;
                            $admission_type     =  Admission::AD_TRAIL_FREE;
                            $admission_status   =  Admission::ACTIVE;
                            $todays_date        =  date_getSystemDate();
                            if($this->free_registration_days > 0){
                                $free_registration_days = $this->free_registration_days;
                            }else{
                                $free_registration_days = 7;
                            }
                            $admission_end_date = date_addDate($todays_date, $da=$free_registration_days, $ma=0, $ya=0, $ha=0);

                            $plan_course = Plan_Course::getPlanselectedData($this->free_registration_plan_id);
                            if(!empty($plan_course)){
                                 $insert_ad_array = array(
                                    'member_id'             => $member_id,
                                    'plan_id'               => $this->free_registration_plan_id,
                                    'fees'                  => $fees,
                                    'tax_amount'            => $tax_amount,
                                    'total_fees'            => $total_fees,
                                    'admission_date'        => $admission_date,
                                    'admission_end_date'    => $admission_end_date,
                                    'admission_type'        => $admission_type,
                                    'status'                => $admission_status,
                                    'created_at'            => $created_at,
                                    'qty'                   => '1'
                                );  
                                $insert_ad = Admission::addAdmission($insert_ad_array);
                                if (isset($insert_ad)) {
                                    //Need to update admission_code
                                     $admission_id = $insert_ad->id;
                                     $admission_code = array_search($admission_type,$this->admission_code_array,true);
                                     if(empty($admission_code))
                                     {
                                        $admission_code = 'TF';
                                     }
                                     $updateMemberCode = Admission::updateAdmissionCode($admission_id,$admission_code);
                                    foreach($plan_course as $value)
                                    {
                                        //$plan_course_arr[] = $value['course_id'];
                                        $insert_adc_array = array(
                                            'member_id'             => $member_id,
                                            'admission_id'          => $admission_id,
                                            'course_id'             => $value['course_id'],
                                            'created_at'            => date_getUnixSystemTime()
                                        );  
                                        $insert_adc = AdmissionCourse::addAddmissionCourse($insert_adc_array);
                                    }
                                }
                            }
                        }
                    }
                    // Delete Temp member data
                    $deleteData = TempMemberRegistration::deleteTempMemberData($mobile);

                    if(isset($deleteData) && isset($updateMemberCode)){

                        //Make registration token
                        //$current_system_time    = date_getUnixSystemTime();
                        $system_date_time      = date_getSystemDateTime();
                        $token_expired_time = strtotime(date_addDateTime($system_date_time, $da=0, $ma=0, $ya=0, $ha=1,$ia=0,$sa=0));

                        $registration_token     = base64_encode($member_id.'-'.$token_expired_time);
                        //Get Email Template Data for send Email
                
                        $message = __('message.msg_registration_success'); 
                           $data['member_id']              = $member_id;
                            $data['registration_token']     = $registration_token;
                            $data['message_title']          = 'Congratulations !';
                            $data['message_description']    = '';
                            //$data['message_description']    = 'The bell just rang for your free '.$this->free_registration_days.'-day trial period Ensure that your friends attend & earn 100 coins';
                            $data['message_buy_subsciption']= 'or Buy Full Subscription';  

                        // Uncomment Below code For send Mail after successfull registration
                           $mail = $mailData = array();
                            $type = 'MemberTrailRegistration';
                            $section = array_search('Member', Config::get('constants.email_template_section'));
                            $lang_code = (isset($post['lang_code'])?$post['lang_code']:'en');
                            $email_template_data = EmailTemplate::getEmailTemplatTypesData($type,$section,$lang_code);
                            
                            $FirstName = isset($post['first_name'])?$post['first_name']:"";
                            $LastName  = isset($post['last_name'])?$post['last_name']:"";
                            $mail['to_email']                   = $post['email'];
                            $mailData['name']                   = $FirstName;
                            $mailData['free_trial_days']        = $this->free_registration_days;
                            $mail['from_mail']                  = $email_template_data[0]['from']; 

                            $sentMail = Mail::send('admin/email/free_trial_registration', $mailData, function ($message) use($mail) {
                                    $message->from($mail['from_mail'], Config::get('settings.SITE_NAME.default'));
                                    $message->to($mail['to_email']);
                                    $message->subject('Free Trial Registration');
                            }); 
                        $this->setStatusCode(Response::HTTP_OK);
                    }
                }
                else
                {
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message = __('message.msg_registration_unsuccessfull');
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
        ## Return Response
        return $this->prepareResult($message, $data);
    }
    
     /**
     * Function: Save User basic detail
     *
     * @param    array  $request
     * @return   json    
     */
    
    public function saveBasicDetail(Request $request)
    {
        
        
         $validator = Validator::make($request->all(), [
           'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'dob' => 'required',
            'password' => 'required',
            'country_code'=>'required'
       
         ]);

        if ($validator->fails()) {
                
                $message = $validator->errors();;
                $data=array();
                return $this->prepareResult($message, $data);
            }
        $data =array();
        try{
             
            if ($request->parent_referral_code != "") {
                
                $parent_referral_code = $request->parent_referral_code;
                
                $count = Member::where('referral_code', $request->parent_referral_code)->count();
                if (!$count) {
                     $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                     $message="Invalid Referral Code.";
                     return $this->prepareResult($message, $data);                  
                }
            }else
            {
                if($request->state=='GJ')
                {
                    $parent_referral_code = "MR00009999";
                }else{
                    $parent_referral_code = "JO11111111";
                }
                
            }
            $registered_from = Member::RANDROID;
            if(!empty($post['device_type']))
            {                   
                if($post['device_type'] == Member::DANDROID){
                    $registered_from = Member::RANDROID; //Android REGISTERED FROM
                }elseif($post['device_type'] == Member::DIOS){
                    $registered_from =Member::RIOS; //IOS REGISTERED FROM
                }elseif($post['device_type'] == Member::DWEB){
                    $registered_from = Member::RWEB;//WEB REGISTERED FROM
                }else{
                    $registered_from = Member::RADMIN;//ADMIN REGISTERED FROM
                }
            } 
            $device_type            = (isset($post['device_type']) ? $post['device_type'] : Member::DANDROID);
            if(is_array($this->member_type_array))
                    {
                       $member_type = array_search('Member',$this->member_type_array,true);
                    }
            
            $profession_id=$request->profession;
            if($request->profession==49)
            {
                if($request->other_profession!='')
                {
                    $profession=Profession::where('name', $request->other_profession)->first();
                    if(!empty($profession))
                    {
                        $profession_id = $profession->id;
                    }else{
                     $pdata=array('name' => $request->other_profession,
                                'status'=> 1
                                );
                     $profession = Profession::create($pdata);
                     $profession_id = $profession->id;
                    }
                }
            }
            
            $education_id=$request->education;
            if($request->education==6)
            {
                if($request->other_education!='')
                {
                    $education=Education::where('title', $request->other_education)->first();
                    if(!empty($education))
                    {
                        $education_id = $education->id;
                    }else{
                     $edata=array('title' => $request->other_education,
                                'status'=> 1
                                );
                     $education = Education::create($edata);
                     $education_id = $education->id;
                    }
                }
            }       
            
            $user = Member::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'whatsapp_number' => $request->whatsapp_number,
                'birth_date' => $request->dob,
                'gender' => $request->gender,
                'profession_id' => $profession_id,
                'university' => $request->university,
                'education_id' => $education_id,
                'plan_id' => $request->course,
                'nationality'=>$request->nationality,
                'country_code'=>$request->country_code,         
                'state_code' => $request->state,
                'city_code' => $request->city,
                'address' => $request->address,
                'pin_code' => $request->pin_code,
                'parent_referral_code' => $parent_referral_code,
                'registered_from' => $registered_from,
                'password' => Hash::make($request->password),
                'device_type' =>$device_type,
                'user_type'=> $member_type,
                'lang_code' => 'en',
                'from_ip' => $request->ip(),
                'status' => 2
            ]);
            

            $id = $user->id;

            $referral_code = strtoupper($request->first_name[0]) . strtoupper($request->last_name[0]) . rand(00000000, 99999999);
            $mc = str_pad($id,8,'0', STR_PAD_LEFT);
            $member_code ='M'.$mc;
            
            $user->referral_code = $referral_code;
            //$user->member_code = $member_code;
            $user->member_code = $id;
            $user->update();
            $data['user'] = $user;
            $this->setStatusCode(Response::HTTP_OK);
            $message = __('message.msg_registration_success');  
         }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
         return $this->prepareResult($message, $data);
        
    }
    
    
     /**
     * Function: Send Registration otp
     *
     * @param    array  $request
     * @return   json    
     */
    public function sendRegistrationOtp(Request $request){
        
         $message = '';
        try{
            ## Variable Declaration
            $data = array();
            $message_str['meassge'] = '';
            $validator = $this->validateRegistrationOTPRequest($request->all());
            if ($validator->fails()) { 
                ## Invalid request parameters
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message[0];
                $member = Member::where('mobile_number',$request->mobile)->first();
                if(!empty($member))
                {
                 $data['user_status'] = $member->status;
                 $data['member_id'] = $member->id;
                }
            }
            else
            {
                $created_at = date_getSystemDateTime();
                $mobile_number = $request->mobile;
                $email = $request->email;
                $langCode = $request->lang_code;
                //Delete entry from temp member
                TempMemberRegistration::deleteTempMemberData($mobile_number);
                $otp = gen_generateRandomDigits(4);

                $expiration_duration  = config('constants.OTP_EXPIRATION_TIME');
                $expired_date         = date_addDateTime($created_at, $da=0, $ma=0, $ya=0, $ha=0,$expiration_duration,$sa=0);
                $section_type         = array_search('Member', config('constants.sms_template_section'));
                $type                 = 'SendRegistrationOTP';
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
                    
                    ## Send otp on email
                        $mail = $mailData = array();
                        $type = 'SendRegistrationOTP';
                        $section = array_search('Member', Config::get('constants.email_template_section'));
                        $lang_code = (isset($post['lang_code'])?$post['lang_code']:'en');
                        $email_template_data = EmailTemplate::getEmailTemplatTypesData($type,$section,$lang_code);
                        
                        $mailData['otp']                   = $otp;                           
                        $mail['from_mail']                  = $email_template_data[0]['from']; 
                        $mail['to_email']                  = $email; 
                        $sentMail = Mail::send('admin/email/registration_otp', $mailData, function ($message) use($mail) {
                                $message->from($mail['from_mail'], Config::get('settings.SITE_NAME.default'));
                                $message->to($mail['to_email']);
                                $message->subject('Registration OTP');
                                 }); 

                    if(isset($msg_reponse['isError']) && $msg_reponse['isError'] == 0){

                        $insert_array = array(
                                    'mobile_number' => $mobile_number,
                                    'otp'           => $otp,
                                    'expired_date'  => $expired_date,
                                    'created_at'    => date_getSystemDateTime(),
                                );
                        $member_data = TempMemberRegistration::addTempMemberRegistration($insert_array);

                        if(isset($member_data)){
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
}
