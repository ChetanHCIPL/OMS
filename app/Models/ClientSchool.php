<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientSchool extends Model
{
    protected $table = 'client_school_type';
    protected $primaryKey = 'id';
    
    const ACTIVE     = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'client_id','school_type_id','status'
    ];
    public $timestamps = false; 

    /**
     * Get Client School data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
     public static function addClientSchoolType($insert_array = array()) {
        return self::create($insert_array);
    }


    /**
     * Update Clients Single
     * @param integer Id
     * @param array Clients Data Array
     * @return array Respose after Update
    */
    public static function updateClientSchoolType($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
     /**
     * Get all ids of client 
      * @param int Client Id 
     * @return array ClientAddress data
     */
    public static function getClientSchoolIdsByClientId($cid){
        $result = self::where(['client_id' => $cid])->pluck('school_type_id AS id')->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Client School Types
     * @param array Client Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientSchoolTypesData($client_id) {
        return self::where('client_id', $client_id)->delete();
    }
}