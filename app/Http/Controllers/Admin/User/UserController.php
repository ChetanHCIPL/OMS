<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\GlobalClass\Design;
use Config;
use Illuminate\Support\Facades\Response;
use Validator;
use Image;
use Auth;
use DB;
use App\Models\Admin;
use App\Models\Country;
use App\Models\State;
use App\Models\Districts;
use App\Models\Taluka;
use App\Models\AccessGrouppermission;
use App\Models\AccessGroup;
use App\Models\Ip;
use App\Models\AdminIpAccess;

class UserController extends AbstractController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('accessrights');
        $this->size_array = Config::get('constants.user_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->countryPath = Config::get('path.country_path');
        $this->img_max_size_MB = @(Config::get('constants.IMG_MAX_SIZE')*0.000001);
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->destinationPath = Config::get('path.user_path');

        $this->is_softDelete = 1;
    }
    /**
     * Show the Admin users
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function grid($acess_groupid = NULL)
    { 
        
        $dateFormate = config::get('constants.DATE_PICKER_FORMAT');
        $acess_groupid = (isset($acess_groupid) && $acess_groupid!= '')?substr($acess_groupid, 3, -3):"";

        return view('admin.user.list')->with(['dateFormate'=>$dateFormate , 'acess_groupid' => $acess_groupid ]);
    }

    /**
    *
    * Function : User listing 
    * @return  json $response
    *
    */
    public function userajaxlist(Request $request)
    {  
        ##Variable Declaration
        $response['data']  = $checkImgArr=$data =$search = array();   
        $getData = $request->all();
        $search =  isset($getData['search']) && $getData['search'] != '' ? $getData['search']['value'] : '';
        
        //$coloumSearch = $getData['columns'];
        $coloumSearch = array();
        if(isset($getData['columns'][0]['search']['value'])){
          $coloumSearch = getAdvanceSearchFilterColsData($getData['columns'][0]['search']['value']);
        }
       $coloumSearch['acess_groupid'] = (isset($getData['user_role']) && $getData['user_role'] != '')?$getData['user_role']: "";
        $totalRecords = Admin::getAdminData($length = NULL,$start = NULL,$sort = NULL,$sortdir = NULL,$search,$coloumSearch);
        $iTotalRecords = count($totalRecords);
        $draw = $getData['draw'];
        $length = (isset($getData['length']) ? intval($getData['length']) : 50);
        $length = $length < 0 ? $iTotalRecords : $length;
        $start = (isset($getData['start']) ? intval($getData['start']) : 0);
        $sorton = (isset($getData['order'][0]['column']) ? $getData['order'][0]['column'] : "");
        $sortdir = (isset($getData['order'][0]['dir']) ? $getData['order'][0]['dir'] : 'ASC');
        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "2":
                    $sort = "name";
                    break;
                case "3":
                    $sort = "admin.email";
                    break;
                case "4":
                    $sort = "admin.username";
                    break;
                case "5":
                    $sort = "ag.access_group";
                    break;
                case "6":
                    $sort = "admin.last_access";
                    break;
                case "8":
                    $sort = "admin.status";
                    break;
                default:
                   $sort = "admin.id";
            }
        } else {
           $sort = "admin.id";
        } 
        $userListData = Admin::getAdminData($length,$start,$sort,$sortdir,$search,$coloumSearch);
        for ($i = 0; $i < count($userListData); $i++) {
            $edit = $view = $delete=$lastAcessDate=$status ='';
            ##  Image popup
            $image = (isset($userListData[$i]->image) && $userListData[$i]->image!="") ? $userListData[$i]->image : '';
            $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array));  
            $user_image = '<a class="fancybox"  rel="gallery1" href="'.$checkImgArr['fancy_box_url'].'" title="" ><img class="rounded-circle"  onerror="isImageExist(this)" noimage="80x80.jpg" src="'.$checkImgArr['img_url'].'" title="'. $userListData[$i]->image.'" /></a>';
            $status =  $userListData[$i]->status == '1' ? 'Active' : 'Inactive';
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color); 
            $link = route('user',['mode'=>'edit','id'=>base64_encode($userListData[$i]->id)]);
            $delLink = route('user',['mode'=>'delete','id'=>$userListData[$i]->id]);
            $conformation = "return confirm('Are you sure you want to delete?')";
            if (per_hasModuleAccess('Users', 'Edit')) {
                $edit = ' <a href="' . route('user',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
                if($userListData[$i]->user_type==2){
                    $edit = ' <a href="' . route('salesuser',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
                }
                $name = ' <a href="' . route('user',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" >'.$userListData[$i]->name.'</a>';
                $user_name = ' <a href="' . route('user',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" >'.$userListData[$i]->username.'</a>';
            }else{
                $name = $userListData[$i]->name;
                $user_name = $userListData[$i]->username;
            }
                // $delete = '<a href="' . route('user',['mode' => 'delete','id' =>base64_encode($userListData[$i]->id)]) . '" title="Delete"  onclick="'.$conformation.'"  ><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-times"></i></span></a> ';
            if(per_hasModuleAccess('Users', 'View')){
                $view = '<a href="' . route('user',['mode' => 'view', 'id' => base64_encode($userListData[$i]->id)]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
                if($userListData[$i]->user_type==2){
                    $view = '<a href="' . route('salesuser',['mode' => 'view', 'id' => base64_encode($userListData[$i]->id)]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
                }
            }
            $lastAcessDate = $userListData[$i]->last_access != '' ? date_getDateTimeAll($userListData[$i]->last_access) : '-';
            $rolename = $userListData[$i]->rolename;
           // echo "<pre>";print_r($userListData[$i]);
            $data[] = array(
                '<input type="checkbox" name="row_' . $i . '" value="'.$userListData[$i]->id.'" class="userchkbox" name="checked"/>',
                $user_image,
                $name,
                $userListData[$i]->email,
                $user_name,
                $rolename ,
                $lastAcessDate,
                $userListData[$i]->tot_login,
                $status,
                $view.$edit
            );
        }

        $response['data']  = $data;
        $response["recordsFiltered"] = $iTotalRecords;
        $response["recordsTotal"] = $iTotalRecords;
        $response["draw"] = $draw;
        
        $response = json_encode($response);
        return $response; 
    }
    /**
     * Add User  / Edit User / Delete User
     * 
     * @param  var $mode , int $id
     * @return  
     */
    public function add($mode=NULL,$id=NULL)
    {
        $checkImgArr= $countryData = $countryDatas =$stateDatas=$access_group_data=$districtsDatas=$ip_data= $admin_ip_data= $admin_ip_data_arr= $stateData = $districtsData =$talukaDatas = array();
        $countryData = Country::getAllCountryData();
        $country = Country::getCountryISDCodeData(['status'=>Country::ACTIVE]);
        $default_isd = Config::get('settings.DEFAULT_ISD');
        $default_country = Config::get('settings.DEFAULT_COUNTRY');
        $image_extention = implode(', ',$this->img_ext_array); 
        /*if(!empty($country)){
            $cnt = count($country);
            for ($i = 0; $i < $cnt; $i++) {
                $countryImgUrl = asset('/images/country') .'/1_'. $country[$i]['flag'];

                if (isset($country[$i]['flag']) && $country[$i]['flag'] != "" && file_exists($this->countryPath.'1_'.$country[$i]['flag'])) {
                    $src = $countryImgUrl;
                } else {
                    $src = asset("/images/no_image/no_image.png");
                }
                $country[$i]['flag'] = $src;
            }
        }*/
        #get all active accessgroup data
        $access_group_data = AccessGroup::getActiveAccessGroup();
        if(isset($mode) && $mode != ""){
            $id = base64_decode($id);
            $ip_data = Ip::getActiveIp($id);
            $admin_ip_data = AdminIpAccess::getadmninIpId($id);
            if(!empty($admin_ip_data)){
                foreach ($admin_ip_data as $adkey => $advalue) {
                     $admin_ip_data_arr[] = $advalue['ip_id'];
                }
            }
            if (isset($id) && $id != "") {
                
                $userData = Admin::getUserDataFromId($id); 

                if($mode == "edit") {
                    $mode = 'Update'; 
                    if(isset($userData[0]['country_id']) && !empty($userData[0]['country_id']))
                    {
                        $stateData = State::getStateCountryWise($userData[0]['country_id']);
                    } 
                    if(isset($userData[0]['state_id']) && !empty($userData[0]['state_id']))
                    {
                        $districtsData = Districts::getDistrictsStateWise($userData[0]['state_id']);
                    }
                    $all_taluka = Taluka::getTalukaDataByDistrictsID($userData[0]['district_id']);
                    ## Check Image is Exist or not
                    $image = (isset($userData[0]['image']) && $userData[0]['image'] !='')?$userData[0]['image'] : '';
                    if($image != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array));
                    }
                    return view('admin.user.add')->with(['mode'=>$mode,'countryData'=>$countryData,'userData'=>$userData[0],'stateData'=>$stateData,'districtsData'=>$districtsData,'country'=>$country,'access_group_data' => $access_group_data,'userImage'=>$checkImgArr, 'default_isd' => $default_isd ,'default_country' => $default_country,'all_taluka'=>$all_taluka,'ip_data'=>$ip_data,'admin_ip_data_arr'=>$admin_ip_data_arr,'img_ext_array' => $image_extention]);
                } elseif ($mode == "view") {
                    $mode = "View";
                    $accessgroup_data  = array();
                    if(!empty($userData)){
                       ## Check Image is Exist or not
                        $image = (isset($userData[0]['image']) && $userData[0]['image'] !='')?$userData[0]['image'] : '';
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array));
                        //var_dump($userData[0]);
                        if(isset($userData[0]['country_id']) && !empty($userData[0]['country_id'])){
                            $countryData = Country::getCountryNameById($userData[0]['country_id']);
                            if(!empty($countryData[0])){
                                $countryDatas = $countryData[0];
                            }else{
                                $countryDatas = array();
                            }
                            if(isset($userData[0]['state_id']) && !empty($userData[0]['state_id'])){
                                $stateData = State::getStateCountryWise($userData[0]['country_id'],$userData[0]['state_id']);
                                if(!empty($stateData[0])){
                                    $stateDatas = $stateData[0];
                                }else{
                                    $stateDatas = array();
                                }                                
                                if(isset($userData[0]['district_id']) && !empty($userData[0]['district_id'])){
                                    $districtsData = Districts::getDistrictsStateWise($userData[0]['state_id'],$userData[0]['district_id']);
                                    if(!empty($districtsData[0]))
                                    {
                                        $districtsDatas = $districtsData[0];
                                    }else{
                                        $districtsDatas = array();
                                    }
                                    if(isset($userData[0]['taluka_id']) && !empty($userData[0]['taluka_id'])){
                                        $talukaData=Taluka::getAllActiveTaluka($userData[0]['taluka_id']);
                                        if(!empty($talukaData[0]))
                                        {
                                            $talukaDatas = $talukaData[0];
                                        }else{
                                            $talukaDatas = array();
                                        }
                                    }
                                }  
                            }  
                        } 
                        $accessgroup_data = AccessGroup::getActiveAccessGroup($userData[0]['access_group_id_arr']);                       
                    }
                    return view('admin.user.view')->with(['mode'=>$mode,'countryData'=>$countryDatas,'userData'=>$userData,'stateData'=>$stateDatas,'districtsData'=>$districtsDatas,'talukaData'=>$talukaDatas,'country'=>$country,'access_group_data' => $accessgroup_data,'userImage' => $checkImgArr,'ip_data'=>$ip_data,'img_ext_array' => $image_extention]);
                }

            } elseif ($mode == 'add') {
                $mode = 'Add';   
            
                // $access_group_data = AccessGroup::getActiveAccessGroup();
             
                return view('admin.user.add')->with(['mode'=>$mode,'countryData'=>$countryData,'country'=>$country,'access_group_data' => $access_group_data,'default_isd' => $default_isd,'default_country' => $default_country,'ip_data'=>$ip_data,'img_ext_array' => $image_extention]);
            }
        }
        abort(404);
    } 

    /**
    *
    * Function : User data save 
    * @param  array $request , int $id
    * @return  array $response
    *
    */
    public function save(Request $request,$id=NULL)
    {
        
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->all();
        $module_id = Config::get('constants.module_id.user');
        if($post['mode'] == 'Add'){
           $validation = $this->validateAdd($post); 
            if ($validation->fails()) {
                $validate_msg = setValidationErrorMessageForPopup($validation); 
                if (isset($validate_msg) && $validate_msg != "") {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = $validate_msg;
                    return Response::json($result_arr);
                }
            }
            if($request->has('image')){
                $image = $request->file('image');
            } 
            $result_arr = $this->insertRecord($post, $image);
            
            return Response::json($result_arr);
        }else if($post['mode'] == 'Update'){   //p($post);
            $validation = $this->validateEdit($post); 
            if ($validation->fails()) {
                $validate_msg = setValidationErrorMessageForPopup($validation); 
                if (isset($validate_msg) && $validate_msg != "") {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = $validate_msg;
                    return Response::json($result_arr);
                }
            }
            if($request->has('image')){
                $image = $request->file('image');
            }
            $result_arr = $this->updateRecord($post, $image);
            return Response::json($result_arr);
        }
    } 
     ## Validation for add record
    private function validateAdd($data = array()) {
       // echo "<pre>"; print_r($data);exit();
        $rules = array(
                'first_name' => 'required|min:3|max:50',
                'last_name' => 'required|min:3|max:50',
                'email' => 'required|email|max:100|unique:admin,email,NULL,id',
                'username' => 'required|min:3|max:20|unique:admin,username,NULL,id', 
                'password' => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'',
                'acess_groupid' => 'required', 
                'mobile' => 'required|min:10|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
                'zip'=>'min:6|max:6',

            );
        $messages = array(
            'first_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'First Name'),
            'first_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'First Name'),
            'first_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'First Name'),
            'last_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Last Name'),
            'last_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Last Name'),
            'last_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Last Name'),
            'email.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'email.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'email.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email'),
            'email.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Email'),
            'username.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Username'),
            'username.min' => sprintf(Config::get('messages.validation_msg.minlength'), '5', 'Username'),
            'username.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Username'),
            'username.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Username'),
            'password.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.min' => sprintf(Config::get('messages.validation_msg.minlength'), config('constants.PASSWORD_MIN'), 'Password'),
            'password.max' => sprintf(Config::get('messages.validation_msg.maxlength'), config('constants.PASSWORD_MAX'), 'Password'),
            'password.regex' => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
            'acess_groupid.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Access Group'),
            'mobile.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Mobile Number'),
            'mobile.min' => sprintf(Config::get('messages.validation_msg.minlength'), '10', 'Mobile Number'),
            'mobile.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'Mobile Number'),
            'mobile.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Mobile Number'),
            'zip.min' => sprintf(Config::get('messages.validation_msg.minmobilelength'),'6', 'Zip Code'),
            'zip.max' => sprintf(Config::get('messages.validation_msg.maxmobilelength'),'6', 'Zip Code'),
        );
        return Validator::make($data, $rules, $messages);
    }

     ## Validation for update record
    private function validateEdit($data = array()) {
        $rules = array(
                'first_name' => 'required|min:3|max:50',
                'last_name' => 'required|min:3|max:50',
                'email' => 'required|email|max:100|unique:admin,email,'.$data['id'].',id',
                'username' => 'required|min:3|max:20|unique:admin,username,'.$data['id'] .',id',
                'password' => 'nullable|required_if:changePasswordChk,1|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'|required_with:password_confirmation|confirmed',
                'password_confirmation' => 'nullable|required_if:changePasswordChk,1',
                'acess_groupid' => 'required', 
                'mobile' => 'required|min:10|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
                'zip'=>'min:6|max:6',
            );

        $messages = array(
            'first_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'First Name'),
            'first_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'First Name'),
            'first_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'First Name'),
            'last_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Last Name'),
            'last_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Last Name'),
            'last_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Last Name'),
            'email.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email'),
            'email.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email'),
            'email.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email'),
            'email.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Email'),
            'username.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Username'),
            'username.min' => sprintf(Config::get('messages.validation_msg.minlength'), '5', 'Username'),
            'username.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Username'),
            'username.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Username'),
            'password.required_if' => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.min' => sprintf(Config::get('messages.validation_msg.minlength'), config('constants.PASSWORD_MIN'), 'Password'),
            'password.max' => sprintf(Config::get('messages.validation_msg.maxlength'), config('constants.PASSWORD_MAX'), 'Password'),
            'password.regex' => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
            'password_confirmation.required_if' =>  sprintf(Config::get('messages.validation_msg.required_field'), 'Confirmation Password'),
            'password.confirmed' =>  sprintf(Config::get('messages.validation_msg.password_confirmed')),
            'acess_groupid.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Access Group'),
            'mobile.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Mobile Number'),
            'mobile.min' => sprintf(Config::get('messages.validation_msg.minlength'), '10', 'Mobile Number'),
            'mobile.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'Mobile Number'),
            'mobile.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Mobile Number'),
            'zip.min' => sprintf(Config::get('messages.validation_msg.minmobilelength'),'6', 'Zip Code'),
            'zip.max' => sprintf(Config::get('messages.validation_msg.maxmobilelength'),'6', 'Zip Code'),
        );
        return Validator::make($data, $rules, $messages);
    }

    ## Insert Record
    private function insertRecord($data = array(), $files = array()) {
        $image = NULL;
        if (!empty($files)) {
           $fileOriname    = $files->getClientOriginalName();
            $image          = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();

            if (in_array($fileExt, $this->img_ext_array)){
                if ($fileSize > $this->img_max_size || $fileSize == 0){
                    $result[0]['isError'] = 1;
                    $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE'). '<br/> Image size must be upto '.$this->img_max_size_MB.' MB';
                    return $result;
                    exit;
                }else {
                    ## Store image in folder in multiple size
                    //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                       // storeImageinAWS($fileRealPath, $this->AWSdestinationPath, $image, $this->size_array);
                  //  }else{

                    storeImageinFolder($fileRealPath, $this->destinationPath, $image, $this->size_array);
                  //  }
                }
            } else {
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }
        $insert_array = array();
        $insert_array = array(
            'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
            'last_name' => (isset($data['last_name']) ? $data['last_name'] : ""),
            'email' => (isset($data['email']) ? $data['email'] : ""),
            'mobile' => (isset($data['mobile']) ? $data['mobile'] : ""),
            'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
            'state_id' => (isset($data['state_id']) ? $data['state_id'] : ""),
            'district_id' => (isset($data['districts_id']) ? $data['districts_id'] : ""),
            'taluka_id' => (isset($data['taluka_id']) ? $data['taluka_id'] : ""),
            'area' => (isset($data['area']) ? $data['area'] : ""),
            'zip' => (isset($data['zip']) ? $data['zip'] : ""),
            'address' => (isset($data['address']) ? $data['address'] : ""),
            'username' => (isset($data['username']) ? $data['username'] : ""),
            'password' => (isset($data['password']) ? Hash::make($data['password']) : ""),
            'status' => (isset($data['status']) ? $data['status'] : "2"), 
            'image' => (isset($image) ? $image : ""),  
            'from_ip' => getIP(),
            'tot_login' => (isset($data['tot_login']) ? $data['tot_login'] : "0"),
            'is_ip_auth' => (isset($data['is_ip_auth']) ? $data['is_ip_auth'] : "0"),
            //'is_mobile_auth' => (isset($data['is_mobile_auth']) ? $data['is_mobile_auth'] : "0"),
           // 'mobile_auth_attempt' => (isset($data['mobile_auth_attempt']) ? $data['mobile_auth_attempt'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : "2"),
            'created_at' => date('Y-m-d H:i:s'),
            'access_group_id' => $data['acess_groupid'],
            'user_type'=>1,
        );  
        $insert = Admin::addUser($insert_array);
        
        if (isset($insert)) {

            $insertedId = (isset($insert['id']) ? $insert['id'] : "");
            ## Insert Admin access Groups
                if (isset($data['acess_groupid']) && !empty($data['acess_groupid'])) { 
                    $group_array =array();
                    //foreach ($data['acess_groupid'] as $rowacess_groupid) {
                         $group_array = array(
                             'admin_id' => $insertedId,
                             'access_group_id' => $data['acess_groupid'],
                         );
                         $insert_group = AccessGrouppermission::addAccessGrouppermission($group_array);
                   //  }
                }
                ## insert access ip
                if (isset($data['ip_id']) && !empty($data['ip_id'])) { 
                    $admin_ip_access_arr = array();
                    foreach ($data['ip_id'] as $rowacess_ipid) {
                        $admin_ip_access_arr[] = array(
                             'admin_id' => $insertedId,
                             'ip_id' => $rowacess_ipid,
                        );
                    }
                    if(!empty($admin_ip_access_arr)){
                        $dlt = AdminIpAccess::deleteAdminIpAccess($insertedId);
                        $insert_admin_ip_access = AdminIpAccess::addAdminIpAccess($admin_ip_access_arr);
                    }
                }
                
                ## Start Add Activity log
                $reference_id = $insertedId;
                $module_id = Config::get('constants.module_id.user');
                $log_text = "User " . $data['first_name'] . "  ".$data['last_name']." added";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $reference_id, "log_text" => $log_text, "req_data" => $data);
                 usr_addActivityLog($activity_arr);
                ## End Add Activity log
                 
                $result[0]['isError'] = 0;
                $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');

        } else {
            $result[0]['isError'] = 1;
            $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');

        }
        return $result;
    }

    # update records
    private function updateRecord($data = array(), $files = array()) {
        $image = NULL;

        $id = $data['id'];
       // echo "<pre>d";print_r($data);exit;
 
        if (!empty($files)) {
            $fileOriname    = $files->getClientOriginalName();
            $image          = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();

            if (in_array($fileExt, $this->img_ext_array)){
                if ($fileSize > $this->img_max_size || $fileSize == 0){
                    $result[0]['isError'] = 1;
                    $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE'). '<br/> Image size must be upto '.$this->img_max_size_MB.' MB';
                    return $result;
                    exit;
                }else {
                    ## Store image in folder in multiple size
                    //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                       // storeImageinAWS($fileRealPath, $this->AWSdestinationPath, $image, $this->size_array);
                  //  }else{

                    storeImageinFolder($fileRealPath, $this->destinationPath, $image, $this->size_array);
                  //  }

                    ### old Image delete from folder
                    $old_image_name = (isset($data['user_image_old'])?$data['user_image_old']:"");
                    deleteImageFromFolder($old_image_name, $this->destinationPath, $this->size_array);
                }
            } else {
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }else{
            $image = (isset($data['user_image_old'])?$data['user_image_old']:"");
        } 
        $update_array = array(
                'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
                'last_name' => (isset($data['last_name']) ? $data['last_name'] : ""),
                'email' => (isset($data['email']) ? $data['email'] : ""),
                'mobile' => (isset($data['mobile']) ? $data['mobile'] : ""),
                'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
                'state_id' => (isset($data['state_id']) ? $data['state_id'] : ""),
                'district_id' => (isset($data['districts_id']) ? $data['districts_id'] : ""),
                'taluka_id' => (isset($data['taluka_id']) ? $data['taluka_id'] : ""),
                'area' => (isset($data['area']) ? $data['area'] : ""),
                'zip' => (isset($data['zip']) ? $data['zip'] : ""),
                'address' => (isset($data['address']) ? $data['address'] : ""),
                'username' => (isset($data['username']) ? $data['username'] : ""), 
                'image' => (isset($image) ? $image : ""),  
                'from_ip' => getIP(),
                'tot_login' => (isset($data['tot_login']) ? $data['tot_login'] : "0"),
                'is_ip_auth' => (isset($data['is_ip_auth']) ? $data['is_ip_auth'] : "0"),
            //    'is_mobile_auth' => (isset($data['is_mobile_auth']) ? $data['is_mobile_auth'] : "0"),
             //   'mobile_auth_attempt' => (isset($data['mobile_auth_attempt']) ? $data['mobile_auth_attempt'] : "0"),
                'status' => (isset($data['status']) ? $data['status'] : "2"),
                'updated_at' => date('Y-m-d H:i:s'),
                'access_group_id' => $data['acess_groupid'],
                'user_type'=>1,
            );
        if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            if(isset($data['password']) && $data['password'] != '')
            { 
                $update_array['password'] = (isset($data['password']) ? Hash::make($data['password']) : "");    
            }
        }
        $update = Admin::updateUser($id,$update_array);
        
        if ($update) { 
            ## Delete Admin access Groups 
            $delete_acessgroup=AccessGrouppermission::accessGroupDelete($id);

            ## Insert Admin access Groups  
            if (!empty($data['acess_groupid'])) {
                //foreach ($data['acess_groupid'] as $rowacess_groupid) {
                    $group_array = array(
                        'admin_id' => $id,
                        'access_group_id' =>$data['acess_groupid'],
                    );
                    $insert_group = AccessGrouppermission::addAccessGrouppermission($group_array);
                //}
            }
            ## insert access ip
            // $is_ip_auth = (isset($data['is_ip_auth']) ? $data['is_ip_auth'] : "0");
            // if($is_ip_auth == 0){
            //      $dlt = AdminIpAccess::deleteAdminIpAccess($id);
            // }
            if (isset($data['ip_id']) && !empty($data['ip_id'])) { 
                $admin_ip_access_arr = array();
                foreach ($data['ip_id'] as $rowacess_ipid) {
                    $admin_ip_access_arr[] = array(
                         'admin_id' => $id,
                         'ip_id' => $rowacess_ipid,
                    );
                }
                if(!empty($admin_ip_access_arr)){
                    $dlt = AdminIpAccess::deleteAdminIpAccess($id);
                    $insert_admin_ip_access = AdminIpAccess::addAdminIpAccess($admin_ip_access_arr);
                }
            }

            # Start Add Activity log
                $reference_id = (isset($id) ? $id : "");
                $module_id = Config::get('constants.module_id.user');
                $log_text = "User " . $data['first_name'] . "  ".$data['last_name']." updated";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $reference_id, "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
            # End Add Activity log 

            $result[0]['isError'] = 0;
            $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
            
        } else {
            $result[0]['isError'] = 1;
            $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result;
    } 

    /**
    *
    * Function : Ajax :  delete multiple User
    * @param  array $request
    *
    */
    public function multipledelete(Request $request)
    {   
        $data = $request->all();
        $response = array();
        if(!isset($data['id']))
        {
            $response[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            $response[0]['isError'] = 1;
        }else{  
            ##get user data 
            $userData = Admin::getUser($data['id']); 
            ##Delete user 
            $delete = Admin::deletRecords($data['id'], $this->is_softDelete);
            ## Delete Admin access Groups 
            $delete_acessgroup=AccessGrouppermission::accessGroupDelete($data['id']);
            if(isset($delete)) {
                if(!empty($userData))
                {
                    for($u=0;$u<count($userData);$u++)
                    {   #Delete user image
                        $image_name = (isset($userData[$u]['image'])?$userData[$u]['image']:"");
                        deleteImageFromFolder($image_name, $this->destinationPath, $this->size_array);
                    } 
                }
                $response[0]['msg'] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), count($data['id']));
                $response[0]['isError'] = 0;
            } else {
                $response[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                $response[0]['isError'] = 1;
            }
        }
        $response = json_encode($response);
        return $response;
    }

     /**
    * Update status
    * @param id,status, $id,$status
    * @return boolean
    */
    public function updatestatus(Request $request) {
        $data = $request->all(); 
        $response = array();
        $status = $data['customActionName'];
        $id = $data['id'];
        if (!empty($id)) {
            if (isset($status) && $status == "Active") {
                $status = 1;
                $statusname = 'Active';
                $data = array("status"=>$status);

                $msg =sprintf(Config::get('messages.msg.MSG_RECORD_ACTIVATED'), count($id));
            }
            elseif (isset($status) && $status == "Inactive") {
                $status = 2;
                $statusname = 'Inactive';
                $data = array("status"=>$status);
                $msg =sprintf(Config::get('messages.msg.MSG_RECORD_INACTIVATED'), count($id));
            }
            $update_status = Admin::updateStatus($id,$data);
            if(isset($update_status)){
                $response[0]['isError'] = "0";
                $response[0]['msg'] = $msg;
            }else {
                $response[0]['isError'] = "1";
                $response[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            }
        }else{
            $response[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            $response[0]['isError'] = "1";
        }
        $response = json_encode($response);
        return $response;
    }

    /**
    * FUnction Remove Image
    * @param $request 
    * @return boolean
    */
    public function removeimage(Request $request) {
        $data = $request->all();
         
        $response = array(); 
        $id = $data['id'];
        if (!empty($id)) { 
            $userData = Admin::getUser($id); 

            #image delete from table            
            $datas = array("image"=>"");
              
            Admin::updateUser($id,$datas); 

            #Image delete from folder
            $image_name = (isset($userData[0]['image'])?$userData[0]['image']:"");
            deleteImageFromFolder($image_name, $this->destinationPath, $this->size_array);
             
            $response['msg'] = "Image deleted successfully";
            $response['isError'] = 0;
        }else{
            $response['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            $response['isError'] = 1;
        } 
        $response = json_encode($response);
        return $response;
    }

    ## Post language data
    public function PostAjaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->all();
        
        if (isset($post['customActionName'])) {
            if ($post['customActionName'] == "GetStateData"){
                    $where_state_arr = array('country_id' => $post['country_id'], 'status' => State::ACTIVE);
                    $state_dd_data = State::getStateDataFromCountryId($where_state_arr);
                return Response::json($state_dd_data);
            }
            elseif ($post['customActionName'] == "GetDistrictsData"){
                $districts_dd_data = Districts::getDistrictsDataFromStateCode($post['country_code'], $post['state_code']);
                return Response::json($districts_dd_data);
            }elseif ($post['customActionName'] == "Delete"){
                $id = $post['id'][0];
                $mode = 'Delete';   
                 ##get user data 
                $userData = Admin::getUser($id); 
               
                #delete user from table
                $delete = Admin::deletRecords($id, $this->is_softDelete);
                ## Delete Admin access Groups 
                $delete_acessgroup=AccessGrouppermission::accessGroupDelete($id);   

                if(isset($delete)){
                    ## delete user image from folder
                    $image_name = (isset($userData[0]['image'])?$userData[0]['image']:"");
                    deleteImageFromFolder($image_name, $this->destinationPath, $this->size_array);

                    $response[0]['msg'] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), count( $post['id']));
                    $response[0]['isError'] = 0;
                }else {
            
                    $response[0]['isError'] = 1;
                    $response[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }
                
                $response = json_encode($response);
                return $response;
            }
        }
    }
}