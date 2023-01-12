<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class UserSalesStateZoneDistricts extends Model
{
    protected $table = 'user_sales_state_zone_districts';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'state_id','zone_id','district_id','created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     * UserSalesStateZoneDistricts::addUserSalesStateZoneDistricts();
     */
    public static function addUserSalesStateZoneDistricts($insert_array = array()) {
        return self::create($insert_array);
    }
    /**
     * Get User Sales State Districts
     * @param array User Data Array
     * @return array Respose after insert
     * UserSalesStateZoneDistricts::addUserSalesStateZoneDistricts();
     */
    public static function UserSalesStateZoneDistrictsByUserID($userid = array()) {
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
     * Delete User Sales State 
     * @param array User Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUserStateZoneDistricts($id = array()) {
        return self::whereIn('user_id', $id)->delete();
    }
    /**
     * Get All Districts list 
     */
    public static function getAllDistrictsListUsers(){
        $query = self::select('user_sales_state_zone_districts.user_id','d.district_name');
        $query->leftjoin('mas_district as d','user_sales_state_zone_districts.district_id', '=', 'd.id');
        //select * from admin as a join user_sales_state as uss on uss.user_id=a.id join mas_state as s on s.id=uss.state_id
        return $query->get()->toArray();
    }
}