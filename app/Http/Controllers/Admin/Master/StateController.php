<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Validator;
use Config;
use App;
use App\GlobalClass\Design;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class StateController extends AbstractController {
	
	public function __construct(){
		$this->middleware('accessrights');  
        $this->destinationLanPath = Config::get('path.language_path');
    }

    /**
     * Function : List  All state records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('admin.master.state_list');
    }

    /**
    *
    * Function : State listing : Get State data and pass json response to data table
    * @return  json $response
    *
    */ 
    public function ajaxData(Request $request) {
		$records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        /*$search_coulmn_arr = array(
            "0" => "staName",
            "1" => "staCode",
            "2" => "couCode",
            "3" => "staStatus",
        );*/
        $search_arr = array();
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
          $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
                 
        $tot_records_data = State::getStateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
        $iTotalRecords = count($tot_records_data);

        $iDisplayLength = (isset($post['length']) ? intval($post['length']) : 50);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sorton = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC');

        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "1":
                    $sort = "state_name";
                    break;
                case "2":
                    $sort = "state_code";
                    break;
                case "3":
                    $sort = "mc.country_name";
                    break;
                case "4":
                    $sort = "display_order";
                    break;
                case "5":
                    $sort = "created_at";
                    break;
                case "6":
                    $sort = "status";
                    break;
                default:
                    $sort = "created_at";
            }
        } else {
            $sort = "created_at";
        }
        $data = State::getStateData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
		//echo "<pre>";print_r($data);exit;
        $cnt = count($data);
        
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);

            $encoded_id = base64_encode($data[$i]->id);
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('States', 'Edit')) {
                $edit = ' <a href="' . route('state',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('States', 'View')) {
                $view .= '<a href="' . route('state',['mode' => 'view','id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
            }
             
            $status = Design::blade('status',$status,$status_color);

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->state_name,
                $data[$i]->state_code,
                $data[$i]->country_name,
				$data[$i]->display_order,
                 (!empty($data[$i]->created_at)) ? date('d-M-Y',strtotime($data[$i]->created_at)):'',
                $status,
                $view.$edit
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
	}

     /**
    *
    * Function : change status , add , edit and delete data
    * @param  array $request
    * @return  json $response
    *
    */
    public function PostAjaxData(Request $request) {
        $records = $data = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        if (isset($post['customActionName'])) {
			$activeAdminId = Auth::guard('admin')->user()->id;;
            
            $module_id = Config::get('constants.module_id.state');
            if ($post['customActionName'] == "Active" || $post['customActionName'] == 'Inactive') {
                $update_status = $this->updateStatus($post['id'], $post['customActionName']);
                if ($update_status == 1) {
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
				// Start Add Activity log
				$tempTxt = (is_array($post['id']) ? implode(',', $post['id']) : $post['id']);
				if(is_array($post['id']) && !empty($post['id'])){
					foreach($post['id'] as $id){
						$log_text = "State " . $id . " Status updated to ".$post['customActionName'];
						$activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
						 usr_addActivityLog($activity_arr);
					}
				}else{
					$log_text = "State " . $post['id'] . " Status updated to ".$post['customActionName'];
					$activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
					 usr_addActivityLog($activity_arr);
				}
                $result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            } elseif ($post['customActionName'] == "Add") {
                $validator = $this->validateFields($post);
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }
                $insert = $this->insertRecord($post);
				if(isset($insert) && !empty($insert)){
					$result_arr[0]['isError'] = $insert['isError'];
					$result_arr[0]['msg'] = $insert['msg'];
				}else{
					$result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
				}
				return Response::json($result_arr);
            } elseif ($post['customActionName'] == "Update") {
                $validator = $this->validateFields($post);
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }else {
                   
                }
                $update = $this->updateRecord($post);
                if(isset($update) && !empty($update)){
					$result_arr[0]['isError'] = $update['isError'];
					$result_arr[0]['msg'] = $update['msg'];
				}else{
					$result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
				}
                return Response::json($result_arr);
            } elseif ($post['customActionName'] == "Delete") {
                $delete = $this->deleteRecord($post['id']);
                if ($delete == 1) {
                    $records["customActionStatus"] = "OK";
                    $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), count($post['id']));
                    $result_arr[0]['isError'] = 0;
                } else {
                    $records["customActionMessage"] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    $result_arr[0]['isError'] = 1;
                }
                $result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            } elseif ($post['customActionName'] == "DeleteImage") {
                $deleteimage = $this->deleteImage($post['id'],$post['flag']);
                if ($deleteimage['isError'] == 1) {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = $deleteimage['msg'];
                } else {
                    $result_arr[0]['isError'] = 0;
                    $result_arr[0]['msg'] = 'Image deleted successfully';
                }
                return Response::json($result_arr);
            }
        }
    }

    /**
    *
    * Function : Load form to Add/Edit/view  State
    * @param  array $mode
    * @param  int $id
    * @return  json $response
    *
    */ 
    public function add($mode = NULL, $id = NULL) {
        $data = $language = $state_language = $country = array();
		##Get Active Countries
		$country = Country::getAllActiveCountries(['status'=>Country::ACTIVE]);
        if(isset($mode) && $mode != ""){
            if (isset($id) && $id != "") {
                $id = base64_decode($id);
                
                $data = State::getStateDataFromId($id);
				
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin.master.state_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'country'=>$country]);
                }elseif ($mode == 'view'){
                    $mode = 'View';
                    $country = Country::getCountryNameById($data[0]['country_id']);
                    $data[0]['country_name']=$country[0]['country_name'];
                    return view('admin.master.state_view')->with(['mode' => $mode, 'data' => $data]);
                } 
            } elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.master.state_add')->with(['mode' => $mode,'country'=>$country]);
            }
        }
        abort(404);
    }

    /**
    *
    * Function : Validation for add and edit form
    * @param  array $data 
    *
    */  
    private function validateFields($data = array()) {
		if($data['customActionName'] == "Add") {
			$rules = array(
				'country_id' => 'required',
				'state_name' => 'required|min:2|max:100|unique:mas_state,state_name,NULL,id',
				'state_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_state,state_code,NULL,id',
			);
		} else {
			$rules = array(
				'country_id' => 'required',
				'state_name' => 'required|min:2|max:100|unique:mas_state,state_name,'.$data['id'].',id',
				'state_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_state,state_code,'.$data['id'].',id',
			);
		}
        $messages = array(
            'country_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country'),
            'state_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State Name'),
            'state_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'State Name'),
            'state_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'State Name'),
            'state_name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'State Name'),
            'state_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State Code'),
            'state_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'State Code'),
            'state_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '5', 'State Code'),
            'state_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'State Code'),
            'state_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'State Code'),
		);
        return Validator::make($data, $rules, $messages);
    }

    /**
    *
    * Function : insert record
    * @param  array $data 
    * @return array $result
    */        
    private function insertRecord($data = array()) {
		$sta_lang_array = array();
        $activeAdminId = Auth::guard('admin')->user()->id;
		
        $insert_array = array(
            'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
            'state_code' => (isset($data['state_code']) ? $data['state_code'] : ""),
            'state_name' => (isset($data['state_name']) ? $data['state_name'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : "2"),
			'created_at'=> date_getSystemDateTime(),
        );
        ## state data insert
        $insert = State::addState($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] : "");
            
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
			$module_id = Config::get('constants.module_id.state');
			$log_text = "State " . $data['state_name'] . " added";
			$activity_arr = array("admin_id" =>$activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $insert['id'], "log_text" => $log_text, "req_data" => $data);
			 usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result;
    }
    /**
    *
    * Function : Update Record
    * @param  array $data 
    * @return array $result
    */   
    private function updateRecord($data = array(), $files = array()) {
		$sta_lang_array = array();
        $id = (isset($data['id']) ? $data['id'] : "");
        $activeAdminId = Auth::guard('admin')->user()->id;
		
        $update_array = array(
            'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
            'state_name' => (isset($data['state_name']) ? $data['state_name'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : "2"),
			'updated_at'=> date_getSystemDateTime(),
        );
        $update = State::updateState($id, $update_array);

        if (isset($update)) {

            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
			$module_id = Config::get('constants.module_id.state');
			$log_text = "State " . $data['state_name'] . " added";
			$activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $id, "log_text" => $log_text, "req_data" => $data);
			 usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result;
    }

    /**
    *
    * Function : Update state status
    * @param  int $id 
    * @param  string status
    * @return boolean 
    */   
    private function updateStatus($id = array(), $status) {
        if (!empty($id)) {
            if (isset($status) && $status == "Active") {
                $status = 1;
            } elseif (isset($status) && $status == "Inactive") {
                $status = 2;
            }
            $update = State::updateStatesStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
    *
    * Function : Delete Records
    * @param  int $id 
    * @return boolean 
    */    
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $state = State::getStateDataFromId($id);
            if (!empty($state)) {
                $stateIdArr = $stateCodeArr = array();
                foreach ($state as $rowLanguage) {
                    $stateIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                }
                if(!empty($stateIdArr)){
                    # Delete records from State
                    $delete_state = State::deleteStates($stateIdArr);
                }
				
            }   
        }
        if (isset($delete_state)) {
            ##Delete state realted  city by state Code
            if(!empty($stateCodeArr)){
                 $delete_city = City::deleteCityByCountryStateId('state_id',$stateIdArr);
            }
            return 1;
        } else {
            return 0;
        }
    }
}
?>