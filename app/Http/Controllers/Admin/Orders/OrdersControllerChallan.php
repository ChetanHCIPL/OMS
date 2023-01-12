<?php

namespace App\Http\Controllers\Admin\Orders;

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
use DB;
use Intervention\Image\File;
use App\GlobalClass\Design;
use App\Models\Orders;
use App\Models\OrdersDetail;
use App\Models\Series;
use App\Models\Segment;
use App\Models\Medium;
use App\Models\Semester;
use App\Models\Board;
use App\Models\OrderStatus;
use App\Models\Clients;
use App\Models\ClientContactPerson;
use App\Models\ClientsAddress;
use App\Models\Admin;
use App\Models\PaymentTerms;
use App\Models\ProductHead;
use App\Models\Products;
use App\Models\Transporter;
class OrdersControllerChallan extends AbstractController
{
	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     //   $this->middleware('admin');
        //$this->middleware('accessrights'); 
        $this->destinationPath = Config::get('path.order_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.order_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->usertype=Config::get('constants.usertype');
    }


    /**
     * Show the All orders records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $statuslist=OrderStatus::getListOrderData();
        $OrdersumR=Orders::getListOrdersStatusData();
        $ordercount=[];
        foreach($OrdersumR as $val){
            $ordercount[$val['status']]=$val['numrow'];
        }
        $ordercount[0]=array_sum($ordercount);
        
        return view('admin.orders.orders_list')->with(['OrderStatus'=>$statuslist,'ordercount'=>$ordercount]);
    }
    /*
    *
    * Function : Ajax :  Get orders list with html
    * @param  array $request
    * @return  json $response
    *
    */
    public function orderslist(Request $request) {
        $data = $request->all();
        $ordersData = Orders::getAllOrdersData();
        return response()->json($ordersData);
    }


