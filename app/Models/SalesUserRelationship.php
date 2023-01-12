<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class SalesUserRelationship extends Model
{
    protected $table = 'sales_users_relationship';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'rsm_id','zsm_id','dy_zsm_id','asm_id','sales_structure_id','created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addSalesUsersRelationship($insert_array = array()) {
        return self::create($insert_array);
    }
    /**
     * get data from user id 
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function UserSalesRelationshipByUserID($userid = array()) {
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
    public static function deleteUserRelationship($id = array()) {
        return self::whereIn('user_id', $id)->delete();
    }
}