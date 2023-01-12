<?php

namespace App\Http\Controllers\Admin\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Support\Facades\Response;
use Validator;
use Config;
use App;
use Image;
use Intervention\Image\File;
use App\GlobalClass\Design;
use App\Models\State;
use App\Models\Country;
use App\Models\Districts;
use App\Models\Zone;

class CountryController extends AbstractController
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
        $this->destinationPath = Config::get('path.country_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.country_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
    }


    /**
     * Show the All country records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.master.country_list');
    }

    /**
	*
	* Function : Ajax :  Get State list with html as per country id wise
	* @param  array $request
	* @return  json $response
	*
	*/
	public function statelist(Request $request)
	{
		$data = $request->all();
		$countryId = $data['countryid'];
		$stateData = State::getStateCountryWise($countryId); 
		//$returnHTML = view('admin.master.statehtml')->with('stateData', $stateData)->render(); 
        return response()->json($stateData);
	}
    /**
	*
	* Function : Ajax :  Get State list with html as per country id wise
	* @param  array $request
	* @return  json $response
	*
	*/
	public function districtslist(Request $request)
	{
		$data = $request->all();
		$zoneid = $data['zoneid'];
		$districtsData = Districts::getDistrictsZoneWise($zoneid); 
        return response()->json($districtsData);
	}

    /**
	*
	* Function : Ajax :  Get Taluka list with html as per District id wise
	* @param  array $request
	* @return  json $response
	*
	*/
	public function talukalist(Request $request)
	{
		$data = $request->all();
		$stateid = $data['stateid'];
		$talukaData = Districts::getTalukaDistrictWise($stateid); 
		//$returnHTML = view('admin.master.statehtml')->with('stateData', $stateData)->render();
        return response()->json($talukaData);
	}

    /**
    *
    * Function : Ajax :  Get Zone list with html as per State id wise
    * @param  array $request
    * @return  json $response
    *
    */
    public function zonelist(Request $request)
    {
        $data = $request->all();
        $stateid = $data['stateid'];
        $zoneaData = Zone::getZonesStateWise($stateid); 
        return response()->json($zoneaData);
    }
    /**
    *
    * Function : Ajax :  Get country list with html
    * @param  array $request
    * @return  json $response
    *
    */
    public function countrylist(Request $request)
    {
        $data = $request->all();
        $countryData = Country::getAllCountryData();
        return response()->json($countryData);
    }


    /**
     * Get Country ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getCountryISDCode($where_arr=array()) {
        $query = self::from('mas_country AS mc');
        $query->select('mc.country_code','mc.isd_code','mc.flag');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        $query->where('mc.isd_code', '!=', '');
        $data = $query->get()->toArray();
        return $data;
    }

    /**
     * Get country data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;

        ## Request Parameters
         $post = $request->All();
       
        $search_arr = array();

        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }

        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = Country::getCountryData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "country_name";
                    break;
                case "2":
                    $sort = "country_code";
                    break;
                case "3":
                    $sort = "isd_code";
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
        $data = Country::getCountryData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            // $encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('Country', 'Edit')) {
                $edit = ' <a href="' . route('country',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                
            }
            if (per_hasModuleAccess('Country', 'View')) {
                $view = '<a href="' . route('country',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            $status = Design::blade('status',$status,$status_color);

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->country_name,
                $data[$i]->country_code,
                $data[$i]->isd_code,
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
    * Function : change status , add , edit and delete Country data
    * @param  array $request
    * @return  json $response
    *
    */
    public function PostAjaxData(Request $request) {
        $records = $data = $flag = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        
        if (isset($post['customActionName'])) {
            $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.country');
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
                        $log_text = "Country " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Country " . $post['id'] . " Status updated to ".$post['customActionName'];
                    $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
                     usr_addActivityLog($activity_arr);
                }
                // End Add Activity log
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
                if($request->has('flag')){
                    $flag = $request->file('flag');
                }
                $insert = $this->insertRecord($post, $flag);
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
                }
                if($request->has('flag')){
                     $flag = $request->file('flag');
                }
                $update = $this->updateRecord($post, $flag);
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
    * Function : Load form to Add/Edit/view 
     * @param  array $mode
    * @param   int $id
    * @return  json $response
    *
    */ 
    public function add($mode = NULL, $id = NULL) {
        
        $data = $language = $country_language = array();
        $image_extention = '';
        if(isset($mode) && $mode != ""){
            $image_extention = implode(', ',$this->img_ext_array); 
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Country::getCountryDataFromId($id);
                ## Check Image is Exist or not
                $image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
                $checkImgArr = checkImageExistWithSetting($this->AWSdestinationPath,$this->destinationPath,'country','','_',$image,count($this->size_array));
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin.master.country_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention]);
                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.master.country_view')->with(['mode' => $mode, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.country_add')->with(['mode' => $mode,'img_ext_array'=>$image_extention]);
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
                'country_name' => 'required|min:2|max:100|unique:mas_country,country_name,NULL,id',
                'country_code' => 'required|min:2|max:3|regex:/^[a-zA-Z ]*$/|unique:mas_country,country_code,NULL,id',
                'isd_code' => 'required|min:2|max:10',
            );
        } else {
            $rules = array(
                'country_name' => 'required|min:2|max:100|unique:mas_country,country_name,'.$data['id'].',id',
                'country_code' => 'required|min:2|max:3|regex:/^[a-zA-Z ]*$/|unique:mas_country,country_code,'.$data['id'].',id',
                'isd_code' => 'required|min:2|max:10',
            );
        }
        $messages = array(
            'country_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country Name'),
            'country_name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Country Name'),
            'country_name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Country Name'),
            'country_name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Country Name'),
            'country_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Country Code'),
            'country_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Country Code'),
            'country_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '5', 'Country Code'),
            'country_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Country Code'),
            'country_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Country Code'),
            'isd_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'ISD Code'),
            'isd_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'ISD Code'),
            'isd_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '5', 'ISD Code'),
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    *
    * Function : insert record
    * @param  array $data 
    * @return array $result
    */
    private function insertRecord($data = array(), $files = array()) {
        $flag = NULL;
        $cou_lang_array = array();
        if (!empty($files)) {   
            // echo "<pre>";print_r($files);exit;
            $fileOriname    = $files->getClientOriginalName();
            $flag          = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();
            if (in_array($fileExt, $this->img_ext_array)){
                if ($fileSize > $this->img_max_size || $fileSize == 0){
                    $result['isError'] = 1;
                    $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE');
                    return $result;
                    exit;
                }else {
                    ## Store image in folder in multiple size
                    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                        storeImageinAWS($fileRealPath, $this->AWSdestinationPath, $flag, $this->size_array);
                    }else{
                        storeImageinFolder($fileRealPath, $this->destinationPath, $flag, $this->size_array); 
                    }                   
                }
            } else {
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }else{
            if(isset($data["flag_old"]) && $data["flag_old"] != ""){
                $flag = $data["flag_old"];
            }
        }
        $country_code = (isset($data['country_code']) ? strtoupper($data['country_code']) : "");
        $insert_array = array(
            'country_name' => (isset($data['country_name']) ? $data['country_name'] : ""),
            'country_code' => $country_code,
            'isd_code' => (isset($data['isd_code']) ? $data['isd_code'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : Country::INACTIVE),
            'flag' => $flag,
            'created_at'=> date_getSystemDateTime(),
        );

        $insert = Country::addCountry($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.country');
            $log_text = "Country " . $data['country_name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $country_code, "log_text" => $log_text, "req_data" => $data);
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
    * Function : update record
    * @param  array $data 
    * @return array $result
    */
    private function updateRecord($data = array(), $files = array()) {
        ##Variable Declaration
        $cou_lang_array = array();
        $id = (isset($data['id']) ? $data['id'] : "");
        $flag = NULL;
        
        if (!empty($files)) {   
            $fileOriname    = $files->getClientOriginalName();
            $flag          = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();
            if (in_array($fileExt, $this->img_ext_array)){
                if ($fileSize > $this->img_max_size || $fileSize == 0){
                    $result['isError'] = 1;
                    $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE');
                    return $result;
                    exit;
                }else {
                    ## Store image in multiple size in folder
                    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                        storeImageinAWS($fileRealPath, $this->AWSdestinationPath, $flag, $this->size_array);
                    }else{
                        storeImageinFolder($fileRealPath, $this->destinationPath, $flag, $this->size_array);
                    }
                    ## Remove old image from folder
                    if (isset($data["flag_old"]) && $data["flag_old"] != "" && $data["flag_old"] != NULL) {
                        ## Delete image from s3
                        if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                            deleteImageFromAWS($data["flag_old"], $this->AWSdestinationPath, $this->size_array);
                        }else{ //## Delete image from local storage
                            deleteImageFromFolder($data["flag_old"], $this->destinationPath, $this->size_array);
                        }                        
                    }
                }
            } else {
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }else{
            if(isset($data["flag_old"]) && $data["flag_old"] != ""){
                $flag = $data["flag_old"];
            }
        }

        
        $country_code = (isset($data['country_code']) ? strtoupper($data['country_code']) : "");
        $update_array = array(
            'country_name' => (isset($data['country_name']) ? $data['country_name'] : ""),
            'country_code' => $country_code,
            'isd_code' => (isset($data['isd_code']) ? $data['isd_code'] : ""),
            'display_order' => (isset($data['display_order']) ? $data['display_order'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : Country::INACTIVE),
            'flag' => $flag,
            'updated_at'=> date_getSystemDateTime(),
        );
        $update = Country::updateCountry($id, $update_array);

        if (isset($update)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.country');
            $log_text = "Country " . $data['country_name'] . " added";
            $activeAdminId   = Auth::guard('admin')->user()->id;

            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $country_code, "log_text" => $log_text, "req_data" => $data);
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
            $update = Country::updateCountriesStatus($id, $status);
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
            $country = Country::getCountryDataFromId($id);
            if (!empty($country)) {
                $countryIdArr = $countryCodeArr = array();
                foreach ($country as $rowLanguage) {
                    $country_image = (isset($rowLanguage['flag'])?$rowLanguage['flag']:"");
                    if ($country_image != "") {
                        ## Delete image from s3
                        if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                            deleteImageFromAWS($country_image, $this->AWSdestinationPath, $this->size_array);
                        }else{ //## Delete image from local storage
                            deleteImageFromFolder($country_image, $this->destinationPath, $this->size_array);
                        }
                    }
                    $countryIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                }
                if(!empty($countryIdArr)){
                    # Delete records from Country
                    $delete_country = Country::deleteCountries($countryIdArr);
                }
            }   
        }
        if (isset($delete_country)) {
            ##Delete contry realted state by Country_id
            if(!empty($countryIdArr)){
                $delete_state = State::deleteStatesByCountryId($countryIdArr);
              
            }
            return 1;
        } else {
            return 0;
        }
    }

    /**
    * Delete Uploaded Image
    */
    private function deleteImage($id,$flag){
        $result = array();
        if($id!="" && $flag!=""){
            ## Delete image from s3
            if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                deleteImageFromAWS($flag, $this->AWSdestinationPath, $this->size_array);
            }else{ //## Delete image from local storage
                deleteImageFromFolder($flag, $this->destinationPath, $this->size_array);
            }
            $update_array = array(
                'flag' => '',
            );
            $deleteimage = Country::updateCountry($id, $update_array);
        }
        if (isset($deleteimage)) {
            $result['isError'] = 0;
            $result['msg'] = "";
        }else{
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result;
    }
}
