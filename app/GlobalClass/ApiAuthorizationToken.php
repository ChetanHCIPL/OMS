<?php

namespace App\GlobalClass;

class ApiAuthorizationToken
{
	/**
     * Function: Check valid Authorization Token passed in API Request Header based on Entity Type(Store, DA, Supplier) and Device Type(WEB, AD, IOS)
     *
     * @param    string  $device_type
     * @param    string  $entity_type_id
     * @return   string  authorization_token
     */
	public static function checkAuthorizationToken($device_type) {
		return \DB::table('mas_authorization_token')->where('device_type', $device_type)->value('authorization_token');
	}

    /**
     * Function: Check valid Authorization Token passed in API Request Header for General API
     *
     * @param    string  $authorization_token
     * @return   array   row of matched token
     */
    public static function checkAuthorizationTokenForGeneralAPI($authorization_token) {
        return \DB::table('mas_authorization_token')->where('authorization_token', $authorization_token)->select('id')->get()->toArray();
    }    
}
