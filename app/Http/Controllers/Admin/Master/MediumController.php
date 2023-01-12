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
use App\Models\Medium;
use App\Models\Board;

class MediumController extends Controller {

    public function __construct(){
		$this->middleware('admin');
        $this->middleware('accessrights');
    }

    /**
    *  Load Medium List Page
    *
    * @param    void
    * @return   template
    */
    public function index($board_id = NULL) {
        $medium=Board::getListBoardData();
        $board_id = (isset($board_id) && $board_id != '') ? substr($board_id, 3, -3) : "";
        return view('admin/master/medium_list')->with(['medium'=>$medium, 'board_id' => $board_id]);
    }

    /**
    * Get Medium data and pass json response to data table
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
        $search_arr['board_id'] = (isset($post['product_board']) && $post['product_board'] != '') ? $post['product_board'] : "";

        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
        $tot_records_data = Medium::getMediumData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "bname";
                    break;
                case "3":
                    $sort = "status";
                    break;
                default:
                    $sort = "name";
            }
        } else {
            $sort = "name";
        }
        $data = Medium::getMediumData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {

            if($data[$i]->status == Medium::ACTIVE){
                $status =  "Active" ;
            }else if($data[$i]->status == Medium::INACTIVE){
                $status =  "Inactive" ;
            } 
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);
             

            $edit = '---';
            $view = '';
            if (per_hasModuleAccess('Medium', 'Edit')) {
                $edit = ' <a href="javascript:void(0)" onclick="add_edit_modal('.$data[$i]->id.',\'Update\')" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                '<span id="gfname' . $data[$i]->id . '"> <span id="gname' . $data[$i]->id . '" class="d-none">' . $data[$i]->name . '</span>'.$data[$i]->name  . '</span>',
                '<span id="gmname' . $data[$i]->id . '"> <span id="mname' . $data[$i]->id . '" class="d-none">' . $data[$i]->bname . '</span>'.$data[$i]->bname  . '</span>',
                '<span id="gfstatus' . $data[$i]->id . '"> <span id="gstatus' . $data[$i]->id . '" class="d-none">' . $data[$i]->status . '</span>'.$status  . '</span>',
                $edit
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

        $records = $data = $image = array();
        $records["data"] = array();
        $err_msg = NULL;
        $post = $request->all();

        if (isset($post['customActionName'])) {
             $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.medium');

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
                        $log_text = "Medium " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Medium " . $post['id'] . " Status updated to ".$post['customActionName'];
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
            'name' => 'required|max:150|unique:mas_medium,name,NULL,id',
        );
        $messages = array(
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Medium'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Medium'),
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
            'name' => 'required|max:150|unique:mas_medium,name,' . $data['id'] . ',id',
        );
        $messages = array(
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Medium'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Medium'),
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
    * Function : Insert Record of Medium 
    *
    * @param  array $data
    * @return $result
    */            
    private function insertRecord($data = array()) {
        $insert_array = array(
            'name' => (isset($data['name']) ? $data['name'] : ""),
            'board_id'=>(isset($data['board_id']) ? $data['board_id'] : "0"),
            'status' => (isset($data['status']) ? $data['status'] : Medium::INACTIVE),
        );

        $insert = Medium::addMedium($insert_array);
        if (!empty($insert)) {
            $result = 1;
            $insertedId = (isset($insert['id']) ? $insert['id'] : "");
            
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.medium');
            $log_text = "Medium " . $data['name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => "", "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
    * Function : Update Record of Medium
    *
    * @param  array $data
    * @return $result;
    */  
    private function updateRecord($data = array()) {
        $id = (isset($data['id']) ? $data['id'] : "");
        $update_array = array(
            'name' => (isset($data['name']) ? $data['name'] : ""),
            'board_id' => (isset($data['board_id']) ? $data['board_id'] : ""),
            'status' => (isset($data['status']) ? $data['status'] : Medium::INACTIVE),
        );
         
        $update = Medium::updateMedium($id,$update_array);

       if (isset($update)) {
            $result = 1;           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.medium');
            $log_text = "Medium " . $data['name'] . " edited";
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
            $update = Medium::updateMediumStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }

     /** 
    * Function : Delete Records of  Medium
    * @param  array $id
    * @return boolean
    */
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $mediums = Medium::getMediumDataFromId($id);
            if(!empty($mediums)){
                foreach ($mediums as $data) {
                    ## Start Add Activity
                    $reference_id = (isset($data['id']) ? $data['id'] : "");
                    $module_id = Config::get('constants.module_id.medium');
                    $log_text = "Medium ". $data['name']. " deleted";
                    $activity_arr= array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                    usr_addActivityLog($activity_arr);
                    ## End Add Activity
                 }                  
            $delete_var = Medium::deleteMediumData($id);
            }
            
            if (isset($delete_var)) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}