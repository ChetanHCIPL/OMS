<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Models\SalesUser;
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
use App\Models\Country;
use App\Models\State;
use App\Models\Districts;
use App\Models\Taluka;
use App\Models\AccessGrouppermission;
use App\Models\AccessGroup;
use App\Models\Ip;
use App\Models\AdminIpAccess;
use App\Models\SalesStructure;
use App\Models\Designation;
use App\Models\Area;
use App\Models\Zone;
use App\Models\UserSalesState;
use App\Models\SalesUserRelationship;
use App\Models\UserSalesStateZone;
use App\Models\UserSalesStateZoneDistricts;
use App\Models\UserSalesStateZoneDistrictsTaluka;

class SalesUserController extends AbstractController{

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

        return view('admin.sales.sales_users_list')->with(['dateFormate'=>$dateFormate , 'acess_groupid' => $acess_groupid ]);
    }
    /**
     * 
     */
    public function SalesUsersListBySid(Request $request){
        $post=$request->All();
        if(!empty($post['getRMlist'])){
            $userlist=UserSalesState::GetSalesStateByStateID($post['sid']);
         //   return $userlist;
            $ulist=[];
            foreach($userlist as $u){
                $ulist[]=$u['user_id'];
            }
            return SalesUser::getUserSalesStructure($ulist,1);   
        }
        if(!empty($post['getasm']) && !empty($post['zsm'])){
            $where=['zsm_id'=>$post['zsm'],'sales_structure_id'=>4];
            if(!empty($post['dyzsm'])){
                $where['dyzsm_id']=$post['dyzsm'];
            }
            return SalesUser::getSalesUserListBySalesStructure($where);   
        }
        if(!empty($post['rsm'])){
         return SalesUser::getSalesUserListBySalesStructure(['rsm_id'=>$post['rsm'],'sales_structure_id'=>2]);
        }
        if(!empty($post['zsm'])){
            return SalesUser::getSalesUserListBySalesStructure(['zsm_id'=>$post['zsm'],'sales_structure_id'=>3]);
        }
        if(!empty($post['asm'])){
            $listdis=UserSalesStateZoneDistricts::UserSalesStateZoneDistrictsByUserID($post['asm']);
            if(!empty($listdis)){
                $dlist=[];
                foreach($listdis as $d){
                    $dlist[]=$d['district_id'];
                }
                if(!empty($dlist)){
                    return Taluka::getTalukaDataByDistrictsID($dlist);
                }else{
                    return '';
                }
            }else{
                return '';
            }            
        }
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
        $getData = $request->All();
        $search =  isset($getData['search']) && $getData['search'] != '' ? $getData['search']['value'] : '';
        
        //$coloumSearch = $getData['columns'];

        $coloumSearch = array();
        if(isset($getData['columns'][0]['search']['value'])){
          $coloumSearch = getAdvanceSearchFilterColsData($getData['columns'][0]['search']['value']);
        }

       //$coloumSearch['acess_groupid'] = 63;

        $totalRecords = SalesUser::getSalesUserData($length = NULL,$start = NULL,$sort = NULL,$sortdir = NULL,$search,$coloumSearch);
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
            $userListData = SalesUser::getSalesUserData($length,$start,$sort,$sortdir,$search,$coloumSearch);
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

            if (per_hasModuleAccess('Admin', 'Edit')) {
                $edit = ' <a href="' . route('salesuser',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
                $name = ' <a href="' . route('salesuser',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" >'.$userListData[$i]->name.'</a>';
                $user_name = ' <a href="' . route('salesuser',['mode' => 'edit', 'id' => base64_encode($userListData[$i]->id)]) . '" >'.$userListData[$i]->username.'</a>';
            }else{
                $name = $userListData[$i]->name;
                $user_name = $userListData[$i]->username;
            }
           // $delete = '<a href="' . route('user',['mode' => 'delete','id' =>base64_encode($userListData[$i]->id)]) . '" title="Delete"  onclick="'.$conformation.'"  ><span class="btn btn-icon btn-danger waves-effect waves-light"><i class="la la-times"></i></span></a> ';
            if (per_hasModuleAccess('Admin', 'View')) {
            $view = '<a href="' . route('salesuser',['mode' => 'view', 'id' => base64_encode($userListData[$i]->id)]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
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
                $rolename,
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
    public function add($mode=NULL,$id=NULL){
        $checkImgArr= $countryData = $countryDatas =$stateDatas=$access_group_data=$districtsDatas=$ip_data= $admin_ip_data= $admin_ip_data_arr= $stateData = $districtsData = array();
        $countryData = Country::getAllCountryData();
        $country = Country::getCountryISDCodeData(['status'=>Country::ACTIVE]);
        $default_isd = Config::get('settings.DEFAULT_ISD');
        $default_country = Config::get('settings.DEFAULT_COUNTRY');
        $image_extention = implode(', ',$this->img_ext_array); 
        if(!empty($country)){
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
        }
        #get all active accessgroup data
        $access_group_data = AccessGroup::getActiveAccessGroup();
        # Get All Sales Structure.
        $sales_structure = SalesStructure::getActiveSalesStructure();
        # Get All Active Designation
        $designations = Designation::getAllActiveDesignation();

        #Get All Active Taluka
        $all_taluka = Taluka::getAllActiveTaluka();
        
        if(isset($mode) && $mode != ""){
            $id = base64_decode($id);
            $ip_data = Ip::getActiveIp($id);
            $admin_ip_data = AdminIpAccess::getadmninIpId($id);
            $rsm_users=SalesUser::getSalesUserBySalesStructure(1);//RSM ID=1
            $zsmdata=SalesUserRelationship::UserSalesRelationshipByUserID($id);
            if(!empty($admin_ip_data)){
                foreach ($admin_ip_data as $adkey => $advalue) {
                     $admin_ip_data_arr[] = $advalue['ip_id'];
                }
            }
            if(isset($id) && $id != ""){
                $userData = SalesUser::getSalesUserDataFromId($id); 
                if($mode == "edit") {
                    $mode = 'Update'; 
                    if(isset($userData[0]['country_id']) && !empty($userData[0]['country_id'])){
                        $stateData = State::getStateCountryWise($userData[0]['country_id']);
                    } 
                    if(isset($userData[0]['state_id']) && !empty($userData[0]['state_id'])){
                        $districtsData = Districts::getDistrictsStateWise($userData[0]['state_id']);
                    }
                    ## Check Image is Exist or not
                    $image = (isset($userData[0]['image']) && $userData[0]['image'] !='')?$userData[0]['image'] : '';
                    if($image != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array));
                    }
                    $rsmstate=[];
                    $zsml=[];
                    $zsm_district=[];
                    $seletedzsm_district=[];
                    $dyzsm=[];
                    $asmlist = array();
                    $asm=[];
                    $talukalist=[];
                    $selta=[];
                        $satelistu=UserSalesState::UserSalesStateByUserID($userData[0]['id']);
                        if(!empty($satelistu)){
                            foreach($satelistu as $s){
                                $rsmstate[]=$s['state_id'];
                            }
                        }
                        $selectedzone=UserSalesStateZone::UserSalesStateZoneByUserID($userData[0]['id']);
                        if(!empty($selectedzone)){
                            $zsmdata[0]['zone_id']=$selectedzone[0]['zone_id'];
                        }
                        $zonelist=array();
                        if(!empty($zsmdata[0]['zone_id'])){
                            $zonelist=Zone::getZonesStateWise($rsmstate[0]);
                            $zsm_district = Districts::getDistrictsStateZoneWise($rsmstate[0],$zsmdata[0]['zone_id']);
                            $dlista=UserSalesStateZoneDistricts::UserSalesStateZoneDistrictsByUserID($userData[0]['id']);
                            foreach($dlista as $d){
                                $seletedzsm_district[]=$d['district_id'];
                            }
                        }
                        $talistarray=UserSalesStateZoneDistrictsTaluka::GetUserTalukalistByuserid($userData[0]['id']);
                        if(!empty($talistarray)){
                            foreach($talistarray as $tl){
                                $selta[]=$tl['id'];
                            }
                        }
                        if(!empty($zsmdata[0]['rsm_id'])){
                            $zsml=SalesUser::getSalesUserListBySalesStructure(['rsm_id'=>$zsmdata[0]['rsm_id'],'sales_structure_id'=>2]);
                        }
                        if(!empty($zsmdata[0]['zsm_id'])){
                            $dyzsm=SalesUser::getSalesUserListBySalesStructure(['zsm_id'=>$zsmdata[0]['zsm_id'],'sales_structure_id'=>3]);
                            $asmlist=SalesUser::getSalesUserListBySalesStructure(['zsm_id'=>$zsmdata[0]['zsm_id'],'sales_structure_id'=>4]);
                        }
                        if(!empty($zsmdata[0]['asm_id'])){
                            $listdis=UserSalesStateZoneDistricts::UserSalesStateZoneDistrictsByUserID($zsmdata[0]['asm_id']);
                            if(!empty($listdis)){
                                $dlist1=[];
                                foreach($listdis as $d){
                                    $dlist1[]=$d['district_id'];
                                }
                                if(!empty($dlist1)){
                                    $talukalist= Taluka::getTalukaDataByDistrictsID($dlist1);
                                }
                            }
                        }
                    return view('admin.sales.sales_user_add')->with(['mode'=>$mode,'countryData'=>$countryData,'userData'=>$userData[0],'stateData'=>$stateData,'districtsData'=>$districtsData,'stateDatalist'=>$stateData,'country'=>$country,'access_group_data' => $access_group_data,'userImage'=>$checkImgArr,'rsm_state'=>$rsmstate,'zsmdata'=>$zsmdata,'zsml'=>$zsml,'RSMusers'=>$rsm_users, 'default_isd' => $default_isd ,'default_country' => $default_country,'ip_data'=>$ip_data,'admin_ip_data_arr'=>$admin_ip_data_arr,'img_ext_array' => $image_extention, 'sales_structure' => $sales_structure,'seletedzsm_district'=>$seletedzsm_district,'zonelist'=>$zonelist, 'designations' => $designations,'all_taluka'=> $all_taluka,'zsm_district'=>$zsm_district,'dyzsm'=>$dyzsm,'selectedtaluka'=>$selta,'talukalist'=>$talukalist,'asmlist'=>$asmlist]);
                }elseif($mode == "view"){
                    $mode = "View";
                    $accessgroup_data  = array();
                    if(!empty($userData)){
                       ## Check Image is Exist or not
                        $image = (isset($userData[0]['image']) && $userData[0]['image'] !='')?$userData[0]['image'] : '';
                        
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'user','','_',$image,count($this->size_array));
                        // Get Sales Structure
                        $sales_structure = SalesStructure::getActiveSalesStructure($userData[0]['sales_structure_id']);
                        $sales_structure_title = $sales_structure[0]['short_name'] ?? '';
                        // Get Designation 
                        $designation = Designation::getAllActiveDesignation($userData[0]['designation_id']);

                        //Get Taluka name
                        $taluka = Taluka::getAllActiveTaluka($userData[0]['taluka_id']);

                        // echo '<pre>'; print_r($userData); echo '</pre>'; exit();
                        if(isset($userData[0]['country_id']) && !empty($userData[0]['country_id']))
                        {
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
                                    if(!empty($districtsData[0])){
                                        $districtsDatas = $districtsData[0];
                                    }else{
                                        $districtsDatas = array();
                                    } 
                                }  
                            }  
                        } 

                        $accessgroup_data = AccessGroup::getActiveAccessGroup($userData[0]['access_group_id_arr']);                       
                    }
                    
                    return view('admin.sales.sales_user_view')->with(['mode'=>$mode,'countryData'=>$countryDatas,'userData'=>$userData,'stateData'=>$stateDatas,'districtsData'=>$districtsDatas,'country'=>$country,'access_group_data' => $accessgroup_data,'userImage' => $checkImgArr,'ip_data'=>$ip_data,'img_ext_array' => $image_extention,'sales_structure' => $sales_structure_title, 'designation' => $designation[0]['name'], 'taluka'=> $taluka]);
                }

            }elseif($mode == 'add'){
                $mode = 'Add';
                $stateData = State::getStateCountryWise(1);
                return view('admin.sales.sales_user_add')->with(['mode'=>$mode,'countryData'=>$countryData,'stateDatalist'=>$stateData,'country'=>$country,'access_group_data' => $access_group_data,'default_isd' => $default_isd,'default_country' => $default_country,'ip_data'=>$ip_data,'img_ext_array' => $image_extention,'RSMusers'=>$rsm_users,'zonelist'=>[],'sales_structure' => $sales_structure, 'designations' => $designations,'all_taluka' => $all_taluka,'selectedtaluka'=>[],'talukalist'=>[],'asm'=>[]]);
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
        $post = $request->All();

        $module_id = Config::get('constants.module_id.salesusers');
        if($post['mode'] == 'Add')
        {
           
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
        }else if($post['mode'] == 'Update')
        {   
            // echo '<pre>'; print_r($post); echo '</pre>'; exit();
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
    private function validateAdd($data = array()){
        $rules = array(
                'first_name' => 'required|min:3|max:50',
                'last_name' => 'required|min:3|max:50',
                'email' => 'required|email|max:100|unique:admin,email,NULL,id',
                'username' => 'required|min:3|max:20|unique:admin,username,NULL,id', 
                'password' => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'',
                'mobile' => 'required|min:10|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/',
                'sales_structure_id' => 'required', 
                'adhar_no' => 'required|max:12',  
                'whatsapp_number' => 'required|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
            );
        if(!empty($data['zip'])){
            $rules['zip']='min:6|max:6';
        }
        if($data['sales_structure_id']==1){
            $rules['sales_state_id']='required';
        }
        if($data['sales_structure_id']>=2){
            $rules['user_type_rsm_state']='required';
            $rules['user_type_rsm']='required';
        }
        if($data['sales_structure_id']==2){
            $rules['user_type_rsm_zone']='required';
        }
        if($data['sales_structure_id']>=3){
            $rules['user_type_zsm']='required';
        }
        if($data['sales_structure_id']==3 || $data['sales_structure_id']==4){
            $rules['user_type_zsm_district']='required';
        }
        if($data['sales_structure_id']==5){
            $rules['user_type_asm']='required';
            $rules['user_type_aso_asm_taluka']='required';
        }
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
            'sales_structure_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Sales Structure'),
            'adhar_no.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Aadhar Number'),
            'whatsapp_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'WhatsApp Number'),
            'whatsapp_number.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'WhatsApp Number'),
            'whatsapp_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'WhatsApp Number'),
            'adhar_no.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '12', 'Aadhar No'),
            'sales_state_id.required' => sprintf(Config::get('messages.validation_msg.required_field'),'state'),
            'user_type_rsm_state.required' => sprintf(Config::get('messages.validation_msg.required_field'),'state'),
            'user_type_rsm.required' => sprintf(Config::get('messages.validation_msg.required_field'),'RSM'),
            'user_type_rsm_zone.required' => sprintf(Config::get('messages.validation_msg.required_field'),'Zone'),
            'user_type_zsm.required' => sprintf(Config::get('messages.validation_msg.required_field'),'ZSM'),
            'user_type_zsm_district.required' => sprintf(Config::get('messages.validation_msg.required_field'),'District'),
            'user_type_asm.required' => sprintf(Config::get('messages.validation_msg.required_field'),'ASM'),
            'user_type_aso_asm_taluka.required' => sprintf(Config::get('messages.validation_msg.required_field'),'Taluka'),
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
                // 'acess_groupid' => 'required', 
                'mobile' => 'required|min:10|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/',
                'sales_structure_id' => 'required', 
                'adhar_no' => 'required|max:12',
                //'designation_id' => 'required', 
                'whatsapp_number' => 'required|max:10|regex:/^(?=.*[0-9])[- +()0-9]+$/', 
            );
            if(!empty($data['zip'])){
                $rules['zip']='min:6|max:6';
            }
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
            'sales_structure_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Sales Structure'),
            'adhar_no.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Aadhar Number'),
            //'designation_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Designation'),
            'whatsapp_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Designation'),
            'whatsapp_number.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '10', 'WhatsApp Number'),
            'whatsapp_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'WhatsApp Number'),
            'adhar_no.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '12', 'Aadhar No'),
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
                    storeImageinFolder($fileRealPath, $this->destinationPath, $image, $this->size_array);
                }
            } else {
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }
        # Get Access Group id From Sales Structure
        $data['access_group_id']=0;
        if(!empty($data['sales_structure_id'])){
            $sales_structure = SalesStructure::getActiveSalesStructure($data['sales_structure_id']);
            $data['access_group_id']=$sales_structure[0]['access_group_id'];
        }
        #
       // $access_group = SalesStructure::getSalesStructureDataFromId($data['sales_structure_id']);
        $insert_array = array(
            'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
            'last_name' => (isset($data['last_name']) ? $data['last_name'] : ""),
            'email' => (isset($data['email']) ? $data['email'] : ""),
            'mobile' => (isset($data['mobile']) ? $data['mobile'] : ""),
            'access_group_id'=>$data['access_group_id'],
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
            'sales_structure_id' => (isset($data['sales_structure_id']) ? $data['sales_structure_id'] : "0"),
            'adhar_no' => (isset($data['adhar_no']) ? $data['adhar_no'] : "0"),
            //'designation_id' => (isset($data['designation_id']) ? $data['designation_id'] : "0"),
            'whatsapp_number' => (isset($data['whatsapp_number']) ? $data['whatsapp_number'] : "0"),
            'area' => (isset($data['area']) ? $data['area'] : "0"),
            'remark' => (isset($data['remark']) ? $data['remark'] : ""),
            'user_type' => 2,
        );  
        $insert = SalesUser::addSalesUser($insert_array);
        if (isset($insert)) {
            if(isset($data['sales_structure_id'])){
                // insert for relationship
                $insertRelationnship=array('user_id'=>$insert['id'],'rsm_id'=>0,'zsm_id'=>0,'dy_zsm_id'=>0,'asm_id'=>0,'sales_structure_id'=>$data['sales_structure_id'],'created_at'=>date('Y-m-d H:i:s'));
                // only for RSM
                if($data['sales_structure_id']==1){
                    if(!empty($data['sales_state_id'])){
                        foreach($data['sales_state_id'] as $val){
                            $insertArray=array('user_id'=>$insert['id'],'state_id'=>$val,'created_at'=>date('Y-m-d H:i:s'));
                            (UserSalesState::addUserSalesState($insertArray));
                        }
                    }
                }
                if($data['sales_structure_id']>=2){
                    if($data['sales_structure_id']>=3){
                        $userdata=UserSalesStateZone::UserSalesStateZoneByUserID($data['user_type_zsm']);
                        $data['user_type_rsm_zone']=$userdata[0]['zone_id'];
                    }
                    $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'created_at'=>date('Y-m-d H:i:s'));
                    UserSalesState::addUserSalesState($insertArray);
                    // only for ZSM
                    
                    $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'created_at'=>date('Y-m-d H:i:s'));
                    UserSalesStateZone::addUserSalesStateZone($insertArray);
                    if(!empty($data['user_type_rsm'])){
                        $insertRelationnship['rsm_id']=$data['user_type_rsm'];
                    }
                    if(!empty($data['user_type_zsm'])){
                        $insertRelationnship['zsm_id']=$data['user_type_zsm'];
                    }
                    if(!empty($data['user_type_dyzsm'])){
                        $insertRelationnship['dy_zsm_id']=$data['user_type_dyzsm'];
                    }
                    if(!empty($data['user_type_asm'])){
                        $insertRelationnship['asm_id']=$data['user_type_asm'];
                    }                 
                    if($data['sales_structure_id']>=3){
                        if(!empty($data['user_type_zsm_district'])){
                            foreach($data['user_type_zsm_district'] as $val){
                                $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'district_id'=>$val,'created_at'=>date('Y-m-d H:i:s'));
                                UserSalesStateZoneDistricts::addUserSalesStateZoneDistricts($insertArray);
                            }
                        }
                    }
                    if($data['sales_structure_id']==5){
                        $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'taluka_id'=>$data['user_type_aso_asm_taluka'],'district_id'=>$data['user_type_aso_asm_taluka'],'created_at'=>date('Y-m-d H:i:s'));
                        UserSalesStateZoneDistrictsTaluka::addUserSalesStateZoneDistrictsTaluka($insertArray);
                    }
                }
                // insert for relationship
                SalesUserRelationship::addSalesUsersRelationship($insertRelationnship);
            }
            $insertedId = (isset($insert['id']) ? $insert['id'] : "");



            ## Insert Admin access Groups
                if (!empty($data['access_group_id'])) { 
                    $group_array =array();
                    //foreach ($data['acess_groupid'] as $rowacess_groupid) {
                        $group_array = array(
                            'admin_id' => $insertedId,
                            'access_group_id' =>$data['access_group_id'],
                        );
                        $insert_group = AccessGrouppermission::addAccessGrouppermission($group_array);
                    //}
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
                $module_id = Config::get('constants.module_id.salesusers');
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
            }else{
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }else{
            $image = (isset($data['user_image_old'])?$data['user_image_old']:"");
        } 
        # Get Access Group id From Sales Structure
        $data['access_group_id']=0;
        if(!empty($data['sales_structure_id'])){
            $sales_structure = SalesStructure::getActiveSalesStructure($data['sales_structure_id']);
            $data['access_group_id']=$sales_structure[0]['access_group_id'];
        }

        $update_array = array(
                'first_name' => (isset($data['first_name']) ? $data['first_name'] : ""),
                'access_group_id'=>$data['access_group_id'],
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
                'status' => (isset($data['status']) ? $data['status'] : "2"),
                'updated_at' => date('Y-m-d H:i:s'),
                'sales_structure_id' => (isset($data['sales_structure_id']) ? $data['sales_structure_id'] : "0"),
                'adhar_no' => (isset($data['adhar_no']) ? $data['adhar_no'] : "0"),
                'whatsapp_number' => (isset($data['whatsapp_number']) ? $data['whatsapp_number'] : "0"),
                'area' => (isset($data['area']) ? $data['area'] : "0"),
                'remark' => (isset($data['remark']) ? $data['remark'] : ""),
            );
            if(isset($data['sales_structure_id'])){/*
                // insert for relationship
                $insertRelationnship=array('user_id'=>$insert['id'],'rsm_id'=>0,'zsm_id'=>0,'dy_zsm_id'=>0,'asm_id'=>0,'sales_structure_id'=>$data['sales_structure_id'],'created_at'=>date('Y-m-d H:i:s'));
                // only for RSM
                UserSalesState::deleteUserState($insert['id']);
                UserSalesStateZone::deleteUserStateZone($insert['id']);
                UserSalesStateZoneDistricts::deleteUserStateZoneDistricts($insert['id']);
                UserSalesStateZoneDistrictsTaluka::deleteUserStateZoneDistrictsTaluka($insert['id']);
                SalesUserRelationship::sRelationship($insert['id']);
                if($data['sales_structure_id']==1){
                    if(!empty($data['sales_state_id'])){
                        foreach($data['sales_state_id'] as $val){
                            $insertArray=array('user_id'=>$insert['id'],'state_id'=>$val,'created_at'=>date('Y-m-d H:i:s'));
                            (UserSalesState::addUserSalesState($insertArray));
                        }
                    }
                }
                if($data['sales_structure_id']>=2){
                    if($data['sales_structure_id']>=3){
                        $userdata=UserSalesStateZone::UserSalesStateZoneByUserID($data['user_type_zsm']);
                        $data['user_type_rsm_zone']=$userdata[0]['zone_id'];
                    }
                    $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'created_at'=>date('Y-m-d H:i:s'));
                    UserSalesState::addUserSalesState($insertArray);
                    // only for ZSM
                    $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'created_at'=>date('Y-m-d H:i:s'));
                    UserSalesStateZone::addUserSalesStateZone($insertArray);
                    if(!empty($data['user_type_rsm'])){
                        $insertRelationnship['rsm_id']=$data['user_type_rsm'];
                    }
                    if(!empty($data['user_type_zsm'])){
                        $insertRelationnship['zsm_id']=$data['user_type_zsm'];
                    }
                    if(!empty($data['user_type_dyzsm'])){
                        $insertRelationnship['dy_zsm_id']=$data['user_type_dyzsm'];
                    }
                    if(!empty($data['user_type_asm'])){
                        $insertRelationnship['asm_id']=$data['user_type_asm'];
                    }                 
                    if($data['sales_structure_id']==3 || $data['sales_structure_id']==4){
                        if(!empty($data['user_type_zsm_district'])){
                            foreach($data['user_type_zsm_district'] as $val){
                                $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'district_id'=>$val,'created_at'=>date('Y-m-d H:i:s'));
                                UserSalesStateZoneDistricts::addUserSalesStateZoneDistricts($insertArray);
                            }
                        }
                    }
                    if($data['sales_structure_id']==5){
                        $insertArray=array('user_id'=>$insert['id'],'state_id'=>$data['user_type_rsm_state'],'zone_id'=>$data['user_type_rsm_zone'],'taluka_id'=>$data['user_type_aso_asm_taluka'],'district_id'=>$data['user_type_aso_asm_taluka'],'created_at'=>date('Y-m-d H:i:s'));
                        UserSalesStateZoneDistrictsTaluka::addUserSalesStateZoneDistrictsTaluka($insertArray);
                    }
                }
                // insert for relationship
                SalesUserRelationship::addSalesUsersRelationship($insertRelationnship);*/
            }
        if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            if(isset($data['password']) && $data['password'] != ''){ 
                $update_array['password'] = (isset($data['password']) ? Hash::make($data['password']) : "");    
            }
        }
        $update = SalesUser::updateSalesUser($id,$update_array);
        if ($update){ 
            ## Delete Admin access Groups 
            $delete_acessgroup=AccessGrouppermission::accessGroupDelete($id);
         
            ## Insert Admin access Groups  
            if (!empty($data['access_group_id'])) {
            
                $group_array = array(
                    'admin_id' => $id,
                    'access_group_id' => $data['access_group_id'],
                );
                $insert_group = AccessGrouppermission::addAccessGrouppermission($group_array);
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
                $module_id = Config::get('constants.module_id.salesusers');
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
            $userData = SalesUser::getSalesUser($data['id']); 
            ##Delete user 
            $delete = SalesUser::deletRecords($data['id']);
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
            $update_status = SalesUser::updateStatus($id,$data);
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
            $userData = SalesUser::getSalesUser($id); 

            #image delete from table            
            $datas = array("image"=>"");
              
            SalesUser::updateSalesUser($id,$datas); 

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
        $post = $request->All();
        
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
                $userData = SalesUser::getSalesUser($id); 
               
                #delete user from table
                $delete = SalesUser::deletRecords($id);
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