<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class TransporterWay extends Model
{
    protected $table = 'transporter_way';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description','created_at', 'updated_at',
    ];

    public $timestamps = true; 
     /**
     * Add User Sales State
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addTransporterWay($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * get User Sales State zone by sales user id
     * @param array User Data Array
     * @return array Respose after insert
     * 
     */
    public static function TransporterWayByUserID($userid = array()) {
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
    public static function GetAllTransporterWay() {
        $query = self::select('*');
        $result =  $query->get()->toArray();
        return $result;
    }
}