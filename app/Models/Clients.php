<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Clients extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'clients';
    protected $primaryKey = 'id';
    
    const ACTIVE     = 1;
    const VERIFIED   = 2;
    const INACTIVE   = 3;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'client_code', 'client_name', 'mobile_number', 'whatsapp_number','total_credit_limit','cash_discount1','cash_discount2','order_approval_limit','payment_term_id', 'email', 'adcn_id', 'is_dealer', 'sales_user_id', 'client_address_id', 'tally_client_name', 'dob', 'gst_no', 'pan_no', 'adhar_no', 'school_type_id', 'zip_code', 'area', 'lattitude', 'longitude', 'hide_date', 'grade_id', 'created_at', 'updated_at', 'created_by', 'status','discount_category','district_id','taluka_id','image','state_id','country_id','cash_discount1_amt','cash_discount2_amt','client_type','principal_contact_name','principal_contact_no','account_contact_name','account_contact_no','latitude','longitude','username','password'
    ];
    public $timestamps = true; 
    /**
    * Add Clients Single/Multiple
    * @param array Clients Data Array
    * @return array Respose after insert
    */
    public static function addClients($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Clients Single
     * @param integer Id
     * @param array Clients Data Array
     * @return array Respose after Update
    */
    public static function updateClients($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Clients  Status
     * @param array Clients Ids Array
     * @param string Clients Status
     * @return array Clients after Update
    */
    public static function updateClientsById($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Clients Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Clients data array
    */

    public static function getClientsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {

        $query = DB::table('clients as c');
        $query = $query->select('c.*', DB::raw("CONCAT(a.first_name,' ',a.last_name) as salespersonname"));
        $query->leftjoin('admin AS a','a.id','=','c.sales_user_id');
        
        if(isset($search) && $search != ""){
            $query->where(function ($query) use ($search) {
                $query->orWhere('c.client_name', 'like', ''.$search.'%');
                $query->orWhere('c.email', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('c.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('c.status', '=', '2');
                } 
            });
        }  
        //$query->where(['c.is_deleted' => 0]);
        $query->whereNull('c.deleted_at');
        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['couName']) && $search_arr['couName'] != '')
                $query->Where('c.client_name', 'like', ''.$search_arr['couName'].'%');
            if(isset($search_arr['email']) && $search_arr['email'] != '')
                $query->Where('c.email', 'like', ''.$search_arr['email'].'%');
            if(isset($search_arr['MobileNo']) && $search_arr['MobileNo'] != '')
                $query->Where('c.mobile_number', 'like', ''.$search_arr['MobileNo'].'%');
            if(isset($search_arr['WhatsApp']) && $search_arr['WhatsApp'] != '')
                $query->Where('c.whatsapp_number', 'like', ''.$search_arr['WhatsApp'].'%');
            if(isset($search_arr['couSales']) && $search_arr['couSales'] != '')
                $query->Where('c.sales_user_id', $search_arr['couSales']);
            if(isset($search_arr['couStatus']) && $search_arr['couStatus'] != '')
                $query->Where('c.status', $search_arr['couStatus']);
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
     * Get Single Clients data
     * @param int Clients Id 
     * @return array Clients data
    */
    public static function getClientsDataFromId($id) {
        $query = self::select('clients.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['clients.id' => $id]);
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Single Clients data
     * @param int Clients Id 
     * @return array Clients data
    */
    public static function getClientsDataFromIdWithAllDetails($id){
        $query = self::select('clients.*','md.district_name','mt.taluka_name',DB::raw("CONCAT(a.first_name,' ',a.last_name) as salespersonname"));
        $query->leftjoin('mas_district AS md','md.id','=','clients.district_id');
        $query->leftjoin('mas_taluka AS mt','mt.id','=','clients.taluka_id');
        $query->leftjoin('admin AS a','a.id','=','clients.sales_user_id');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['clients.id' => $id]);
        }
        //$query->where(['clients.is_deleted' =>0]);
        $query->whereNull('clients.deleted_at');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Single Clients data
     * @param int Clients Id 
     * @return array Clients data
    */
    public static function getListClientsData() {
        $query = self::select('clients.*');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get Clients sum data by status 
     * @return array Clients data
    */
    public static function getListClientsStatusData() {
        $query = self::select('clients.status',DB::raw('count(*) AS numrow'));
        $result =  $query->groupBy('status')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Clients
     * @param array Clients Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientsData($id = array(), $is_softDelete) {

        if( $is_softDelete == 1 ) {
            return self::whereIn('id', $id)->delete();
        }
        else {
            return self::whereIn('id', $id)->forceDelete();
        }
        //return self::whereIn('id', $id)->update(['is_deleted'=>1]);
        
    }

    /**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientDataList($client_id = NULL) {
        $query = DB::table('clients');
        $query->select('id','client_name', 'discount_category');
        if ($client_id){
            $query->where(['id' => $client_id]);
        }
        $query->where(['status' => 2]);
        //$query->where(['is_deleted' => 0]);
        $query->whereNull('clients.deleted_at');
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Client Address data
     * @param int Client ID passed
     * @param string Address Flag passed
     * @return array Respose Client Address Array
     */
    public static function getClientAddress($client_id = NULL) {
        $query = DB::table('client_address');
        
        $query->select('*');
        if ($client_id){
            $query->where(['client_id' => $client_id]);
        }
        $query->where(['status' => 1]);
        $query->where(['is_deleted' => 0]);
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
        $query->select('ca.title', 'ca.id', 'ca.address1', 'ca.address2', 'ca.zip_code', 'mc.country_name', 'ms.state_name', 'md.district_name', 'mt.taluka_name');
        $query->leftjoin('mas_country AS mc','mc.id','=','ca.country_id');
        $query->leftjoin('mas_state AS ms','ms.id','=','ca.state_id');
        $query->leftjoin('mas_district AS md','md.id','=','ca.district_id');
        $query->leftjoin('mas_taluka AS mt','mt.id','=','ca.taluka_id');

        if ($client_address_id){
            $query->where(['ca.id' => $client_address_id]);
        }

        $query->limit('1');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Client Address data
     * @param int Client ID passed
     * @param string Address Flag passed
     * @return array Respose Client Address Array
     */
    public static function getClientContacts($client_id = NULL) {
        $query = DB::table('client_contact_person');
        
        $query->select('id','full_name','mobile_number');
        if ($client_id){
            $query->where(['client_id' => $client_id]);
        }
        $query->where(['status' => 1]);

        $query->orderBy('full_name', 'asc');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
    * Get Client by admin Id
    * @param int Client ID passed
    * @return array Respose Clients List Array
    */
    public static function getClientsList($user_id = NULL, $where_arr = array()) {
        $query = self::from('clients AS clients');
        $query->leftJoin('client_address AS ca', 'clients.id', '=', 'ca.client_id');
        $query->leftJoin('mas_district AS d', 'ca.district_id', '=', 'd.id');
        if ($user_id){
          //  $query->where(['clients.sales_user_id' => $user_id]);
        }
        $query->addSelect('clients.*', 'd.district_name');
        //$query->where('clients.status','!=', 2);
        if (isset($where_arr['district_id'])){
            $query->where('clients.district_id','=', $where_arr['district_id']);
        }
        if (isset($where_arr['sales_user_id'])){
            $query->where('clients.sales_user_id','=', $where_arr['sales_user_id']);
        }
        if (isset($where_arr['status'])){
            $query->where('clients.status','=', $where_arr['status']);
        }
        if (isset($where_arr['taluka_id'])){
            $query->where('clients.taluka_id','=', $where_arr['taluka_id']);
        }
        if (isset($where_arr['state_id'])){
            $query->where('clients.state_id','=', $where_arr['state_id']);
        }
        $search_keyword = isset($where_arr['search_keyword']) ? $where_arr['search_keyword'] : '';

        if (isset($search_keyword) && $search_keyword != "") {
            $query->where(function ($query) use ($search_keyword) {
                $query->orWhere('clients.client_name', 'like', '%' . $search_keyword . '%');
            });
        }
        
        // $query->where(['clients.deleted_at' => NULL]);
        $query->whereNull('clients.deleted_at');
        if (isset($where_arr['type'])){
            if($where_arr['type'] == 'total_count'){
                //$result = $query->count();
                $query->groupBy('clients.id');
                $result = count($query->get()->toArray());
            }else{
                if($where_arr['type'] == 'paginated_data'){
                    if (isset($where_arr['records_per_page']) && $where_arr['records_per_page'] != "") {
                        $query->limit($where_arr['records_per_page']);
                    }
                    if (isset($where_arr['start']) && $where_arr['start'] != "") {
                        $query->offset($where_arr['start']);
                    }
                }     
                $query->orderBy('clients.id', 'desc'); 
                $query->groupBy('clients.id');
                $result = $query->get()->toArray();
            }
        }else{
            $query->groupBy('clients.id');
            $query->orderBy('clients.id', 'desc'); 
            $result = $query->get()->toArray();
        }

        
        
        return json_decode(json_encode($result), true);
    }
    /**
     * Summary of updateClientCode
     * Set Unique Code with Prefix
     * 
     * @param mixed $id
     * @param mixed $code
     * @return mixed
     */
    public static function updateClientCode($id,$code)
    {
        return self::where('id', $id)->update(['client_code' => DB::raw("CONCAT('".$code."', LPAD(".$id.", 10, '0'))")]);
    }

    /**
     * Function : Get User data by Email / Mobile
     * @param   : Array $where_Arr
     * @retrun  : Array with User Data True
     */
    public static function getClientDetailsforAppLogin($where_arr) {
        $query = self::from('clients');
        if(isset($where_arr['email']) && $where_arr['email'] != ""){
            $query->where('email', $where_arr['email']);
        }
        if(isset($where_arr['mobile']) && $where_arr['mobile'] != ""){
            $query->orWhere('mobile_number', $where_arr['mobile']);
        }
        if(isset($where_arr['username']) && $where_arr['username'] != ""){
            $query->orWhere('username', $where_arr['username']);
        }
        if(isset($where_arr['password']) && $where_arr['password'] != ""){
            $query->where('password', $where_arr['password']);
        }

        if(isset($where_arr['id']) && $where_arr['id'] != ""){
            $query->where('id', $where_arr['id']);
        }
        //$query->where('id', $where_arr['id']);
        
        $query->select('*');
        $query->orderBy('id', 'desc');

        // TODO Add WHERE Clause for deleted
        return $query->get()->toArray();
    
    }

    /**
     * Summary of getUserDataFromIdForAppLogin
     * Get Client data Fro Login Response
     * 
     * @param mixed $id
     * @return mixed
     */
    public static function getClientDataFromIdForAppLogin($id) {
        $query = self::select(
                    'id', 
                    'client_code',
                    'client_name', 
                    'mobile_number',
                    'whatsapp_number',
                    'email',
                    'username',
                    'is_dealer',
                    'sales_user_id',
                    'client_type',
                    'gst_no',
                    'pan_no',
                    'adhar_no',
                    'zip_code',
                    'country_id',
                    'state_id',
                    'district_id',
                    'zone_id',
                    'taluka_id',
                    'grade_id',
                    'payment_term_id',
                    'status',
                    'image',
                );
        
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['id' => $id]);
        }     
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Check to Client Exist or not for Forgot Password
     * @param string Email
     * @return array
     */
    public static function checkClientEmailExist($vEmail){
        $result = self::where('email', $vEmail)->select('id', 'client_name')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Client list with search
     */
    public static function getClientsListBySearch($name){
        $query = self::select('id','client_name');
        if(!empty($name)){
            $query->where('.client_name','Like',''.$name.'%');
        }

        $query->where(['status' => 2]);
        
        $query->whereNull('deleted_at');

        $query->limit(10);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}