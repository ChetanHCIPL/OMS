<?php

namespace App\Traits\Order;

use Validator;
use Config;

trait Order
{
    
    /**
     * Function: Check serverside validation to add order request
     *
     * @param    array  $data
     * @return   object of validator class
     */
    private function validateOrderAdd($data=array()) {
        
        $rules = $this->addOrderValidateRules($data);
        $messages = $this->OrderAddMessages($data);

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function: Validation rules to add order
     *
     * @param    array  $data
     * @return   array of rules
     */
    private function addOrderValidateRules($data){
        return [
            'client_id'=>'required',
            'billing_address_id' => 'required',
            'client_contact_person_id' => 'required',
            'shipping_address_id' => 'required',
            'sales_user_id' => 'required',
            'product_head' => 'required',
            'transporter' => 'required',
            'route_area' => 'required',
            'payment_due_days' => 'required',
            'due_date' => 'required',
            'dispatch_date' => 'required',
            'order_date' => 'required',
        ];
    }

    /**
     * Function: Validation messages to add/edit order request
     *
     * @return   array of messages
     */
    private function OrderAddMessages($data = array()){
        return [
            'client_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client '),
            'billing_address_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Address'),
            'client_contact_person_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Contact '),
            'shipping_address_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Shipment Address'),
            'sales_user_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Sales User'),
            'product_head.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Product Head'),
            'transporter.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Transporter'),
            'route_area.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Route Area'),
            'payment_due_days.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Payment Due Days'),
            'due_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Payment Due Date'),
            'dispatch_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Dispatch Date'),
            'order_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Order Date'),
        ];
    }
}