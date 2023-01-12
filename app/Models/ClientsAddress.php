<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientsAddress extends Model
{
    protected $table = 'client_address';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'title', 'address1', 'address2', 'mobile_number', 'email', 'country_id', 'state_id', 'district_id', 'taluka_id', 'zip_code', 'use_for_billing', 'use_for_shipping', 'is_default_billing', 'is_default_shipping', 'is_locked', 'is_approved', 'client_contact_person_id', 'approved_date', 'status', 'created_at', 'updated_at','is_deleted'
    ];
    public $timestamps = true; 
    /**
    * Add ClientAddress Single/Multiple
    * @param array ClientAddress Data Array
    * @return array Respose after insert
    */
    public static function addClientAddress($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update ClientAddress Single
     * @param integer Id
     * @param array ClientAddress Data Array
     * @return array Respose after Update
    */
    public static function updateClientAddress($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    /**
     * Update ClientAddress Single
     * @param integer Id
     * @param array ClientAddress Data Array
     * @return array Respose after Update
    */
    public static function updateClientAddressDefultSet($where, $update_array = array()) {
        return self::where($where)->update($update_array);
    }
    
    /**
     * Update ClientAddress  Status
     * @param array ClientAddress Ids Array
     * @param string ClientAddress Status
     * @return array ClientAddress after Update
    */
    public static function updateClientAddressById($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the ClientAddress Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array ClientAddress data array
    */

    public static function getClientAddressData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('client_address as c');
        
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
        $query->where(['is_deleted'=>0]);
        $query->groupBy('c.id');
        $result = $query->get();
    
        return $result;
    }

    /**
     * Get Single ClientAddress data
     * @param int ClientAddress Id 
     * @return array ClientAddress data
    */
    public static function getClientAddressDataFromId($id) {
        $query = self::select('client_address.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['client_address.id' => $id]);
        }
        $query->where(['is_deleted'=>0]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get all ids of client 
      * @param int Client Id 
     * @return array ClientAddress data
     */
    public static function getClientDataListofids($cid){
        $query = self::select('client_address.id');
        $query->where(['is_deleted'=>0]);
        $query->where(['client_address.client_id' => $cid]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Single ClientAddress data
     * @param int ClientAddress Id 
     * @return array ClientAddress data
    */
    public static function getListClientAddressData() {
        $query = self::select('client_address.*');
        $query->where(['is_deleted'=>0]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get ClientAddress sum data by status 
     * @return array ClientAddress data
    */
    public static function getListClientAddressStatusData() {
        $query = self::select('client_address.status',DB::raw('count(*) AS numrow'));
        $query->where(['is_deleted'=>0]);
        $result =  $query->groupBy('status')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete ClientAddress enter in is_delete
     * @param array ClientAddress Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientAddressData($id = array()) {
        return self::whereIn('id', $id)->update(['is_deleted'=>1]);
    }

    /**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientDataList($client_id = NULL) {
        $query = DB::table('client_address');
        $query->select('*');
        if ($client_id){
            $query->where(['client_id' => $client_id]);
        }
        $query->where(['is_deleted'=>0]);
        $query->where(['status' => self::ACTIVE]);
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientDataListWithStateDistrictTaluka($client_id = NULL) {
        $query = DB::table('client_address as ca');
        $query->leftjoin('mas_state AS ms','ms.id','=','ca.state_id');
        $query->leftjoin('mas_district AS md','md.id','=','ca.district_id');
        $query->leftjoin('mas_taluka AS mt','mt.id','=','ca.taluka_id');
        $query->select('ca.*','ms.state_name','md.district_name','mt.taluka_name');
        $query->where(['client_id' => $client_id]);
        $query->where(['is_deleted'=>0]);
        $query->where(['ca.status' => self::ACTIVE]);
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Client Address data
     * @param int Client ID passed
     * @param string Address Flag passed
     * @return array Respose Client Address Array
     */
    public static function getClientAddress($client_id = NULL, $address_flag = '') {
        $query = DB::table('client_address');
        
        $query->select('id','address1');
        if ($client_id){
            $query->where(['id' => $client_id]);
        }
        $query->where(['status' => 1]);

        if( $address_flag == 'billing' ) {
            $query->where(['use_for_billing' => 1]);    
        }
        else if( $address_flag == 'shipping' ) {
            $query->where(['use_for_shipping' => 1]);    
        }
        $query->where(['is_deleted'=>0]);
        $query->orderBy('address1', 'asc');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Client Address detail
     * @param int Client Address ID passed
     * @return array Respose Client Address Detail Array
     */
    public static function getClientAddressDetail($client_address_id = NULL) {
        $query = DB::table('client_address AS ca');
        
        $query->select('ca.id', 'ca.address1', 'ca.address2', 'ca.zip_code', 'mc.country_name', 'ms.state_name', 'md.district_name', 'mt.taluka_name');
        $query->leftjoin('mas_country AS mc','mc.id','=','ca.country_id');
        $query->leftjoin('mas_state AS ms','ms.id','=','ca.state_id');
        $query->leftjoin('mas_district AS md','md.id','=','ca.district_id');
        $query->leftjoin('mas_taluka AS mt','mt.id','=','ca.taluka_id');
        $query->where(['ca.is_deleted'=>0]);
        if ($client_address_id){
            $query->where(['ca.id' => $client_address_id]);
        }

        $query->limit('1');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}