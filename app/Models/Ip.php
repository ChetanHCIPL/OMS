<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Ip extends Model
{
    protected $table = 'mas_ip';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'ip', 'status' ,'created_at','updated_at'
    ];

    public $timestamps = false; 
     
    /**
    * Add IP Single
    * @param array IP Data Array
    * @return array Respose after insert
    */
    public static function addIp($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update IP Single
     * @param integer IP Id
     * @param array IP Data Array
     * @return array Respose after Update
    */
    public static function updateIp($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update IP  Status
     * @param array IP Ids Array
     * @param string IP Status
     * @return array IP after Update
    */
    public static function updateIpStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the IP Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array IP data array
     */

    public static function getIpData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_ip as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('description', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['ipdescription']) && $search_arr['ipdescription'] != '')
                $query->Where('description', 'like', ''.$search_arr['ipdescription'].'%');
             if(isset($search_arr['ipIp']) && $search_arr['ipIp'] != '')
                $query->Where('ip', 'like', ''.$search_arr['ipIp'].'%');
            if(isset($search_arr['ipStatus']) && $search_arr['ipStatus'] != '')
                $query->Where('mi.status', $search_arr['ipStatus']);
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
     * Get Single IP data
     * @param int IP Id 
     * @return array IP data
    */
    public static function getIpDataFromId($id) {
        $query = self::select('mas_ip.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_ip.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete IP
     * @param array IP Ids Array
     * @return array Respose after Delete
    */
    public static function deleteIpData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
     * Get IP data
     * @return array IP data
    */
    public static function getActiveIp($admin_id = NULL) {
        $query = self::select('mas_ip.*'); 
        $query->leftjoin('admin_ip_access AS aic', function($admin_ip_access_join) use($admin_id){
                $admin_ip_access_join->on('aic.ip_id', "=", 'mas_ip.id');
                $admin_ip_access_join->where('aic.admin_id', "=", $admin_id);
            });
        $query->where('mas_ip.status', self::ACTIVE);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get IP data
     * @return array IP data
    */
    public static function getadminIp($ip_id_arr) {
        $query = self::select('mas_ip.*'); 
        $query->whereIn('mas_ip.id', $ip_id_arr);
        $query->where('mas_ip.status', self::ACTIVE);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}