    /**
     * Get Orders ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    

    /**
     * Get orders data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {
        $statuslist=OrderStatus::getListOrderData();
        $Transport=[1=>'Patel Transport',2=>'Shree Ganesh',3=>'Mahashager',4=>'Geeta Transport'];
        $Status=[];
        foreach($statuslist as $s){
            $Status[$s['id']]=$s['name'];
        }
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;

        ## Request Parameters
        $post = Input::get();
       
        $search_arr = array();

        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }

        $PheadDetails=[];
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");       
        $tot_records_data = Orders::getOrdersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "id";
                    break;
                default:
                    $sort = "id";
            }
        } else {
            $sort = "name";
        }
        $data = Orders::getOrdersData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            //$encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('Orders', 'Edit')) {
                $edit = ' <a href="' . route('orders',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                
            }
            if (per_hasModuleAccess('Orders', 'View')) {
                $view = '';//'<a href="' . route('orders',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            
            $status = Design::blade('status',$status,$status_color);
            $pdfimage="No uploaded";
            if(!empty($data[$i]->order_form_photo)){
                $pdfimage='<a href="http://192.168.32.160/ideal_oms/public/images/product/'.$data[$i]->order_form_photo.'" target="_blank">';
                $pdfimage.=(!empty(strpos($data[$i]->order_form_photo,'.pdf'))) ? 'pdf':'image';
                $pdfimage.='</a>';
            }
            $print="";
            if($data[$i]->status==1){
                $print=' <a href="' . route('order/challan',['mode'=>'add','id' => $encoded_id]) . '" title="Edit">'.Design::button('print').'</a>';
            }
            $salesname="";
            if($data[$i]->sales_user_id==1){
                $salesname='Admin Admin';
            }
            if($data[$i]->sales_user_id==2){
                $salesname='Jack Sparrow';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->order_no,
                (!empty($data[$i]->order_date)) ? date('d-M-Y',strtotime($data[$i]->order_date)):'',
                $pdfimage,
                $data[$i]->client_name,
                $salesname,
                number_format($data[$i]->order_amount,2),
                $data[$i]->id.'.000',
                (!empty($data[$i]->order_expected_dispatched_date)) ? date('d-M-Y',strtotime($data[$i]->order_expected_dispatched_date)):'',
                $Status[$data[$i]->status],
                $view.$edit.$print
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

    /**
    *
    * Function : change status , add , edit and delete Orders data
    * @param  array $request
    * @return  json $response
    *
    */
    public function PostAjaxData(Request $request) {
        $records = $data = $flag = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = Input::get();
        
        if (isset($post['customActionName'])) {
            $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.orders');
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
                        $log_text = "Orders " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Orders " . $post['id'] . " Status updated to ".$post['customActionName'];
                    $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
                     usr_addActivityLog($activity_arr);
                }
                // End Add Activity log
                $result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            } elseif ($post['customActionName'] == "Add") {
                
                echo '<pre>'; print_r($post); echo '</pre>'; exit();
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
    // addChallan
        /**
    *
    * Function : Load form to Add/Edit/view challan
    * @param   int $id
    * @return  json $response
    *
    */ 
    public function addChallan($mode = NULL, $id = NULL) {
        $data = $language = $orders_language = $allmediumlist = array();
        $allmediumlist=Medium::getMediumDataList();
        $allsems= Semester::getSemesterDataList();
        $boards=Board::getListBoardData();
        $clientData = Clients::getClientDataList();
        $salesUsers = Admin::getSalesUsersList();
        $productHeads = ProductHead::getProductHeadAllList();
        $products = Products::getAllProductsData();
        $mediums = Medium::getMediumDataWithBoad();
        $paymentTerms = PaymentTerms::getAllPaymentTermsData();
        $transporters = Transporter::getAllTransporters();
      $image_extention = '';
    if(isset($mode) && $mode != ""){
        $image_extention = implode(', ',$this->img_ext_array); 
        
        $series=Series::getSeriesDataList();
        $segment=Segment::getAllActiveSegmentData();
        if (isset($id) && $id != "") {
            $id = substr($id, 3, -3);
            
        $data = Orders::getOrdersDataFromId($id);
        $order_details_data= OrdersDetail::getClientDetailsByOrderId($id);
         $addresses=ClientsAddress::getClientDataList($data[0]['client_id']);
         $contacts=ClientContactPerson::getClientDataList($data[0]['client_id']);
         // var_dump($addresses);
            ## Check Image is Exist or not
         // $image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
            $checkImgArr ='';
            /*$image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                if($image != ''){
                    $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'order','','_',$image,count($this->size_array));
                }*/
            if($mode == 'edit'){/*
                    $mode = 'Update';
                    return view('admin.orders.orders_add_challan')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards]); */
            }
            else if($mode == 'view'){
                $mode = 'View';
                return view('admin.orders.orders_view_challan')->with(['mode' => $mode,'img_ext_array'=>[],'data' => $data,'series'=>$series,'segment'=>$segment,'order_product_data'=>$order_details_data,'addresses'=>$addresses,'contacts'=>$contacts,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters]);
            }
            elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.orders.orders_add_challan')->with(['mode' => $mode,'img_ext_array'=>[],'data' => $data,'series'=>$series,'segment'=>$segment,'order_product_data'=>$order_details_data,'addresses'=>$addresses,'contacts'=>$contacts,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters]);
            }
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
        $data = $language = $orders_language = $allmediumlist = array();
        $allmediumlist=Medium::getMediumDataList();
        $allsems= Semester::getSemesterDataList();
        $boards=Board::getListBoardData();
        $clientData = Clients::getClientDataList();
        $salesUsers = Admin::getSalesUsersList();
        $productHeads = ProductHead::getProductHeadAllList();
        $products = Products::getAllProductsData();
        $mediums = Medium::getMediumDataWithBoad();
        $paymentTerms = PaymentTerms::getAllPaymentTermsData();
        $transporters = Transporter::getAllTransporters();
        
        $image_extention = '';
        if(isset($mode) && $mode != ""){
            $image_extention = implode(', ',$this->img_ext_array); 
            
            $series=Series::getSeriesDataList();
            $segment=Segment::getAllActiveSegmentData();
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Orders::getOrdersDataFromId($id);
                ## Check Image is Exist or not
                //$image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
                $checkImgArr ='';
                $image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                    if($image != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'order','','_',$image,count($this->size_array));
                    }
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin.orders.orders_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards]);
                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.orders.orders_view')->with(['mode' => $mode, 'data' => $data,'checkImgArr'=>$checkImgArr,'img_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'Board'=>$boards]);
                }
            }elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.orders.orders_add')->with(['mode' => $mode,'img_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters]);
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
                'order_head_id'=>'required',
                'name' => 'required|min:3|max:100|unique:orders,name,NULL,id',
                'orders_code' => 'required|min:3|max:20|regex:/^[a-zA-Z ]*$/|unique:orders,code,NULL,id',
            );
        } else {
            $rules = array(
                'order_head_id'=>'required',
                'name' => 'required|min:3|max:100|unique:orders,name,'.$data['id'].',id',
                'orders_code' => 'required|min:3|max:20|regex:/^[a-zA-Z ]*$/|unique:orders,code,'.$data['id'].',id',
            );
        }
        $messages = array(
            'order_head_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Order Head'),
            'name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Orders Name'),
            'name.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Orders Name'),
            'name.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Orders Name'),
            'name.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Orders Name'),
            'orders_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Orders Code'),
            'orders_code.min' => sprintf(Config::get('messages.validation_msg.minlength'), '3', 'Orders Code'),
            'orders_code.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Orders Code'),
            'orders_code.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Orders Code'),
            'orders_code.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Orders Code'),
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
             //echo "<pre>";print_r($files);exit;
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
        $orders_code = (isset($data['orders_code']) ? strtoupper($data['orders_code']) : "");
        $data['qrcode'] = (isset($data['qrcode']) ? strtoupper($data['qrcode']) : "");
        $data['lock_for_order']= isset($data['lock_for_order']) ? $data['lock_for_order'] : 0;
        $data['cn_lock']= isset($data['cn_lock']) ? $data['cn_lock'] : 0;
        $data['segment_id']= isset($data['segment_id']) ? $data['segment_id'] : 0;
        $data['series_id']= isset($data['series_id']) ? $data['series_id'] : 0;
        $insert_array = array( 
        'is_kit_order'=>0, 
        'order_head_id'=>$data['order_head_id'], 
        'series_id'=>$data['series_id'], 
        'name'=>$data['name'], 
        'order_number'=>1, 
        'code'=>$data['orders_code'], 
        'hsn_number'=>$data['orders_code'], 
        'qrcode'=>$data['qrcode'], 
        'mrp'=>$data['mrp'], 
        'pages'=>$data['pages'], 
        'badho'=>$data['badho'], 
        'weight'=>$data['weight'], 
        'image'=>$flag, 
        'description'=>$data['description'], 
        'stock'=>0, 
        'lock_for_order'=>$data['lock_for_order'], 
        'cn_lock'=>$data['cn_lock'], 
        'created_at'=>date_getSystemDateTime(), 
        'status' => (isset($data['status']) ? $data['status'] : Orders::INACTIVE),
        );
        $insert = Orders::addOrders($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.orders');
            $log_text = "Orders " . $data['name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $orders_code, "log_text" => $log_text, "req_data" => $data);
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
        $orders_code = (isset($data['orders_code']) ? strtoupper($data['orders_code']) : "");
        $data['qrcode'] = (isset($data['qrcode']) ? strtoupper($data['qrcode']) : "");
        $data['lock_for_order']= isset($data['lock_for_order']) ? $data['lock_for_order'] : 0;
        $data['cn_lock']= isset($data['cn_lock']) ? $data['cn_lock'] : 0;
        $update_array = array( 
        'is_kit_order'=>0, 
        'medium_id'=>1, 
        'order_head_id'=>$data['order_head_id'], 
        'segment_id'=>$data['segment_id'], 
        'series_id'=>$data['series_id'], 
        'semester_id'=>0, 
        'name'=>$data['name'], 
        'order_number'=>1, 
        'code'=>$data['orders_code'], 
        'hsn_number'=>$data['orders_code'], 
        'qrcode'=>$data['qrcode'], 
        'mrp'=>$data['mrp'], 
        'pages'=>$data['pages'], 
        'badho'=>$data['badho'], 
        'weight'=>$data['weight'], 
        'image'=>$flag, 
        'description'=>$data['description'], 
        'stock'=>0, 
        'lock_for_order'=>$data['lock_for_order'], 
        'cn_lock'=>$data['cn_lock'], 
        'created_at'=>date_getSystemDateTime(), 
        'status' => (isset($data['status']) ? $data['status'] : Orders::INACTIVE),
        );
        $update = Orders::updateOrders($id, $update_array);

        if (isset($update)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.orders');
            $log_text = "Orders " . $data['name'] . " added";
            $activeAdminId   = Auth::guard('admin')->user()->id;

            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $orders_code, "log_text" => $log_text, "req_data" => $data);
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
            $update = Orders::updateCountriesStatus($id, $status);
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
            $orders = Orders::getOrdersDataFromId($id);
            if (!empty($orders)) {
                $ordersIdArr = $ordersCodeArr = array();
                foreach ($orders as $rowLanguage) {
                    $orders_image = (isset($rowLanguage['flag'])?$rowLanguage['flag']:"");
                    if ($orders_image != "") {
                        ## Delete image from s3
                        if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                            deleteImageFromAWS($orders_image, $this->AWSdestinationPath, $this->size_array);
                        }else{ //## Delete image from local storage
                            deleteImageFromFolder($orders_image, $this->destinationPath, $this->size_array);
                        }
                    }
                    $ordersIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                }
                if(!empty($ordersIdArr)){
                    # Delete records from Orders
                    $delete_orders = Orders::deleteCountries($ordersIdArr);
                }
            }   
        }
        if (isset($delete_orders)) {
            ##Delete contry realted state by Orders_id
            if(!empty($ordersIdArr)){
                $delete_state = State::deleteStatesByOrdersId($ordersIdArr);
              
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
            $deleteimage = Orders::updateOrders($id, $update_array);
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

    /**
    *
    * Function : Ajax : Get Products list based on filters
    * @param  array $request
    * @return  json $response
    *
    */
    public function filteredProductsList(Request $request)
    {
        $data = $request->all();

        $client_category = Clients::getClientsDataFromId($id = $data['search_arr']['client_id']);

        $productsList = Products::getFilteredProducts($data['search_arr'], $client_category[0]['discount_category'] ? $client_category[0]['discount_category'] : '');
        return response()->json($productsList);
    }

    /**
    *
    * Function : Ajax : Get Products Data based on product IDs
    * @param  array $request
    * @return  json $response
    *
    */
    public function ProductsDataByIds(Request $request){
        $data = $request->all();
        $productsData = Products::getProductsDataFromId($data['product_ids'], $data['client_cat_id']);
        return response()->json($productsData);
    }

    /**
     * Show the All orders records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function challanList(){
        $statuslist=OrderStatus::getListOrderData();
        $OrdersumR=Orders::getListOrdersStatusData();
        $ordercount=[];
        foreach($OrdersumR as $val){
            $ordercount[$val['status']]=$val['numrow'];
        }
        $ordercount[0]=array_sum($ordercount);
        
        return view('admin.orders.create_challan')->with(['OrderStatus'=>$statuslist,'ordercount'=>$ordercount]);
    }

    /**
    *
    * Function : Ajax : Update order status
    * @param  array $request
    * @return  json $response
    *
    */
    public function updateOrderStatus(Request $request)
    {
        $data = $request->formData;
        $id = $data['id'];
        $update_array = array(
            'Status' => $data['status'],
        );
        $updateOrder = Orders::updateOrders($id, $update_array);
        //return $updateOrder;
        if (isset($updateOrder) && $updateOrder != 0) {
            $result['isError'] = '';
            $result['msg'] = "Challan generated";
            $result['data'] =  $update_array;
        }else{
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }

        return $result;
    }
}
