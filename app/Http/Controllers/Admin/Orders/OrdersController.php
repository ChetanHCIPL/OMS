<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Admin\Master\ProductHeadController;
use App\Models\Districts;
use App\Models\Stateuse;
Use App\Models\Taluka;
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
use App\Models\TransporterWay;
use App\Models\State;
use App\Models\UserSalesState;
use App\Models\SalesUserRelationship;
use App\Models\UserSalesStateZone;
use App\Models\UserSalesStateZoneDistricts;
use App\Models\UserSalesStateZoneDistrictsTaluka;

class OrdersController extends AbstractController
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
        $this->file_max_size = Config::get('constants.FILE_MAX_SIZE');
        $this->file_max_size_MB = @(Config::get('constants.FILE_MAX_SIZE')*0.000001);
        $this->file_ext_array = array('pdf');
        $this->usertype=Config::get('constants.usertype');
        $this->countryid=config('settings.country');
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
        $SalesUsers=Admin::getSalesUsersList();
        $clientData = Clients::getClientDataList();
        
        return view('admin.orders.orders_list')->with(['OrderStatus'=>$statuslist,'ordercount'=>$ordercount,'SalesUsers'=>$SalesUsers,'clientData'=>$clientData]);
    }
    /**
     * Show the All godown details
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function godownpage(){
        $statuslist=OrderStatus::getListOrderData();
        $OrdersumR=Orders::getListOrdersStatusData();
        //$Transportlist=[1=>'Courier',2=>'Parcle',3=>'Bolero'];
        $Transportlist=TransporterWay::GetAllTransporterWay();
        $ordercount=[];
        foreach($OrdersumR as $val){
            $ordercount[$val['status']]=$val['numrow'];
        }
        $ordercount[0]=array_sum($ordercount);
        $SalesUsers=Admin::getSalesUsersList();
        
        return view('admin.orders.godown')->with(['OrderStatus'=>$statuslist,'ordercount'=>$ordercount,'SalesUsers'=>$SalesUsers,'Transportlist'=>$Transportlist]);
    }
    /**
     * Function : Ajax :  Get orders list with html
    * @param  array $request
    * @return  json $response
    *
     */
    public function godownajax(Request $request) {
        $statuslist=OrderStatus::getListOrderData();
        $Status=[];
        foreach($statuslist as $s){
            $Status[$s['id']]=$s['name'];
        }
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
        $PheadDetails=[];
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");       
        $tot_records_data = Orders::getOrdersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr,'om.client_id');
        $iTotalRecords = count($tot_records_data);
        $iDisplayLength = (isset($post['length']) ? intval($post['length']) : 50);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sorton = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'DESC');
        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "0":
                    $sort = "om.client_name";
                    break;
                case "1":
                    $sort = "om.order_date";
                    break;
                case "2":
                    $sort = "om.order_date";
                    break;
                case "3":
                    $sort = "a.first_name";
                    break;
                case "4":
                    $sort = "om.order_total";
                    break;
            }
        } else {
            $sort = "om.client_name";
        }
        $data = Orders::getOrdersData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr,'om.client_id')->toArray();
        //echo "<pre>"; print_r($data);exit();
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            //$encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
           /* $actions = '<span class="challan-pointer row-details row-details-close fa fa-plus-square overduedate" onclick="addRowsMore(this,\''.$encoded_id.'\');><input type="hidden" name="plus_fld" value="0" class="plus_hidden"></span>';*/
           $actions = "---";
           $show_data =  '<td class="alignCenter DueClass overdue"><span style="margin-top:5px;" class="challan-pointer row-details row-details-close fa overduedate fa-plus-square" onclick="addRowsMore(this,\''.$encoded_id.'\');" dataname="'.$data[$i]->client_name.'" datasnane="'.$data[$i]->sales_user_name.'"datamax="'.$data[$i]->numrow.'"><input type="hidden" name="plus_fld" value="1" class="plus_hidden"></span></td>';


            $status = Design::blade('status',$status,$status_color);
            $pdfimage="No uploaded";
            if(!empty($data[$i]->order_form_photo)){
                $pdfimage='<a href="http://192.168.32.160/ideal_oms/public/images/product/'.$data[$i]->order_form_photo.'" target="_blank">';
                $pdfimage.=(!empty(strpos($data[$i]->order_form_photo,'.pdf'))) ? 'pdf':'image';
                $pdfimage.='</a>';
            }
            
            
            $records["data"][] = array(
                $show_data,
                $data[$i]->client_name,
                "Area",
                "Total Bill:".$data[$i]->numrow,
                "Total Weight:235 kg",
                $data[$i]->sales_user_name,
                /*$actions*/
                /*'<div class="row"><div class="col-sm-2">'.$show_data.'</div><div class="col-sm-2">'.$data[$i]->client_name.'</div><div class="col-sm-2">Area</div><div class="col-sm-2">Total Bill:'.$data[$i]->numrow.'</div><div class="col-sm-2">Total Weight:235 kg</div></div>','','','','',$data[$i]->sales_user_name,
                $actions*/
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }
/**
     * Function : Ajax :  Get orders list with html of client id
    * @param  array $request
    * @return  json $response
    *
     */
    public function godownajaxDetails(Request $request) {
        $post = $request->All();
        //echo "<pre>"; print_r($post);exit();
        if(!empty($post['datamax'])){
            $data = $post;
           return Response::json($data);
        }
    }
    /*N
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
     * get client list
     * * @param  array $request
     * @return  json $response
     */
    public function PostAjaxDataClientsSearch(Request $request) {
        $records = $data = $flag = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->all();
        if(!empty($post['search_arr'])){
            /*if(!empty($post['search_status'])){
                $result_arr=Orders::getClientsDroupDownBySearch($post['search_arr'],$post['search_status']);
            }else{
                $result_arr=Orders::getClientsDroupDownBySearch($post['search_arr']);
            }*/
            $result_arr = Clients::getClientsListBySearch($post['search_arr']);
           return Response::json($result_arr); 
        }
    }

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
        $post = $request->All();       
        $search_arr = array();
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
        $PheadDetails=[];
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");       
        //DB::enableQueryLog();
        ##---query----
        $tot_records_data = Orders::getOrdersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr);
        //$quries = DB::getQueryLog();
        //echo "<pre>";print_r($tot_records_data);print_r($quries);exit;
        $iTotalRecords = count($tot_records_data);
        $iDisplayLength = (isset($post['length']) ? intval($post['length']) : 50);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sorton = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'DESC');

        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "1":
                    $sort = "om.order_no";
                    break;
                case "2":
                    $sort = "om.order_date";
                    break;
                case "4":
                    $sort = "om.client_name";
                    break;
                case "5":
                    $sort = "a.first_name";
                    break;
                case "6":
                    $sort = "om.order_total";
                    break;
                case "7":
                    $sort = "om.id";
                    break;
                case "8":
                    $sort = "om.order_expected_dispatched_date";
                    break;
                case "9":
                    $sort = "om.status";
                    break;
                default:
                    $sort = "om.order_no";
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
            $actions = '<div class="btn-group mr-1">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                <div class="dropdown-menu arrow">';

                    if (per_hasModuleAccess('Orders', 'Edit')) {
                        $actions .= '<a href="' . route('ordersm',['mode' => 'edit', 'id' => $encoded_id]) . '" class="dropdown-item" title="Edit">Edit</a>';
                    }
                    if (per_hasModuleAccess('Orders', 'View')) {
                        $actions .= '<a href="' . route('ordersm',['mode' => 'view', 'id' => $encoded_id]) . '" class="dropdown-item" title="View">View</a>';
                    }
                    if($data[$i]->status == 1) {
                        $actions .= '<a href="' . route('order/challan',['mode'=>'add','id' => $encoded_id]) . '" class="dropdown-item" title="Create Challan">Create Challan</a>';
                    }
                $actions .= '</div>
            </div>';
            
            $status = Design::blade('status',$status,$status_color);
            $pdfimage="No uploaded";
            if(!empty($data[$i]->order_form_photo)){
                $pdfimage='<a href="http://192.168.32.160/ideal_oms/public/images/product/'.$data[$i]->order_form_photo.'" target="_blank">';
                $pdfimage.=(!empty(strpos($data[$i]->order_form_photo,'.pdf'))) ? 'pdf':'image';
                $pdfimage.='</a>';
            }
            
            
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->order_no,
                (!empty($data[$i]->order_date)) ? date('d-M-Y',strtotime($data[$i]->order_date)):'',
                $pdfimage,
                $data[$i]->client_name,
                $data[$i]->sales_user_name,
                '₹ '. number_format($data[$i]->order_total,2),
                $data[$i]->id.'.000',
                (!empty($data[$i]->order_expected_dispatched_date)) ? date('d-M-Y',strtotime($data[$i]->order_expected_dispatched_date)):'',
                $Status[$data[$i]->status],
                $actions
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
        $post = $request->All();
        
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
                
                $validator = $this->validateFields($post);
                
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }

                if (isset($post['prd_id']) && is_array($post['prd_id']) && !empty($post['prd_id'])) {
                    $products = array();
                    $error = array();
                    foreach ($post['prd_id'] as $key => $value) {

                        $productData = Products::getProductsDataFromId($post['prd_id'][$key]['product_id'], $post['client_discount_category_id']);


                        if ($productData[0]['max_order_qty'] < $post['prd_quantity'][$key]['qty']) {
                            $error[] = $productData[0]['name'] . ': Quantity more than order product max quantity<br>';
                        }

                        if ($productData[0]['max_discount'] < $post['prd_discount'][$key]['dic_percentage']) {
                            $error[] = $productData[0]['name'] . ': Discount Percentage is more than order product max percentage.<br>';
                        }
                    }

                    if(isset($error) && !empty($error)){
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = implode( " ", $error );
                        return Response::json($result_arr);
                    }
                }

                // echo '<pre>'; print_r($products); echo '</pre>'; exit();

                $file = $request->file('order_form_photo');

                $result_arr = $this->insertRecord($post, $file);
                /*if(isset($insert) && !empty($insert)){
                    $result_arr[0]['isError'] = $insert['isError'];
                    $result_arr[0]['msg'] = $insert['msg'];
                }else{
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }*/
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
                $result_arr = $this->updateRecord($post, $flag);
                /*if(isset($update) && !empty($update)){
                    $result_arr[0]['isError'] = $update['isError'];
                    $result_arr[0]['msg'] = $update['msg'];
                }else{
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }*/
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
            else if ($post['customActionName'] == "CustomerAddressAdd"){
                $insert = $this->insertBillingAddressRecord($post);
               if(isset($insert) && !empty($insert)){
                    $result_arr[0]['isError'] = $insert['isError'];
                    $result_arr[0]['msg'] = $insert['msg'];
                }else{
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
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
        $image_extention = implode(', ',$this->file_ext_array); 
        
        $series=Series::getSeriesDataList();
        $segment=Segment::getAllActiveSegmentData();
        if (isset($id) && $id != "") {
            $id = substr($id, 3, -3);
            
            $data = Orders::getOrdersDataFromId($id);
         //   var_dump($data);
         $addresses=ClientsAddress::getClientDataListofids($data[0]['client_id']);
            ## Check Image is Exist or not
            //$image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
            $checkImgArr ='';
            /*$image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                if($image != ''){
                    $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'order','','_',$image,count($this->size_array));
                }*/
            if($mode == 'edit'){/*
                    $mode = 'Update';
                    return view('admin.orders.orders_add_challan')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards]);
                }else if($mode == 'view'){
                    $mode = 'View';
                    return view('admin.orders.orders_view')->with(['mode' => $mode, 'data' => $data,'checkImgArr'=>$checkImgArr,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'Board'=>$boards]);
                }*/
            }elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.orders.orders_add_challan')->with(['mode' => $mode,'file_ext_array'=>[],'data' => $data,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters]);
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
        $StateList=UserSalesState::GetAllStateOfSalesUser();
        $usersate=[];
        foreach($StateList as $us){
            $usersate[$us['user_id']][]=$us['state_name'];
        }
        
        $allUserZone=UserSalesStateZone::GetZoneDetailsofusers();
       $userzone=[];
        foreach($allUserZone as $us){
            $userzone[$us['user_id']]=$us['zone_name'];
        }
        $dLUsers=UserSalesStateZoneDistricts::getAllDistrictsListUsers();
        $userd=[]; 
        foreach($dLUsers as $ds){
            $userd[$ds['user_id']][]=$ds['district_name'];
        }
        $talukaUL=UserSalesStateZoneDistrictsTaluka::getAllTalukaListUsers();
        $userdt=[]; 
        foreach($talukaUL as $dt){
            $userdt[$dt['user_id']][]=$dt['taluka_name'];
        }
        $USalesStatus=['rsm'=>$usersate,'zsm'=>$userzone,'asm'=>$userd,'aso'=>$userdt];
        
        /*foreach ($clientData as $key => $value) {
            $client_id_arr[] = $value['id'];
        }
        echo "<pre>"; print_r($client_id_arr);exit();*/
        $salesUsers = Admin::getSalesUsersList();
        $productHeads = ProductHead::getProductHeadAllList();
        $products = Products::getAllProductsData();
        $mediums = Medium::getMediumDataWithBoad();
        $paymentTerms = PaymentTerms::getAllPaymentTermsData();
        $transporters = Transporter::getAllTransporters();
         $state=State::getStateFromCountryCode($this->countryid);
        
        $image_extention = '';
        
        if(isset($mode) && $mode != ""){
            $image_extention = implode(', ',$this->file_ext_array); 
            
            $series=Series::getSeriesDataList();
            $segment=Segment::getAllActiveSegmentData();
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Orders::getOrdersDataFromId($id);
                $order_details_data= OrdersDetail::getClientDetailsByOrderId($id);
                $addresses=ClientsAddress::getClientDataList($data[0]['client_id']);
                $contacts=ClientContactPerson::getClientDataList($data[0]['client_id']);
                ## Check Image is Exist or not
                //$image = isset($data[0]['flag']) && $data[0]['flag'] !='' ? $data[0]['flag'] : '';
                $checkImgArr ='';
                $image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                    if($image != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'order','','_',$image,count($this->size_array));
                    }
                if($mode == 'edit'){
                    $mode = 'Update';
                   // return view('admin.orders.orders_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'checkImgArr'=>$checkImgArr,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards, 'transporters' => $transporters]);
                   return view('admin.orders.orders_add')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters,'state'=>$state]);
                }else if($mode == 'view'){
                    $mode = 'View';
                   // return view('admin.orders.orders_view')->with(['mode' => $mode, 'data' => $data,'checkImgArr'=>$checkImgArr,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'Board'=>$boards]);
                   return view('admin.orders.orders_view')->with(['mode' => $mode,'file_ext_array'=>[],'data' => $data,'series'=>$series,'segment'=>$segment,'order_product_data'=>$order_details_data,'addresses'=>$addresses,'contacts'=>$contacts,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters]);
                }
            }elseif($mode == 'add') {
                $mode = "Add";
                return view('admin.orders.orders_add')->with(['mode' => $mode,'file_ext_array'=>$image_extention,'series'=>$series,'segment'=>$segment,'MediumList'=>$allmediumlist,'SemsList'=>$allsems,'Board'=>$boards,'ClientList'=>$clientData,'salesUsers'=>$salesUsers,'productHeads'=>$productHeads,'products'=>$products,'mediums'=>$mediums, 'paymentTerms' => $paymentTerms, 'transporters' => $transporters,'SUS'=>$USalesStatus,'state'=>$state]);
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
                'client_id'=>'required',
                'client_address_id' => 'required',
                'client_contact_id' => 'required',
                'client_ship_address_id' => 'required',
                'sales_user_id' => 'required',
                'product_head' => 'required',
                'transporter' => 'required',
                'route_area' => 'required',
                'payment_due_days' => 'required',
                'due_date' => 'required',
                'dispatch_date' => 'required',
                'order_date' => 'required',
            );
        } else {
            $rules = array(
                'client_id'=>'required',
                'client_address_id' => 'required',
                'client_contact_id' => 'required',
                'client_ship_address_id' => 'required',
                'sales_user_id' => 'required',
                'product_head' => 'required',
                'transporter' => 'required',
                'route_area' => 'required',
                'payment_due_days' => 'required',
                'due_date' => 'required',
                'dispatch_date' => 'required',
                'order_date' => 'required',
            );
        }
        $messages = array(
            'client_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client '),
            'client_address_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Address'),
            'client_contact_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Contact '),
            'client_ship_address_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Shipment Address'),
            'sales_user_id.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Sales User'),
            'product_head.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Product Head'),
            'transporter.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Transporter'),
            'route_area.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Route Area'),
            'payment_due_days.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Payment Due Days'),
            'due_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Payment Due Date'),
            'dispatch_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Dispatch Date'),
            'order_date.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Order Date'),
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

        $paymentTerms = PaymentTerms::getPaymentTermsDataFromId($data['payment_due_days']);

        $clientContactPerson = ClientContactPerson::getClientContactPersonDataFromId($data['client_contact_id']);

        $clientData = Clients::getClientsDataFromId($data['client_id']);

        $bill_address = $this->getAddressByAddressID($data['client_address_id']);
        // Shipping Address
        $ship_address = $this->getAddressByAddressID($data['client_ship_address_id']);

        $file = '';
        if (!empty($files)) {
            $fileOriname    = $files->getClientOriginalName();
            $file           = time().'_'.gen_remove_spacial_char($fileOriname);
            $fileExt        = $files->getClientOriginalExtension();
            $fileSize       = $files->getSize();
            $fileRealPath   = $files->getRealPath();

            if (in_array($fileExt, $this->file_ext_array)){

                if ($fileSize > $this->file_max_size || $fileSize == 0){
                    $result[0]['isError'] = 1;
                    $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE'). '<br/> File size must be upto '.$this->file_max_size_MB.' MB';
                    return $result;
                    exit;
                }
                else {
                    //storeFileInFolder($fileRealPath, $this->destinationPath, $file);
                    $files->move($this->destinationPath, $file);
                }
            } else {
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                return $result;
                exit;
            }
        }

        $insert_array = array(
            'user_id'       => Auth::guard('admin')->user()->id,
            'user_type'     => 1, 
            'parrent_id'    => 0, 
            'order_date'    => $data['order_date'],
            'order_expected_dispatched_date'    => $data['dispatch_date'], 
            'order_total'  => $data['order_total'],
            'order_subtotal' => $data['sub_total_value'], 
            'order_discount' => $data['dis_total_value'],
            'transport_id' => $data['transporter'],
            'order_payment_due_days' => $paymentTerms[0] ? $paymentTerms[0]['due_type_value'] : '', 
            'order_payment_due_date' => $data['due_date'], 
            'order_remark' => $data['order_remark'], 
            'client_contact_person_id' => $data['client_address_id'],
            'order_responsible_person_name' => $clientContactPerson[0] ? $clientContactPerson[0]['full_name'] : '',
            'order_responsible_person_number' => $clientContactPerson[0] ? $clientContactPerson[0]['mobile_number'] : '',
            'client_id' => $data['client_id'],
            'client_name' => $clientData[0] ? $clientData[0]['client_name'] : '',
            'client_number' => $clientData[0] ? $clientData[0]['mobile_number'] : '',
            'sales_user_id' => $data['sales_user_id'], 
            'status' => 1,
            'billing_address_id' => $data['client_address_id'], 
            'billing_address_name' => $bill_address['title'],
            'billing_address' => $bill_address['address'],
            'shipping_address_id' => $data['client_ship_address_id'],
            'shipping_address_name' => $ship_address['address'],
            'shipping_address' => $ship_address['title'],
            'order_form_photo' => $file,
            'created_by' => Auth::guard('admin')->user()->id
        );
        $getSRShip=SalesUserRelationship::UserSalesRelationshipByUserID($data['sales_user_id']);
        $insert_array['rsm_id']=0;
        $insert_array['zsm_id']=0;
        $insert_array['dy_zsm_id']=0;
        $insert_array['asm_id']=0;
        if(!empty($getSRShip)){
            $insert_array['rsm_id']=$getSRShip[0]['rsm_id'];
            $insert_array['zsm_id']=$getSRShip[0]['zsm_id'];
            $insert_array['dy_zsm_id']=$getSRShip[0]['dy_zsm_id'];
            $insert_array['asm_id']=$getSRShip[0]['asm_id'];
        }
        $insert = Orders::addOrders($insert_array);
        if (!empty($insert)) {

            if ($data['prd_id']){
                $products = array();
                foreach ($data['prd_id'] as $key => $product) {
                    $productData = Products::getProductsDataFromId($product['product_id']);
                    // Create entry for each product in order detail.
                    $products[] = array(
                        'order_id' => $insert['id'],
                        'product_id' => $product['product_id'],
                        'product_name' => $productData[0]['name'],
                        'product_sku' => $productData[0]['code'],
                        'order_qty' => $data['prd_quantity'][$key]['qty'],
                        'price' => $productData[0]['mrp'], 
                        'final_amount' => $data['prd_amount'][$key]['prd_amount'],
                        'discount' => $data['prd_dis_amount'][$key]['dis_price'],
                        'max_discount' => $data['prd_discount'][$key]['dic_percentage'],
                        'created_by' => Auth::guard('admin')->user()->id,
                    );
                }
                OrdersDetail::addOrderProduct($products);
            }

            //Update Order Number
            Orders::updateOrderNumber($insert['id'], Config::get('constants.order_prefix'));

            $result[0]['isError'] = 0;
            $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');

            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.orders');
            $log_text = "Orders " . $insertedId . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $insert['id'], "log_text" => $log_text, "req_data" => $data);
             usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result[0]['isError'] = 1;
            $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
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
            if (in_array($fileExt, $this->file_ext_array)){
                if ($fileSize > $this->file_max_size || $fileSize == 0){
                    $result[0]['isError'] = 1;
                    $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE');
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
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
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
            $result[0]['isError'] = 0;
            $result[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.orders');
            $log_text = "Orders " . $data['name'] . " added";
            $activeAdminId   = Auth::guard('admin')->user()->id;

            $activity_arr = array("admin_id" => $activeAdminId, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $orders_code, "log_text" => $log_text, "req_data" => $data);
             usr_addActivityLog($activity_arr);
            ## End Add Activity
        } else {
            $result[0]['isError'] = 1;
            $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
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
    * Function : Add Individual User Data
    *
    * @param    array  $data
    * @return   array   $result_arr
    */
    private function insertBillingAddressRecord($data){
        $is_locked = 0;
        $approved_date = "";
        $client_id = isset($data['client_ids']) ? $data['client_ids'] : '';
        $title = isset($data['title']) ? $data['title'] : '';
        $address1 = isset($data['address1']) ? $data['address1'] : '';
        $address2 = isset($data['address2']) ? $data['address2'] : '';
        $mobile_number = isset($data['mobile_number']) ? $data['mobile_number'] : '';
        $email = isset($data['email']) ? $data['email'] : '';
        $state_id = isset($data['state1']) ? $data['state1'] : '';
        $district_id = isset($data['district1']) ? $data['district1'] : '';
        $taluka_id = isset($data['taluka1']) ? $data['taluka1'] : '';
        $zip_code = isset($data['zip_code']) ? $data['zip_code'] : '';
        $used_for_billing = isset($data['used_for_billing']) ? $data['used_for_billing'] : '';
        $used_for_shipping = isset($data['used_for_shipping']) ? $data['used_for_shipping'] : '';
        $is_default_billing = isset($data['is_default_billing']) ? $data['is_default_billing'] : '';
        $is_default_shipping = isset($data['is_default_shipping']) ? $data['is_default_shipping'] : '';
        $is_approved = isset($data['is_approved']) ? $data['is_approved'] : '';
        if($is_approved == 1){
            $is_locked = 1;
            $approved_date = date_getSystemDateTime();
        }
        $status = isset($data['status']) ? $data['status'] : ClientsAddress::INACTIVE;
        $created_at = date_getSystemDateTime();
         ## Create Insert Array
        $address_client = array(
            'client_id'=>$client_id,
            'title' => $title,
            'address1' => $address1,
            'address2' => $address2,
            'mobile_number' => $mobile_number,
            'email' => $email,
            'state_id' => $state_id,
            'district_id' => $district_id,
            'taluka_id' => $taluka_id,
            'zip_code' => $zip_code,
            'use_for_billing' => $used_for_billing,
            'use_for_shipping' => $used_for_shipping,
            'is_default_billing' => $is_default_billing,
            'is_default_shipping' => $is_default_shipping,
            'is_approved' => $is_approved,
            'is_locked' => $is_locked,
            'approved_date' => $approved_date,
            'status' => $status,
            'created_at'=>date_getSystemDateTime(),
            'updated_at'=>date_getSystemDateTime()
        );
       // echo "<pre>"; print_r($address_client);exit();
        ## Add Client  Address data
        $insert_address = ClientsAddress::addClientAddress($address_client);
         $result['isError'] = 0;
           if(isset($insert_address) && !empty($insert_address)){
                    $result['isError'] = 0;
                    $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
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

        $productsList = Products::getFilteredProducts($data['search_arr'], $client_category ? $client_category[0]['discount_category'] : '');
        return response()->json($productsList);
    }

    /**
    * Function : Ajax : Get Products Data based on product IDs
    * @param  array $request
    * @return  json $response
    */
    public function ProductsDataByIds(Request $request)
    {

        $data = $request->all();
        $data['client_cat_id']=1;
        $productsData = Products::getProductsDataFromId($data['product_ids'], $data['client_cat_id']);

        return response()->json($productsData);
    }

    /**
     * Show the All orders records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function challanList(){
        /*$statuslist=OrderStatus::getListOrderData();
        $OrdersumR=Orders::getListOrdersStatusData();
        $ordercount=[];
        foreach($OrdersumR as $val){
            $ordercount[$val['status']]=$val['numrow'];
        }
        $ordercount[0]=array_sum($ordercount);*/
        
        return view('admin.orders.create_challan');
    }


    /**
     * return Formatted Address By Address ID
     * @param mixed $address_id
     * @return array
     */
    private function getAddressByAddressID($address_id) {
        $shippingAddress = ClientsAddress::getClientAddressDataFromId($address_id);

        // Shipping Address
        $ship_address = array();
        $address = '';
        if (isset($shippingAddress[0])){
            $address .= $shippingAddress[0]['address1'] ? $shippingAddress[0]['address1'] : '';
            $address .= $shippingAddress[0]['address2'] ? ', '. $shippingAddress[0]['address2'] : '';
            if ($shippingAddress[0]['state_id']){
                $state = State::getStateDataFromId($shippingAddress[0]['state_id']);
                $address .= $state[0]['state_name'] ? ', '. $state[0]['state_name'] : '';
            }
            if ($shippingAddress[0]['district_id']){
                $dist = Districts::getDistrictsDataFromId($shippingAddress[0]['district_id']);
                $address .= $dist[0]['district_name'] ? ', '. $dist[0]['district_name'] : '';
            }
            if ($shippingAddress[0]['district_id']){
                $dist = Taluka::getTalukaDataFromId($shippingAddress[0]['taluka_id']);
                $address .= $dist[0]['taluka_name'] ? ', '.$dist[0]['taluka_name'] : '';
            }
            $address .= $shippingAddress[0]['zip_code'] ? ' - '. $shippingAddress[0]['zip_code'] : '';

            $ship_address['address'] = $address;
            $ship_address['title'] = $shippingAddress[0] ? $shippingAddress[0]['title'] : '';
        }        


        return $ship_address;
    }
}
