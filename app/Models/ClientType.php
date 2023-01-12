<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $table = 'mas_client_type';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name','status'
    ];
    public $timestamps = true; 
    /**
    * Add ClientType Single/Multiple
    * @param array ClientType Data Array
    * @return array Respose after insert
    */
    public static function addClientType($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update ClientType Single
     * @param integer Id
     * @param array ClientType Data Array
     * @return array Respose after Update
    */
    public static function updateClientType($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update ClientType  Status
     * @param array ClientType Ids Array
     * @param string ClientType Status
     * @return array ClientType after Update
    */
    public static function updateClientTypeById($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the ClientType Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array ClientType data array
    */

    public static function getClientTypeData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_client_type as c');
        
        if(isset($search) && $search != ""){
            $query->where(function ($query) use ($search) {
                $query->orWhere('client_name', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('c.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('c.status', '=', '2');
                } 
            });
        }        
        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['client_name']) && $search_arr['client_name'] != '')
                $query->Where('client_name', 'like', ''.$search_arr['client_name'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('c.status', $search_arr['status']);
        }

        if (isset($iDisplayLength) && $iDisplayLength != "") {
            $query->limit($iDisplayLength);
        }
        if (isset($iDisplayStart) && $iDisplayStart != "") {
            $query->offset($iDisplayStart);
        }
        if (isset($sort) && $sort != "" && isset($sortdir) && $sortdir != "") {
            $query->orderBy($sort, $sortdir);
        }
        
        $query->groupBy('c.id');
        $result = $query->get();
    
        return $result;
    }

    /**
     * Get Single ClientType data
     * @param int ClientType Id 
     * @return array ClientType data
    */
    public static function getClientTypeDataFromId($id) {
        $query = self::select('mas_client_type.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_client_type.id' => $id]);
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get Single ClientType data
     * @param int ClientType Id 
     * @return array ClientType data
    */
    public static function getListClientTypeData() {
        $query = self::select('mas_client_type.*');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get ClientType sum data by status 
     * @return array ClientType data
    */
    public static function getListClientTypeStatusData() {
        $query = self::select('mas_client_type.status',DB::raw('count(*) AS numrow'));
        $result =  $query->groupBy('status')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete ClientType
     * @param array ClientType Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientTypeData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientDataList() {
        $query = DB::table('mas_client_type');
        
        $query->select('*');
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

}