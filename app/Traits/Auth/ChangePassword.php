<?php 

namespace App\Traits\ChangePassword;

use Validator;

trait ChangePassword
{
    
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
            'entity_type_id'    => 'required',
            'device_type'       => 'required',
            'current_password'  => 'required',
            /*'current_password' => ['required', function ($attribute, $value, fail) {
                $current_password = isset($data['current_password'])?$data['current_password']:"";
                if (Hash::check($current_password, 'password')) {
                    return fail(__('The current password is incorrect.'));
                }
            },*/
            'new_password'      => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT'),
            'confirm_password' => ['required', 'required_with:new_password', 'same:new_password']
        ];
    }

    /**
     * Function: Check serverside validation - Change Password
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateChangePassword($data=array()) {
        
        $rules = $this->changePasswordMobileValidateRules($data);
        $messages = $this->changePasswordMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules - Change Password
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function changePasswordMobileValidateRules($data){
        return [
            'entity_type_id'    => 'required',
            'device_type'       => 'required',
            'otp'     => 'required|min:4|max:4',
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
            'entity_type_id.required'   => __('general/message.REQUIRED_FIELD', ['attribute' => strtolower(__('general/label.ENTITY_TYPE'))]),
        	'device_type.required' => __('general/message.REQUIRED_FIELD', ['attribute' => strtolower(__('general/label.DEVICE_TYPE'))]),

            'confirm_password.required'   => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('general/label.PASSWORD'))]),
            'confirm_password.required_with' => __('general/message.CONFIRM_PASSWORD_ERROR', ['attribute' => strtolower(__('general/label.PASSWORD'))]),
            'confirm_password.same' => __('general/message.CONFIRM_PASSWORD_MATCH_ERROR', ['attribute' => strtolower(__('general/label.PASSWORD'))]),

            'otp.required' => __('general/message.REQUIRED_FIELD', ['attribute' => "OTP Code is required"]),
            'otp.min' => __('general/message.MIN_STRING', ['attribute' => 'otp', 'min' => '4']),
            'otp.max' => __('general/message.MAX_STRING', ['attribute' => 'otp', 'max' => '4']),

            'new_password.required' => __('general/message.REQUIRED_FIELD', ['attribute' => strtolower(__('general/label.PASSWORD'))]),
            'new_password.min' => __('general/message.MIN_STRING', ['attribute' => strtolower(__('general/label.PASSWORD')), 'min' => config('constants.PASSWORD_MIN')]),
            'new_password.max' => __('general/message.MAX_STRING', ['attribute' => strtolower(__('general/label.PASSWORD')), 'max' => config('constants.PASSWORD_MAX')]),
            'new_password.regex' => __('general/message.REGEX', ['attribute' => strtolower(__('general/label.PASSWORD'))]),

        ];
    }
}