<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class UserSalesStateZone extends Model
{
    protected $table = 'user_sales_state_zone';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'state_id','zone_id','created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addUserSalesStateZone($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * get User Sales State zone by sales user id
     * @param array User Data Array
     * @return array Respose after insert
     * 
     */
    public static function UserSalesStateZoneByUserID($userid = array()) {
        $query = self::select('*');
        if(!empty($userid)){
            if(is_array($userid)){
                $query->whereIn('user_id', $userid);
            }else {
                $query->where(['user_id' => $userid]);
            }
        }
        $result =  $query->get()->toArray();
        return $result;
    }
    /**
     * Delete User Sales State  zone
     * @param array User Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUserStateZone($id = array()) {
        return self::whereIn('user_id', $id)->delete();
    }
    /**
     * Get zone of sales users
     */
    public static function GetZoneDetailsofusers(){
        $query = self::select('user_sales_state_zone.user_id','z.zone_name');
        $query->leftjoin('mas_zone as z','user_sales_state_zone.zone_id', '=', 'z.id');
        //select * from admin as a join user_sales_state as uss on uss.user_id=a.id join mas_state as s on s.id=uss.state_id
        return $query->get()->toArray();
    }
}