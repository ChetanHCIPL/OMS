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
use App\Models\ProductHead;
use App\Models\UserDiscountCategory;
use App\Models\ProductHeadCategoryDiscount;

class ProductHeadController extends Controller {

    public function __construct(){
		$this->middleware('admin');
        $this->middleware('accessrights');
    }

    /**
    *  Load Porduct Head List Page
    *
    * @param    void
    * @return   template
    */
    public function index() {
        return view('admin/master/product_head_list');
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
        
        $data = array();
        $categories = UserDiscountCategory::getAllCategories(); // Get Categories

        if(isset($mode) && $mode != ""){
            
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Producthead::getProductheadDataFromId($id);
                $existingMappedCategory = ProductheadCategoryDiscount::getProductheadCategoryDiscount($id);

                $prepared_cat = array();

                foreach($categories as $key => $value){
                    foreach($existingMappedCategory as $category){
                        if ($value['id'] == $category['discount_category_id']){
                            $prepared_cat[$value['id']] = $category;
                        }
                    }                    
                }

                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin.master.product_head_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data, 'categories' => $categories, 'prepared_cat' => $prepared_cat]);
                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.master.product_head_view')->with(['mode' => $mode, 'data' => $data, 'categories' => $categories, 'prepared_cat' => $prepared_cat]);
                }
            } elseif($mode == 'add') {
                
                $mode = "Add";
                return view('admin.master.product_head_add')->with(['mode' => $mode, 'categories' => $categories]);
            }
        }
        abort(404);
    }

    /**
    * Get Product Head data and pass json response to data table
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
              
        $tot_records_data = Producthead::getProductHeadData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
        $data = Producthead::getProductHeadData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {

            if($data[$i]->status == Producthead::ACTIVE){
                $status =  "Active" ;
            }else if($data[$i]->status == Producthead::INACTIVE){
                $status =  "Inactive" ;
            } 
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);
             
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view = '';
            if (per_hasModuleAccess('ProductHead', 'Edit')) {
                $edit = ' <a href="' . route('producthead',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
            }
            if (per_hasModuleAccess('ProductHead', 'View')) {
                $view = '<a href="' . route('producthead',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                '<span id="gfname' . $data[$i]->id . '"> <span id="gname' . $data[$i]->id . '" class="d-none">' . $data[$i]->name . '</span>'.$data[$i]->name  . '</span>',
                '<span id="gfstatus' . $data[$i]->id . '"> <span id="gstatus' . $data[$i]->id . '" class="d-none">' . $data[$i]->status . '</span>'.$status  . '</span>',
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

        $records = $data = $image = array();
        $records["data"] = array();
        $err_msg = NULL;
        $post = $request->all();

        if (isset($post['customActionName'])) {
             $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.producthead');

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
                        $log_text = "Product Head " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Product Head " . $post['id'] . " Status updated to ".$post['customActionName'];
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
            }
            /**
             * For Update Existing Entry
             */
             elseif ($post['customActionName'] == "Update") {

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
    *
    * Function : Validation for add and edit form
    * @param  array $data 
    *
    */  
    private function validateFields($data = array()) {
        if($data['customActionName'] == "Add") {

            $rules = array(
                'name' => 'required|min:2|max:100|unique:mas_product_head,name,NULL,id',
                'max_discount' => 'required|numeric|between:0,100',
                'approval_discount' => 'required|numeric|between:0,100',
                
            );
            
            foreach($data as $key=>$value){
                if("category_" == substr($key,0,9)){
                    $rules[$key] = 'required|numeric|between:0,100';
                }
            }
        } else {
            $rules = array(
                'name' => 'required|min:2|max:100|unique:mas_product_head,name,'.$data['id'].',id',
                'max_discount' => 'required|numeric|between:0,100',
                'approval_discount' => 'required|numeric|between:0,100',
            );

            foreach($data as $key=>$value){
                if("category_" == substr($key,0,9)){
                    $rules[$key] = 'required|numeric|between:0,100';
                }
            }
        }
        $messages = array(
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Product Head'),
            'name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Product Head'),
            'name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Product Head'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Product Head'),
        );

        foreach($data as $key=>$value){
            if("category_" == substr($key,0,9)){
                $messages[$key.'.required'] = sprintf(Config::get('messages.validation_msg.required_field'), strtoupper(str_replace(' ', '_', $key)));
                $messages[$key.'.numeric'] = sprintf(Config::get('messages.validation_msg.numeric'), strtoupper(str_replace(' ', '_', $key)));
                $messages[$key.'.between'] = sprintf(Config::get('messages.validation_msg.percantage_between'), strtoupper(str_replace(' ', '_', $key)));
            }
        }

        return Validator::make($data, $rules, $messages);
    }

    /**
    * Function : Insert Record of Product Head
    *
    * @param  array $data
    * @return $result
    */            
    private function insertRecord($data = array()) {

        $key_arr = array();
        if (isset($data['discount']) && is_array($data['discount'])){

            $max_discount = isset($data['max_discount']) ? $data['max_discount'] : 0;

            $discount_arr = array_values($data['discount']);
            foreach($discount_arr as $key=>$value){
                
                if($value > $max_discount) {

                    $key_arr[] = $key+1;
                }
            }
        }

        if( !empty($key_arr) ) {
            $result['isError'] = 1;
            $result['msg'] = sprintf(Config::get('messages.validation_msg.value_between'), 'Discount', '0', $max_discount." for indexes: ".implode(', ', $key_arr));
        }
        else {

            $insert_array = array(
                'name' => (isset($data['name']) ? $data['name'] : ""),
                'max_discount' => (isset($data['max_discount']) ? $data['max_discount'] : ""),
                'approval_discount' => (isset($data['approval_discount']) ? $data['approval_discount'] : ""),
                'status' => (isset($data['status']) ? $data['status'] : Producthead::INACTIVE),
            );

            $insert = Producthead::addProducthead($insert_array);
            if (!empty($insert)) {
                $result['isError'] = 0;
                $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
                $insertedId = (isset($insert['id']) ? $insert['id'] : "");

                // Filter $post data.
                if (isset($data['discount']) && is_array($data['discount'])){
                    foreach($data['discount'] as $key=>$value){
                        $insert_categories[] = array(
                            'product_head_id' => $insertedId,
                            'discount_category_id' => $key,
                            'max_discount' => $value,
                            'status' => 1,
                        );
                    }

                    //Insert Category Discount based on Product head.
                    ProductheadCategoryDiscount::addProductHeadCategoryDiscount($insert_categories);
                }

                
                
                ## Start Add Activity
                $reference_id = (isset($insertedId) ? $insertedId : "");
                $module_id = Config::get('constants.module_id.product_head');
                $log_text = "Product Head " . $data['name'] . " added";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => "", "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
                ## End Add Activity
            } else {
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            }
        }
        return $result;
    }

    /**
    * Function : Update Record of Product Head
    *
    * @param  array $data
    * @return $result;
    */  
    private function updateRecord($data = array()) {

        $key_arr = array();
        if (isset($data['discount']) && is_array($data['discount'])){

            $max_discount = isset($data['max_discount']) ? $data['max_discount'] : 0;

            $discount_arr = array_values($data['discount']);
            foreach($discount_arr as $key=>$value){
                
                if($value > $max_discount) {

                    $key_arr[] = $key+1;
                }
            }
        }

        if( !empty($key_arr) ) {
            $result['isError'] = 1;
            $result['msg'] = sprintf(Config::get('messages.validation_msg.value_between'), 'Discount', '0', $max_discount." for indexes: ".implode(', ', $key_arr));
        }
        else {

            $id = (isset($data['id']) ? $data['id'] : "");
            $update_array = array(
                'name' => (isset($data['name']) ? $data['name'] : ""),
                'max_discount' => (isset($data['max_discount']) ? $data['max_discount'] : ""),
                'approval_discount' => (isset($data['approval_discount']) ? $data['approval_discount'] : ""),
                'status' => (isset($data['status']) ? $data['status'] : Producthead::INACTIVE),
                
            );
             
            $update = Producthead::updateProductHead($id,$update_array);

            if (isset($update)) {
                $result['isError'] = 0;
                $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
                ## Start Add Activity
                $reference_id = (isset($id) ? $id : "");
     
                // Filter $post data.
                if (isset($data['discount']) && is_array($data['discount'])){
                    foreach($data['discount'] as $key=>$value){
                        $insert_categories[] = array(
                            'product_head_id' => $id,
                            'discount_category_id' => $key,
                            'max_discount' => $value,
                            'status' => 1,
                        );
                    }
                    
                    // Delete Existing entry
                    ProductheadCategoryDiscount::deleteProductheadCategoryDiscount($id);
                    // Create New Category Discount based on Product head Entry.
                    ProductheadCategoryDiscount::addProductHeadCategoryDiscount($insert_categories);
                }

                $module_id = Config::get('constants.module_id.product_head');
                $log_text = "Product Head " . $data['name'] . " edited";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => "", "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
                ## End Add Activity
            } else {
                $result['isError'] = 1;
                $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            }
        }
        return $result;
    }

     /**
    *
    * Function : Update status for Product Head
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
            $update = Producthead::updateProductheadStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }

     /** 
    * Function : Delete Records of  Product Head
    * @param  array $id
    * @return boolean
    */
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $productheads = Producthead::getProductheadDataFromId($id);
            if(!empty($productheads)){
                foreach ($productheads as $data) {
                    ## Start Add Activity
                    $reference_id = (isset($data['id']) ? $data['id'] : "");
                    $module_id = Config::get('constants.module_id.product_head');
                    $log_text = "Product Head ". $data['name']. " deleted";
                    $activity_arr= array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                    usr_addActivityLog($activity_arr);
                    ## End Add Activity
                 }                  
                 // Delete Also Mapping For Same Product Head to category Disocunt.
                 ProductheadCategoryDiscount::deleteProductheadCategoryDiscount($id);
            $delete_var = Producthead::deleteProductheadData($id);
            }
            
            if (isset($delete_var)) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}