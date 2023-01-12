<?php

namespace App\Traits\Auth;

use Validator;
use Hash;

trait Auth
{
    
    /**
     * Function: Check serverside validation - Login
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateLoginRequest($data) {
        $validator = NULL;  
        $rules = $this->loginValidateRules($data);
        $messages = $this->loginMessages($data);
        $validator = Validator::make($data, $rules, $messages);
        $validator->sometimes('password', ['required'], function ($data){
            return $data['social_login'] == '0';
        });
        return $validator;
    }

    /**
     * Function: Validation rules - Login
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function loginValidateRules($data){
            
        return [
            // 'email'             => 'required_if:mobile,NULL|email', 
            // 'mobile'            => 'required_if:email,NULL|regex:/^[0-9]+$/', 
            //'email'          => 'required',
            'username'          => 'required',
            'password'          => 'required',
            'device_type'       => 'required',
            'device_uuid'       => 'required',
            'device_no'         => 'required',
            'device_name'       => 'required',
            'device_platform'   => 'required',
            'device_model'      => 'required',
            'app_version'       => 'required',
            'device_version'    => 'required'
        ];
    }

    /**
     * Function: Validation messages - Login
     *
     * @return   array of messages
     */
    private function loginMessages(){
        return [
            //'email.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile_number'))]),
            'username.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_username'))]),

            'password.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_password'))]),
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'device_uuid.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_uuid'))]),
            'device_no.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_no'))]),
            'device_name.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_name'))]),
            'device_platform.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_platform'))]),
            'device_model.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_model'))]),
            'device_version.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_version'))]),
            'app_version.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_app_version'))]),
        ];
    }

    /**
     * Function: Validation rules - Login
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function loginValidateRulesRegToken($data){
        return [
                'registration_token'    => 'required',
                'device_type'           => 'required',
                'device_uuid'           => 'required',
                'device_no'             => 'required',
                'device_name'           => 'required',
                'device_platform'       => 'required',
                'device_model'          => 'required',
                'device_version'        => 'required'
        ];
    }

    /**
     * Function: Validation messages - Login
     *
     * @return   array of messages
     */
    private function loginMessagesRegToken(){
        return [
            'registration_token.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_registration_token'))]),
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'device_uuid.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_uuid'))]),
            'device_no.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_no'))]),
            'device_name.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_name'))]),
            'device_platform.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_platform'))]),
            'device_model.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_model'))]),
            'device_version.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_version'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Logout
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateLogoutRequest($data=array()) {
        
        $rules = $this->logoutValidateRules($data);
        $messages = $this->logoutMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Logout
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function logoutValidateRules($data){
        return [
            'user_id'       => 'required', 
            'login_log_id'      => 'required',
            'device_type'       => 'required'
        ];
    }

    /**
     * Function: Validation messages - Logout
     *
     * @return   array of messages
     */
    private function logoutMessages(){
        return [
            'user_id.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_user_id'))]),
            'login_log_id.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_login_id'))]),
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Forgot Password
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateForgotPasswordRequest($data=array()) {
        
        $rules = $this->forgotPasswordValidateRules($data);
        $messages = $this->forgotPasswordMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Forgot Password
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function forgotPasswordValidateRules($data){
        return [ 
            'device_type'       => 'required',
            'email'             => 'required|email', 
        ];
    }

    /**
     * Function: Validation messages - Forgot Password
     *
     * @return   array of messages
     */
    private function forgotPasswordMessages(){
        return [ 
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'email.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_email'))]),
            'email.email' => __('message.msg_email_format', ['attribute' => strtolower(__('label.lbl_email'))]),
        ];
    }


    /**
     * Function: Check serverside validation - Validate Forgot Password Link
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateForgotPasswordLinkRequest($data=array()) {
        $rules = $this->forgotPasswordLinkValidateRules($data);
        $messages = $this->forgotPasswordLinkValidateRulesMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Validate Forgot Password Link
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function forgotPasswordLinkValidateRules($data){
        return [
           
            'token'             => 'required', 
            'device_type'       => 'required',
        ];
    }

    /**
     * Function: Validation messages - Validate Forgot Password Link
     *
     * @return   array of messages
     */
    private function forgotPasswordLinkValidateRulesMessages(){
        return [
            'token.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_token'))]),
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Validate Reset Password
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateResetPasswordRequest($data=array()) {
        $rules = $this->resetPasswordValidateRules($data);
        $messages = $this->changePasswordMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Validate Reset Password
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function resetPasswordValidateRules($data){
        return [
            
            'token'             => 'required', 
            'device_type'       => 'required',
            'email'             => 'required|email', 
            'password'    => 'required|confirmed|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT')
        ];
    }

    // /**
    //  * Function: Validation messages - Validate Reset Password
    //  *
    //  * @return   array of messages
    //  */
    // private function changePasswordMessages(){
    //     return [
           
    //         'token.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_token'))]),
    //         'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
    //         'email.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_email'))]),
    //         'email.email' => __('message.msg_email_format', ['attribute' => strtolower(__('label.lbl_email'))]),
    //         'password.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_password'))]),
    //         'password.min' => __('message.msg_min_string', ['attribute' => strtolower(__('label.lbl_password')) , 'min' => config('constants.PASSWORD_MIN')]),
    //         'password.max' => __('message.msg_max_string', ['attribute' => strtolower(__('label.lbl_password')) , 'max' => config('constants.PASSWORD_MAX')]),
    //         'password.regex'  => __('message.msg_regex_password', ['attribute' => strtolower(__('label.lbl_password'))]),
    //         'password.confirmed'  => __('message.msg_confirmed', ['attribute' => strtolower(__('label.lbl_password'))]),
    //     ];
    // }


    /**
     * Function: Check serverside validation - Change Password
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateChangePasswordRequest($data=array()) {
        
        $rules = $this->changePasswordValidateRules($data);
        $messages = $this->changePasswordMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Change Password
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function changePasswordValidateRules($data){
        return [
            'device_type'       => 'required',
            'new_password'      => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT'),
            'confirm_password' => ['required', 'required_with:new_password', 'same:new_password']
        ];
    }

     /**
     * Function: Validation messages - Registration
     *
     * @return   array of messages
     */
    private function changePasswordMessages(){
        return [
            'confirm_password.required'   => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'confirm_password.required_with' => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'confirm_password.same' => __('general/message.CONFIRM_PASSWORD_MATCH_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'new_password.required' => __('general/message.REQUIRED_FIELD', ['attribute' => strtolower(__('label.lbl_password'))]),
            'new_password.min' => __('general/message.MIN_STRING', ['attribute' => strtolower(__('label.lbl_password')), 'min' => config('constants.PASSWORD_MIN')]),
            'new_password.max' => __('general/message.MAX_STRING', ['attribute' => strtolower(__('label.lbl_password')), 'max' => config('constants.PASSWORD_MAX')]),
            'new_password.regex' => __('general/message.REGEX', ['attribute' => strtolower(__('label.lbl_password'))]),

        ];
    }



    /**
     * Function: Check serverside validation - Change Password
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateChangeCurrentPasswordRequest($data=array()) {
        
        $rules = $this->changeCurrentPasswordValidateRules($data);
        $messages = $this->changeCurrentPasswordMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Change Password
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function changeCurrentPasswordValidateRules($data){
        $current_password = isset($data['current_password'])?$data['current_password']:"";
        return [
            'device_type'       => 'required',
            'current_password'  => 'required',
            // 'current_password'  => ['required', function ($attribute, $value) {
            //     if (!Hash::check($value, 'password')) {
            //         echo "dfd";exit;
            //         return fail(__('The current password is incorrect.'));
            //     }else{
            //         echo "dfdese";exit;
            //     }
            // }],
            // 'current_password'  => 'required',
             
            'new_password'      => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT'),
            'confirm_password' => ['required', 'required_with:new_password', 'same:new_password']
        ];
    }

     /**
     * Function: Validation messages - Registration
     *
     * @return   array of messages
     */
    private function changeCurrentPasswordMessages(){
        return [
            'current_password.required'   => __('general/message.CURRENT_PASSWORD_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'confirm_password.required'   => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'confirm_password.required_with' => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'confirm_password.same' => __('general/message.CONFIRM_PASSWORD_MATCH_ERROR', ['attribute' => strtolower(__('label.lbl_password'))]),
            'new_password.required' => __('general/message.REQUIRED_FIELD', ['attribute' => strtolower(__('label.lbl_password'))]),
            'new_password.min' => __('general/message.MIN_STRING', ['attribute' => strtolower(__('label.lbl_password')), 'min' => config('constants.PASSWORD_MIN')]),
            'new_password.max' => __('general/message.MAX_STRING', ['attribute' => strtolower(__('label.lbl_password')), 'max' => config('constants.PASSWORD_MAX')]),
            'new_password.regex' => __('general/message.REGEX', ['attribute' => strtolower(__('label.lbl_password'))]),

        ];
    }

     /**
     * Function: Check serverside validation - Device Change
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateChangeDeviceRequest($data) {
        $rules = $this->DeviceChangeValidateRules($data);
        $messages = $this->DeviceChangeValidateMessages($data);
        $validator = Validator::make($data, $rules, $messages);
        return $validator;
    }

    /**
     * Function: Validation rules - Login
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function DeviceChangeValidateRules($data){
            
        return [
            'mobile_number'            => 'required|regex:/^[0-9]+$/|digits:10',
            'user_id'         => 'required',
            'device_type'       => 'required',
            'device_uuid'       => 'required',
            'device_no'         => 'required',
            'device_name'       => 'required',
            'device_platform'   => 'required',
            'device_model'      => 'required',
            'device_version'    => 'required'
        ];
    }

    /**
     * Function: Validation messages - Login
     *
     * @return   array of messages
     */
    private function DeviceChangeValidateMessages(){
        return [
            
            'user_id.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_user_id'))]),
            'device_type.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'device_uuid.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_uuid'))]),
            'device_no.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_no'))]),
            'device_name.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_name'))]),
            'device_platform.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_platform'))]),
            'device_model.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_model'))]),
            'device_version.required' => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_version'))]),
            'mobile_number.required'       => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile_number.regex'          => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile_number.digits'         => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateDeviceOTPRequest($data=array()) {
        
        $rules = $this->DeviceOtpValidateRules($data);
        $messages = $this->DeviceOtpMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function DeviceOtpValidateRules($data){
        return [ 
            'device_type'       => 'required',
            'mobile'            => 'required|regex:/^[0-9]+$/|digits:10',
        ];
    }

    /**
     * Function: Validation messages - Registration OTP Verify mobile
     *
     * @return   array of messages
     */
    private function DeviceOtpMessages($data){
        return [ 
            'device_type.required'  => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'mobile.required'       => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.regex'          => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.digits'         => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateChangeDeviceVerifyOTPRequest($data=array()) {
        
        $rules = $this->DeviceOtpVerifyValidateRules($data);
        $messages = $this->DeviceOtpVerifyMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function DeviceOtpVerifyValidateRules($data){
        return [ 
            'device_type'       => 'required',
            'otp'               => 'required',
            'mobile'            => 'required|regex:/^[0-9]+$/|digits:10',
        ];
    }

    /**
     * Function: Validation messages - Registration OTP Verify mobile
     *
     * @return   array of messages
     */
    private function DeviceOtpVerifyMessages($data){
        return [ 
            'device_type.required'  => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'otp.required'  => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_otp'))]),
            'mobile.required'       => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.regex'          => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.digits'         => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
        ];
    }

}