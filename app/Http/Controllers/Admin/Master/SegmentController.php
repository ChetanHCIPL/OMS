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
use App\Models\Segment;
use App\Models\Medium;
use App\Models\Semester;

class SegmentController extends AbstractController
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
    }


    /**
     * Show the All Segments records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.master.segment_list');
    }


    /**
     * Get Segment data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {
        $records = $data = array();
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
              
        $tot_records_data = Segment::getSegmentData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "name";
                    break;
                case "2":
                    $sort = "status";
                    break;
                default:
                    $sort = "name";
            }
        } else {
            $sort = "name";
        }
        $data = Segment::getSegmentData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            // $encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('Segment', 'Edit')) {
                $edit = ' <a href="' . route('segment',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                
            }
            if (per_hasModuleAccess('Segment', 'View')) {
                $view = '<a href="' . route('segment',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            $status = Design::blade('status',$status,$status_color);

            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->name,
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
    * Function : change status , add , edit and delete Segment data
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

            $module_id = Config::get('constants.module_id.segment');
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
                        $log_text = "Segment " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Segment " . $post['id'] . " Status updated to ".$post['customActionName'];
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
        
        $data = $language = array();
        $image_extention = '';

        // Get Mediums For Dropdown
        $mediums = Medium::getMediumDataList();

        // Get Semester For Dropdown
        $semesters = Semester::getSemesterDataList();

        if(isset($mode) && $mode != ""){
            
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Segment::getSegmentDataFromId($id);
                
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin.master.segment_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data, 'mediums' => $mediums, 'semesters' => $semesters]);
                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.master.segment_view')->with(['mode' => $mode, 'data' => $data, 'mediums' => $mediums, 'semesters' => $semesters]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.segment_add')->with(['mode' => $mode, 'mediums' => $mediums, 'semesters' => $semesters]);
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
                'name' => 'required|min:2|max:100|unique:mas_segment,name,NULL,id',
                // 'medium_id' => 'required',
                // 'semester_id' => 'required',
            );
        } else {
            $rules = array(
                'name' => 'required|min:2|max:100|unique:mas_segment,name,'.$data['id'].',id',
                // 'medium_id' => 'required',
                // 'semester_id' => 'required',
            );
        }
        $messages = array(
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Segment Name'),
            'name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Segment Name'),
            'name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Segment Name'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Segment Name'),
            // 'medium_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Medium'),
            // 'semester_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Semester'),
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
        
        $name = (isset($data['name']) ? strtoupper($data['name']) : "");

        $insert_array = array(
            'name' => (isset($data['name']) ? $data['name'] : ""),
            'medium_id' => (isset($data['medium_id']) ? $data['medium_id'] : ""),
            'semester_id' => (isset($data['semester_id']) ? $data['semester_id'] : ""),
            'status' => (isset($data['status']) ? $data['status'] : Segment::INACTIVE),
            'created_at'=> date_getSystemDateTime(),
            'created_by'=> Auth::guard('admin')->user()->id
        );

        $insert = Segment::addSegment($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.segment');
            $log_text = "Segment " . $data['name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "log_text" => $log_text, "req_data" => $data);
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
    private function updateRecord($data = array()) {
        ##Variable Declaration
        $id = (isset($data['id']) ? $data['id'] : "");

        $name = (isset($data['name']) ? strtoupper($data['name']) : "");
        $update_array = array(
            'name' => (isset($data['name']) ? $data['name'] : ""),
            'medium_id' => (isset($data['medium_id']) ? $data['medium_id'] : ""),
            'semester_id' => (isset($data['semester_id']) ? $data['semester_id'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : Segment::INACTIVE),
            'updated_at'=> date_getSystemDateTime(),
        );
        $update = Segment::updateSegment($id, $update_array);

        if (isset($update)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.segment');
            $log_text = "Segment " . $data['name'] . " Updated";
            $activeAdminId   = Auth::guard('admin')->user()->id;

            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "log_text" => $log_text, "req_data" => $data);
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
            $update = Segment::updateSegmentStatus($id, $status);
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
            $segment = Segment::getSegmentDataFromId($id);
            if (!empty($segment)) {
                $segmentIdArr = array();
                foreach ($segment as $rowLanguage) {
                    $segmentIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                }
                if(!empty($segmentIdArr)){
                    # Delete records from Segment
                    $delete_segment = Segment::deleteSegments($segmentIdArr);
                }
            }   
            return 1;
        }else{
            return 0;
        }
    }

}
