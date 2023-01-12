<?php

namespace App\Traits\Auth;

use Validator;
use Hash;

trait ClientAuth
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
}