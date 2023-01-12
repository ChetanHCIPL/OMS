<?php
namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Input;
use Validator;
use Config;
use App;
use DB;
use App\GlobalClass\Design;
use App\Models\Zone;
use App\Models\Country;
use App\Models\State;

class ZoneController extends Controller {

    public $_zoneModel = '';

    public function __construct(){
		$this->middleware('admin');
        $this->middleware('accessrights');

        if($this->_zoneModel == ''){
            $this->_zoneModel = new Zone();
        }
    }

    /**
    *  Load Zone List Page
    *
    * @param    void
    * @return   template
    */
    public function index() {
        return view('admin/master/zone_list');
    }
    /**
    *  Load Zone List Ajax
    *
    * @param    void
    * @return   template
    */
    public function zoneListAjax(Request $request) {
        $post = $request->all();
        return Zone::getZonesStateWise($post['sid']);
        //return view('admin/master/zone_list');
    }

    /**
    * Get Zone data and pass json response to data table
    *
    * @param  array $request
    * @return json $records
    */
    public function ajaxData(Request $request) {

        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->all();

        $search_arr = array();
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = Zone::getZoneData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "zone_name";
                    break;
                case "2":
                    $sort = "zone_code";
                    break;
                case "3":
                    $sort = "country_name";
                    break;
                case "4":
                    $sort = "state_name";
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
        $data = Zone::getZoneData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {

            if($data[$i]->status == Zone::ACTIVE){
                $status =  "Active" ;
            }else if($data[$i]->status == Zone::INACTIVE){
                $status =  "Inactive" ;
            } 
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);

            $encoded_id = base64_encode($data[$i]->id);
             
            $edit = '---';
            $view = '';
            /*if (per_hasModuleAccess('Zone', 'Edit')) {
                $edit = ' <a href="javascript:void(0)" onclick="add_edit_modal('.$encoded_id.',\'Update\')" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }*/
            if(per_hasModuleAccess('Zone', 'Edit')){
            $edit = ' <a href="' . route('zone',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('Zone', 'View')) {
            $view = ' <a href="' . route('zone',['mode' => 'view','id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
            }

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                '<span>'.$data[$i]->zone_name.'</span> - <span>'.$data[$i]->state_name.'</span>',
                '<span>'.$data[$i]->zone_code.'</span>',
                '<span>'.$data[$i]->country_name.'</span>',
                (!empty($data[$i]->created_at)) ? date('d-M-Y',strtotime($data[$i]->created_at)):'',
                '<span>'.$status.'</span>',
                $view.$edit
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

     /**
    *Function : Get perform actions like Add & Update & Delete Record
    *
    * @param  array $request
    * @return  json
    */
    public function PostAjaxData(Request $request) {

        $records = $data = array();
        $records["data"] = array();
        $err_msg = NULL;
        $post = $request->all();

        if (isset($post['customActionName'])) {
            $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.zone');

            /**
             * For Bulk Action Like Active, Deactivate and Delete
             */
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
                        $log_text = "Zone " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Zone " . $post['id'] . " Status updated to ".$post['customActionName'];
                    $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
                     usr_addActivityLog($activity_arr);
                }
                // End Add Activity log
                $result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            }

            /**
             * For Create New Entry 
             */
            if ($post['customActionName'] == "Add") {

                $validator = $this->validateAdd($post);
                
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }
                $insert = $this->insertRecord($post);
                if ($insert == 1) {
                    $result_arr[0]['isError'] = 0;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
                } else {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }
                return Response::json($result_arr);
            }
            /**
             * For Update Existing Entry
             */
             elseif ($post['customActionName'] == "Update") {

                $validator = $this->validateEdit($post);

                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }

                $update = $this->updateRecord($post);
                if ($update == 1) {
                    $result_arr[0]['isError'] = 0;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                } else {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }
                return Response::json($result_arr);
            }
            /**
             * For Delete Existing Entry
             */
             elseif ($post['customActionName'] == "Delete") {
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
            }
        }
    }

