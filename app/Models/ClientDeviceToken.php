<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ClientDeviceToken extends Model
{
    protected $table = 'client_device_token';

    const WEB_EDOS  = 1;
    const AD_EDOS   = 2;
    const IOS_EDOS  = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Function: Add new user device token
     *
     * @param    array 	$insert_array
     * @return   array       
     */
    public static function addClientDeviceToken($insert_array)
    {
        return self::create($insert_array)->toArray();
    }

    /**
     * Function: Update user device token
     * @param    array    $id
     * @param    array    $update_array
     * @return   boolean       
     */
    public static function updateClientDeviceToken($id, $update_array)
    {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Function: Delete user device token
     *
     * @param    string     $ref_entity_id
     * @param    string     $entity_type_id
     * @param    string     $device_type
     * @return   boolean       
     */
    public static function deleteClientDeviceToken($user_id)
    {
        return self::where('user_id', $user_id)->delete();
    }

    /**
     * Function: Get user device token
     *
     * @param    array  $where_array
     * @return   array  $data       
     */
    public static function getClientDeviceToken($where_array)
    {
        $query = self::select('*');

        if(isset($where_array['device_type']) && $where_array['device_type'] != ""){
            $query->where('device_type', $where_array['device_type']);
        }
        if(isset($where_array['token']) && $where_array['token'] != ""){
            $query->where('token', $where_array['token']);
        }
        $query->orderBy('id', 'DESC');
        $data = $query->get()->toArray();
        return $data;
    }

}
