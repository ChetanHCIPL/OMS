<?php

namespace App\Http\Controllers\Admin\AccessGroup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Validator;
use Config;
use Auth;
use App\GlobalClass\Design;
use App\Models\AccessGroup;
use App\Models\AccessModule;
use App\Models\AccessGroupModuleRole;
use App\Models\AccessGrouppermission;
use App\Models\AdminAccessGeneral;
use App\Models\AdminAccessGeneralRole;


class AccessGroupController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('accessrights');
    }

    /**
    * Function to Load Access Group List
    *
    * @param    void
    * @return   view
    */
    public function grid()
    {
        return view('admin/access_group/list');
    }


    /**
	* Prepare Grid data of Acess Group List
    *
    * @param  array $request
	* @return array
	*/
    public function ajaxData(Request $request)
    {
    	$records["data"] =  $search_arr = array();
    	$post = $request->All();
    	$search_coulmn_arr = array(
            "0" => "access_group",
            "1" => "users",
            "2" => "status",
        );
        if(isset($post['columns']) && count($post['columns']) > 0) {
            foreach ($post['columns'] as $key => $field) {
                if($field['search']['value'] != '')
                    $search_arr[$search_coulmn_arr[$key]] = $field['search']['value'];
            }
        }

    	$search = (isset($post['search']['value']) ? ($post['search']['value']) : "");

    	$tot_records_data =  AccessGroup::getAccessGroupData($length = NULL, $start = NULL,$sort = NULL, $sortdir = NULL,$search,$search_arr);
    	$totalRecords = count($tot_records_data);


        $length = (isset($post['length']) ? intval($post['length']) : 10);
        $length = $length < 0 ? $totalRecords : $length;
        $start = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sort = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC');

        if (isset($sort) && $sort != "") {
            switch ($sort) {
                case "1":
                    $sort = "access_group";
                    break;
                case "2":
                    $sort = "users";
                    break;
                case "3":
                    $sort = "status";
                    break;
                default:
                    $sort = "access_group";
            }
        } else {
            $sort = "access_group";
        }

        $data =   AccessGroup::getAccessGroupData($length, $start,$sort,$sortdir,$search,$search_arr);

        for($i=0;$i<count($data);$i++)
        {
        	$status =  $data[$i]->status == '1' ? 'Active' : 'Inactive';
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);

            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
             if ($data[$i]->users > 0) {
                if (gen_checkAllAccessRights('Users')) {
                    $tot_user = '( <a href="' . route('admin.user', ['acess_groupid' => $encoded_id]) . '" target="_blank">' . $data[$i]->users . '</a> )';
                } else {
                    $tot_user = '( ' . $data[$i]->users . ' )';
                }
            } else {
                $tot_user = '( ' . $data[$i]->users . ' )';
            }

            $edit = "---";
            $view="";
            if (per_hasModuleAccess('Roles', 'Edit')) {
                $edit = '<a href="' . route('access-role',['mode' => 'edit', 'id' => base64_encode($data[$i]->id) ]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('Roles', 'View')) {
                $view ='<a href="' . route('access-role',['mode' => 'view', 'id' => base64_encode($data[$i]->id) ]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>';
            }


            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->access_group ,
                $tot_user ,
                $status,
                $view.$edit
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $totalRecords;
        $records["recordsFiltered"] = $totalRecords;

        echo json_encode($records);

    }

     /** 
     * Function load add/edit page of Access Group
     *
     * @param  string $mode
     * @param  int    $id
     * @return view
     */
    public function add($mode = NULL ,$id = NULL){
        $roles_arr =array();
    	if(isset($mode) && $mode != ''){
    		if(isset($id) && $id != ''){
    			$id = base64_decode($id);
    			#get roles data
    			$data = AccessGroup::getAccessGroupDataFromId($id);

    			if ($mode == "edit"){
    				$mode = "Update";

                    $roles_arr = $this->acessGroupRoleData($id);
                    
    				return view('admin/access_group/add')->with(['mode'=>$mode , 'data'=>$data , 'id'=>$id , 'permission_arr' => $roles_arr]);
    			}elseif ($mode == 'view') {
                	
               		$mode = 'View';
                	return view('admin/access_group/view')->with(['mode' => $mode, 'id' => $id,'data' => $data]);
            	}
    		}
    		if($mode == "add"){

    			$mode = "Add";
                $id="";
                $roles_arr = $this->acessGroupRoleData($id);
    			return view('admin/access_group/add') -> with(['mode'=>$mode,'permission_arr' => $roles_arr]);
    		}
    	}
    }


    /** 
     * Function to post data of access group
     *
     * @param array $request
     * @return json response
     *
    */
    public function PostAjaxData(Request $request){
    	$records = $result_arr = array();
    	$records['data']  = array();
		$post = $request->All();
		
		if(isset($post['customActionName'])){
			if($post['customActionName'] == "Add"){
				$validator = $this->validateFields($post);
				if ($validator->fails()) {
    				$validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                    }
                }else{
                	$insert = $this->insertRecords($post);
                	if($insert == true){
                		 $result_arr[0]['isError'] = 0;
            			 $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
                	}else{
                		$result_arr[0]['isError'] = 1;
                    	$result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                	}
                }

                ## Return Response
        		return Response::json($result_arr);
			} elseif ($post['customActionName'] == "Update"){
				$validator = $this->validateFields($post);
				if ($validator->fails()) {
    				$validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                    }
                }else{
                	$insert = $this->updateRecords($post);
                	if($insert == true){
                		 $result_arr[0]['isError'] = 0;
            			 $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                	}else{
                		$result_arr[0]['isError'] = 1;
                    	$result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                	}
                }

                ## Return Response
        		return Response::json($result_arr);
			} elseif ($post['customActionName'] == "Active" || $post['customActionName'] == 'Inactive') {
                $update_status = $this->updateStatus($post['id'], $post['customActionName']);
                if ($update_status == true) {
                    if ($post['customActionName'] == "Active") {
                        $records["customActionStatus"] = "OK";
                        $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_ACTIVATED'), count($post['id']));
						$result_arr[0]['isError'] = 0;
                    } else {
                        $records["customActionStatus"] = "OK";
                        $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_INACTIVATED'), count($post['id']));
						$result_arr[0]['isError'] = 0;
                    }
                } else {
                    $records["customActionMessage"] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
					$result_arr[0]['isError'] = 1;
                }
				$result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            }elseif ($post['customActionName'] == "Delete") {
                $delete = $this->deleteAccessGroup($post['id']);
                $records = array();
                
				if(!empty($delete)){
					$flag = $delete['flag'];
					if($flag == 0){
						
						$cannot_delete_ids = $delete['cannot_delete'];
						if(isset($delete['delete'])){
							$delete_ids = $delete['delete'];
						}
						if(!empty($cannot_delete_ids)){

							$group_name = AccessGroup::getAccessGroupName($cannot_delete_ids);
							
							if (isset($group_name[0]['access_group']) && $group_name[0]['access_group'] != "") {
								$records[] = sprintf(Config::get('messages.msg.MSG_RECORD_USER_ATTACHAED_NOT_DELETED'), $group_name[0]['access_group']);
								$result_arr[0]['isError'] = 1;
							} else {
								$records[] = sprintf(Config::get('messages.msg.MSG_RECORD_CONTAINS_USERS'));
								$result_arr[0]['isError'] = 1;
							}	
						}
						if(!empty($delete_ids)){
							$records[] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), $delete_ids);
							$result_arr[0]['isError'] = 0;
						}
					}else if($flag=1){
						$records[] = $delete['msg'];
                        $result_arr[0]['isError'] = 0;
					}
				}
				
				$result_arr[0]['msg'] = implode("<br>",$records);
				return Response::json($result_arr);
            }
		}
    }


    /**
    * Validation for form
    * @return validator object
    */
    private function validateFields($data = array()){
    	if($data['customActionName'] == "Add"){
			$rules = array(
				'access_group' => 'required|max:50|unique:admin_access_group,access_group,NULL,id',
			);
		}else{
			$rules = array(
				'access_group' => 'required|max:50|unique:admin_access_group,access_group,'.$data['id'].',id',
			);
		}
        $messages = array(
            'access_group.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Role Name'),
            'access_group.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '50', 'Role Name'),
            'access_group.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Role Name'),
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    * Function Add Access Group Data
    *
    * @param    array  $data
    * @return   boolean 
    *
    */   
    private function insertRecords($data = array()){
    	
    	$status = isset($data['status'])?$data['status']: AccessGroup::INACTIVE;

    	$insert_array  = array(
    		'access_group' => $data['access_group'],
    		'status' => $status
    	);

    	$insert = AccessGroup::addAccessGroup($insert_array);

    	if (!empty($insert)){
            $insertedId = (isset($insert['id']) ? $insert['id'] : "");

            if(!empty($data['module_id']) || !empty($data['general_id'])){
               $result_accessrole = $this->saveAccessGroupRoleData($insertedId,$data);
             }

            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.roles');
            $log_text = "Roles " . $data['access_group'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
             usr_addActivityLog($activity_arr);
            ## End Add Activity
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
    * Function Update  Access Group Data
    *
    * @param    array  $data
    * @return   boolean 
    *
    */ 
    private function updateRecords($data  = array()){
    	
    	$status = isset($data['status'])?$data['status']: AccessGroup::INACTIVE;
    	$id = $data['id'];
    	$update_array  = array(
    		'access_group' => $data['access_group'],
    		'status' => $status
    	);
    	$update = AccessGroup::updateAccessGroup($update_array,$id);

    	if(isset($update)){
             if(!empty($data['module_id']) || !empty($data['general_id'])){
               $result_accessrole = $this->saveAccessGroupRoleData($id,$data);
             }
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.roles');
            $log_text = "Roles " . $data['access_group'] . " edited";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
             usr_addActivityLog($activity_arr);
            ## End Add Activity
    		return true;
    	}else{
    		return false;
    	}
    }


    /**
    * Function Add Access Group Role Data (Save Access Module and Genral Role )
    *
    * @param int Acess Group Id
    * @param array $data
    * @return json result_arr
    *
    */
   public function saveAccessGroupRoleData($iAGroupId,$data){

        $module_arr  = $insert= $insert_gen = array();
        $flag ="0";
        if($data['customActionName'] == "Update"){

           ## Check If access group available 
          $iAGroup = AccessGroup::getAccessGroupId($iAGroupId);
          $flag = (!empty($iAGroup))?"1":"0";
           ## If access module roles available then delete...
           $roleArr = AccessGroupModuleRole::deleteGroupModuleData($iAGroupId);

        }else {
            //Add mode
            $flag = "1";
        }
        if ( $flag == "1") {
           
                ## get all Modules array
                $ModuleArrId = AccessModule::getModuleIdData();
                ## Convert Modules array to single dimentional array
                foreach ($ModuleArrId as $rowModuleArr) {
                    foreach ($rowModuleArr as $value) {
                        $iAModuleId[] = $value;
                    }
                }

                    if(!empty($data['module']))
                    {
                        $module_arr = $data['module'];
                    }
                if (!empty($ModuleArrId)) {
                    for ($i = 0; $i < count($ModuleArrId); $i++) {
                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]['list']) && $module_arr[$ModuleArrId[$i]['access_module_id']]['list'] != "") {
                            $id_arry_eList = $module_arr[$ModuleArrId[$i]['access_module_id']]['list'];
                        } else {
                            $id_arry_eList = "2";
                        }
                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["view"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["view"] != "") {
                            $id_arry_eView = $module_arr[$ModuleArrId[$i]['access_module_id']]["view"];
                        } else {
                            $id_arry_eView = "2";
                        }
                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["add"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["add"] != "") {
                            $id_arry_eAdd = $module_arr[$ModuleArrId[$i]['access_module_id']]["add"];
                        } else {
                            $id_arry_eAdd = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["edit"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["edit"] != "") {
                            $id_arry_eEdit = $module_arr[$ModuleArrId[$i]['access_module_id']]["edit"];
                        } else {
                            $id_arry_eEdit = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["delete"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["delete"] != "") {
                            $id_arry_eDelete = $module_arr[$ModuleArrId[$i]['access_module_id']]["delete"];
                        } else {
                            $id_arry_eDelete = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["status"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["status"] != "") {
                            $id_arry_eStatus = $module_arr[$ModuleArrId[$i]['access_module_id']]["status"];
                        } else {
                            $id_arry_eStatus = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["export"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["export"] != "") {
                            $id_arry_eExport = $module_arr[$ModuleArrId[$i]['access_module_id']]["export"];
                        } else {
                            $id_arry_eExport = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["print"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["print"] != "") {
                            $id_arry_ePrint = $module_arr[$ModuleArrId[$i]['access_module_id']]["print"];
                        } else {
                            $id_arry_ePrint = "2";
                        }

                        if (isset($module_arr[$ModuleArrId[$i]['access_module_id']]["date_period"]) && $module_arr[$ModuleArrId[$i]['access_module_id']]["date_period"] != "") {
                            $id_arry_eDatePeriod = $module_arr[$ModuleArrId[$i]['access_module_id']]["date_period"];
                        } else {
                            $id_arry_eDatePeriod = "7";//No Limit
                        }

                        if ($id_arry_eList != "2" ||$id_arry_eView != "2" || $id_arry_eAdd != "2" || $id_arry_eEdit != "2" || $id_arry_eDelete != "2" || $id_arry_eStatus != "2" || $id_arry_eExport != "2" || $id_arry_ePrint != "2"  || $id_arry_eDatePeriod != "0") {
                            $insert_array = array(
                                'access_module_id' => $ModuleArrId[$i]['access_module_id'],
                                'access_group_id' => $iAGroupId,
                                'list' => $id_arry_eList,
                                'view' => $id_arry_eView,
                                'add' => $id_arry_eAdd,
                                'edit' => $id_arry_eEdit,
                                'delete' => $id_arry_eDelete,
                                'status' => $id_arry_eStatus,
                                'export' => $id_arry_eExport,
                                'print' => $id_arry_ePrint,
                                'date_period' => $id_arry_eDatePeriod,
                            );
                            $insert[] = AccessGroupModuleRole::insertGroupModule($insert_array);
                            
                        }
                    }
                    
                    if (!empty($insert)) {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_CHANGES_UPDATED');
                    } else {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    }
                }else {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }

           
                    ## get all General array
                    $GenArrId =  AdminAccessGeneral::getGeneralId();
                    ## Convert General array to single dimentional array
                    // foreach ($GenArrId as $rowGenArr) {
                    //     foreach ($rowGenArr as $value) {
                    //         $iAGeneralId[] = $value;
                    //     }
                    // }
                    ## If access general role available then delete..
                    //$roleGenArr = AdminAccessGeneralRole::deleteAccessGeneral($iAGroupId,$iAGeneralId);
                    if(!empty($GenArrId)){
                        for ($i = 0; $i < count($GenArrId); $i++) {
                            if(!empty($data["general"][$GenArrId[$i]['id']]) && $data["general"][$GenArrId[$i]['id']] == "1"){
                                $insertgen_array = array(
                                    'access_group_id' => $iAGroupId,
                                    'access_general_id' => $GenArrId[$i]['id']
                                );
                                $insert_gen[] = AdminAccessGeneralRole::insertAccessGeneral($insertgen_array);
                            }
                        }
                        
                        if (!empty($insert_gen)) {
                            $result_arr[0]['isError'] = 0;
                            $result_arr[0]['msg'] = Config::get('messages.msg.MSG_CHANGES_UPDATED');
                        } else {
                            $result_arr[0]['isError'] = 0;
                            $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                        }
                    }else {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    }
            
        } else {
            $result_arr[0]['isError'] = 1;
            $result_arr[0]['msg'] = Config::get('messages.msg.MSG_UNAUTHORIZED_ACCESS');
        }

        return $result_arr;
    }

  

    /**
    * Function Update status of Access Group
    *
    * @param array $id
    * @param string $status
    * @return boolean
    *
    */ 
    public function updateStatus($id , $status)
    {
    	if(!empty($id) && isset($status)){
    		if($status == "Active"){
    			$status = AccessGroup::ACTIVE;
    		} elseif ($status == "Inactive"){
    			$status = AccessGroup::INACTIVE;
    		}
    		$update =AccessGroup::updateAccessGroupStatus($id,$status);
    	}
    	if(isset($update)) {
    		return true;
    	} else {
    		return false;
    	}
    }


    /**
    * Function Delete  Access Group
    *
    * @param array $id
    * @return array $result_arr
    *
    */ 
    private function deleteAccessGroup($id = array()) {
        if (!empty($id)) {
            $cannot_deleted = $access_group_id = $result_arr = array();
            foreach ($id as $accessId) {
                $admin_count = AccessGrouppermission::getAdminCountWithAdminChecking($accessId);
                if (isset($admin_count) && $admin_count > 0) {
                    $cannot_deleted[] = $accessId;
                }
            }
			$access_group_id = array_diff($id,$cannot_deleted);
			if(!empty($access_group_id)){
                $delete = AccessGroup::deleteGroup($access_group_id);
				if(!empty($delete)){
                    #delete module permissions 
                    $delete_mod_per = AccessGroupModuleRole::deleteGroupModuleData($access_group_id);
                    #delete general permissions 
                    $delete_general_per = AdminAccessGeneralRole::deleteGroupGeneralData($access_group_id,'');
					$result_arr['delete']=$delete;
				}
				$result_arr['cannot_delete']=$cannot_deleted;
				$result_arr['flag']=0;
			}else{
                //selected access group assign to user(s) so can not delete selectd access group
                $result_arr['flag'] = 1;
                $result_arr['msg'] = Config::get('messages.msg.MSG_ACCESS_GROUP_ALREADY_ASSIGNED');
            }
        }else{
            $result_arr['flag']=1;
            $result_arr['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
		return $result_arr;
    }


     

    /**
    * Function Get Access Group Role Data(Set accessible Modules)
    *
    * @param integer Access Group Id
    * @return   boolean $result
    *
    */
    public function acessGroupRoleData($iAGroupId){
        ##Variable Declaration 
        $modules_arr = $par_am_array = $par_rep_array = $data =  array();

        $module = $report = $general = array();
        $module_html = $report_html = $general_html='';
        $checked_print = $str_print =$str_export = $str_dt ="";
        $m=$k=$g =0;
       
            $acc_mod_assoc_arr = $asso_array = $iParentId = $iAModuleId = $data = array();
      
                ## Get Module Array
                $ModuleArr = AccessModule::getModuleData();

                foreach ($ModuleArr as $rowModuleArr) {
                    foreach ($rowModuleArr as $value) {
                        $iAModuleId[] = $value;
                    }
                }
                ## Get Role Array for all module by access group
                $roleArr = AccessGroupModuleRole::getGroupModuleData($iAGroupId,$iAModuleId);
              
                ## Get Parent Array
                $parentArr = AccessModule::getParentData();

                foreach ($parentArr as $rowParentArr) {
                    foreach ($rowParentArr as $value) {
                        $iParentId[] = $value;
                    }
                }

                $module = AccessModule::getModuleData();

                for ($i = 0, $mo = count($module); $i < $mo; $i++) {
                    $acc_mod_assoc_arr[$module[$i]['parent_id']][] = $module[$i];
                }

                for ($j = 0, $ji = count($roleArr); $j < $ji; $j++) {
                    $asso_array[$roleArr[$j]['access_module_id']] = $roleArr[$j];
                }
              
                $par_am_array = self::getChildAccessModuleList(0, "", "", 0, 5, $acc_mod_assoc_arr, $asso_array);

                $modules_arr = &$par_am_array;

                for ($i = 0; $i < count($modules_arr); $i++) {
                    $str_list = $str_view = $str_add = $str_edit = $str_delete = $str_status = "";
                    $checked_list = $checked_view = $checked_add = $checked_edit = $checked_delete = $checked_status = "1";
                    $iAModuleId = $modules_arr[$i]['access_module_id'];
                    $str_module = '<input type="hidden" name ="module_id[]" value="'.$iAModuleId.'">';
                    if (isset($modules_arr[$i]['list']) && $modules_arr[$i]['list'] == "1") {
                        $str_list = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_list" id="list_' . $iAModuleId . '" name="module['.$iAModuleId.'][list]" title="List" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['list'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="list_' . $iAModuleId . '"></label></div>';

                        $checked_list = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['list'] == "1");
                    } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['view'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                        $str_list = 'Listing';
                    }
                    if (isset($modules_arr[$i]['view']) && $modules_arr[$i]['view'] == "1") {
                        $str_view = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_view" id="view_' . $iAModuleId . '" name="module['.$iAModuleId.'][view]"  title="View" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['view'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="view_' . $iAModuleId . '"></label></div>';

                        $checked_view = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['view'] == "1");
                    } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['view'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                        $str_view = 'View';
                    }
                    if (isset($modules_arr[$i]['add']) && $modules_arr[$i]['add'] == "1") {
                        $str_add = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_add" id="add_' . $iAModuleId . '" name="module['.$iAModuleId.'][add]"  title="Add" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['add'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="add_' . $iAModuleId . '" ></label></div>';

                        $checked_add = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['add'] == "1");
                    } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['view'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                        $str_add = 'Add';
                    }
                    if (isset($modules_arr[$i]['edit']) && $modules_arr[$i]['edit'] == "1") {
                        $str_edit = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_edit" id="edit_' . $iAModuleId . '"  name="module['.$iAModuleId.'][edit]"  title="Edit" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['edit'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="edit_' . $iAModuleId . '"></label></div>';

                        $checked_edit = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['edit'] == "1");
                    } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['view'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                        $str_edit = 'Edit';
                    }
                    if (isset($modules_arr[$i]['delete']) && $modules_arr[$i]['delete'] == "1") {
                        $str_delete = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_delete" id="delete_' . $iAModuleId . '"  name="module['.$iAModuleId.'][delete]" title="Delete" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['delete'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="delete_' . $iAModuleId . '"></label></div>';

                        $checked_delete = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['delete'] == "1");
                    } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['view'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                        $str_delete = 'Delete';
                    }
                    if (isset($modules_arr[$i]['export']) && $modules_arr[$i]['export'] == "1") {
                        $str_export = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_export" id="export_' . $iAModuleId . '"  name="module['.$iAModuleId.'][export]"  title="Export" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['export'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="export_' . $iAModuleId . '"></label></div>';

                        $checked_export = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['export'] == "1");
                    }else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['export'] == "2") {
                        $str_export = '';
                    }
                    if (isset($modules_arr[$i]['print']) && $modules_arr[$i]['print'] == "1") {
                        $str_print = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_print" id="print_' . $iAModuleId . '"  name="module['.$iAModuleId.'][print]"  title="Print" value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['print'] == "1") ? "checked" : "" : "") . ' onclick="chekRowchk(this);"><label class="custom-control-label" for="print_' . $iAModuleId . '"></label></div>';

                        $checked_print = (!empty($asso_array) && !empty($asso_array[$iAModuleId]) && $asso_array[$iAModuleId]['print'] == "1");
                    }else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['print'] == "2") {
                        $str_print = '';
                    }
                    $str_dt='';
                    if (isset($modules_arr[$i]['date_period']) &&  $modules_arr[$i]['date_period'] =="1") {
                        $str_dt='';
                        $str_dt .= '<select  name="module['.$iAModuleId.'][date_period]"  id="date_period_'.$iAModuleId.'">';
                        $str_dt .='<option value="1" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "1") ? "selected" : "" : "") . '>Today</option>';
                        $str_dt .='<option value="2" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "2") ? "selected" : "" : "") . '>7 Days</option>';
                        $str_dt .='<option value="3" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "3") ? "selected" : "" : "") . '>1 Month</option>';
                        $str_dt .='<option value="4" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "4") ? "selected" : "" : "") . '>3 Months</option>';
                        $str_dt .='<option value="5" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "5") ? "selected" : "" : "") . '>6  Months</option>';
                        $str_dt .='<option value="6" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "6") ? "selected" : "" : "") . '>1 Year</option>';
                        $str_dt .='<option value="7" ' . ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['date_period'] == "7") ? "selected" : "" : "") . '>No Limit</option>';
                        $str_dt .='</select>';
                    }else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['date_period'] == "2") {
                        $str_dt = '';
                    }
                    if( ($modules_arr[$i]['access_module_id'] != 4 && $modules_arr[$i]['parent_id'] != 4) ){
                        $checked_raw = ($checked_list && $checked_view && $checked_add && $checked_edit && $checked_delete) ? "checked" : "";
                    
                        ## Show the checkbox if module has any access (eg. list, add, edit, delete, status)
                        if ($modules_arr[$i]['list'] == "1" ||$modules_arr[$i]['view'] == "1" || $modules_arr[$i]['add'] == "1" || $modules_arr[$i]['edit'] == "1" || $modules_arr[$i]['delete'] == "1" || $modules_arr[$i]['status'] == "1" || $modules_arr[$i]['export'] == "1" || $modules_arr[$i]['print'] == "1") {
                            $first_col = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case" onclick="checkAllRow(this);"  value="' . $iAModuleId . '" ' . $checked_raw . ' id="selectrow_' . $iAModuleId . '" title="Select Row" /><label class="custom-control-label" for="selectrow_' . $iAModuleId . '"></label></div>';
                        } else {
                            $first_col = '';
                        }
                        $module_html .= "<tr><td>".($m= $m+1)."</td>" ;
                        $module_html .= "<td>".$first_col."</td>" ;
                        $module_html .= "<td>".$modules_arr[$i]['vPath'].$str_module."</td>" ;
                        $module_html .= "<td class='text-center'>".$str_list."</td>" ;
                        $module_html .= "<td class='text-center'>".$str_view."</td>" ;
                        $module_html .= "<td class='text-center'>".$str_add."</td>" ;
                        $module_html .= "<td class='text-center'>".$str_edit."</td>" ;
                        $module_html .= "<td class='text-center'>".$str_delete."</td>" ;
                        $module_html .= "</tr>" ;                        
                    }
                    if( $modules_arr[$i]['access_module_id'] != 4 && $modules_arr[$i]['parent_id'] == 4){ //report
                        $checked_raw = ($checked_print && $checked_export) ? "checked" : "";
                    
                        ## Show the checkbox if module has any access (eg. list, add, edit, delete, status)
                        if ($modules_arr[$i]['export'] == "1" || $modules_arr[$i]['print'] == "1") {
                            $first_col = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case_report" onclick="checkAllRowReport(this);"  value="' . $iAModuleId . '" ' . $checked_raw . ' id="selectrow_' . $iAModuleId . '" title="Select Row" /><label class="custom-control-label" for="selectrow_' . $iAModuleId . '"></label></div>';
                        } else {
                            $first_col = '';
                        }
                        $report_html .= "<tr><td>".($k = $k+1)."</td>";
                        $report_html .= "<td class='text-center'>".$first_col."</td>";
                        $report_html .= "<td>".$modules_arr[$i]['vPath'].$str_module."</td>";
                        $report_html .= "<td class='text-center'>".$str_dt."</td>";
                        $report_html .= "<td class='text-center'>".$str_export."</td>";
                        $report_html .= "<td class='text-center'>".$str_print."</td>";
                        $report_html .= "</tr>";
                    }   
                }
                ##General Tab
                $general_arr = AdminAccessGeneral::getGeneralData($iAGroupId);
                 $cnt_gen_arr = count($general_arr);
                 for ($i = 0; $i <$cnt_gen_arr; $i++) {
                    $access_general_id = $general_arr[$i]['id'];
                    $str_general = '<input type="hidden" name ="general_id[]" value="'.$general_arr[$i]['id'].'">';
                    $checked_gen = (!empty($general_arr[$i]['access_group_id']))?'1':'';
                    $checked_raw = ($checked_gen) ? "checked" : "";
                    
                    $first_col = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input case-general" name="general['.$general_arr[$i]['id'].']" value="1" '.$checked_raw.' id="select_row'.$general_arr[$i]['id'] .'" title="Select Row" /><label class="custom-control-label" for="select_row' . $general_arr[$i]['id'] . '"></label></div>';
                    
                    $general_html  .= "<tr>";
                    $general_html .= "<td>".($g= $g+1)."</td>" ;
                    $general_html .= "<td>".$first_col.$str_general."</td>" ;
                    $general_html .= "<td>".$general_arr[$i]['name']."</td>" ;
                    $general_html .= "</tr>" ;

                }

                $data['module'] = $module_html;
                $data['report'] = $report_html;
                $data['general'] = $general_html;
                
        	   return $data;
        
    }


     /**
    * Function Get Child Access Module List
    *
    * @param integer  iParent  , iCatIdNot, loop, maxloop
    * @param string old_cat
    * @param  array acc_mod_assoc_arr , asso_array
    * @return  array data
    *
    */ 
    private  function getChildAccessModuleList($iParent = 0, $old_cat = "", $iCatIdNot = "0", $loop = 0, $maxloop = 5, $acc_mod_assoc_arr, $asso_array) {
        global $par_am_array;
        if ($loop <= $maxloop && isset($acc_mod_assoc_arr[$iParent]) && is_array($acc_mod_assoc_arr[$iParent])) {
            foreach ($acc_mod_assoc_arr[$iParent] as $Pid => $db_amodule_rs) {
                if ($iCatIdNot != $db_amodule_rs['access_module_id']) {
                    if ($loop > 0)
                        $path = $old_cat . "L&nbsp;&nbsp;" . $db_amodule_rs['title'];
                    else
                        $path = $db_amodule_rs['title'];
                    $par_am_array[] = array_merge($db_amodule_rs, array('vPath' => $path, 'loop' => $loop));
                    self::getChildAccessModuleList($db_amodule_rs['access_module_id'], $old_cat . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $iCatIdNot, $loop + 1, $maxloop, $acc_mod_assoc_arr, $asso_array);
                }
            }
        }
        $old_cat = "";
        return $par_am_array;
    }

    ## View accessible Modules 
     /**
    * Function set data of access module role for view page
    *
    * @param $request
    * @param  integer Access group Id
    * @return json array records
    *
    */ 
    public function accessGroupRolesView(Request $request, $iAGroupId = NULL) {
       
        $modules_arr = $par_am_array = $data =  array();
        if ($request->isMethod('post')) {
           $post = $request->All();
            $iAGroupId = (isset($post['iAGroupId'])?$post['iAGroupId']:'');
            $tab       = (isset($post['tab'])?$post['tab']:'');
            $acc_mod_assoc_arr = $asso_array = $iParentId = $iAModuleId = $data = array();
            if($tab == 'module' || $tab == 'report'){
                
                ##Get Date Priode Array
                $date_periodArr = AccessGroup::renderDatePriode();

                ## Get Access Module Array Data
                $ModuleArr = AccessModule::getModuleData();
                foreach ($ModuleArr as $rowModuleArr) {
                    foreach ($rowModuleArr as $value) {
                        $iAModuleId[] = $value;
                    }
                }
                ## Get Role Array for all module by access group
                $roleArr = AccessGroupModuleRole::getGroupModuleData($iAGroupId,$iAModuleId);
                ## Get Parent Array
                $parentArr = AccessModule::getParentData();
                foreach ($parentArr as $rowParentArr) {
                    foreach ($rowParentArr as $value) {
                        $iParentId[] = $value;
                    }
                }
                

                for ($i = 0, $mo = count($ModuleArr); $i < $mo; $i++) {
                    $acc_mod_assoc_arr[$ModuleArr[$i]['parent_id']][] = $ModuleArr[$i];
                }

                for ($j = 0, $ji = count($roleArr); $j < $ji; $j++) {
                    $asso_array[$roleArr[$j]['access_module_id']] = $roleArr[$j];
                }
                if($tab == 'module'){
                    $par_am_array = self::getChildAccessModuleList(0, "", 4, 0, 5, $acc_mod_assoc_arr, $asso_array);
                }
                if($tab == 'report'){
                    $par_am_array = self::getChildAccessModuleList(4,"","",0,5,$acc_mod_assoc_arr, $asso_array); 
                }
                if(is_array($par_am_array)){
                    // Modules Permission Data
                    $modules_arr = &$par_am_array;
                    for ($i = 0; $i < count($modules_arr); $i++) {
                        $str_list = $str_add = $str_edit = $str_delete = $str_status = $str_export =  $str_print = "";
                        $span_list = $span_add = $span_edit = $span_delete = $span_status = $span_export = $span_print = "";
                        $iAModuleId = $modules_arr[$i]['access_module_id'];
                        if (isset($modules_arr[$i]['list']) && $modules_arr[$i]['list'] == "1") {                           
                           $span_list = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['list'] == "1") ? "1" : "0" : "0");
                            if($span_list == '1')
                                $str_list = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_list = '<span class="badge badge-secondary">No</span>';
                        
                        } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                            $str_list = 'Listing';
                        }
                        if (isset($modules_arr[$i]['add']) && $modules_arr[$i]['add'] == "1") {
                            $span_add = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['add'] == "1") ? "1" : "0" : "0");
                            if($span_add == '1')
                                $str_add = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_add = '<span class="badge badge-secondary">No</span>';

                        } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                            $str_add = 'Add';
                        }
                        if (isset($modules_arr[$i]['edit']) && $modules_arr[$i]['edit'] == "1") {
                            $span_edit = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['edit'] == "1") ? "1" : "0" : "0");
                            if($span_edit == '1')
                                $str_edit = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_edit = '<span class="badge badge-secondary">No</span>';

                        } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                            $str_edit = 'Edit';
                        }
                        if (isset($modules_arr[$i]['delete']) && $modules_arr[$i]['delete'] == "1") {
                             $span_delete = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['delete'] == "1") ? "1" : "0" : "0");
                            if($span_delete == '1')
                                $str_delete = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_delete = '<span class="badge badge-secondary">No</span>';

                        } else if ($modules_arr[$i]['parent_id'] == "0" && $modules_arr[$i]['list'] == "2" && $modules_arr[$i]['add'] == "2" && $modules_arr[$i]['edit'] == "2" && $modules_arr[$i]['delete'] == "2") {
                            $str_delete = 'Delete';
                        }
                        if (isset($modules_arr[$i]['export']) && $modules_arr[$i]['export'] == "1") {
                            $span_export = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['export'] == "1") ? "1" : "0" : "0");
                            if($span_export == '1')
                                $str_export = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_export = '<span class="badge badge-secondary">No</span>';
                        }
                        if (isset($modules_arr[$i]['print']) && $modules_arr[$i]['print'] == "1") {
                              $span_print = ((!empty($asso_array) && !empty($asso_array[$iAModuleId])) ? ($asso_array[$iAModuleId]['print'] == "1") ? "1" : "0" : "0");
                            if($span_print == '1')
                                $str_print = '<span class="badge badge-primary">Yes</span>';
                            else
                                $str_print = '<span class="badge badge-secondary">No</span>';
                        }
                        if (isset($modules_arr[$i]['date_period']) && !empty($modules_arr[$i]['date_period'])) {
                            $str_dt='';
                            if(!empty($asso_array) && !empty($asso_array[$iAModuleId])){
                                $str_dt = $date_periodArr[$asso_array[$iAModuleId]['date_period']]['label'];
                            }else{
                                $str_dt ='---';
                            }
                        }
                        if($tab == 'module'){
                            $data[] = array(
                                ($j = $i + 1),
                                $modules_arr[$i]['vPath'],
                                $str_list,
                                $str_add,
                                $str_edit,
                                $str_delete,
                            );
                        }
                        if($tab == 'report'){
                            $data[] = array(
                                ($j = $i + 1),
                                $modules_arr[$i]['vPath'],
                                $str_dt,
                                $str_export,
                                $str_print
                            );
                        }
                        
                    }
                }
            }else if($tab == 'general'){
                $general_arr = AdminAccessGeneral::getGeneralData($iAGroupId);
                 $cnt_gen_arr = count($general_arr);
                 for ($i = 0; $i <$cnt_gen_arr; $i++) {
                    $access_general_id = $general_arr[$i]['id'];
                    $checked_gen = (!empty($general_arr[$i]['access_group_id']))?'1':'';
                    $first_col = ($checked_gen) ? '<span class="badge badge-primary">Yes</span>' : '<span class="badge badge-secondary">No</span>';
                    $data[] = array(
                        ($j = $i + 1),
                        $first_col,
                        $general_arr[$i]['name']
                    );
                 }
            }
            $records["data"] = $data;
            $sEcho = (isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 1);
            $records["draw"] = $sEcho;
            $records["recordsTotal"] = 100;
            $records["recordsFiltered"] = 100;

            echo json_encode($records);
        } else { 
            abort(404);
        } 
    }
}
?>
