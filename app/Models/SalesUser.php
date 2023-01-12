<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SalesUser extends Authenticatable
{
    use Notifiable;

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
    * @param integer Admin Id
    * @return array Access Group data array
    */
    public static function getSalesUserData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array(), $admin_id = NULL) {
        $query = DB::table('admin');
        $query->select('admin.*', 'ag.access_group AS rolename',DB::raw('CONCAT(first_name, " ", last_name) AS name'));
        //$query->leftjoin('admin_access_group_permission AS a','a.admin_id', '=', 'admin.id');
        $query->leftjoin('admin_access_group AS ag','ag.id', '=', 'admin.access_group_id');
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
     * Delete User Status
     * @param array User Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUser($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
    
    /**
     * Get Single User data For API Login
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
    public static function getSalesUserDataFromId($id) {
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
    public static function addSalesUser($insert_array = array()) {
        return self::create($insert_array);
    }
   
    /**
     * Update User Single
     * @param integer User Id
     * @param array User Data Array
     * @return array Respose after Update
     */
    public static function updateSalesUser($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /*
    * Function : Get User data
    * @param :  int  $id
    * @return :  Array $data
    */
    public static function getSalesUser($id)
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
           return  self::whereIn('id',$ids)->delete();
        } else {
          return  self::where('id',$ids)->delete();  
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
    public static function getSalesUserListBySalesStructure($salesId=[]) {
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
    public static function getSalesUserBySalesStructure($ssids) {
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
     * Function : Get User data by Email / Mobile
     * @param   : Array $where_Arr
     * @retrun  : Array with User Data True
     */
    public static function getSalesUserDetailsforAppLogin($where_arr) {
        $query = self::from('admin');
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
}