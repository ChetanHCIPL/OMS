<?php

namespace App\Traits\Client;

use Validator;
use Config;

trait Client
{
    
    /**
     * Function: Check serverside validation to add client request
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientAdd($data=array()) {
        
        $rules = $this->addClientValidateRules($data);
        $messages = $this->ClientAddMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules to add client
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function addClientValidateRules($data){
        return [
            'client_name' => 'required',
            'email' => 'unique:clients,email',
            'mobile_number' => 'required|unique:clients,mobile_number|min:10|max:10|regex:/^[0-9]+$/',
            'whatsapp_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
            'type' => 'required',
            'state_id' => 'required',
            'district_id' => 'required',
            'taluka_id' => 'required',
            'zip_code' => 'required|min:6|max:6|regex:/^[0-9]+$/',
        ];
    }
    /**
     * Function: Check serverside validation to add client address 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientAddAddress($data=array()) {
        
        $rules = $this->addClientAddressValidateRules($data);
        $messages = $this->ClientAddAddressMessages($data);

        return Validator::make($data, $rules, $messages);
    }
    /**
     * Function: Check serverside validation to edit client address 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientEditAddress($data=array()) {
        
        $rules = $this->editClientAddressValidateRules($data);
        $messages = $this->ClientEditAddressMessages($data);

        return Validator::make($data, $rules, $messages);
    }
    /**
     * Function: Check serverside validation to add client contact 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientAddContact($data=array()) {
        
        $rules = $this->addClientAddContactValidateRules($data);
        $messages = $this->ClientAddContactMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Check serverside validation to edit client contact 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientEditContact($data=array()) {
        
        $rules = $this->editClientContactValidateRules($data);
        $messages = $this->ClientAddContactMessages($data);

        return Validator::make($data, $rules, $messages);
    }
    


    /**
     * Function: Validation messages to add/edit client request
     *
     * @return   array of messages
     */
    private function ClientAddMessages($data = array()){
        return [
            'client_name.required' => sprintf(config('messages.validation_msg.required_field'), 'client name'),
            'mobile_number.required' => sprintf(config('messages.validation_msg.required_field'), 'mobile number'),
            'mobile_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'mobile number'),
            'mobile_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'mobile number'),
            'mobile_number.regex' => sprintf(config('messages.validation_msg.regex'), 'mobile number'),
            'whatsapp_number.required' => sprintf(config('messages.validation_msg.required_field'), 'whatsapp number'),
            'whatsapp_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'whatsapp number'),
            'whatsapp_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'whatsapp number'),
            'whatsapp_number.regex' => sprintf(config('messages.validation_msg.regex'), 'whatsapp number'),
            'email.unique' => sprintf(config('messages.validation_msg.unique_field'), 'Email'),
            'mobile_number.unique' => sprintf(config('messages.validation_msg.unique_field'), 'Mobile Number'),
            'state_id.required' => sprintf(config('messages.validation_msg.required_field'), 'state'),
            'district_id.required' => sprintf(config('messages.validation_msg.required_field'), 'district'),
            'taluka_id.required' => sprintf(config('messages.validation_msg.required_field'), 'taluka'),
            'zip_code.required' => sprintf(config('messages.validation_msg.required_field'), 'zip code'),
            'zip_code.min' => sprintf(config('messages.validation_msg.minlength'), '6', 'zip code'),
            'zip_code.max' => sprintf(config('messages.validation_msg.maxlength'), '6', 'zip code'),
            'zip_code.regex' => sprintf(config('messages.validation_msg.regex'), 'zip code'),
        ];
    }
    
    /**
     * Function: Validation rules to edit client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function editClientAddressValidateRules($data){
        return [
            "client_id"=>'required',
            "address_id" => 'required',
            "title"=>'required',
            'mobile_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
            "address1"=>'required',
            "address2"=>'required',            
            "state_id"=>'required',
            "district_id"=>'required',
            "taluka_id"=>'required',
            "zip_code"=>'required|min:6|max:6|regex:/^[0-9]+$/',
        ];
    }
    /**
     * Function: Validation rules to add client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function addClientAddressValidateRules($data){
        return [
            "client_id"=>'required',
            "title"=>'required',
            'mobile_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
            "address1"=>'required',
            "address2"=>'required',            
            "state_id"=>'required',
            "district_id"=>'required',
            "taluka_id"=>'required',
            "zip_code"=>'required|min:6|max:6|regex:/^[0-9]+$/',
        ];
    }
    /**
     * Function: Validation messages to add/edit client Address request
     *
     * @return   array of messages
     */
    private function ClientAddAddressMessages($data = array()){
        return [
            'client_id.required' => sprintf(config('messages.validation_msg.required_field'), 'client id'),
            'title.required' => sprintf(config('messages.validation_msg.required_field'), 'full name'),
            'address1.required' => sprintf(config('messages.validation_msg.required_field'), 'address 1'),
            'address2.required' => sprintf(config('messages.validation_msg.required_field'), 'address 2'),
            'mobile_number.required' => sprintf(config('messages.validation_msg.required_field'), 'mobile number'),
            'mobile_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'mobile number'),
            'mobile_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'mobile number'),
            'mobile_number.regex' => sprintf(config('messages.validation_msg.regex'), 'mobile number'),
            'email.max' => sprintf(config('messages.validation_msg.maxlength'), '150', 'email'),
            'email.unique' => sprintf(config('messages.validation_msg.unique_field'), 'Email'),
            'state_id.required' => sprintf(config('messages.validation_msg.required_field'), 'state id'),
            'district_id.required' => sprintf(config('messages.validation_msg.required_field'), 'district id'),
            'taluka_id.required' => sprintf(config('messages.validation_msg.required_field'), 'taluka id'),
            'zip_code.required' => sprintf(config('messages.validation_msg.required_field'), 'zip code'),
            'zip_code.min' => sprintf(config('messages.validation_msg.minlength'), '6', 'zip code'),
            'zip_code.max' => sprintf(config('messages.validation_msg.maxlength'), '6', 'zip code'),
            'zip_code.regex' => sprintf(config('messages.validation_msg.regex'), 'zip code'),
        ];
    }

