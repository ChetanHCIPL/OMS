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
use App\Models\City;

class CityController extends AbstractController
{
    public $_cityModel = '';
    protected $_cityLanguageModel = '';
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('accessrights');
        $this->destinationLanPath = Config::get('path.language_path');
        $this->destinationPath = Config::get('path.city_path');
        $this->size_array = Config::get('constants.city_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');

        if($this->_cityModel == ''){
            $this->_cityModel = new City();
        }
    }


    /**
     * Show the All city records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.master.city_list',array('currentClass'=>$this));
    }

    /**
    *
    * Function : Ajax :  Get city list with html
    * @param  array $request
    * @return  json $response
    *
    */
    /*
    public function citylist(Request $request)
    {
        $data = $request->all();
        $cityCode = $data['citycode'];
        $cityData = City::getCityStateWise($cityCode);
        $returnHTML = view('admin.master.cityhtml')->with('cityData', $cityData)->render();
        //return response()->json(array('success' => 'success', 'html'=>$returnHTML));
        return response()->json($cityData);
    }*/
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
            "city_name",
            "city_code",
            "country_code",
            "state_code",
            "status"
        );
        $search_arr = array();
     
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
          $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
      
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
        ## for paggnation getting data
        $tot_records_data = $this->_cityModel->getCityData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "city_name";
                    break;
                case "2":
                    $sort = "city_code";
                    break;
                case "3":
                    $sort = "country_code";
                    break;
                case "4":
                    $sort = "state_code";
                    break;
                case "6":
                    $sort = "status";
                    break;
                default:
                    $sort = "display_order";
            }
        } else {
            $sort = "display_order";
        }
        $data = $this->_cityModel->getCityData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
        
        $stausArr = $this->_cityModel->renderCityStatus();
        for ($i = 0; $i < $cnt; $i++){
            $j = $i + 1;
            $status = $stausArr[$data[$i]->status]['label'];
 
            $status_color = Config::get('constants.status_color.' . $status);            
            $encoded_id = base64_encode($data[$i]->id);
            $edit = '---';
            $view = '';
            if(per_hasModuleAccess('Cities', 'Edit')){
            $edit = ' <a href="' . route('city',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('Cities', 'View')) {
            $view = ' <a href="' . route('city',['mode' => 'view','id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a> ';
            }

            $status = Design::blade('status',$status,$status_color);

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->city_name,
                $data[$i]->city_code,
                $data[$i]->country_code,
                $data[$i]->state_code,
                $status ,
                $view.$edit
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

    /**
    * Function  : Add/Edit City
    * @return template
    */
    public function add($mode = NULL, $id = NULL) {
        $data = $language = $city_language = array();
        if(isset($mode) && $mode != ""){
            // Active Countries
            $stateData = $country = $state = array();
            $country = Country::getAllActiveCountries(['status'=>Country::ACTIVE]);
            if (isset($id) && $id != "") {
                $id = base64_decode($id);
                //$id = substr($id, 3, -3);
               
                $data = City::getCityDataFromId($id);
                if(!empty($data) && !empty($data[0]['city_language'])){
                    foreach($data[0]['city_language'] as $rowLanguage){
                        $city_language[$rowLanguage['lang_code']] = (isset($rowLanguage['city_name'])?$rowLanguage['city_name']:"");
                    }
                }
                if($mode == 'edit'){ 
                    if(isset($data[0]['country_id']) && !empty($data[0]['country_id']))
                    {
                        $mode = 'Update';
                        $stateData = State::getStateCountryWise($data[0]['country_id']); 
                    } 
                    return view('admin.master.city_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'country' => $country,'currentClass'=>$this,'stateData'=>$stateData]);   
                }else if($mode == 'view' ){
                    $mode = 'View';
                    if(!empty($data[0]['country_id']))
                    {
                        $country = Country::getCountryNameById($data[0]['country_id']);
                        $where_state_arr = array('country_id' => $data[0]['country_id'], 'status' => State::ACTIVE);
                        $state = State::getStateDataFromCountryId($where_state_arr);
                    } 
                    return view('admin.master.city_view')->with(['mode' => $mode, 'data' => $data,'country' => $country,'state'=> $state]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.city_add')->with(['mode' => $mode,'country' => $country,'currentClass'=>$this]);
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
            $update = City::updateCityStatus($id, $status);
        }
        if(isset($update))
            return true;
        
        return false;
    }

    /**
    * Function : Save City
    */
    public function PostAjaxData(Request $request)
    {
        $records = $data = $result = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        
        if(isset($post['customActionName'])){
           
            $activeAdminId = Auth::guard('admin')->user()->id;
            
            $module_id = Config::get('constants.module_id.cities');
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
                            $log_text = "District Id " . $id . " Status updated to ".$post['customActionName'];
                            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                             usr_addActivityLog($activity_arr);
                        }
                    }else{
                        $log_text = "District Id " . $post['id'] . " Status updated to ".$post['customActionName'];
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
                $arraytoDb['city_name'] = (isset($post['city_name']) ? trim($post['city_name']) : '');
                $arraytoDb['city_code'] = (isset($post['city_code']) ? trim(strtoupper($post['city_code'])) : '');
                $arraytoDb['country_code'] = (isset($post['country_code']) ? trim(strtoupper($post['country_code'])) : '');
                $arraytoDb['state_code'] = (isset($post['state_code']) ? trim(strtoupper($post['state_code'])) : '');
                $arraytoDb['display_order'] = (isset($post['display_order']) ? $post['display_order'] : '0');
                $arraytoDb['status'] = (isset($post['status']) ? $post['status'] : City::INACTIVE);
                $arraytoDb['created_at'] = date_getSystemDateTime();
                $arraytoDb['updated_at'] = date_getSystemDateTime();

                if($post['customActionName'] == "Add"){
                    try{
                        $flag = City::addCity($arraytoDb);
                        if(!empty($flag)){
                            $result[0]['isError'] = 0;
                            $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
                            $insertedId = (isset($flag['id']) ? $flag['id'] : '');
                          
                            // Start Add Activity log
                            $reference_id = (isset($insertedId) ? $insertedId : "");
                            $log_text = "City " . $arraytoDb['city_name'] . " added";
                            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $arraytoDb['city_code'], "log_text" => $log_text, "req_data" => $post);
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
                        $_data = City::find($post['id']);
                        $data = json_decode(json_encode($_data),true);
                        if($data['id']){
                            unset($arraytoDb['created_at']);
                            try{
                                $flag = City::updateCity($data['id'],$arraytoDb);
                                if(isset($flag)){
                                    $result[0]['isError'] = 0;
                                    $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                                    
                                    // Start Add Activity log
                                    $reference_id = (isset($post['id']) ? $post['id'] : "");
                                    $log_text = "District " . $arraytoDb['city_name'] . " updated";
                                    $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $arraytoDb['city_code'], "log_text" => $log_text, "req_data" => $data);
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
            $city = City::getCityDataFromId($id);
            if (!empty($city)) {
                $cityIdArr = $cityCodes = array();
                foreach ($city as $rowLanguage) {
                    $cityIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                    $cityCodes[] = (isset($rowLanguage['city_code'])?$rowLanguage['city_code']:"");
                }
                if(!empty($cityIdArr)){
                    # Delete records from City
                    $delete_city = City::deleteCity($cityIdArr);
                }
                
            }   
        }

        if (isset($delete_city)) {
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
                'city_name' => 'required|min:2|max:100',
                'city_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_city,city_code,NULL,id',
                'country_code' => 'required',
                'state_code' => 'required',
            );
        } else {
            $rules = array(
                'city_name' => 'required|min:2|max:100',
                'city_code' => 'required|min:2|max:5|regex:/^[a-zA-Z ]*$/|unique:mas_city,city_code,'.$data['id'].',id',
                'country_code' => 'required',
                'state_code' => 'required',
            );
        }
        $messages = array(
            'city_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'District Name'),
            'city_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'District Name'),
            'city_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'District Name'),
            'city_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'District Code'),
            'city_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'District Code'),
            'city_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '3', 'District Code'),
            'city_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'District Code, only characters allowed'),
            'city_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'District Code'),
            'country_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country'),
            'state_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'State Code'),
        );
        return Validator::make($data, $rules, $messages);
    }
}
