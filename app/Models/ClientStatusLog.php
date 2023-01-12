<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;
class ClientStatusLog extends Model
{
    protected $table = 'client_status_log';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','client_id','old_status', 'new_status','created_at', 'created_by'
    ];

    public $timestamps = true;

    /**
    * Add ClientStatusLog Single
    * @param array ClientStatusLog Data Array
    * @return array Respose after insert
    */
    public static function addClientStatusLog($insert_array = array()) {
        return self::insert($insert_array);
    }
    
    /**
     *  @return array Respose Array
     */
    public static function getAllActiveClientStatusLogDataByClientID($clientId) {
        $query = self::select('client_status_log.*');
        if(!empty($clientId)){
            $query->where(['client_id' =>$clientId]);
        }
        $result =  $query->orderBy('id','desc')->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}