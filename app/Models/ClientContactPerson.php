<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientContactPerson extends Model
{
    protected $table = 'client_contact_person';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'client_id', 'full_name', 'mobile_number', 'whatsapp_number', 'designation_id', 'department', 'dob', 'created_by', 'is_default', 'created_at', 'updated_at', 'status'
    ];
    public $timestamps = true; 
    /**
    * Add ClientContactPerson Single/Multiple
    * @param array ClientContactPerson Data Array
    * @return array Respose after insert
    */
    public static function addClientContactPerson($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update ClientContactPerson Single
     * @param integer Id
     * @param array ClientContactPerson Data Array
     * @return array Respose after Update
    */
    public static function updateClientContactPerson($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    /**
     * Update ClientContactPerson Single
     * @param integer Id
     * @param array ClientContactPerson Data Array
     * @return array Respose after Update
    */
    public static function updateClientPersonDefultSet($where,$update_array){
        return self::where($where)->update($update_array);
    }
    /**
     * 
     * Get all ids of client 
      * @param int Client Id 
     * @return array ClientAddress data
     */
    public static function getClientDataListofids($cid){
        $query = self::select('client_contact_person.id');
        $query->where(['client_contact_person.client_id' => $cid]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Update ClientContactPerson  Status
     * @param array ClientContactPerson Ids Array
     * @param string ClientContactPerson Status
     * @return array ClientContactPerson after Update
    */
    public static function updateClientContactPersonById($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the ClientContactPerson Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array ClientContactPerson data array
    */

    public static function getClientContactPersonData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('client_contact_person as c');
        
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
     * Get Single ClientContactPerson data
     * @param int ClientContactPerson Id 
     * @return array ClientContactPerson data
    */
    public static function getClientContactPersonDataFromId($id) {
        $query = self::select('client_contact_person.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['client_contact_person.id' => $id]);
        }
        $query->where(['is_deleted'=>0]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get Single ClientContactPerson data
     * @param int ClientContactPerson Id 
     * @return array ClientContactPerson data
    */
    public static function getListClientContactPersonData() {
        $query = self::select('client_contact_person.*');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get ClientContactPerson sum data by status 
     * @return array ClientContactPerson data
    */
    public static function getListClientContactPersonStatusData() {
        $query = self::select('client_contact_person.status',DB::raw('count(*) AS numrow'));
        $result =  $query->groupBy('status')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete ClientContactPerson
     * @param array ClientContactPerson Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientContactPersonData($id = array()) {
        return self::whereIn('id', $id)->update(['is_deleted' =>1]);
    }

    /**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientDataList($client_id = NULL) {
        $query = DB::table('client_contact_person');
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
    public static function getClientDataListWithDesignation($client_id = NULL) {
        $query = DB::table('client_contact_person as ccp');
        $query->leftjoin('mas_designation AS md','md.id','=','ccp.designation_id');
        $query->select('ccp.*','md.name as desiname');
        if ($client_id){
            $query->where(['client_id' => $client_id]);
        }
        $query->where(['is_deleted'=>0]);
        $query->where(['status' => self::ACTIVE]);
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Client Address data
     * @param int Client ID passed
     * @param string Address Flag passed
     * @return array Respose Client Address Array
     */
    public static function getClientContactPerson($client_id = NULL, $address_flag = '') {
        $query = DB::table('client_contact_person');
        
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

        $query->orderBy('address1', 'asc');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

}