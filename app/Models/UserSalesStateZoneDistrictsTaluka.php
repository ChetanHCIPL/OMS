<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class UserSalesStateZoneDistrictsTaluka extends Model
{
    protected $table = 'user_sales_state_zone_districts_taluka';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'state_id','zone_id','district_id','taluka_id','created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     * UserSalesStateZoneDistricts::addUserSalesStateZoneDistricts();
     */
    public static function addUserSalesStateZoneDistrictsTaluka($insert_array = array()) {
        return self::create($insert_array);
    }
    /**
     * Delete User Sales State 
     * @param array User Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUserStateZoneDistrictsTaluka($id = array()) {
        return self::whereIn('user_id', $id)->delete();
    }
    /***
     * get Taluka list by user id 
     * * @param Single User Id
     * @return array Respose Array
     */
    public static function GetUserTalukalistByuserid($userid){
        return self::where('user_id', $userid)->get()->toArray();
    }
    /**
     * Get All Taluka list 
     */
    public static function getAllTalukaListUsers(){
        $query = self::select('user_sales_state_zone_districts_taluka.user_id','t.taluka_name');
        $query->leftjoin('mas_taluka as t','user_sales_state_zone_districts_taluka.taluka_id', '=', 't.id');
        //select * from admin as a join user_sales_state as uss on uss.user_id=a.id join mas_state as s on s.id=uss.state_id
        return $query->get()->toArray();
    }
}