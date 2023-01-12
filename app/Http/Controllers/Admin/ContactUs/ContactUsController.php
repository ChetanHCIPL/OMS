<?php

namespace App\Http\Controllers\Admin\ContactUs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Support\Facades\Response;
use Config;
use App;
use DB;
use App\GlobalClass\Design;
use App\Models\ContactUs;
use App\Models\Admin;
use App\Models\SendContactEmail;
use App\Models\EmailTemplate;
use Mail;
use Validator;

class ContactUsController extends AbstractController
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
        $this->contact_us_question_type = config('constants.contact_us_question_type');
    }


    /**
     * Show the All Contact Us records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $member_data = Admin::getActiveMember();
        $dateFormate = config::get('constants.DATETIME_PICKER_FORMAT');
        return view('admin.contact_us.contact_us_list')->with(['member_data'=>$member_data,'dateFormate'=>$dateFormate]);
    }

    /**
     * Get Contact Us data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {

        $records = $data = $member_id_array =  $member_data_arr = array() ;
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
              
        $tot_records_data = ContactUs::getContactUsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr);
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
                    $sort = "m.first_name";
                    break;
                case "3":
                    $sort ="message";
                    break;
                case "4":
                    $sort ="created_at";
                    break;
                default:
                    $sort = "created_at";
            }
        } else {
            $sort = "created_at";
        }

        $data = ContactUs::getContactUsData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr);
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');

            $edit = '---';
            $view ='';
            $type = '';

            if (per_hasModuleAccess('ContactUs', 'View')) {
                $view = '<a href="' . route('contact-us',['mode' => 'view','id' => $encoded_id]) . '" title="View">'.Design::button('reply').'</a> ';
            }
            if($data[$i]->status == 1){
                $status = "Open";    
            }else if($data[$i]->status == 2){
                $status = "Closed";
            }else{
                $status = "Cancelled";
            }
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);
            
            $date = date_getDateTimeAll($data[$i]->created_at);
            if(!empty($data[$i]->question_type) && array_key_exists($data[$i]->question_type,$this->contact_us_question_type))
                {
                    $type =  $this->contact_us_question_type[$data[$i]->question_type];
                }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->member_name,
                $type,
                nl2br($data[$i]->message),
                $date,
                $status,
                $view
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

    /**
    *
    * Function : delete contact us data
    * @param  array $request
    * @return  json $response
    *
    */
    public function PostAjaxData(Request $request) {
        $records = $data = $flag = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = Input::get();
        // echo "<pre>"; print_r($post);exit();

        if (isset($post['customActionName'])) {
    
            if ($post['customActionName'] == "Delete") {
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
            if ($post['customActionName'] == "SentEmailtoinquiry") {
                $validator = $this->validateFields($post);
                if ($validator->fails()) {
                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $validate_msg;
                        return Response::json($result_arr);
                    }
                }
                $email_inquiry = $this->sentEmailToInquiry($post);
                if ($email_inquiry == 1) {
                    $records["customActionStatus"] = "OK";
                    $records["customActionMessage"] = Config::get('messages.msg.MSG_EMAIL_SENT');
                    // $records["customActionMessage"] = sprintf(Config::get('messages.msg.MSG_RECORD_DELETED'), count($post['id']));
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
        

        $data =  $getbatchdata = $course_plan = $course_plan_wise_data = $where_arr = array();
        $dateFormate = config::get('constants.DATETIME_PICKER_FORMAT');
        $timeFormate = config::get('constants.CUSTOM_TIME_PICKER_FORMAT');
        if(isset($mode) && $mode != ""){
            
            if (isset($id) && $id != "") {
                $admission_type = '---';
                $type = '---';
                $id = substr($id, 3, -3);
                
                ## Get all Contact Us data ##
                // echo $id;exit();

                $data = ContactUs::getContactUsDataFromId($id);
                // echo "<pre>";print_r($data);exit;
                if(!empty($data)){
                    if(!empty($data[0]['question_type']) && array_key_exists($data[0]['question_type'],$this->contact_us_question_type))
                    {
                        $type =  $this->contact_us_question_type[$data[0]['question_type']];
                    }

                    $email_log_data = SendContactEmail::getPreviousEmailDetails($id);
                    $cnt_email_data = count($email_log_data);
                    
                    if(!empty($email_log_data) && $cnt_email_data > 1){
                        $subject = "Re : ".$email_log_data[0]['email_subject'];

                    }else{
                        $subject = "Re : ".$type;
                    }
                    $message = " \n";
                    $message .= Config::get('settings.SYSTEM_EMAIL_FOOTER');
                    $message .= "\n--------------------------------------------------------------------";
                    $message .= "\n";
                    $message .= isset($email_log_data[0]['email_message']) && !empty($email_log_data[0]['email_message']) ? $email_log_data[0]['email_message']: $data[0]['message'];
                    $email_from = env('MAIL_FROM_ADDRESS');
                    
                    if($mode == 'view'){
                        $mode = 'View';
                        return view('admin.contact_us.contact_us_view')->with(['mode' => $mode, 'id' => $id, 'data' => $data,'type'=> $type,'subject'=> $subject,'message'=> $message,'email_from'=>$email_from]);
                    }
                }else{
                    $member_data = Member::getActiveMember();
                    $dateFormate = config::get('constants.DATETIME_PICKER_FORMAT');
                    return view('admin.contact_us.contact_us_list')->with(['member_data'=>$member_data,'dateFormate'=>$dateFormate]);
                }
            } 
        }
        abort(404);
    }

    /**
    *
    * Function : Delete Records
    * @param  int $id
    * @return boolean 
    */  
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $contactUs = ContactUs::getContactUsDataFromId($id);
            if (!empty($contactUs)) {
                $delete_contact_us = ContactUs::deleteContactUs($contactUs);
            }   
        }
        if (isset($delete_contact_us)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
    *
    * Function : SendEmail Records
    * @param  int $id
    * @return boolean 
    */  
    private function sentEmailToInquiry($data = array()) {
         $insert_array = array(
            'contact_id' => (isset($data['contact_id']) ? $data['contact_id'] : ""),
            'member_id' => (isset($data['member_id']) ? $data['member_id'] : ""),
            'email_to' => (isset($data['email_to']) ? $data['email_to'] : ""),
            'email_from' => (isset($data['email_from']) ? $data['email_from'] : ""),
            'email_subject' => (isset($data['email_subject']) ? $data['email_subject'] : ""),
            'email_message' => (isset($data['email_message']) ? $data['email_message'] : ""),
            'status' => (isset($data['email_status']) ? $data['email_status'] : SendContactEmail::OPENED),
            'created_at'=> date_getUnixSystemTime(),
        );
        $insert = SendContactEmail::addContactEmailLog($insert_array);

        if(isset($insert)) {
            $sentMail = Mail::send([],[], function ($message) use($data) {
                $message->from(env('MAIL_FROM_ADDRESS'), Config::get('settings.SITE_TITLE.default'));
                $message->to((isset($data['email_to']) ? $data['email_to'] : ""));   
                $message->subject((isset($data['email_subject']) ? $data['email_subject'] : ""));
                $message->setBody((isset($data['email_message']) ? $data['email_message'] : ""), 'text/plain');
            });
            $update_array = array("status"=>$insert_array['status']);//Success
            ContactUs::updateContactUs($data['contact_id'], $update_array);
            return 1;
        } else {
            return 0;
        } 

    }

    /**
     * Get Contact Us data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function contactMailLogs(Request $request) {

        $records = $data = $member_id_array =  $member_data_arr = array() ;
        $records["data"] = array();
        $pattern = $err_msg = NULL;

        ## Request Parameters
        $post = Input::get();
        $search_arr = array();

        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }

        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
        $search_arr['contact_id'] = (isset($post['contact_id']) ? ($post['contact_id']) : "0");
        // echo "<pre>"; print_r($search_arr);exit();
        $tot_records_data = SendContactEmail::getcontactMailLogsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr);
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
                    $sort = "email_message";
                    break;
                case "3":
                    $sort ="m.first_name";
                    break;
                case "4":
                    $sort ="created_at";
                    break;
                default:
                    $sort = "created_at";
            }
        } else {
            $sort = "created_at";
        }

        $data = SendContactEmail::getcontactMailLogsData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr);
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');

            $edit = '---';
            $type = '';

            if($data[$i]->status == 1){
                $status = "Open";    
            }else if($data[$i]->status == 2){
                $status = "Closed";
            }else{
                $status = "Cancelled";
            }
            $status_color = Config::get('constants.status_color.' . $status);
            $status = Design::blade('status',$status,$status_color);
            $admin_name = ((isset(Auth::guard('admin')->user()->first_name) && isset(Auth::guard('admin')->user()->last_name))?Auth::guard('admin')->user()->first_name.' '.Auth::guard('admin')->user()->last_name:"");
            $date = date_getDateTimeAllFromStr($data[$i]->created_at);
            if(!empty($data[$i]->question_type) && array_key_exists($data[$i]->question_type,$this->contact_us_question_type))
                {
                    $type =  $this->contact_us_question_type[$data[$i]->question_type];
                }
            $records["data"][] = array(
                $i+1,
                $data[$i]->email_subject,
                nl2br($data[$i]->email_message),
                $data[$i]->email_from,
                $data[$i]->email_to,
                $date,
                $status
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

    /**
    *
    * Function : Validation for add and edit form
    * @param  array $data 
    *
    */  
    private function validateFields($data = array()) {
        
        $rules = array(
            'email_to' =>  'required|email|max:100',
            'email_from' => 'required|email|max:100',
            'email_subject'    => 'required|min:2|max:100|',
            'email_message' => 'required',
            // 'email_status'    => 'numeric',
        );
        $messages = array(
            'email_to.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email To'),
            'email_to.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email To'),
            'email_to.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email To'),
            'email_from.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email From'),
            'email_from.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Email From'),
            'email_from.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Email From'),
            'email_message.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Message'),
            'email_subject.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Subject'),
            'email_subject.min' => sprintf(Config::get('messages.validation_msg.minlength'), '2', 'Subject'),
            'email_subject.max' => sprintf(Config::get('messages.validation_msg.maxlength'), '100', 'Subject'),

        );
        return Validator::make($data, $rules, $messages);
    }

}
