<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'admin';

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
        'user_type', 'access_group_id', 'ref_user_id', 'first_name', 'last_name', 'email', 'username', 'password', 'designation_id', 'remember_token', 'mobile','image','address', 'area', 'country_id','state_id','district_id', 'taluka_id', 'parent_id', 'zone_id','zip','from_ip', 'tot_login', 'last_access', 'is_ip_auth','status','created_at','updated_at', 'sales_structure_id', 'adhar_no', 'designation_id', 'whatsapp_number', 'area', 'remark', 'otp', 'expired_date', 'deleted_at'
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
     * Summary of getActiveMember
     * 
     * @return mixed
     */
    public static function getActiveMember()
    {
        $query = self::select('admin.*'); 
        $query->where('status','1');
        $query->whereNull('deleted_at');
        $result =  $query->get()->toArray();
      
        return json_decode(json_encode($result), true);
    }

    /** 
    *Get the Access Group
    * @param integer Display Length
    * @param integer Display Start
    * @param string  Sort order field
    * @param string  Sort order Type ASC|DSC
    * @param string  Searching Value
    * @param array   Searching array fields and its serching value
    * @param integer Admin Id
    * @return array Access Group data array
    */
    public static function getAdminData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array(), $admin_id = NULL) {
        $query = DB::table('admin');
        $query->select('admin.*', 'ag.access_group AS rolename',DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        //$query->leftjoin('admin_access_group_permission AS a','a.admin_id', '=', 'admin.id');
        $query->leftjoin('admin_access_group AS ag','ag.id', '=', 'admin.access_group_id');
        
        $query->whereNull('admin.deleted_at');

        if(isset($search) && $search != ''){ 
            $query->where(function ($query) use ($search) {
                $query->orWhere('admin.first_name', 'like', '%'.$search.'%');
                $query->orWhere('admin.last_name', 'like', '%'.$search.'%');
                $query->orWhere('admin.email', 'like', '%'.$search.'%');
                $query->orWhere('admin.username', 'like', '%'.$search.'%');
                if(strtolower($search) == 'active' ) {
                    $query->orWhere('admin.status', '=',self::ACTIVE);            
                }else if(strtolower($search) == 'inactive' ) {
                    $query->orWhere('admin.status', '=', self::INACTIVE);
                }
            });
        }
        if(!empty($search_arr) && count($search_arr) > 0)
        {
            if(isset($search_arr['UserName']) && $search_arr['UserName'] != '')
            {
                $query->where('admin.username', 'like', '%'.$search_arr['UserName'].'%');
            }
            
            if(isset($search_arr['Email']) && $search_arr['Email'] != '')
            {
                $query->where('admin.email', 'like', '%'.$search_arr['Email'].'%');
            }
            if(isset($search_arr['Status']) && $search_arr['Status'] != '')
            {
               $query->where('admin.status', '=', $search_arr['Status']);
            }
            if(isset($search_arr['Name']) && $search_arr['Name'] != '')
            {
                $query->where(DB::Raw('CONCAT(first_name, " ", last_name)'), 'like', '%' .$search_arr['Name']. '%');
            }
            if(isset($search_arr['acess_groupid']) && $search_arr['acess_groupid']!='')
            {
                $query->where('admin.access_group_id','=',$search_arr['acess_groupid']);
            }
        }
        if (isset($admin_id) && $admin_id != "") {
            $query->Where('admin.id', $admin_id);
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
        //$query = self::select('admin.*');
        $query = self::select(
                'admin.id', 
                'admin.user_type', 
                'admin.access_group_id', 
                'admin.first_name', 
                'admin.last_name', 
                'admin.email', 
                'admin.mobile', 
                'admin.sales_structure_id',  
                'admin.image', 
                'admin.country_id', 
                'admin.state_id', 
                'admin.tot_login', 
                'admin.last_access', 
                'admin.status', 
                'admin.district_id', 
                'admin.designation_id', 

                DB::raw('group_concat(admin_access_group_permission.access_group_id) as access_group_id_arr'));
        $query->leftjoin('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'admin.id');
        $query->groupBy('admin.id');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['admin.id' => $id]);
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
        //$query = self::select('admin.*');
        $query = self::select('admin.*', DB::raw('group_concat(admin_access_group_permission.access_group_id) as access_group_id_arr'), DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->leftjoin('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'admin.id');
        $query->groupBy('admin.id');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['admin.id' => $id]);
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
        $result = self::select('admin.*')->where('status', self::ACTIVE)->get()->toArray();
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
     * @param integer Admin Id
     * @return array
     */
    public static function checkAdminStatus($admin_id){
        return self::where('id',$admin_id)
            ->where('status',self::ACTIVE)
            ->get()->toArray();
    }

    /**
     * Check to User Name
     * @param integer Admin Id
     * @return array  User's Name array
     */
    public static function getAdminNameFromId($id) {
        $query = self::select(DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->where(['admin.id' => $id]);
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
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
    public static function deletRecords($ids, $is_softDelete = NULL)
    {
        if( $is_softDelete == 1 ) {
            if(is_array($ids)){
               return self::whereIn('id',$ids)->delete();
            } else {
              return self::where('id',$ids)->delete();  
            }
        }
        else {
            if(is_array($ids)){
               return self::whereIn('id',$ids)->forceDelete();
            } else {
              return self::where('id',$ids)->forceDelete();  
            }
        }
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
    public static function getSalesUsersList(){
        $query = DB::table('admin');
        $query->leftjoin('sales_structure AS ss','ss.id', '=', 'admin.sales_structure_id');
        $query->select('admin.id', DB::raw('CONCAT(first_name, " ", last_name) AS name'), 'ss.short_name','admin.sales_structure_id');
        $query->where(['admin.user_type' => self::USER_TYPE_SALES_USER]); //Sales Users
        $query->where(['admin.status' => self::ACTIVE]);
        $query->whereNull('admin.deleted_at');
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
    public static function getAdminDeviceToken($where_array)
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
        //$query = self::select('admin.*');
        $query = self::select('admin.*');
        $query->where('email', $username);
        $query->orWhere('username', $username);
        $query->orWhere('mobile', $username);
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
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
    /**
     * join sales user relationship
     * * @param int User id 
     * @return array
     */
    public static function getUserListBySalesStructure($salesId=[]) {
        $query=self::select('admin.id','usz.zone_id','z.zone_name',DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        $query->leftjoin('sales_users_relationship AS sr','sr.user_id', '=', 'admin.id');
        $query->leftjoin('user_sales_state_zone AS usz','usz.user_id', '=', 'admin.id');
        $query->leftjoin('mas_zone AS z','z.id', '=', 'usz.zone_id');
        if(!empty($salesId)){
            foreach($salesId as $key=>$w){
                $query->where('sr.'.$key,$w);
            }
        }
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
}