<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SalesUsers extends Authenticatable
{
    use Notifiable;

    protected $table = 'sales_users';

    const ACTIVE    = 1;
    const INACTIVE  = 2;
    const BLOCKED   = 3;

    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_SALES_USER = 2;
    const USER_TYPE_DEALER = 3;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type', 'access_group_id', 'ref_user_id', 'first_name', 'last_name', 'email', 'username', 'password', 'designation_id', 'remember_token', 'mobile','image','address', 'area', 'country_id','state_id','district_id', 'taluka_id', 'parent_id', 'zone_id','zip','from_ip', 'tot_login', 'last_access', 'is_ip_auth','status','created_at','updated_at', 'sales_structure_id', 'adhar_no', 'designation_id', 'whatsapp_number', 'area', 'remark', 'otp', 'expired_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public $timestamps = true;

    /** 
    *Get the Access Group
    * @param integer Display Length
    * @param integer Display Start
    * @param string  Sort order field
    * @param string  Sort order Type ASC|DSC
    * @param string  Searching Value
    * @param array   Searching array fields and its serching value
    * @param integer SalesUsers Id
    * @return array Access Group data array
    */
    public static function getSalesUsersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array(), $admin_id = NULL) {
        $query = DB::table('sales_users');
        $query->select('sales_users.*', 'ag.access_group AS rolename',DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->leftjoin('admin_access_group AS ag','ag.id', '=', 'sales_users.access_group_id');
        if(isset($search) && $search != ''){ 
            $query->where(function ($query) use ($search) {
                $query->orWhere('sales_users.first_name', 'like', '%'.$search.'%');
                $query->orWhere('sales_users.last_name', 'like', '%'.$search.'%');
                $query->orWhere('sales_users.email', 'like', '%'.$search.'%');
                $query->orWhere('sales_users.username', 'like', '%'.$search.'%');
                if(strtolower($search) == 'active' ) {
                    $query->orWhere('sales_users.status', '=',self::ACTIVE);            
                }else if(strtolower($search) == 'inactive' ) {
                    $query->orWhere('sales_users.status', '=', self::INACTIVE);
                }
            });
        }
        if(!empty($search_arr) && count($search_arr) > 0)
        {
            if(isset($search_arr['UserName']) && $search_arr['UserName'] != '')
            {
                $query->where('sales_users.username', 'like', '%'.$search_arr['UserName'].'%');
            }
            
            if(isset($search_arr['Email']) && $search_arr['Email'] != '')
            {
                $query->where('sales_users.email', 'like', '%'.$search_arr['Email'].'%');
            }
            if(isset($search_arr['Status']) && $search_arr['Status'] != '')
            {
               $query->where('sales_users.status', '=', $search_arr['Status']);
            }
            if(isset($search_arr['Name']) && $search_arr['Name'] != '')
            {
                $query->where(DB::Raw('CONCAT(first_name, " ", last_name)'), 'like', '%' .$search_arr['Name']. '%');
            }
            if(isset($search_arr['acess_groupid']) && $search_arr['acess_groupid']!='')
            {
             //   $query->where('sales_users.access_group_id','=',$search_arr['acess_groupid']);
            }
        }
        if (isset($admin_id) && $admin_id != "") {
            $query->Where('sales_users.id', $admin_id);
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
     * Update User Status
     * @param array  User Array
     * @param string User Status
     * @return array Respose after Update
    */
    public static function updateUserStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
    
    /**
     * Delete User Status
     * @param array User Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUser($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
    
    /**
     * Get Single User data
     * @param int User id 
     * @return array
    */
    public static function getUserDataFromIdForAppLogin($id) {
        //$query = self::select('sales_users.*');
        $query = self::select(
                'sales_users.id', 
                'sales_users.user_type', 
                'sales_users.access_group_id', 
                'sales_users.ref_dealer_id', 
                'sales_users.first_name', 
                'sales_users.last_name', 
                'sales_users.email', 
                'sales_users.mobile', 
                'sales_users.sales_structure_id',  
                'sales_users.image', 
                'sales_users.country_id', 
                'sales_users.state_id', 
                'sales_users.tot_login', 
                'sales_users.last_access', 
                'sales_users.status', 
                'sales_users.district_id', 
                'sales_users.designation_id', 

                DB::raw('group_concat(admin_access_group_permission.access_group_id) as access_group_id_arr'));
        $query->leftjoin('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'sales_users.id');
        $query->groupBy('sales_users.id');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['sales_users.id' => $id]);
        }     
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Get Single User data
     * @param int User id 
     * @return array
    */
    public static function getUserDataFromId($id) {
        //$query = self::select('sales_users.*');
        $query = self::select('sales_users.*', DB::raw('group_concat(admin_access_group_permission.access_group_id) as access_group_id_arr'), DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->leftjoin('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'sales_users.id');
        $query->groupBy('sales_users.id');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['sales_users.id' => $id]);
        }     
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
    
    /**
     * Add User Single
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addUser($insert_array = array()) {
        return self::create($insert_array);
    }
   
    /**
     * Update User Single
     * @param integer User Id
     * @param array User Data Array
     * @return array Respose after Update
     */
    public static function updateUser($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get All Active Users data
     * @return array
     */
    public static function getAllActiveUser() {
        $result = self::select('sales_users.*')->where('status', self::ACTIVE)->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Check to User Exist or not for Forgot Password
     * @param string Email
     * @return array
     */
    public static function checkEmailExist($vEmail){
        $result = self::where('email', $vEmail)->select('id', 'first_name', 'last_name')->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Check to User is Active or not
     * @param integer SalesUsers Id
     * @return array
     */
    public static function checkSalesUsersStatus($admin_id){
        return self::where('id',$admin_id)
            ->where('status',self::ACTIVE)
            ->get()->toArray();
    }

    /**
     * Check to User Name
     * @param integer SalesUsers Id
     * @return array  User's Name array
     */
    public static function getSalesUsersNameFromId($id) {
        $query = self::select(DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->where(['sales_users.id' => $id]);
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    public static function getUserData($id)
    {
        $query = DB::table('sales_users');
        $query->Where('sales_users.id', $id);
        $result = $query->get();
        return $result;
    }
/**
 * Get sales user from sales sturecture 
 */
    public static function getUserSalesStructure($id,$saless=[]){ 
        /*p($id);*/
        if(is_array($id)){
            $data = self::select('*')->whereIn('id',$id);
        }else{
            $data = self::select('*')->where(['id'=>$id]);
        } 
        if(!empty($saless)){
            if(is_array($saless)){
                $data->whereIn('sales_structure_id',$saless);
            }else{
                $data->where(['sales_structure_id'=>$saless]);
            }
        }
        return json_decode(json_encode($data->get()->toArray()),true);
    }
    /*
    * Function : Get User data
    * @param :  int  $id
    * @return :  Array $data
    */
    public static function getUser($id)
    { 
        /*p($id);*/
        if(is_array($id)){
            $data = self::select('*')->whereIn('id',$id)->get()->toArray();
        }else{

            $data = self::select('*')->where(['id'=>$id])->get()->toArray();
        } 
        return $data;
    }

    /* 
    * Function :  delete multiple records
    * @param  :  Array|int  $ids
    * @return :  true
    */
    public static function deletRecords($ids)
    {
        if(is_array($ids)){
            return self::whereIn('id',$ids)->update(['is_deleted'=>1,'deleted_date'=>date('Y-m-d H:i:s')]);
          // return  self::whereIn('id',$ids)->delete();
        } else {
        //  return  self::where('id',$ids)->delete();
        return  self::where('id',$ids)->update(['is_deleted'=>1,'deleted_date'=>date('Y-m-d H:i:s')]);  
        }
        //self::whereIn('id',$ids)->delete();
        // return true;
    }

    /* 
    * Function :  status change records
    * @param  :  Array  $ids , Array $data
    * @return :  true
    */
    public static function updateStatus($ids,$data)
    {
         self::whereIn('id',$ids)->update($data);
         return true;
    } 
    /**
     * Function : Get User data by Email / Mobile
     * @param   : Array $where_Arr
     * @retrun  : Array with User Data True
     */
    public static function getUserDetailsforAppLogin($where_arr) {
        $query = self::from('sales_users');
        if(isset($where_arr['email']) && $where_arr['email'] != ""){
            $query->where('email', $where_arr['email']);
        }
        if(isset($where_arr['mobile']) && $where_arr['mobile'] != ""){
            $query->orWhere('mobile', $where_arr['mobile']);
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
        $query->where('user_type',self::USER_TYPE_SALES_USER); //Sales Users
        $query->select('id', 'first_name','last_name','email','mobile', 'password', 'user_type', 'country_id', 'state_id', 'taluka_id', 'zip', 'district_id','sales_structure_id', 'status');
        $query->orderBy('id', 'desc');

        // TODO Add WHERE Clause for deleted
        return $query->get()->toArray();
    
    }

    /**
     * Function : Get User data by Email / Mobile
     * @param   : Array $where_Arr
     * @retrun  : Array with User Data True
     */
    public static function getSalesUsersDetails($where_arr) {
        $query = self::from('sales_users');
        if(isset($where_arr['email']) && $where_arr['email'] != ""){
            $query->where('email', $where_arr['email']);
        }
        if(isset($where_arr['mobile']) && $where_arr['mobile'] != ""){
            $query->orWhere('mobile', $where_arr['mobile']);
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
        $query->select('id', 'first_name','last_name','email','mobile', 'password', 'status');
        $query->orderBy('id', 'desc');

        // TODO Add WHERE Clause for deleted
        return $query->get()->toArray();
    
    }

    /**
    * Status
    * @return array
    */
    public static function renderStatus()
    {
        return [
            self::ACTIVE => ['label' => 'Active','code' => self::ACTIVE,'color' => config('constants.status_color.Active')],
            self::INACTIVE => ['label' => 'Inactive','code' => self::INACTIVE,'color' => config('constants.status_color.Inactive')],
            self::BLOCKED => ['label' => 'Blocked','code' => self::BLOCKED,'color' => config('constants.status_color.Blocked')]

        ];
    }

    /**
     * Get Sales Users list
     * @return array Respose Sales Users Array
     */
    public static function getSalesUsersList() {
        $query = DB::table('sales_users');
        
        $query->select('id', DB::raw('CONCAT(first_name, " ", last_name) AS name'));

        $query->where(['user_type' => self::USER_TYPE_SALES_USER]); //Sales Users
        $query->where(['status' => self::ACTIVE]);

        $query->orderBy('name', 'asc');

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Function: Get Member device token
     *
     * @param    array  $where_array
     * @return   array  $data       
     */
    public static function getSalesUsersDeviceToken($where_array)
    {
        $query = self::select('*')->orderBy('id', 'DESC');
       
        if(isset($where_array['device_type']) && $where_array['device_type'] != ""){
            $query->where('device_type', $where_array['device_type']);
        }

        if(isset($where_array['id']) && $where_array['id'] != ""){
            $query->where('id', $where_array['id']);
        }

        if(isset($where_array['token']) && $where_array['token'] != ""){
            $query->where('token', $where_array['token']);
        }

        $data = $query->take(1)->get()->toArray();
        return $data;
    }

    
    /**
     * Get Single User data By username || email
     * @param int User id 
     * @return array
    */
    public static function getUserByUsername($username) {
        //$query = self::select('sales_users.*');
        $query = self::select('sales_users.*');
        $query->where('email', $username);
        $query->orWhere('username', $username);
        $query->orWhere('mobile', $username);
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
    /**
     * Get sales user by where sales structure 
     * @param int sales structure id 
     * @return array
    */
    public static function getUserBySalesStructure($ssids) {
        $query = self::select('*');
        if(is_array($ssids)){
            $query->whereIn('sales_structure_id', $ssids);
        }else{
            $query->where('sales_structure_id', $ssids);
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * join sales user relationship
     * * @param int User id 
     * @return array
     */
    public static function getUserListBySalesStructure($salesId=[]) {
        $query=self::select('sales_users.id','usz.zone_id','z.zone_name',DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->leftjoin('sales_users_relationship AS sr','sr.user_id', '=', 'sales_users.id');
        $query->leftjoin('user_sales_state_zone AS usz','usz.user_id', '=', 'sales_users.id');
        $query->leftjoin('mas_zone AS z','z.id', '=', 'usz.zone_id');
        if(!empty($salesId)){
            foreach($salesId as $key=>$w){
                $query->where('sr.'.$key,$w);
            }
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}