    /**
     * Function: Validation messages to add/edit client Address request
     *
     * @return   array of messages
     */
    private function ClientEditAddressMessages($data = array()){
        return [
            'client_id.required' => sprintf(config('messages.validation_msg.required_field'), 'client id'),
            'address_id.required' => sprintf(config('messages.validation_msg.required_field'), 'address id'),
            'title.required' => sprintf(config('messages.validation_msg.required_field'), 'full name'),
            'address1.required' => sprintf(config('messages.validation_msg.required_field'), 'address 1'),
            'address2.required' => sprintf(config('messages.validation_msg.required_field'), 'address 2'),
            'mobile_number.required' => sprintf(config('messages.validation_msg.required_field'), 'mobile number'),
            'mobile_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'mobile number'),
            'mobile_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'mobile number'),
            'mobile_number.regex' => sprintf(config('messages.validation_msg.regex'), 'mobile number'),
            'email.max' => sprintf(config('messages.validation_msg.maxlength'), '150', 'email'),
            'email.unique' => sprintf(config('messages.validation_msg.unique_field'), 'Email'),
            'state_id.required' => sprintf(config('messages.validation_msg.required_field'), 'state id'),
            'district_id.required' => sprintf(config('messages.validation_msg.required_field'), 'district id'),
            'taluka_id.required' => sprintf(config('messages.validation_msg.required_field'), 'taluka id'),
            'zip_code.required' => sprintf(config('messages.validation_msg.required_field'), 'zip code'),
            'zip_code.min' => sprintf(config('messages.validation_msg.minlength'), '6', 'zip code'),
            'zip_code.max' => sprintf(config('messages.validation_msg.maxlength'), '6', 'zip code'),
            'zip_code.regex' => sprintf(config('messages.validation_msg.regex'), 'zip code'),
        ];
    }
    
    /**
     * Function: Validation rules to add client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function addClientAddContactValidateRules($data){
        return [
            "client_id"=>'required',
            "full_name"=>'required',
            'mobile_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
            'whatsapp_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
        ];
    }
    /**
     * Function: Validation rules to edit client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function editClientContactValidateRules($data){
        return [
            "client_id"=>'required',
            "contact_id" => 'required',
            "full_name"=>'required',
            'mobile_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
            'whatsapp_number' => 'required|min:10|max:10|regex:/^[0-9]+$/',
        ];
    }
    /**
     * Function: Validation messages to add/edit client Address request
     *
     * @return   array of messages
     */
    private function ClientAddContactMessages($data = array()){
        return [
            'client_id.required' => sprintf(config('messages.validation_msg.required_field'), 'client id'),
            'contact_id.required' => sprintf(config('messages.validation_msg.required_field'), 'contact id'),
            'full_name.required' => sprintf(config('messages.validation_msg.required_field'), 'full name'),
            'mobile_number.required' => sprintf(config('messages.validation_msg.required_field'), 'mobile number'),
            'mobile_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'mobile number'),
            'mobile_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'mobile number'),
            'mobile_number.regex' => sprintf(config('messages.validation_msg.regex'), 'mobile number'),
            'whatsapp_number.required' => sprintf(config('messages.validation_msg.required_field'), 'whatsapp number'),
            'whatsapp_number.min' => sprintf(config('messages.validation_msg.minlength'), '10', 'whatsapp number'),
            'whatsapp_number.max' => sprintf(config('messages.validation_msg.maxlength'), '10', 'whatsapp number'),
            'whatsapp_number.regex' => sprintf(config('messages.validation_msg.regex'), 'whatsapp number'),
        ];
    }

    /**
     * Function: Validate For Edit 
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientAddressForDelete($data=array()) {
        
        $rules = $this->deleteClientAddressValidateRules($data);
        $messages = $this->deleteClientAddressMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules to Delete client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function deleteClientAddressValidateRules($data){
        return [
            "client_id"=>'required',
            "address_id" => 'required',
            "user_id" => 'required'
        ];
    }

    /**
     * Function: Validation messages to Delete client Address request
     *
     * @return   array of messages
     */
    private function deleteClientAddressMessages($data = array()){
        return [
            'client_id.required' => sprintf(config('messages.validation_msg.required_field'), 'client id'),
            'address_id.required' => sprintf(config('messages.validation_msg.required_field'), 'address id'),
            'user_id.required' => sprintf(config('messages.validation_msg.regex'), 'User id'),
        ];
    }

    /**
     * Function: Validate For Delete
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateClientContactForDelete($data=array()) {
        
        $rules = $this->deleteClientContactValidateRules($data);
        $messages = $this->deleteClientContactMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules to Delete client Address
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function deleteClientContactValidateRules($data){
        return [
            "client_id"     =>'required',
            "contact_id"    => 'required',
            "user_id"       => 'required'
        ];
    }

    /**
     * Function: Validation messages to Delete client Address request
     *
     * @return   array of messages
     */
    private function deleteClientContactMessages($data = array()){
        return [
            'client_id.required'    => sprintf(config('messages.validation_msg.required_field'), 'client id'),
            'contact_id.required'   => sprintf(config('messages.validation_msg.required_field'), 'address id'),
            'user_id.required'      => sprintf(config('messages.validation_msg.regex'), 'User id'),
        ];
    }
}