     /**
    *Function : Validation for insert new record
    *
    * @param  array $data
    * @return Validator object
    */
    private function validateAdd($data = array()) {
        $rules = array(
            'zone_name' => 'required|max:150',
            'zone_code' => 'required|min:2|max:5|unique:mas_zone,zone_code,NULL,id',
            'country_id' => 'required',
            'state_id' => 'required',
        );
        $messages = array(
            'zone_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Zone Name'),
            'zone_name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Zone Name'),
            'zone_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '150', 'Zone Name'),
            'zone_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Zone Code'),
            'zone_code.min' => sprintf(Config::get('messages.validation_msg.maxlength'), '2', 'Zone Code'),
            'zone_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '5', 'Zone Code'),
            'zone_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Zone Code'),
            'country_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country'),
            'state_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State'),
        );
        return Validator::make($data, $rules, $messages);
    }
     /**
    * Function : Validation for update record
    *
    * @param  array $data
    * @return Validator object
    */
    private function validateEdit($data = array()) {
        $rules = array(
            'zone_name' => 'required|max:150',
            'zone_code' => 'required|min:2|max:5|unique:mas_zone,zone_code,' . $data['id'] . ',id',
            'country_id' => 'required',
            'state_id' => 'required',
        );
        $messages = array(
            'zone_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Zone Name'),
            'zone_name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Zone Name'),
            'zone_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '150', 'Zone Name'),
            'zone_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Zone Code'),
            'zone_code.min' => sprintf(Config::get('messages.validation_msg.maxlength'), '2', 'Zone Code'),
            'zone_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '5', 'Zone Code'),
            'zone_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Zone Code'),
            'country_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country'),
            'state_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State'),
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    * Function : Insert Record of Zone 
    *
    * @param  array $data
    * @return $result
    */            
    private function insertRecord($data = array()) {
        $insert_array = array(
            'zone_name' => (isset($data['zone_name']) ? $data['zone_name'] : ""),
            'zone_code' => (isset($data['zone_code']) ? $data['zone_code'] : ""),
            'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
            'state_id' => (isset($data['state_id']) ? $data['state_id'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : ""),
            'created_at' => date_getSystemDateTime(),
            'status' => (isset($data['status']) ? $data['status'] : Zone::INACTIVE),
        );
        $insert = Zone::addZone($insert_array);
        if (!empty($insert)) {
            $result = 1;
            $insertedId = (isset($insert['id']) ? $insert['id'] : "");
            
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.zone');
            $log_text = "Zone " . $data['zone_name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $reference_id, "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
    * Function : Update Record of Zone
    *
    * @param  array $data
    * @return $result;
    */  
    private function updateRecord($data = array()) {
        $id = (isset($data['id']) ? $data['id'] : "");
        $update_array = array(
            'zone_name' => (isset($data['zone_name']) ? $data['zone_name'] : ""),
            'zone_code' => (isset($data['zone_code']) ? $data['zone_code'] : ""),
            'country_id' => (isset($data['country_id']) ? $data['country_id'] : ""),
            'state_id' => (isset($data['state_id']) ? $data['state_id'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : ""),
            'updated_at' => date_getSystemDateTime(),
            'status' => (isset($data['status']) ? $data['status'] : Zone::INACTIVE),
        );
         
        $update = Zone::updateZone($id,$update_array);

       if (isset($update)) {
            $result = 1;           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.zone');
            $log_text = "Zone " . $data['zone_name'] . " edited";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => "", "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result = 0;
        }
        return $result;
    }

     /**
    *
    * Function : Update status
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
            $update = Zone::updateZoneStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }

     /** 
    * Function : Delete Records of  Zone
    * @param  array $id
    * @return boolean
    */
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $zone = Zone::getZoneDataFromId($id);
            if(!empty($zone)){
                foreach ($zone as $data) {
                    ## Start Add Activity
                    $reference_id = (isset($data['id']) ? $data['id'] : "");
                    $module_id = Config::get('constants.module_id.zone');
                    $log_text = "Zone ". $data['zone_name']. " deleted";
                    $activity_arr= array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                    usr_addActivityLog($activity_arr);
                    ## End Add Activity
                 }                  
            $delete_var = Zone::deleteZoneData($id);
            }
            
            if (isset($delete_var)) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
    * Function  : Add/Edit Zone
    * @return template
    */
    public function add($mode = NULL, $id = NULL) {
        $data =  array();
        if(isset($mode) && $mode != ""){
            // Active Countries
            $stateData = $country = $state = array();
            $country = Country::getAllActiveCountries(['status'=>Country::ACTIVE]);
            if (isset($id) && $id != "") {
                $id = base64_decode($id);
               
                $data = Zone::getZoneDataFromId($id);
                if($mode == 'edit'){ 
                    if(isset($data[0]['country_id']) && !empty($data[0]['country_id']))
                    {
                        $mode = 'Update';
                        $stateData = State::getStateCountryWise($data[0]['country_id']); 
                    } 
                    return view('admin.master.zone_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'country' => $country,'currentClass'=>$this,'stateData'=>$stateData]);   
                }else if($mode == 'view' ){
                    $mode = 'View';
                    if(!empty($data[0]['country_id']))
                    {
                        $country = Country::getCountryNameById($data[0]['country_id']);
                        $where_state_arr = array('country_id' => $data[0]['country_id'], 'status' => State::ACTIVE);
                        $state = State::getStateDataFromCountryId($where_state_arr);
                    } 
                    return view('admin.master.zone_view')->with(['mode' => $mode, 'data' => $data,'country' => $country,'state'=> $state]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.zone_add')->with(['mode' => $mode,'country' => $country,'currentClass'=>$this]);
            } 
        }
        abort(404);
    }
}