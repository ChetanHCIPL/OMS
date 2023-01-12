<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class UserSalesState extends Model
{
    protected $table = 'user_sales_state';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'state_id', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     * 
     */
    public static function addUserSalesState($insert_array = array()) {
        return self::create($insert_array);
    }
     /**
     * get User Sales State by sales user id
     * @param array User Data Array
     * @return array Respose after insert
     * 
     */
    public static function UserSalesStateByUserID($userid = array()) {
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
     * get User Sales State by sales user id
     * @param array User Data Array
     * @return array Respose after insert
     * 
     */
    public static function GetSalesStateByStateID($stateid = array()) {
        $query = self::select('*');
        if(!empty($stateid)){
            if(is_array($stateid)){
                $query->whereIn('state_id', $stateid);
            }else {
                $query->where(['state_id' => $stateid]);
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
    public static function deleteUserState($id = array()) {
        return self::whereIn('user_id', $id)->delete();
    }
    /**
     * Get state list by user id on rsm
    */
    public static function GetStateListBySalesStructure($sasid) {
        $query = self::select('*');
        if(!empty($sasid)){
            if(is_array($sasid)){
                $query->whereIn('sales_structure_id',$sasid);
            }else {
                $query->where(['sales_structure_id'=>$sasid]);
            }
        }
        $result =  $query->get()->toArray();
        return $result;
    }
    /**
     * Get state of sales users
     */
    public static function GetAllStateOfSalesUser() {
        $query = self::select('user_sales_state.user_id','s.state_name');
        $query->leftjoin('mas_state as s','user_sales_state.state_id', '=', 's.id');
        //select * from admin as a join user_sales_state as uss on uss.user_id=a.id join mas_state as s on s.id=uss.state_id
        return $query->get()->toArray();
    }
}