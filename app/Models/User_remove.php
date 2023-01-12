<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin';
    const ACTIVE    = 1;
    const INACTIVE  = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password','remember_token','mobile','image','created_at','updated_at','mobile_isd','address', 'area', 'country_code','state_code','city_code','zip','from_ip','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
    * Function :  Get All User records
    * @param   Array $length , $start,$search
    * @return  Array $userArray 
    */
    public static function getAllUser($length = NULL,$start = NULL,$search,$dir = NULL,$columSearch, $sort = NULL, $sortdir = NULL,$userRole)
    {          
         $query = DB::table('admin');
         if($userRole != '')
         {  

            $query->join('admin_access_group_permission AS a', function($join) use($userRole){
                                $join->on('a.admin_id', '=', 'admin.id');
                                $join->where('a.access_group_id', $userRole);
            });
            //$query->leftjoin('admin_access_group_permission as a', 'a.admin_id', '=', 'admin.id');      
         }
         
         if(isset($search) && $search != '')
         { 
           $query->orWhere('first_name', 'like', '%'.$search.'%');
           $query->orWhere('last_name', 'like', '%'.$search.'%');
           $query->orWhere('email', 'like', '%'.$search.'%');
           $query->orWhere('username', 'like', '%'.$search.'%');
           if(strtolower($search) == 'Active' || $search == 'active') {
                $query->orWhere('user.status', '=',self::ACTIVE);            
            }else if(strtolower($search) == 'inactive' || strtolower($search) == 'Inactive') {
                $query->orWhere('user.status', '=', self::INACTIVE);
            }
         }
         if(!empty($columSearch))
         {
            //p($columSearch);
            if(isset($columSearch['UserName']) && $columSearch['UserName'] != '')
            {
                $query->where('username', 'like', '%'.$columSearch['UserName'].'%');
            }
            
            if(isset($columSearch['Email']) && $columSearch['Email'] != '')
            {
                $query->where('email', 'like', '%'.$columSearch['Email'].'%');
            }
            // if(isset($columSearch['Name']) && $columSearch['Name'] != '')
            // {
            //    // $query->where('email', 'like', '%'.$columSearch[3]['search']['value'].'%');
            // }
            // if(isset($columSearch['Name']) && $columSearch['Name'] != '')
            // {
            //    // $query->where('email', 'like', '%'.$columSearch[3]['search']['value'].'%');
            // }
            if(isset($columSearch['Status']) && $columSearch['Status'] != '')
            {
               $query->where('status', '=', $columSearch['Status']);
            }
            if(isset($columSearch['Name']) && $columSearch['Name'] != '')
            {
                $query->orWhere('first_name', 'like', '%'.$columSearch['Name'].'%');
                $query->orWhere('last_name', 'like', '%'.$columSearch['Name'].'%');
            }
         }
         if(isset($length) && $length != '')
         { 
            $query->limit($length);
         } 
         if(isset($start) && $start != '')
         {
            $query->offset($start);
         }

         if(isset($dir) && $dir != '')
         {
            $dir = 'desc';
         } 
         if (isset($sort) && $sort != "" && isset($sortdir) && $sortdir != "") {
            $query->orderBy($sort, $sortdir);
         }
         $userData =  $query->get()->toArray();
         return $userData;
    }   
    /* 
    * Function :  Form validation
    * @param  :  Array  $requestData
    * @return :  Array  validation
    */
    public static function validation($requestData)
    {

        if($requestData['mode'] == 'Update')
        {
            $validation = Validator::make($requestData,[
                'first_name'=>'required', 
                'last_name'=>'required', 
                'email'=>'required|email|unique:admin,email,' . $requestData['id'] . ',id', 
                'username'=>'required|unique:admin,username,' . $requestData['id'] . ',id', 
                'acess_groupid' => 'required', 
                ],[
                    'first_name.required' => ' The first name field is required.',
                    'last_name.required' => ' The last name field is required',
                    'email.required' => ' The email field is required',
                    'email.email' => ' Invalid email address',
                    'username.required' => ' The user name field is required.', 
                    'acess_groupid.required' => ' The access group field is required.',  
                    'mobile.required' => ' The mobile number field is required',
                    'email.unique' => '  The email field is unique',

            ]);
        }else{
            $validation = Validator::make($requestData,[
                'first_name'=>'required', 
                'last_name'=>'required', 
                'email'=>'required|email|unique:admin,email', 
                'username'=>'required|unique:admin,username',
                'password'=>'required|min:6',
                'acess_groupid' => 'required',  
                'mobile'=>'required',  
                ],[
                    'first_name.required' => ' The first name field is required.',
                    'last_name.required' => ' The last name field is required',
                    'email.required' => ' The email field is required',
                    'email.email' => ' Invalid email address',
                    'email.unique' => '  The email field is unique',
                    'username.required' => ' The first name field is required.',
                    'password.required' => ' The first name field is required.',
                    'password.min' => ' The password must be at least 6.',
                    'username.required' => ' The user name field is required.', 
                    'username.unique' => '  The user field is unique',
                    'acess_groupid.required' => ' The access group field is required.',  
                    'mobile.required' => ' The mobile number field is required', 
            ]);
        }
        return $validation;
    }
    /*
    * Function : save User data
    * @param :  Array  $data
    * @return  :  int $userid
    */
    public static function saveData($data)
    { 
        $data = self::create($data); 
        return $data;
    }

    /*
    * Function : delete User data
    * @param :  int  $id
    */
    public static function deleteData($id)
    { 
        return self::where(['id'=>$id])->delete();  
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
        //echo $result =  $query->toSql();exit();

        return json_decode(json_encode($result), true);
    }
    
    
    /*
    * Function : Update User data
    * @param :  int  $id
    * @return :  boolen true
    */
    public static function updateData($data,$id)
    { 
        self::where(['id'=>$id])->update($data); 
        return true;
    }

    /* 
    * Function :  delete multiple records
    * @param  :  Array  $ids
    * @return :  true
    */
    public static function deletMultipleRecords($ids)
    {
         self::whereIn('id',$ids)->delete();
         return true;
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
}