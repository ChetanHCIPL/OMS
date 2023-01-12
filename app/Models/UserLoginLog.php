<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserLoginLog extends Model
{
    protected $table = 'user_login_log'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Function: Add User login log
     *
     * @param    array 	$insert_array
     * @return   array  (Inserted Data)     
     */
    public static function addUserLoginLog($insert_array)
    {
        return self::create($insert_array)->toArray();
    }

    /**
     * Function: Update customer login log
     *
     * @param    integer    $id
     * @param    array      $update_array
     * @return   boolean    (true/false)
     */
    public static function updateUserLoginLog($id, $update_array)
    {
        return self::where('id', $id)->update($update_array);
    }

    public static function getUserLoginData($where_arr)
    {
        $query = self::from('user_login_log');
       
        if(isset($where_arr['id']) && $where_arr['id'] != ""){
            $query->where('id', $where_arr['id']);
        }
        if(isset($where_arr['user_id']) && $where_arr['user_id'] != ""){
            $query->where('user_id', $where_arr['user_id']);
        }
        return $query;
    }


     /**
     * Get User Name
     *
     * @param string Ids
     * @return array 
    */
    public static function CountUserTotalLoginLogs($user_id_array)
    {
        $query = self::select('user_id',DB::raw('count(id) as total'));
        if(is_array($user_id_array)){
            $query->whereIn('user_id', $user_id_array);
        } 
        $query->groupBy('user_id');
        $result =  $query->get()->toArray();
        return $result;
    }

    /**
     * Function: Delete User Login Log
     *
     * @param    string     $user_id 
     * @param    string     $device_type
     * @return   boolean       
     */
    public static function deleteUserLoginLog($user_id, $device_type=null)
    {
        $query = DB::table('user_login_log');
        $query->where('user_id', $user_id);
        if(!empty($device_type) && $device_type > 0){
            $query->where('device_type', $device_type);
        }
        return $query->delete(); 
    }

    /**
     * Summary of getUserLogin
     * Get User Login
     * 
     * @param mixed $where_arr
     * @return mixed
     */
    public static function getUserLogin($where_arr)
    {
        $query = self::from('user_login_log');
       
        if(isset($where_arr['user_id']) && $where_arr['user_id'] != ""){
            $query->where('user_id', $where_arr['user_id']);
        }
        if(isset($where_arr['token']) && $where_arr['user_id'] != ""){
            $query->where('token', $where_arr['token']);
        }
        $query->orderBy('id', 'DESC');
        $data = $query->get()->toArray();
        return $data;
    }
}
