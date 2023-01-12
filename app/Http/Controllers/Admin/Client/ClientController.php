<?php
namespace App\Http\Controllers\Admin\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Facades\Response;
use Validator;
use Config;
use App;
use Image;
use Intervention\Image\File;
use App\GlobalClass\Design;
use App\Models\Clients;
use App\Models\ClientContactPerson;
use App\Models\ClientsAddress;
use App\Models\Designation;
use App\Models\ClientType;
use App\Models\UserDiscountCategory;
use App\Models\State;
use App\Models\Districts;
use App\Models\Taluka;
use App\Models\Grade;
use App\Models\PaymentTerms;
use App\Models\ClientStatusLog;
use App\Models\Section;
use App\Models\SchoolType;
use App\Models\Board;
use App\Models\Medium;
use App\Models\ClientSection;
use App\Models\ClientSchool;
use App\Models\ClientBoard;
use App\Models\ClientMedium;
use App\Models\UserSalesState;
use App\Models\UserSalesStateZone;
use App\Models\UserSalesStateZoneDistricts;
use App\Models\UserSalesStateZoneDistrictsTaluka;
use App\Models\Admin;
use App\Models\ClientDocument;
use Db;
use URL;
class ClientController extends AbstractController
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
        $this->destinationPath = Config::get('path.client_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.client_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->countryid=config('settings.country');
        $this->client_document_ext_array = Config::get('constants.client_document_ext_array');
        $this->client_document_accept_ext_array = Config::get('constants.client_document_accept_ext_array');
        $this->client_documents_path = Config::get('path.client_documents_path');
        $this->client_document_size = Config::get('constants.client_document_size');
        $this->is_softDelete = 1;
    }
    /**
     * Show the All clients records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $salesUsers = Admin::getSalesUsersList();
        return view('admin.client.client_list')->with(['salesUsers'=>$salesUsers]);
    }
    /*
    *
    * Function : Ajax :  Get clients list with html
    * @param  array $request
    * @return  json $response
    *
    */
    public function clientslist(Request $request)
    {
        $data = $request->all();
        $clientsData = Clients::getAllClientData();
        return response()->json($clientsData);
    }
    
    /**
     * Get clients data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;

        ## Request Parameters
        $post = $request->all();
       
        $search_arr = array();

        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");     
        $tot_records_data = Clients::getClientsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "client_name";
                    break;
                // case "2":
                //     $sort = "billing_name";
                //     break;
                case "2":
                    $sort = "email";
                    break;
                case "3":
                    $sort = "mobile_number";
                    break;
                case "4":
                    $sort = "whatsapp_number";
                    break;
                case "5":
                    $sort = "salespersonname";
                    break; 
                case "6":
                    $sort = "created_at";
                    break;                    
                case "7":
                    $sort = "status";
                    break;
                default:
                    $sort = "client_name";
            }
        } else {
            $sort = "client_name";
        }
        $data = Clients::getClientsData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $client_status = $data[$i]->status;
            $status = 'Inactive';
            if($client_status == 1){
               $status = 'Active'; 
            }elseif($client_status == 2){
                $status = 'Verified';
            }
            
            $status_color = Config::get('constants.status_color.' . $status);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view ='';
            if (per_hasModuleAccess('Client', 'Edit')) {
                $edit = ' <a href="' . route('client',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                
            }
            if (per_hasModuleAccess('Client', 'View')) {
                $view = '';//'<a href="' . route('client',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('view').'</a> ';
            }
            $status = Design::blade('status',$status,$status_color);
          //$headname=(!empty($PheadDetails[$data[$i]->client_head_id]))?$PheadDetails[$data[$i]->client_head_id]:'';
          
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->client_name,
                // $data[$i]->billing_name,
                $data[$i]->email,
                $data[$i]->mobile_number,
                $data[$i]->whatsapp_number,
                $data[$i]->salespersonname,
                $data[$i]->created_at,
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
    * Function : change status , add , edit and delete Client data
    * @param  array $request
    * @return  json $response
    *
    */
    public function PostAjaxData(Request $request) {
        $records = $data = $flag = $document_file = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->all();

        if (isset($post['customActionName'])) {
            $activeAdminId = Auth::guard('admin')->user()->id;

            $module_id = Config::get('constants.module_id.client');
            if ($post['customActionName'] == "Active" || $post['customActionName'] == 'Inactive'){
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
                        $log_text = "Client " . $id . " Status updated to ".$post['customActionName'];
                        $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $id, "no" => $id, "log_text" => $log_text, "req_data" => $post);
                         usr_addActivityLog($activity_arr);
                    }
                }else{
                    $log_text = "Client " . $post['id'] . " Status updated to ".$post['customActionName'];
                    $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $post['id'], "no" => $post['id'], "log_text" => $log_text, "req_data" => $post);
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
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }

                $image="";
                if($request->has('image')){
                    $image = $request->file('image');
                } 

                if($request->hasFile('document_file')){
                    $document_file = $request->file('document_file');
                }
                // dd($request->file('document_file'));
                $update = $this->updateRecord($post, $image, $document_file);
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
            } elseif ($post['customActionName'] == "DeleteClientDocument") {
                $deletefile = $this->deleteClientDocumentFile($post['id'],$post['document_file']);
                if ($deletefile['isError'] == 1) {
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = $deletefile['msg'];
                } else {
                    $result_arr[0]['isError'] = 0;
                    $result_arr[0]['msg'] = 'Document deleted successfully';
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
        $designation = $data = $language = $clients_language = $allmediumlist = $board_id_arr =  array();
        $designation=Designation::getAllActiveDesignation();
        $ClientType=ClientType::getClientDataList();
        $Section=Section::getSectionDataList();
        $SchoolType=SchoolType::getSchoolTypeDataList();
        $board_id=ClientBoard::getClientBoardData($id);
        $kyc_sataus_array = array('Pending'=>0,'Approved'=>1,'Disapproved'=>2);
        foreach ($board_id as $key => $value) {
          $board_id_arr[]= $value['board_id'];
        }
        $Board=Board::getListBoardData();
    
        $DisCat=UserDiscountCategory::getAllCategories();
        $salesUsers=Admin::getSalesUsersList();
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
        $listgrade=Grade::getAllActiveGradeData();
        $paymentlist=PaymentTerms::getAllPaymentTermsData();
        $state=State::getStateFromCountryCode($this->countryid);
        $alldes=[];
        foreach($designation as $d){
            $alldes[$d['id']]=$d['name'];
        }
        $image_extention = '';
        $checkImgArr ='';
        if(isset($mode) && $mode != ""){
            $image_extention = implode(', ',$this->img_ext_array); 
            if (isset($id) && $id != "") {
                $id = substr($id, 3, -3);
                
                $data = Clients::getClientsDataFromId($id);
                $data[0]['section_id'] = ClientSection::getClientSectionIdsByClientId($id);
                $data[0]['school_type_id'] = ClientSchool::getClientSchoolIdsByClientId($id);
                $data[0]['board_id'] = ClientBoard::getClientBoardIdsByClientId($id);
                $data[0]['medium_id'] = ClientMedium::getClientMediumIdsByClientId($id);

                $image = (isset($data[0]['image']) && $data[0]['image'] !='')?$data[0]['image'] : '';
                    if($image != ''){
                        $checkImgArr = checkImageExistWithSetting('',$this->destinationPath,'client','','_',$image,count($this->size_array));
                    }
                if($mode == 'edit'){
                    $address=ClientsAddress::getClientDataList($id);
                    $ClientDocuments=ClientDocument::getClientDocumentData($id);
                    foreach ($ClientDocuments['documentData'] as $document => $documentFileData) {
                        $ClientDocuments['documentData'][$document]['document_link'] = '';
                        if(!empty($documentFileData['file_name'])){
                            $doc_file_path = getOriginalFileUrl('client_documents',$documentFileData['file_name']);
                            $ClientDocuments['documentData'][$document]['document_link'] = $doc_file_path;
                        }
                    }


                    $talukae=[];
                    $districta=[];
                    $la=[];
                    $da=[];
                    $documentAcceptExt = implode(', ', $this->client_document_accept_ext_array);
                    foreach($address as $a){
                        $la[]=$a['taluka_id'];
                        $da[]=$a['district_id'];
                    }
                    if(!empty($la)){
                        $talukalista=Taluka::getAllActiveTaluka(implode(",",$la));
                        foreach($talukalista as $t1){
                            $talukae[$t1['id']]=$t1['taluka_name'];
                        }
                    }
                    if(!empty($da)){
                        $districtlista=Districts::getDistrictsDataFromId($da);
                        foreach($districtlista as $d1){
                            $districta[$d1['id']]=$d1['district_name'];
                        }
                    }
                    $contact=ClientContactPerson::getClientDataList($id);
                    
                    $logHistory=ClientStatusLog::getAllActiveClientStatusLogDataByClientID($id);
                    $district=Districts::getAllActiveDistricts(['state_id'=>$data[0]['state_id']]);
                    $mode = 'Update';
                    return view('admin.client.client_add')->with(['mode' =>$mode,'school_type'=>$ClientType,'userData'=>$data[0],'address'=>$address,'checkImgArr'=>$checkImgArr,'contact'=>$contact,'designation'=>$alldes,'discountcat'=>$DisCat,'salesUsers'=>$salesUsers,'district'=>$district,'taluka'=>[],'talukae'=>$talukae,'districta'=>$districta,'gradearray'=>$listgrade,'payment_list'=>$paymentlist,'LogHistory'=>$logHistory,'state'=>$state,'Section'=>$Section,'SchoolType'=>$SchoolType,'Board'=>$Board,'board_id_arr'=>$board_id_arr,'ClientDocuments'=>$ClientDocuments['documentData'],'countDocument'=>$ClientDocuments['countDocument'],'countVerifyDocument'=>$ClientDocuments['countVerifyDocument'],'client_document_ext_array'=>$this->client_document_ext_array,'client_document_ext'=>$documentAcceptExt,'kyc_sataus_array'=>$kyc_sataus_array]);
                }else if($mode == 'view'){
                    $mode = 'View';
                }
            }elseif($mode == 'add') {
                $mode = "Add";
                
                return view('admin.client.client_add')->with(['mode' =>'Add','SUS'=>$USalesStatus,'checkImgArr'=>$checkImgArr,'school_type'=>$ClientType,'designation'=>$alldes,'discountcat'=>$DisCat,'salesUsers'=>$salesUsers,'district'=>[],'gradearray'=>$listgrade,'payment_list'=>$paymentlist,'LogHistory'=>[],'state'=>$state,'Section'=>$Section,'SchoolType'=>$SchoolType,'Board'=>$Board,'board_id_arr'=>$board_id_arr,'kyc_sataus_array'=>$kyc_sataus_array]);
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
       // echo "<pre>"; print_r($data);exit();
        if($data['customActionName'] == "Add") {
            $rules = array(
                'client_name'=>'required',
                // 'billing_name'=>'required',
                'email'=>'required|email|max:100|unique:clients,email,NULL,id',
                'mobile_number'=>'required|regex:/^[0-9]*$/',
                'whatsapp_number'=>'required|regex:/^[0-9]*$/',
                'mobile_number'=>'required|regex:/^[0-9]*$/',
                'zip_code'=>'required|min:6|max:6',
                'username' => 'required|min:3|max:20|unique:clients,username,NULL,id', 
                'password' => 'required|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'',
            );
        } else {
            $rules = array(
                'client_name'=>'required',
                // 'billing_name'=>'required',
                'email' => 'required|email|max:100|unique:clients,email,' . $data['id'] . ',id',
                'mobile_number'=>'required|regex:/^[0-9]*$/',
                'whatsapp_number'=>'required|regex:/^[0-9]*$/',
                'zip_code'=>'required|min:6|max:6',
                'username' => 'required|min:3|max:20|unique:clients,username,'.$data['id'] .',id',
                'password' => 'nullable|required_if:changePasswordChk,1|min:'.config('constants.PASSWORD_MIN').'|max:'.config('constants.PASSWORD_MAX').'|regex:'.config('constants.PASSWORD_FORMAT').'|required_with:password_confirmation|confirmed',
                'password_confirmation' => 'nullable|required_if:changePasswordChk,1',
            );
        }
        $messages = array(
            'client_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Client Name'),
            // 'billing_name.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Billing Name'),
            'whatsapp_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Whatsapp'),
            'whatsapp_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Whatsapp'),
            'mobile_number.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Registered Mobile'),
            'mobile_number.regex' => sprintf(Config::get('messages.validation_msg.regex'), 'Registered Mobile'),
            'zip_code.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Zip Code'),
            'zip_code.min' => sprintf(Config::get('messages.validation_msg.minmobilelength'),'6', 'Zip Code'),
            'zip_code.max' => sprintf(Config::get('messages.validation_msg.maxmobilelength'),'6', 'Zip Code'),
           /* 'principal_contact_no.min' => sprintf(Config::get('messages.validation_msg.minlength'),'10', 'Principal Contact Number '),
            'principal_contact_no.max' => sprintf(Config::get('messages.validation_msg.maxlength'),'10', 'Principal Contact Number '),
            'account_contact_no.min' => sprintf(Config::get('messages.validation_msg.minlength'),'10', 'Account Contact Number '),
            'account_contact_no.max' => sprintf(Config::get('messages.validation_msg.maxlength'),'10', 'Account Contact Number '),*/
            'email.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Registered Email'),
            'email.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Registered Email'),
            'email.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Registered Email'),
            'gst_no.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'GST No.'),
            'pan_no.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'PAN No.'),
            'username.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Username'),
            'username.min' => sprintf(Config::get('messages.validation_msg.minlength'), '5', 'Username'),
            'username.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '20', 'Username'),
            'username.unique' => sprintf(Config::get('messages.validation_msg.unique_field'), 'Username'),
            'password.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Password'),
            'password.min' => sprintf(Config::get('messages.validation_msg.minlength'), config('constants.PASSWORD_MIN'), 'Password'),
            'password.max' => sprintf(Config::get('messages.validation_msg.maxlength'), config('constants.PASSWORD_MAX'), 'Password'),
            'password.regex' => sprintf(Config::get('messages.validation_msg.regex_password'), 'Password'),
            
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

        $getdistrictdata=Districts::getDistrictsDataFromId($data['district']);
        $insert_array = array( 
            'verified_date'=> (!empty($data['verified_date'])) ? date_convertDateInDBFormat($data['verified_date']) : '',
            'client_code'=>$data['client_code'], 
            'client_name'=>$data['client_name'], 
            // 'billing_name'=>$data['billing_name'], 
            'mobile_number'=>$data['mobile_number'], 
            'whatsapp_number'=>$data['whatsapp_number'], 
            'email'=>$data['email'], 
            'discount_category'=>$data['discount_category'],
            'is_dealer'=>isset($data['is_dealer']) ? $data['is_dealer'] : '', 
            'sales_user_id'=>$data['sales_user_id'], 
            // 'client_address_id'=>$data['client_address_id'], 
            'client_type'=>$data['client_type'], 
            'principal_contact_name'=>$data['principal_contact_name'], 
            'principal_contact_no'=>$data['principal_contact_no'], 
            'account_contact_name'=>$data['account_contact_name'], 
            'account_contact_no'=>$data['account_contact_no'], 
            'latitude'=>$data['latitude'], 
            'longitude'=>$data['longitude'], 
            'tally_client_name'=>$data['tally_client_name'], 
            // 'dob'=>$data['dob'], 
            'gst_no'=>$data['gst_no'], 
            'pan_no'=>$data['pan_no'], 
            'adhar_no'=>$data['adhar_no'], 
            // 'school_type_id'=>$data['school_type_id'], 
            'zip_code'=>$data['zip_code'], 
            // 'area'=>$data['area'], 
            'image'=>$flag, 
            'taluka_id'=>$data['taluka'],
            'district_id'=>$data['district'],
            'zone_id'=>$getdistrictdata[0]['zone_id'],
            'country_id'=>$getdistrictdata[0]['country_id'],
            'state_id'=>$data['state'],
            'grade_id'=>$data['grade_id'], 
            'cash_discount1' => isset($data['cash_discount1']) ? $data['cash_discount1'] : '', 
            'cash_discount2' => isset($data['cash_discount2']) ? $data['cash_discount2'] : '',
            'cash_discount1_amt' => isset($data['cash_discount1_amt']) ? $data['cash_discount1_amt'] : '', 
            'cash_discount2_amt' => isset($data['cash_discount2_amt']) ? $data['cash_discount2_amt'] : '', 
            'order_approval_limit' => isset($data['order_approval_limit']) ? $data['order_approval_limit'] : '',
            'total_credit_limit' => isset($data['total_credit_limit']) ? $data['total_credit_limit'] : '',
            'payment_term_id' => isset($data['payment_term_id']) ? $data['payment_term_id'] : '',
            'created_at' => date_getSystemDateTime(), 
            'created_by' => Auth::guard('admin')->user()->id, 
            'status' => (isset($data['status']) ? $data['status'] : Clients::INACTIVE),
            'kyc_status' => $data['kyc_status'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        );

        if($data['kyc_status'])
        {
            $insert_array['kyc_approved_date'] = date_getSystemDateTime();
        }

         $insert = Clients::addClients($insert_array);
        if (!empty($insert)) {
            $result['isError'] = 0;
            $ltype=ClientType::getClientDataList();
            $codetype=[];
            foreach($ltype as $d1){
                $codetype[$d1['id']]=strtoupper(substr($d1['name'], 0, 2));
            }
            Clients::updateClientCode($insert['id'],$codetype[$data['client_type']]);
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_ADDED');
            $insertedId = (isset($insert['id']) ? $insert['id'] :'');
            if(!empty($insertedId)){

            $section_id_array= $data['section_id'] ?? array();
            if(!empty($section_id_array)){
                foreach ($section_id_array as $key => $value) {
                $section_array=['client_id'=>$insertedId,'section_id'=>$value,'status'=>ClientSection::ACTIVE];
                ClientSection::addClientSection($section_array);
                }
            }
            $school_type_id_array= $data['school_type_id']  ?? array();
            if(!empty($school_type_id_array)){
                foreach ($school_type_id_array as $key => $value) {
                    $school_type_array=['client_id'=>$insertedId,'school_type_id'=>$value,'status'=>ClientSchool::ACTIVE];
                    ClientSchool::addClientSchoolType($school_type_array);
                }
            }
            $board_id_array= $data['board_id']  ?? array();
            if(!empty($board_id_array)){
                foreach ($board_id_array as $key => $value) {
                $board_array=['client_id'=>$insertedId,'board_id'=>$value,'status'=>ClientBoard::ACTIVE];
                ClientBoard::addClientBoard($board_array);
                }
            }
            $medium_id_array= $data['medium_id']  ?? array();
            if(!empty($medium_id_array)){
                foreach ($medium_id_array as $key => $value) {
                $medium_array=['client_id'=>$insertedId,'medium_id'=>$value,'status'=>ClientMedium::ACTIVE];
                ClientMedium::addClientMedium($medium_array);
                }
            }     

            if((!empty($data['address_title']) || !empty($data['contact_full_name']))){
                if(!empty($data['address_title'])){
                    // insert Address
                    foreach($data['address_title'] as $key=>$t){ 
                        $is_approved = $data['is_approved'][$key];
                        $is_locked = 0;
                        $approved_date = "";
                         if($is_approved == "true"){
                            $is_approve=1;
                            $is_locked = 1;
                            $approved_date = date_getSystemDateTime();
                        } 
                        $addContactDetails=['client_id'=>$insertedId,'title'=>$data['address_title'][$key],'address1'=>$data['address_address1'][$key],'address2'=>$data['address_address2'][$key],'mobile_number'=>$data['address_mobile_number'][$key],'email'=>$data['address_email'][$key],'district_id'=>$data['address_district1'][$key],'taluka_id'=>$data['address_taluka1'][$key],'zip_code'=>$data['address_zip_code'][$key],'use_for_billing'=>$data['address_used_for_billing'][$key],'use_for_shipping'=>$data['address_used_for_shipping'][$key],'is_default_shipping'=>$data['address_is_default_shipping'][$key],'is_default_billing'=>$data['address_is_default_billing'][$key],'is_approved'=>$is_approve,'is_locked'=>$is_locked,'approved_date'=>$approved_date,'status'=>$data['address_status'][$key],'country_id'=>1,'state_id'=>67,'created_by'=>Auth::guard('admin')->user()->id,'created_at'=>date_getSystemDateTime()];
                        // echo "<pre>"; print_r($addContactDetails);exit();
                          ClientsAddress::addClientAddress($addContactDetails);
                      }
                }
                // insert Contact
                if(!empty($data['contact_full_name'])){
                    foreach($data['contact_full_name'] as $key=>$n){
                        if(1==1){
                            $insertArray=['client_id'=>$insertedId, 'full_name'=>$data['contact_full_name'][$key], 'mobile_number'=>$data['contact_mobile_number'][$key], 'whatsapp_number'=>$data['contact_whatsapp_number'][$key], 'designation_id'=>$data['contact_designation_id'][$key], 'department'=>$data['contact_department'][$key], 'dob'=>date('Y-m-d'), 'created_by'=>Auth::guard('admin')->user()->id, 'is_default'=>$data['contact_is_default'][$key], 'created_at'=>date_getSystemDateTime(), 'status'=>$data['contact_status'][$key]];
                        ClientContactPerson::addClientContactPerson($insertArray);
                        }
                    }
                }
            }
            }
            ## Start Add Activity
            $reference_id = (isset($insertedId) ? $insertedId : "");
            $module_id = Config::get('constants.module_id.client');
            $log_text = "Client " . $data['client_name'] . " added";
            $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => $data['client_code'], "log_text" => $log_text, "req_data" => $data);
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
    private function updateRecord($data = array(), $files = array(), $documentsFiles = array()){
        ##Variable Declaration
        $cou_lang_array = array();
        $id = (isset($data['id']) ? $data['id'] : "");
        $flag = NULL;
        $docfileName = NULL;
        $docfilelog = NULL;
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

        ## Section Upload Client Document Files
        foreach ($data['document'] as $docid => $olfFile) {
            $docfileName[$docid] = $olfFile['old_doc_file'];
        }

        if(!empty($documentsFiles))
        {
             foreach($documentsFiles as $docid => $docFile){
                $docfileOriname = $docFile->getClientOriginalName();
                $docfileName[$docid]  = $id.'_'.$docid.'_'.time().'_'.gen_remove_spacial_char($docfileOriname);
                $docfileExt        = $docFile->getClientOriginalExtension();
                $docfileSize       = $docFile->getSize();
                $docfileRealPath   = $docFile->getRealPath();
                $docfilelog[] = $docfileName[$docid];
                if (in_array($docfileExt, $this->client_document_ext_array)){
                    if ($docfileSize > $this->img_max_size || $docfileSize == 0){
                        $result['isError'] = 1;
                        $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_SIZE');
                        return $result;
                        exit; 
                    }else{
                        ## Store image in multiple size in folder
                        if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                            storeImageinAWS($docfileRealPath, $this->AWSdestinationPath, $docfileName[$docid], $this->client_document_size);
                        }else{
                            storeDocumentInFolder($docFile,$this->client_documents_path,$docfileName[$docid]);
                        }

                        ## Remove old image from folder
                        if (isset($data['document'][$docid]["old_doc_file"]) && $data['document'][$docid]["old_doc_file"] != "" && $data['document'][$docid]["old_doc_file"] != NULL) {
                            ## Delete image from s3
                            if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                                deleteImageFromAWS($data['document'][$docid]["old_doc_file"], $this->AWSdestinationPath, $this->client_document_size);
                            }else{ //## Delete image from local storage
                                deleteDocFromFolder($data['document'][$docid]["old_doc_file"], $this->client_documents_path);
                            }                        
                        }                       
                    }
                } else {
                    $result['isError'] = 1;
                    $result['msg'] = Config::get('messages.msg.MSG_IMG_INVALID_EXTENSION');
                    return $result;
                    exit;
                }
            }
        }
        ## Section Upload Client Document Files End

        $olddata = Clients::getClientsDataFromId($id);
        if($olddata[0]['status']!=$data['status']){
            //'old_status', 'new_status','created_at', 'created_by'
            $arraystatus=[1=>'Active',2=>'Verified',3=>'Inactive'];
            (ClientStatusLog::addClientStatusLog(['client_id'=>$id,'old_status'=>$arraystatus[$olddata[0]['status']],'new_status'=>$arraystatus[$data['status']],'created_at'=>date_getSystemDateTime(),'created_by'=>Auth::guard('admin')->user()->id]));
        }

        $getdistrictdata=Districts::getDistrictsDataFromId($data['district']);
        $update_array = array( 
            'client_name'=>$data['client_name'], 
            // 'billing_name'=>$data['billing_name'], 
            'email'=>$data['email'],
            'tally_client_name'=>$data['tally_client_name'],
            'mobile_number'=>$data['mobile_number'], 
            'whatsapp_number'=>$data['whatsapp_number'],  
            'discount_category'=>$data['discount_category'],
            'sales_user_id'=>$data['sales_user_id'],
            'client_type'=>$data['client_type'],
            'principal_contact_name'=>$data['principal_contact_name'], 
            'principal_contact_no'=>$data['principal_contact_no'], 
            'account_contact_name'=>$data['account_contact_name'], 
            'account_contact_no'=>$data['account_contact_no'], 
            'latitude'=>$data['latitude'], 
            'longitude'=>$data['longitude'],  
            'adhar_no'=>$data['adhar_no'], 
            'gst_no'=>$data['gst_no'], 
            'pan_no'=>$data['pan_no'], 
            'zip_code'=>$data['zip_code'], 
            'image'=>$flag, 
            'grade_id'=>$data['grade_id'], 
            'cash_discount1' => isset($data['cash_discount1']) ? $data['cash_discount1'] : '', 
            'cash_discount2' => isset($data['cash_discount2']) ? $data['cash_discount2'] : '',
            'cash_discount1_amt' => isset($data['cash_discount1_amt']) ? $data['cash_discount1_amt'] : '', 
            'cash_discount2_amt' => isset($data['cash_discount2_amt']) ? $data['cash_discount2_amt'] : '',
            'payment_term_id'=>$data['payment_term_id'], 
            'order_approval_limit'=>$data['order_approval_limit'],
            'total_credit_limit'=>$data['total_credit_limit'],
            'taluka_id'=>$data['taluka'],
            'district_id'=>$data['district'],
            'zone_id'=>$getdistrictdata[0]['zone_id'],
            'country_id'=>$getdistrictdata[0]['country_id'],
            'state_id'=>$getdistrictdata[0]['state_id'],
            'grade_id'=>$data['grade_id'],
            'updated_at'=>date_getSystemDateTime(),
            'verified_date'=> (!empty($data['verified_date'])) ? date_convertDateInDBFormat($data['verified_date']) : '',
            'status' =>$data['status'],
            'kyc_status' => $data['kyc_status'],
            'username' => (isset($data['username']) ? $data['username'] : ""),
        );

        if(isset($data['changePasswordChk']) && $data['changePasswordChk'] =='1'){
            if(isset($data['password']) && $data['password'] != '')
            { 
                $update_array['password'] = (isset($data['password']) ? Hash::make($data['password']) : "");    
            }
        }

        if($data['kyc_status'])
        {
            $update_array['kyc_approved_date'] = date_getSystemDateTime();
        }

        $update = Clients::updateClients($id,$update_array);
        if(!empty($id)){

            ## First delete old section data from db
            $delete_section = ClientSection::deleteClientSectionsData($id);

            ## Then insert the new section data into the db
            $section_id_array = (isset($data['section_id']) && $data['client_type'] == 1) ? $data['section_id'] : array();
            if(!empty($section_id_array) && $delete_section) {
                foreach ($section_id_array as $value) {
                    $section_array = ['client_id'=>$id,'section_id'=>$value,'status'=>ClientSection::ACTIVE];
                    ClientSection::addClientSection($section_array);
                }
            }

            ## First delete old school type data from db
            $delete_school_type = ClientSchool::deleteClientSchoolTypesData($id);

            ## Then insert the new school type data into the db
            $school_type_id_array = (isset($data['school_type_id']) && $data['client_type'] == 1) ? $data['school_type_id'] : array();
            if(!empty($school_type_id_array) && $delete_school_type) {
                foreach ($school_type_id_array as $value) {
                    $school_type_array = ['client_id'=>$id,'school_type_id'=>$value,'status'=>ClientSchool::ACTIVE];
                    ClientSchool::addClientSchoolType($school_type_array);
                }
            }

            ## First delete old board data from db
            $delete_school_type = ClientBoard::deleteClientBoardsData($id);

            ## Then insert the new board data into the db
            $board_id_array = (isset($data['board_id']) && $data['client_type'] == 1) ? $data['board_id'] : array();
            if(!empty($board_id_array)){
                foreach ($board_id_array as $value) {
                    $board_array = ['client_id'=>$id,'board_id'=>$value,'status'=>ClientBoard::ACTIVE];
                    ClientBoard::addClientBoard($board_array);
                }
            }

            ## First delete old medium data from db
            $delete_school_type = ClientMedium::deleteClientMediumsData($id);

            ## Then insert the new medium data into the db
            $medium_id_array = (isset($data['medium_id']) && $data['client_type'] == 1) ? $data['medium_id'] : array();
            if(!empty($medium_id_array)){
                foreach ($medium_id_array as $value) {
                    $medium_array = ['client_id'=>$id,'medium_id'=>$value,'status'=>ClientMedium::ACTIVE];
                    ClientMedium::addClientMedium($medium_array);
                }
            }

            $address1=ClientsAddress::getClientDataListofids($id);
            $contact=ClientContactPerson::getClientDataListofids($id);
            if(!empty($data['deleted_address'])){
                (ClientsAddress::deleteClientAddressData($data['deleted_address']));
            }
            if(!empty($data['deleted_contact'])){
                ClientContactPerson::deleteClientContactPersonData($data['deleted_contact']);
            }
            $address=[];
            foreach($address1 as $val){
                $address[]=$val['id'];
            }
            $conarray=[];
            foreach($contact as $val){
                $conarray[]=$val['id'];
            } 

            if(!empty($data['contact_editid'])){
                foreach($data['contact_editid'] as $key=>$idt){
                    /*if($data['contact_is_default'][$key]==1){
                        ClientContactPerson::updateClientPersonDefultSet(['client_id'=>$id],['is_default'=>0]);
                    }*/
                    if(!empty($idt) && in_array($idt,$conarray)){
                        $arraycontact[]=$idt;
                        $conarray=array_diff($conarray,[$idt]);
                        $insertArray=['full_name'=>$data['contact_full_name'][$key], 'mobile_number'=>$data['contact_mobile_number'][$key], 'whatsapp_number'=>$data['contact_whatsapp_number'][$key], 'designation_id'=>$data['contact_designation_id'][$key], 'department'=>$data['contact_department'][$key],'is_default'=>$data['contact_is_default'][$key], 'updated_at'=>date_getSystemDateTime(), 'status'=>$data['contact_status'][$key]];
                        $multiplecontactadd[]=$insertArray;
                        ClientContactPerson::updateClientContactPerson($idt,$insertArray);
                    }else{
                        $insertArray=['client_id'=>$id, 'full_name'=>$data['contact_full_name'][$key], 'mobile_number'=>$data['contact_mobile_number'][$key], 'whatsapp_number'=>$data['contact_whatsapp_number'][$key], 'designation_id'=>$data['contact_designation_id'][$key], 'department'=>$data['contact_department'][$key], 'dob'=>$data['contact_dob'][$key], 'created_by'=>Auth::guard('admin')->user()->id, 'is_default'=>$data['contact_is_default'][$key], 'created_at'=>date_getSystemDateTime(), 'status'=>$data['contact_status'][$key]];
                        $multiplecontactadd[]=$insertArray;
                        ClientContactPerson::addClientContactPerson($insertArray);
                    }
                }
            }
            if(!empty($address)){
               // ClientContactPerson::deleteClientContactPersonData($address);
            }
            $arrayaddress=[];
            if(!empty($data['address_editid'])){
                foreach($data['address_editid'] as $key=>$ict){
                    if(!empty($ict) && in_array($ict,$address)){
                        $address=array_diff($address,[$ict]);
                        $addressupdated=['title'=>$data['address_title'][$key],'address1'=>$data['address_address1'][$key],'address2'=>$data['address_address2'][$key],'mobile_number'=>$data['address_mobile_number'][$key],'email'=>$data['address_email'][$key],'district_id'=>$data['address_district1'][$key],'taluka_id'=>$data['address_taluka1'][$key],'zip_code'=>$data['address_zip_code'][$key],'use_for_billing'=>$data['address_used_for_billing'][$key],'use_for_shipping'=>$data['address_used_for_shipping'][$key],'is_default_shipping'=>$data['address_is_default_shipping'][$key],'is_default_billing'=>$data['address_is_default_billing'][$key],'status'=>$data['address_status'][$key],'updated_at'=>date_getSystemDateTime()];
                        ClientsAddress::updateClientAddress($ict,$addressupdated);
                    }else{
                        $addContactDetails=['client_id'=>$id,'title'=>$data['address_title'][$key],'address1'=>$data['address_address1'][$key],'address2'=>$data['address_address2'][$key],'mobile_number'=>$data['address_mobile_number'][$key],'email'=>$data['address_email'][$key],'district_id'=>$data['address_district1'][$key],'taluka_id'=>$data['address_taluka1'][$key],'zip_code'=>$data['address_zip_code'][$key],'use_for_billing'=>$data['address_used_for_billing'][$key],'use_for_shipping'=>$data['address_used_for_shipping'][$key],'is_default_shipping'=>$data['address_is_default_shipping'][$key],'is_default_billing'=>$data['address_is_default_billing'][$key],'status'=>$data['address_status'][$key],'country_id'=>1,'state_id'=>67,'created_by'=>Auth::guard('admin')->user()->id,'created_at'=>date_getSystemDateTime()];
                        $multipleaddress[]=$addContactDetails;
                        ClientsAddress::addClientAddress($addContactDetails);
                    }
                }
            }     
          //  var_dump($address1);
            if(!empty($conarray)){
               // ClientsAddress::deleteClientAddressData($conarray);
            }

            ## Section
            /*if(!empty($data['section_id'])){
                foreach($data['section_id'] as $section_id){

                    
                }
            }*/

            ## Section Add Client Document Start
            $documents = $data['document'];
            foreach ($documents as $documentID => $documentValue) {
                if(!empty($documentValue['notes']) || !empty($docfileName[$documentID]))
                {
                    $docPost['client_id'] = $id;
                    $docPost['document_id'] = $documentID;
                    $docPost['file_name'] = isset($docfileName[$documentID]) ? $docfileName[$documentID] : '';
                    $docPost['notes'] = !empty($documentValue['notes']) ? $documentValue['notes'] : '';
                    $docPost['uploaded_by'] = Auth::guard('admin')->user()->id;
                    $docPost['upload_date'] = date_getSystemDateTime();
                    $docPost['is_verified'] = isset($documentValue['is_verified']) ? $documentValue['is_verified'] : 0;
                    if(isset($documentValue['is_verified']))
                    {
                        $docPost['verified_by'] = Auth::guard('admin')->user()->id;
                        $docPost['is_verified'] = $documentValue['is_verified'];
                        $docPost['verified_date'] = date_getSystemDateTime();
                    } 

                    ClientDocument::addClientDocument($docPost);
                }
            }

            ## Section Add Client Document End

        }
        if (isset($update)) {
            $result['isError'] = 0;
            $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');           
            ## Start Add Activity
            $reference_id = (isset($id) ? $id : "");
            $module_id = Config::get('constants.module_id.clients');
            $log_text = "Client " . $data['client_name'] . " added";
            if(!empty($docfilelog))
            {
                $log_text .= " and Client Document uploaded";
                $data['uploaded_files'] = implode(',',$docfilelog);
            }

            $activeAdminId   = Auth::guard('admin')->user()->id;
            $activity_arr = array("admin_id" =>Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" =>$id, "log_text" => $log_text, "req_data" => $data);
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
                $status = 3;
            }
            $update = Clients::updateClientsById($id, $status);
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
        if(!empty($id)){
            # Delete records from Client
            $delete_clients = Clients::deleteClientsData($id);
        }
        if (!empty($id) && 1==2) {
            $clients = Clients::getClientDataFromId($id);
            if (!empty($clients)) {
                $clientsIdArr = $clientsCodeArr = array();
                foreach ($clients as $rowLanguage) {
                    $clients_image = (isset($rowLanguage['flag'])?$rowLanguage['flag']:"");
                    if ($clients_image != "") {
                        ## Delete image from s3
                        if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                            deleteImageFromAWS($clients_image, $this->AWSdestinationPath, $this->size_array);
                        }else{ //## Delete image from local storage
                            deleteImageFromFolder($clients_image, $this->destinationPath, $this->size_array);
                        }
                    }
                    $clientsIdArr[] = (isset($rowLanguage['id'])?$rowLanguage['id']:"");
                }
                if(!empty($clientsIdArr)){
                    # Delete records from Client
                    $delete_clients = Clients::deleteClientsData($clientsIdArr);
                }
            }   
        }
        if (isset($delete_clients)) {
            ##Delete contry realted state by Client_id
            if(!empty($clientsIdArr)){
                $delete_state = State::deleteStatesByClientId($clientsIdArr);
              
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
                'image' => '',
            );
            $deleteimage = Clients::updateClients($id, $update_array);
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
    * Function : Ajax : Get Client Address list based on client id
    * @param  array $request
    * @return  json $response
    *
    */
    public function clientAddressList(Request $request)
    {
        $data = $request->all();
        if(!empty($data['client_id'])){
            $client_id = $data['client_id'];
            //$address_flag = $data['address_flag'];
    
            $addressData = array();
    
            if($client_id != '') {
                $addressData = Clients::getClientAddress($client_id); 
            }
          //  $CDetails=Clients::getClientsDataFromId($client_id);
            //return response()->json(['address'=>$addressData,'client'=>$CDetails[0]]);
            return response()->json($addressData);
        }
    }

    /**
    *
    * Function : Ajax : Get Client Address Detail based on client address id
    * @param  array $request
    * @return  json $response
    *
    */
    public function clientAddressDetail(Request $request)
    {
        $data = $request->all();
        $client_address_id = $data['client_address_id'];
        $addressData = array();
        $addressData[0]='';
        if($client_address_id != '') {
            $addressData = Clients::getClientAddressDetail($client_address_id); 
        }
        //return response()->json($addressData);         
        /**
         * 
         */
        $a=$addressData[0];
        $returnData=['billingname'=>$a['title'],'address'=>$a['address1'].', '. $a['address2'].'<br>' .$a['state_name'].' ,
        '. $a['district_name'].','.$a['taluka_name'].','.$a['zip_code']];
        return response()->json($returnData);
    }

    /**
    *
    * Function : Ajax : Get Client Contacts list based on client id
    * @param  array $request
    * @return  json $response
    *
    */
    public function clientContactsList(Request $request)
    {
        $data = $request->all();
        $client_id = $data['client_id'];

        $contactsList = array();

        if($client_id != '') {

            $contactsList = Clients::getClientContacts($client_id); 
        }
        return response()->json($contactsList);
    }


     /**
    * Delete Client Document Uploaded File
    */
    private function deleteClientDocumentFile($id,$file){
        $result = array();
        if($id!="" && $file!=""){
            ## Delete image from s3
            if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
                deleteImageFromAWS($file, $this->AWSdestinationPath, $this->size_array);
            }else{ //## Delete document file from local storage
                deleteDocFromFolder($file, $this->client_documents_path);
            }
            $update_array = array('file_name' => '', 'uploaded_by' => Auth::guard('admin')->user()->id, 'upload_date' => date_getSystemDateTime());
            $deletefile = ClientDocument::updateClientDocument($id, $update_array);
        }
        if (isset($deletefile)) { 
            $result['isError'] = 0;
            $result['msg'] = "";          
            ## Start Add Activity
            $data = array('id'=>$id,'document_file'=>$file);
            $reference_id = $id;
            $module_id = Config::get('constants.module_id.clients');
            $log_text = "Client Document " . $file . " deleted";
            $activeAdminId   = Auth::guard('admin')->user()->id;
            $activity_arr = array("admin_id" =>Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" =>$id, "log_text" => $log_text, "req_data" => $data);
            usr_addActivityLog($activity_arr);
            ## End Add Activity            
        }else{
            $result['isError'] = 1;
            $result['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result;
    }

    // public function downloadFile($file)
    // {
    //     return response()->download(public_path($this->client_documents_path.'1_'.$file));
    // }
}
