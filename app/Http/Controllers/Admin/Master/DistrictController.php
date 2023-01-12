<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\User;
use Config;
use Validator;
use App;
use Auth; 
use App\GlobalClass\Design;
use App\Models\Country;
use App\Models\State;
use App\Models\Districts;
use App\Models\Zone;
use App\Models\UserSalesStateZone;


class DistrictController extends AbstractController
{
    public $_districtModel = '';
    protected $_districtLanguageModel = '';
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('accessrights');
        if($this->_districtModel == ''){
            $this->_districtModel = new Districts();
        }
    }


    /**
     * Show the All district records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.master.district_list',array('currentClass'=>$this));
    }
    
    /**
    *
    * Function : Ajax :  Get district list with html
    * @param  array $request
    * @return  json $response
    *
    */
    public function districtslist(Request $request)
    {
        $data = $request->all();
        $stateid = $data['stateid'];
        if(!empty($data['zsmid'])){
            $userdata=UserSalesStateZone::UserSalesStateZoneByUserID($data['zsmid']);
            if(!empty($userdata)){
                $districtData = Districts::getDistrictsStateZoneWise($stateid,$userdata[0]['zone_id']);
                return response()->json($districtData);
            }else{
                return '';
            }
        }
        $districtData = Districts::getDistrictsStateWise($stateid,null);
       // $returnHTML = view('admin.master.districthtml')->with('districtData', $districtData)->render();
       return response()->json($districtData);
    }

     public function districtslistData(Request $request)
    {
        $data = $request->all();
        $districtid = $data['districtid'];
        $districtData = Districts::getDistrictsStateWise($districtid);
       // $returnHTML = view('admin.master.districthtml')->with('districtData', $districtData)->render();
       
        return response()->json($districtData);
    }
    /**
    * Prepare Grid data
    * @return array
    */
    public function ajaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        $search_coulmn_arr = array(
            "district_name",
            "district_code",
            "country_name",
            "state_name",
            "status"
        );
        $search_arr = array();
     
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
          $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
      
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
        ## for paggnation getting data
        $tot_records_data = $this->_districtModel->getDistrictsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "district_name";
                    break;
                case "2":
                    $sort = "district_code";
                    break;
                case "3":
                    $sort = "country_name";
                    break;
                case "4":
                    $sort = "state_name";
                    break;
                case "5":
                    $sort = "zone_name";
                    break;
                case "6":
                    $sort = "created_at";
                    break;
                case "7":
                    $sort = "status";
                    break;
                default:
                    $sort = "created_at";
            }
        } else {
            $sort = "created_at";
        }
        $data = $this->_districtModel->getDistrictsData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
     //   var_dump($data);die();
        $cnt = count($data);
        
        $stausArr = $this->_districtModel->renderDistrictStatus();
        for ($i = 0; $i < $cnt; $i++){
            $j = $i + 1;
            $status = $stausArr[$data[$i]->status]['label'];
 
            $status_color = Config::get('constants.status_color.' . $status);            
            $encoded_id = base64_encode($data[$i]->id);
            $edit = '---';
            $view = '';
            if(per_hasModuleAccess('Districts', 'Edit')){
            $edit = ' <a href="' . route('districts',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('Districts', 'View')) {
            $view = ' <a href="' . route('districts',['mode' => 'view','id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
            }

            $status = Design::blade('status',$status,$status_color);

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->district_name,
                $data[$i]->district_code,
                $data[$i]->country_name,
                $data[$i]->state_name,
                $data[$i]->zone_name,
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
    * Function  : Add/Edit Districts
    * @return template
    */
    public function add($mode = NULL, $id = NULL) {
        $data = $language = $district_language = array();
        if(isset($mode) && $mode != ""){
            // Active Countries
            $stateData = $country = $state = $zoneArray = array();
            $country = Country::getAllActiveCountries(['status'=>Country::ACTIVE]);
            $zoneArray = Zone::getAllActiveZone();
            if (isset($id) && $id != "") {
                $id = base64_decode($id);
                //$id = substr($id, 3, -3);

                $data = Districts::getDistrictsDataFromId($id);
                $zoneArray = Zone::getZonesStateWise($data[0]['state_id']);

                if($mode == 'edit'){ 
                    if(isset($data[0]['country_id']) && !empty($data[0]['country_id']))
                    {
                        $mode = 'Update';
                        $stateData = State::getStateCountryWise($data[0]['country_id']); 
                    } 
                    return view('admin.master.district_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'country' => $country,'currentClass'=>$this,'stateData'=>$stateData,'zoneArray'=>$zoneArray]);   
                }else if($mode == 'view' ){
                    $mode = 'View';
                    if(!empty($data[0]['country_id']))
                    {
                        $country = Country::getCountryNameById($data[0]['country_id']);
                        $where_state_arr = array('country_id' => $data[0]['country_id'], 'status' => State::ACTIVE);
                        $state = State::getStateDataFromCountryId($where_state_arr);
                    } 
                    return view('admin.master.district_view')->with(['mode' => $mode, 'data' => $data,'country' => $country,'state'=> $state,'zoneArray'=>$zoneArray]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.district_add')->with(['mode' => $mode,'country' => $country,'currentClass'=>$this,'zoneArray'=>$zoneArray]);
            } 
        }
        abort(404);
    }


    /**
    * Function :  Update status
    * @return boolean
    */
    private function updateStatus($id = array(), $status) {
        if (!empty($id)) {
            if (isset($status) && $status == "Active") {
                $status = 1;
            }
            elseif (isset($status) && $status == "Inactive") {
                $status = 2;
            }
            $update = Districts::updateDistrictStatus($id, $status);
        }
        if(isset($update))
            return true;
        
        return false;
    }

    /**
    * Function : Save Districts
    */
    public function PostAjaxData(Request $request)
    {
        $records = $data = $result = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        
        if(isset($post['customActionName'])){
           
            $activeAdminId = Auth::guard('admin')->user()->id;
            
            $module_id = Config::get('constants.module_id.Districts');
            // Active/Inactive status
            if($post['customActionName'] == "Active" || $post['customActionName'] == 'Inactive'){
                
                $update_status = $this->updateStatus($post['id'], $post['customActionName']);
                
                if ($update_status == true) {
                    if ($post['customActionName'] == "Active") {
                        $records["customActionStatus"] = "OK";
                        $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_ACTIVATED'), count($post['id']));
                        $result[0]['isError'] = 0;
                    } else {
                        $records["customActionStatus"] = "OK";
                        $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_INACTIVATED'), count($post['id']));
                        $result[0]['isError'] = 0;
                    }
                    
                    // Start Add Activity log
                    $tempTxt = (is_array($post['id']) ? implode(',', $post['id']) : $post['id']);
                    if(is_array($post['id']) && !empty($post['id'])){
                        foreach($post['id'] as $id){
                            $log_text = "Districts Id " . $id . " Status updated to ".$post['customActionName'];
                            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                             usr_addActivityLog($activity_arr);
                        }
                    }else{
                        $log_text = "Districts Id " . $post['id'] . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                    // End Add Activity log
                } else {
                    $records["customActionMessage"] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    $result[0]['isError'] = 1;
                }
                $result[0]['msg'] = $records["customActionMessage"];
                return Response::json($result);
            }
            elseif($post['customActionName'] == "Add" || $post['customActionName'] == "Update"){
                // Check validation
                $validator = $this->validateFields($post);

                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result[0]['isError'] = 1;
                        $result[0]['msg'] = $validate_msg;
                        return Response::json($result);
                    }
                }
                // Prepare array for add/update
                $arraytoDb = array();
                $arraytoDb['district_name'] = (isset($post['district_name']) ? trim($post['district_name']) : '');
                $arraytoDb['district_code'] = (isset($post['district_code']) ? trim(strtoupper($post['district_code'])) : '');
                $arraytoDb['country_id'] = (isset($post['country_id']) ? trim(strtoupper($post['country_id'])) : '');
                $arraytoDb['state_id'] = (isset($post['state_id']) ? trim(strtoupper($post['state_id'])) : '');
                $arraytoDb['zone_id'] = (isset($post['zone_id']) ? trim(strtoupper($post['zone_id'])) : '');
                $arraytoDb['display_order'] = (isset($post['display_order']) ? $post['display_order'] : '0');
                $arraytoDb['status'] = (isset($post['status']) ? $post['status'] : Districts::INACTIVE);
                $arraytoDb['created_at'] = date_getSystemDateTime();
                $arraytoDb['updated_at'] = date_getSystemDateTime();
                // echo "<pre>";print_r($arraytoDb);exit;
                if($post['customActionName'] == "Add"){
                    try{
                        
                        $flag = Districts::addDistricts($arraytoDb);
                        if(!empty($flag)){
                            $result[0]['isError'] = 0;
                            $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
                            $insertedId = (isset($flag['id']) ? $flag['id'] : '');
                          
                            // Start Add Activity log
                            $reference_id = (isset($insertedId) ? $insertedId : "");
                            $log_text = "Districts " . $arraytoDb['district_name'] . " added";
                            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $arraytoDb['district_code'], "log_text" => $log_text, "req_data" => $post);
                             usr_addActivityLog($activity_arr);
                            // End Add Activity log
                        }
                    }
                    catch(Exception $e){
                        $result[0]['isError'] = 1;
                        $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    }
                    return Response::json($result);
                }
                elseif($post['customActionName'] == "Update"){
                    if(isset($post['id'])){
                        // If id posted and data is avaliable in database, update
                        $_data = Districts::find($post['id']);
                        $data = json_decode(json_encode($_data),true);
                        if($data['id']){
                            unset($arraytoDb['created_at']);
                            try{
                                $flag = Districts::updateDistricts($data['id'],$arraytoDb);
                                if(isset($flag)){
                                    $result[0]['isError'] = 0;
                                    $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                                    
                                    // Start Add Activity log
                                    $reference_id = (isset($post['id']) ? $post['id'] : "");
                                    $log_text = "Districts " . $arraytoDb['district_name'] . " updated";
                                    $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $arraytoDb['district_code'], "log_text" => $log_text, "req_data" => $data);
                                     usr_addActivityLog($activity_arr);
                                    // End Add Activity log
                                }
                            }
                            catch(Exception $e){
                                $result[0]['isError'] = 1;
                                $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                            }
                        }
                    }
                    return Response::json($result);
                }
            }
            elseif($post['customActionName'] == "Delete"){
                $delete = $this->deleteRecord($post['id']);
                if ($delete == true) {
                    $records["customActionStatus"] = "OK";
                    $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), count($post['id']));
                    $result[0]['isError'] = 0;
                } else {
                    $records["customActionMessage"] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                    $result[0]['isError'] = 1;
                }
              
                // End Add Activity log
                $result[0]['msg'] = $records["customActionMessage"];
                return Response::json($result);
            }
        }
        return Response::json($result);
    }

   

    /** 
    * Delete Records
    * @return boolean
    */
    private function deleteRecord($id = array())
    {
        if (!empty($id)) {
            $district = Districts::getDistrictsDataFromId($id);
            if (!empty($district)) {
                $districtIdArr = $districtCodes = array();
                foreach ($district as $rowLanguage) {
                    $districtIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                    $districtCodes[] = (isset($rowLanguage['district_code'])?$rowLanguage['district_code']:"");
                }
                if(!empty($districtIdArr)){
                    # Delete records from Districts
                    $delete_district = Districts::deleteDistricts($districtIdArr);
                }
                
            }   
        }

        if (isset($delete_district)) {
            return true;
        }
        return false;
    }

    /**
    * Validation for form
    * @return validator object
    */
    private function validateFields($data = array()) {
        if($data['customActionName'] == "Add") {
            $rules = array(
                'district_name' => 'required|min:2|max:100',
                'district_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_district,district_code,NULL,id',
                'country_id' => 'required',
                'state_id' => 'required',
            );
        } else {
            $rules = array(
                'district_name' => 'required|min:2|max:100',
                'district_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_district,district_code,'.$data['id'].',id',
                'country_id' => 'required',
                'state_id' => 'required',
            );
        }
        $messages = array(
            'district_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Districts Name'),
            'district_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Districts Name'),
            'district_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Districts Name'),
            'district_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Districts Code'),
            'district_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Districts Code'),
            'district_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '3', 'Districts Code'),
            'district_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Districts Code, only characters allowed'),
            'district_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Districts Code'),
            'country_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country'),
            'state_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State'),
        );
        return Validator::make($data, $rules, $messages);
    }
}
