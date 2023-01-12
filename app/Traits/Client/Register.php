<?php ## Created by Krupali as on 19th August 2019

namespace App\Traits\Auth;

use Validator;
use Config;

trait Register
{
    
    /**
     * Function: Check serverside validation - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateRegistrationOTPRequest($data=array()) {
        
        $rules = $this->RegistrationOtpValidateRules($data);
        $messages = $this->RegistrationOtpMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Registration OTP Verify mobile
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function RegistrationOtpValidateRules($data){
        return [ 
            'device_type'       => 'required',
            'mobile'            => 'required|unique:member,mobile_number,NULL,id|regex:/^[0-9]+$/|digits:10',
        ];
    }

    /**
     * Function: Validation messages - Registration OTP Verify mobile
     *
     * @return   array of messages
     */
    private function RegistrationOtpMessages($data){
        return [ 
            'device_type.required'  => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'mobile.required'       => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.unique'         => __('message.msg_mobile_exist'),
            'mobile.regex'          => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.digits'         => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
        ];
    }

    /**
     * Function: Check serverside validation - Registration 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateRegistrationRequest($data=array()) {
        
        $rules = $this->RegistrationValidateRules($data);
        $messages = $this->RegistrationMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Registration 
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function RegistrationValidateRules($data){
        return [ 
            'device_type'       => 'required',
            'mobile'            => 'required|unique:member,mobile_number,NULL,id|regex:/^[0-9]+$/|digits:10',
            'first_name'        => 'required|min:3|max:50',
            'last_name'         => 'required|min:3|max:50',
            'email'             => 'required|email|max:100',
            'address'           => 'required',
            'profession'        => 'required',
            'password'          => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'', 
        ];
    }

    /**
     * Function: Validation messages - Registration 
     *
     * @return   array of messages
     */
    private function RegistrationMessages($data){
        return [ 
            'device_type.required'         => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_device_type'))]),
            'mobile.required'              => __('message.msg_required', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.unique'                => __('message.msg_mobile_exist'),
            'mobile.regex'                 => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'mobile.digits'                => __('message.msg_mobile_format', ['attribute' => strtolower(__('label.lbl_mobile'))]),
            'first_name.required'          => sprintf(Config::get('messages.validation_msg.required_field'), 'First Name'),
            'first_name.min'               => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'First Name'),
            'first_name.max'               => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'First Name'),
            'last_name.required'           => sprintf(Config::get('messages.validation_msg.required_field'), 'Last Name'),
            'last_name.min'                => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Last Name'),
            'last_name.max'                => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Last Name'),
            'email.required'               => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'email.email'                  => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'email.max'                    => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email'),
            'address.required'             => sprintf(Config::get('messages.validation_msg.required_field'), 'Address'),
            'profession.required'          => sprintf(Config::get('messages.validation_msg.required_field'), 'Profession'),
            'password.required'            => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.min'                 => sprintf(Config::get('messages.validation_msg.minlength'), config('constants.PASSWORD_MIN'), 'Password'),
            'password.max'                 => sprintf(Config::get('messages.validation_msg.maxlength'), config('constants.PASSWORD_MAX'), 'Password'),
            'password.regex'               => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
        ];
    }
}