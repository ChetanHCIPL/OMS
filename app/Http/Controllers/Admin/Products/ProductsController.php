<?php

namespace App\Http\Controllers\Admin\Products;

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
use App\Models\Products;
use App\Models\ProductHead;
use App\Models\Series;
use App\Models\Segment;
use App\Models\Medium;
use App\Models\Semester;
use App\Models\Board;
use App\Models\ProductBoardSegmentSemester;
use App\Models\ProductsKit;

class ProductsController extends AbstractController
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
        $this->destinationPath = Config::get('path.product_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.product_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
    }


    /**
     * Show the All products records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $product_type = Config::get('constants.product_type');

        $product_head = ProductHead::getProductHeadAllList();
        return view('admin.products.products_list')->with(['product_type' => $product_type, 'product_head' => $product_head]);
    }
    /*
    *
    * Function : Ajax :  Get products list with html
    * @param  array $request
    * @return  json $response
    *
    */
    public function productslist(Request $request)
    {
        $data = $request->all();
        $productsData = Products::getAllProductsData();
        return response()->json($productsData);
    }


    /**
     * Get Products ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    

    /**
     * Get products data and pass json response to data table
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

        $productHead=ProductHead::getProductHeadAllList();
        $PheadDetails=[];
        foreach($productHead as $h){
            $PheadDetails[$h['id']]=$h['name'];
        }
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = Products::getProductsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
        $iTotalRecords = count($tot_records_data);

        $iDisplayLength = (isset($post['length']) ? intval($post['length']) : 50);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sorton = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC');

        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "2":
                    $sort = "name";
                    break;
                case "3":
                    $sort = "name";
                    break;
                case "4":
                    $sort = "type_of_product";
                    break;
                case "5":
                    $sort = "code";
                    break;
                case "6":
                    $sort = "hsn_number";
                    break;
                case "7":
                    $sort = "qrcode";
                    break;
                case "8":
                    $sort = "stock";
                    break;
                case "9":
                    $sort = "status";
                    break;
                default:
                    $sort = "name";
            }
        } else {
            $sort = "name";
        }
        $data = Products::getProductsData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            //$encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('Products', 'Edit')) {
                if ($data[$i]->type_of_product == 0){
                    $edit = ' <a href="' . route('products',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                }else {
                    $edit = ' <a href="' . route('kit',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                }
                
            }
            if (per_hasModuleAccess('Products', 'View')) {
                $view = '';//'<a href="' . route('products',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            $status = Design::blade('status',$status,$status_color);
            $headname=(!empty($PheadDetails[$data[$i]->product_head_id]))?$PheadDetails[$data[$i]->product_head_id]:'';

            $type = 'Product';

            if ($data[$i]->type_of_product == 1) {
                $type = "Kit";
            }
          
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->name,
                $headname,
                $type,
                $data[$i]->code,
                $data[$i]->hsn_number,
                $data[$i]->stock,
                '₹ '.number_format($data[$i]->mrp,2),
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
    * Function : change status , add , edit and delete Products data
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

            $module_id = Config::get('constants.module_id.products');
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
                        $log_text = "Products " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Products " . $post['id'] . " Status updated to ".$post['customActionName'];
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

                // VAlidate & Insert Segment Mapping in Respected Table
                if ($post['type_of_product'] == 0 && !empty($post['medium']) && !empty($post['segment']) && !empty($post['semester'])){
                    $str=array();
                    if ($post['medium'] && $post['segment'] && $post['semester']){
                        $i = 1;
                        foreach($post['medium'] as $key => $item){
                            $str[$i] = $item .'-'. $post['segment'][$key] . '-' . $post['semester'][$key];
                            $i++;
                        }
                    }

                    $duplicates = array_intersect($str, array_unique(array_diff_key($str, array_unique($str))));
                    $keys = implode(', ', array_keys($duplicates));

                    if ($keys){
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = 'There is duplicate value in Segment Mapping Table at row '. $keys;
                        return Response::json($result_arr);
                    }
                } elseif ($post['type_of_product'] == 1){
                    $kit_str=array();
                    if ($post['product_ids'] && $post['product_quantity']){
                        $i = 1;
                        foreach($post['product_ids'] as $key => $item){
                            $kit_str[$i] = $item .'-'. $post['product_quantity'][$key];
                            $i++;
                        }
                    }

                    $duplicates = array_intersect($kit_str, array_unique(array_diff_key($kit_str, array_unique($kit_str))));
                    $keys = implode(', ', array_keys($duplicates));

                    if ($keys){
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = 'There is duplicate value in Products Mapping Table at row '. $keys;
                        return Response::json($result_arr);
                    }
                }
                
                if($request->has('flag')){
                    $flag = $request->file('flag');
                }
                $image="";
                if($request->has('image')){
                    $image = $request->file('image');
                } 
                
                $insert = $this->insertRecord($post, $image);
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

                // Validation For Duplicate Entries
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }

                // Check If Type is Product.
                if ($post['type_of_product'] == 0 && !empty($post['medium']) && !empty($post['segment']) && !empty($post['semester'])){
                    $str=array();
                    if ($post['medium'] && $post['segment'] && $post['semester']){
                        $i = 1;
                        foreach($post['medium'] as $key => $item){
                            $str[$i] = $item .'-'. $post['segment'][$key] . '-' . $post['semester'][$key];
                            $i++;
                        }
                    }
    
                    $duplicates = array_intersect($str, array_unique(array_diff_key($str, array_unique($str))));
                    $keys = implode(', ', array_keys($duplicates));

                    if ($keys){
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = 'There is duplicate value in Segment Mapping Table at row '. $keys;
                        return Response::json($result_arr);
                    }
                } elseif ($post['type_of_product'] == 1){
                    $kit_str=array();
                    if ($post['product_ids'] && $post['product_quantity']){
                        $i = 1;
                        foreach($post['product_ids'] as $key => $item){
                            $kit_str[$i] = $item .'-'. $post['product_quantity'][$key];
                            $i++;
                        }
                    }

                    $duplicates = array_intersect($kit_str, array_unique(array_diff_key($kit_str, array_unique($kit_str))));
                    $keys = implode(', ', array_keys($duplicates));

                    if ($keys){
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = 'There is duplicate value in Products Mapping Table at row '. $keys;
                        return Response::json($result_arr);
                    }
                }

                $image="";
                if($request->has('image')){
                    $image = $request->file('image');
                } 
                $update = $this->updateRecord($post, $image);
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
        $data = $language = $products_language = $allmediumlist = array();
        $allmediumlist=Medium::getMediumDataList();
        $allsems= Semester::getSemesterDataList();
        $boards=Board::getListBoardData();
        $mediumsBoard = Medium::getMediumBoard();

        $allProducts = Products::getAllProductsData();
        $allKitProducts = Products::getAllNormalProductsData();

        $image_extention = '';

        $subProduct = array();

        
        if(isset($mode) && $mode != ""){
            $image_extention = implode(', ',$this->img_ext_array); 
            $productHead=ProductHead::getProductHeadAllList();
            $series=Series::getSeriesDataList();
            $segment=Segment::getAllActiveSegmentData();
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $productMapping = ProductBoardSegmentSemester::getProductMappedData($id);

                $kitproductMapping = ProductsKit::getProductKitMappedData($id);

                
                $data = Products::getProductsDataFromId($id);
                ## Check Image is Exist or not
                //$image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
                $checkImgArr ='';
                $image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                if($image != ''){
                    $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'product','','_',$image,count($this->size_array));
                }

                if($data[0]['type_of_product'] == 0) {
                    $subProduct = Products::getKitsProductData($id);
                }

                if($mode == 'edit'){
                    
                    $mode = 'Update';

                    return view('admin.products.products_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention,'productHead'=>$productHead,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards, 'mediumBoard' => $mediumsBoard, 'productMapping' => $productMapping, 'allProduct' => $allKitProducts, 'kit_products' => $kitproductMapping, 'subProduct' => $subProduct]);

                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.products.products_view')->with(['mode' => $mode, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention,'productHead'=>$productHead,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'Board'=>$boards , 'mediumBoard' => $mediumsBoard]);
                }
            }elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.products.products_add')->with(['mode' => $mode,'img_ext_array'=>$image_extention,'productHead'=>$productHead,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards, 'mediumBoard' => $mediumsBoard, 'allProduct' =>$allKitProducts, 'subProduct' => $subProduct]);
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
                'product_head_id'=>'required',
                'series_id'=>'required',
                'name' => 'required|min:3|max:100|unique:products,name,NULL,id',
                'products_code' => 'required|min:3|max:20|regex:/^[a-zA-Z0-9 ]*$/|unique:products,code,NULL,id',
                'hsn_number'=> 'required|min:3|max:20|unique:products,hsn_number,NULL,id',
                'max_order_qty'=> 'required|regex:/^[0-9]*$/',
                'stock'=> 'required|regex:/^[0-9]*$/',
                'mrp'=>'required|regex:/^[0-9.]*$/',
                'weight'=>'required|regex:/^[0-9.]*$/'
            );

            if ($data['type_of_product'] == 0){
                $rules['badho'] = 'required|regex:/^[0-9]*$/';
                $rules['pages'] = 'required|regex:/^[0-9]*$/';
            }
                
        } else {
            $rules = array(
                'product_head_id'=>'required',
                'series_id'=>'required',
                'name' => 'required|min:3|max:100|unique:products,name,'.$data['id'].',id',
                'products_code' => 'required|min:3|max:20|regex:/^[a-zA-Z0-9 ]*$/|unique:products,code,'.$data['id'].',id',
                'hsn_number'=> 'required|min:3|max:20|unique:products,hsn_number,'.$data['id'].',id',
                'max_order_qty'=> 'required|regex:/^[0-9]*$/',
                'mrp'=>'required|regex:/^[0-9.]*$/',
                'weight'=>'required|regex:/^[0-9.]*$/',
            );

            if ($data['type_of_product'] == 0){
                $rules['pages'] = 'required|regex:/^[0-9]*$/';
                $rules['badho'] = 'required|regex:/^[0-9]*$/';
            }
        }

        // // Validation For Duplicate Entries
        // $str=array();
        // if ($data['medium'] && $data['segment'] && $data['semester']){
        //     $i = 0;
        //     foreach($data['medium'] as $key => $item){
        //         $str[$i] = $item .'-'. $data['segment'][$key] . '-' . $data['semester'][$key];
        //         $i++;
        //     }
        // }
        $messages = array(
            'product_head_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Product Head'),
            'series_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Series'),
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Products Name'),
            'name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Products Name'),
            'name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Products Name'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Products Name'),
            'hsn_number.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', ' HSN Number'),
            'hsn_number.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', ' HSN Number'),
            'hsn_number.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), ' HSN Number'),
            'hsn_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'HSN Number'),
            'products_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Products Code'),
            'products_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Products Code'),
            'products_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Products Code'),
            'products_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Products Code'),
            'products_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Products Code'),
            'max_order_qty.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Max Order Qty'),
            'max_order_qty.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Max Order Qty'),
            'mrp.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'MRP'),
            'mrp.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'MRP'),            
            'weight.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Weight'),
            'weight.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Weight'),
        );

        if ( $data['type_of_product'] == 0 ){
            $messages['pages.required'] = sprintf(Config::get('messages.validation_msg.required_field'), 'Pages');
            $messages['pages.regex'] = sprintf(Config::get('messages.validation_msg.regex'), 'Pages');
            $messages['badho.required'] = sprintf(Config::get('messages.validation_msg.required_field'), 'Badho');
            $messages['badho.regex'] = sprintf(Config::get('messages.validation_msg.regex'), 'Badho');
        }
        
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
        $products_code = (isset($data['products_code']) ? strtoupper($data['products_code']) : "");
        $data['qrcode'] = (isset($data['qrcode']) ? strtoupper($data['qrcode']) : "");
        $data['lock_for_order']= isset($data['lock_for_order']) ? $data['lock_for_order'] : 0;
        $data['cn_lock']= isset($data['cn_lock']) ? $data['cn_lock'] : 0;
        $data['segment_id']= isset($data['segment_id']) ? $data['segment_id'] : 0;
        $data['series_id']= isset($data['series_id']) ? $data['series_id'] : 0;
        $data['stock_alert']= isset($data['stock_alert']) ? $data['stock_alert'] : 0;
        $data['version_no']= isset($data['version_no']) ? $data['version_no'] : 0;
        $insert_array = array( 
            'is_kit_product'=>0, 
            'version_no'=>$data['version_no'],
            'stock_alert'=>$data['stock_alert'],
            'product_head_id'=>$data['product_head_id'], 
            'series_id'=>$data['series_id'], 
            'stock_alert_qty'=>$data['stock_alert_qty'],
            'name'=>$data['name'], 
            'max_order_qty'=>$data['max_order_qty'],
            'product_number'=>1, 
            'code'=>$data['products_code'], 
            'hsn_number'=>$data['hsn_number'], 
            'qrcode'=>$data['qrcode'], 
            'mrp'=>$data['mrp'], 
            'weight'=>$data['weight'], 
            'image'=>$flag, 
            'description'=>$data['description'], 
            'stock'=>$data['stock'], 
            'row_stock'=>$data['row_stock'],
            'lock_for_order'=>$data['lock_for_order'], 
            'cn_lock'=>$data['cn_lock'], 
            'created_at'=>date_getSystemDateTime(), 
            'status' => (isset($data['status']) ? $data['status'] : Products::INACTIVE),
            'type_of_product' => $data['type_of_product'],
        );

        if ($data['type_of_product'] == 0){
            $insert_array['pages'] = $data['pages'];
            $insert_array['badho'] = $data['badho']; 
        }
        
        $insert = Products::addProducts($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.products');
            $log_text = "Products " . $data['name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $products_code, "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            
            if ($data['type_of_product'] == 0 && !empty($post['medium']) && !empty($post['segment']) && !empty($post['semester'])){
                $mapping_data = array();
                if ($data['medium'] && $data['segment'] && $data['semester']){
                    foreach($data['medium'] as $key => $medium){
                        $mapping_data[] = array(
                            'product_id'        => $insertedId,
                            'medium_board_id'   => $medium,
                            'segment_id'        => $data['segment'][$key],
                            'semester_id'       => $data['semester'][$key]
                        );                    
                    }
                }
                if(is_array($mapping_data)){
                    ProductBoardSegmentSemester::addProductMapping($mapping_data);
                }
            }elseif ($data['type_of_product'] == 1){
                $kit_mapping_data = array();

                if ($data['product_ids'] && $data['product_quantity']) {
                    foreach($data['product_ids'] as $key => $p_id) {
                        $kit_mapping_data[] = array(
                            'kit_product_id' => $insertedId,
                            'product_id' => $p_id,
                            'quantity'   => $data['product_quantity'][$key],
                        );
                    }
                }

                // Create Multi Entries For the Kit Mapping data.
                if (is_array($kit_mapping_data)){
                    ProductsKit::addProductsKitMapping($kit_mapping_data);
                }
            }
            

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

        
        $products_code = (isset($data['products_code']) ? strtoupper($data['products_code']) : "");
        $data['qrcode'] = (isset($data['qrcode']) ? strtoupper($data['qrcode']) : "");
        $data['lock_for_order']= isset($data['lock_for_order']) ? $data['lock_for_order'] : 0;
        $data['cn_lock']= isset($data['cn_lock']) ? $data['cn_lock'] : 0;
      //  $data['segment_id']= isset($data['segment_id']) ? $data['segment_id'] : 0;
         $data['stock_alert']= isset($data['stock_alert']) ? $data['stock_alert'] : 0;
        $data['series_id']= isset($data['series_id']) ? $data['series_id'] : 0;
        $update_array = array( 
            'is_kit_product'=>0, 
            'version_no'=>$data['version_no'],
            'product_head_id'=>$data['product_head_id'], 
        // 'segment_id'=>$data['segment_id'], 
            'series_id'=>$data['series_id'], 
        // 'semester_id'=>0, 
            'name'=>$data['name'], 
            'stock_alert'=>$data['stock_alert'],
            'stock_alert_qty'=>$data['stock_alert_qty'],
            'max_order_qty'=>$data['max_order_qty'],
            'product_number'=>1, 
            'code'=>$data['products_code'], 
            'hsn_number'=>$data['hsn_number'], 
            'qrcode'=>$data['qrcode'], 
            'mrp'=>$data['mrp'],             
            'weight'=>$data['weight'], 
            'image'=>$flag, 
            'description'=>$data['description'], 
            // 'stock'=>$data['stock'], 
            'row_stock'=>$data['row_stock'],
            'lock_for_order'=>$data['lock_for_order'], 
            'cn_lock'=>$data['cn_lock'], 
            'created_at'=>date_getSystemDateTime(), 
            'status' => (isset($data['status']) ? $data['status'] : Products::INACTIVE),
            'type_of_product' => $data['type_of_product'],
        );

        if($data['type_of_product'] == 0){
            $update_array['pages'] = $data['pages'];
            $update_array['badho'] = $data['badho'];
        }

        $update = Products::updateProducts($id, $update_array);

        // Insert Segment Mapping Data
        if ($data['type_of_product'] == 0 && !empty($data['medium']) && !empty($data['segment']) && !empty($data['semester'])){
            
            //Initialze Array for Multi Insert Data
            $mapping_data = array();
            $exist_ids = array();
            if ($data['medium'] && $data['segment'] && $data['semester']){
                foreach($data['medium'] as $key => $medium){

                    $mapping = ProductBoardSegmentSemester::getMappingData($id, $medium, $data['segment'][$key], $data['semester'][$key]);

                    if ($mapping){
                        $exist_ids[] = $mapping[0]['id'];
                    }else{
                        $mapping_data = array(
                            'product_id'        => $id,
                            'medium_board_id'   => $medium,
                            'segment_id'        => $data['segment'][$key],
                            'semester_id'       => $data['semester'][$key]
                        );
                        $insert_ids = ProductBoardSegmentSemester::addProductBoardSegmentSemesterMapping($mapping_data);
                        $exist_ids[] = $insert_ids['id'];
                    }
                }
                // Delete Records Where Not exist in Same Table.
                ProductBoardSegmentSemester::deleteProductMapping($id, $exist_ids);
            }
        }
        // Insert | Update Product Ids & Quantity For Kit.
        elseif($data['type_of_product'] == 1){
            $products_ids = array();
            $products_exist_ids  = array();

            if ($data['product_ids'] && $data['product_quantity']){
                foreach($data['product_ids'] as $key => $p_id){
                    $check_exist = ProductsKit::getMappingData( $id, $p_id, $data['product_quantity'][$key]);
                    
                    if ($check_exist){
                        $products_exist_ids[] = $check_exist[0]['id'];
                    }else{
                        $kit_products = array(
                            'kit_product_id' => $id,
                            'product_id' => $p_id,
                            'quantity'   => $data['product_quantity'][$key],
                        );
                        $inserted_id = ProductsKit::addProductKitMapping($kit_products);
                        $products_exist_ids[] = $inserted_id['id'];
                    }
                }
                //Delete Records Where Not Exist in Same Table.
                if($products_exist_ids){
                    ProductsKit::deleteProductKit( $id, $products_exist_ids );
                }
            }
        }
        if (isset($update)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.products');
            $log_text = "Products " . $data['name'] . " added";
            $activeAdminId   = Auth::guard('admin')->user()->id;

            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $products_code, "log_text" => $log_text, "req_data" => $data);
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
            $update = Products::updateCountriesStatus($id, $status);
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
            $products = Products::getProductsDataFromId($id);
            if (!empty($products)) {
                $productsIdArr = $productsCodeArr = array();
                foreach ($products as $rowLanguage) {
                    $products_image = (isset($rowLanguage['image'])?$rowLanguage['image']:"");
                    if ($products_image != "") {
                        ## Delete image from s3
                        //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                          //  deleteImageFromAWS($products_image, $this->AWSdestinationPath, $this->size_array);
                        //}else{ //## Delete image from local storage
                            deleteImageFromFolder($products_image, $this->destinationPath, $this->size_array);
                            return 1;
                        //}
                    }
                }
            }   
        }
    }

    /**
    * Delete Uploaded Image
    */
    private function deleteImage($id,$flag){
        $result = array();
        if($id!="" && $flag!=""){
            ## Delete image from s3
            //if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
              //  deleteImageFromAWS($flag, $this->AWSdestinationPath, $this->size_array);
            //}else{ //## Delete image from local storage
                deleteImageFromFolder($flag, $this->destinationPath, $this->size_array);
            //}
            $update_array = array(
                'image' => '',
            );
            $deleteimage = Products::updateProducts($id, $update_array);
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
