<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'mas_zone';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zone_code', 'zone_name', 'country_id', 'state_id', 'display_order', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     
    /**
    * Add Zone Single/Multiple
    * @param array Zone Data Array
    * @return array Respose after insert
    */
    public static function addZone($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Zone Single
     * @param integer Id
     * @param array Zone Data Array
     * @return array Respose after Update
    */
    public static function updateZone($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Zone  Status
     * @param array Zone Ids Array
     * @param string Zone Status
     * @return array Zone after Update
    */
    public static function updateZoneStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Zone Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Zone data array
    */

    public static function getZoneData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_zone as mz');
        $query->select('mz.*','mc.country_name','ms.state_name');
        $query->leftjoin('mas_country AS mc','mc.id','=','mz.country_id');
        $query->leftjoin('mas_state AS ms','ms.id','=','mz.state_id');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('zone_name', 'like', ''.$search.'%');
                $query->orWhere('zone_code', 'like', '' . $search . '%');
                if (strtolower($search) == 'active') {
                    $query->orWhere('status', '=', self::ACTIVE);
                } else if (strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', self::INACTIVE);
                }
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){

            if(isset($search_arr['zone_name']) && $search_arr['zone_name'] != '')
                $query->Where('zone_name', 'like', ''.$search_arr['zone_name'].'%');
            if (isset($search_arr['zone_code']) && $search_arr['zone_code'] != '') {
                $query->Where('zone_code', 'like', '' . $search_arr['zone_code'] . '%');
            }
            // country name 
            if (isset($search_arr['country_name']) && $search_arr['country_name'] != '') {
                $query->Where('mc.country_name', 'like', '' . $search_arr['country_name'] . '%');
            }
            // state name 
            if (isset($search_arr['state_name']) && $search_arr['state_name'] != '') {
                $query->Where('ms.state_name', 'like', '' . $search_arr['state_name'] . '%');
            }
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('mz.status', $search_arr['status']);
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
        
        $result = $query->get();

        return $result;
    }

    /**
     * Get Single Zone data
     * @param int Zone Id 
     * @return array Zone data
    */
    public static function getZoneDataFromId($id) {
        $query = self::select('mas_zone.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_zone.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Zone
     * @param array Zone Ids Array
     * @return array Respose after Delete
    */
    public static function deleteZoneData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /** Get Active Zone
    * @param integer id
    * @return Array Zone Array
    */
    public static function getAllActiveZone($id = NULL){
        if($id != '')
        {   
            $id = explode(',',$id);
            $query = self::select('*');
            if(is_array($id)){

                $query->whereIn('mas_zone.id', $id);
            }else {
               
                $query->where(['mas_zone.id' => $id]);
            } 
            $result =  $query->get()->toArray();

            return $result;

        }else{
            return self::where(['status' =>'1'])->get()->toArray();    
        }
    }

    /**
     * Function :  Get All Zone records for ajax with state id wise
     * @param : string state code
     * @return  json $zoneData
     */
    public static function getZonesStateWise($stateId) {
        $query = DB::table('mas_zone');
        
        $query->where(['state_id' => $stateId]);
        $query->orderBy('display_order');
        $zoneData = $query->get()->toArray();
        return json_decode(json_encode($zoneData), true);;
    }
}