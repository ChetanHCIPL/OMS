<?php
namespace App\Http\Controllers\Admin\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\EmailTemplate;
use App\Models\Variable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Validator;
use Config;
use App;
use Image;
use Intervention\Image\File;
use App\GlobalClass\Design;


class EmailTemplateController extends Controller { 

    public function __construct(){
        $this->middleware('accessrights');
        $this->destinationLanPath = Config::get('path.language_path');

        Validator::extend("emails", function($attribute, $value, $parameters) {
                $rules = [
                    'from' => 'email'
                    // 'cc' => 'required|email',
                    // 'reply_to' => 'required|email',
                ];
                foreach ($value as $email) {
                    
                    $data = [
                        'from' => $email
                        // 'cc' => $email,
                        // 'reply_to'=>$email
                    ];
                    $validator = Validator::make($data, $rules);
                    if ($validator->fails()) {
                        return false;
                    }
                }
                return true;
        });

    }
     /**
     * Function : Load List Page
     *
     * @return void
     */
    public function index() {

        $section_arr = Config::get('constants.email_template_section');
        return view('admin.tools.email_template_list')->with(["section_arr" => $section_arr]);
    }

    /**
     * Function : Get data and pass json response to data table
     * @param array $request
     * @return json $records
     */
    public function ajaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        $search_coulmn_arr = array(
            "0" => "search_type",
            "1" => "search_status",
        );
        $search_arr = array(); 
              
        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }
        //Section Id 
        if(isset($post['sectionid'])){
            $search_arr['sectionid'] = $post['sectionid'];
        } 
       
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = EmailTemplate::getEmailTemplateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
                    $sort = "type";
                    break;
                case "2":
                    $sort = "status";
                    break;
                default:
                    $sort = "type";
            }
        } else {
            $sort = "type";
        }
        $data = EmailTemplate::getEmailTemplateData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        $cnt = count($data);
        
        for ($i = 0; $i < $cnt; $i++) {
           // $section = Config::get('constants.email_template_section.' . $data[$i]->section);

            $encoded_id = base64_encode($data[$i]->id);

            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status); 
            $status = Design::blade('status',$status,$status_color);

            $edit = '---';
            $view = '';
            if (per_hasModuleAccess('EmailTemplate', 'Edit')) {
                $edit = ' <a href="' . route('email-template',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('EmailTemplate', 'View')) {
             $view = '<a href="' . route('email-template',['mode' => 'view',  'id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>';
            }
            
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                '<span>' . $data[$i]->type . '</span>',
                '<span>' . $status . '</span>',
                $view.$edit
            );
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
       
        return Response::json($records);
    }

    /**
     * Function :  Post emailtemplate data for add ,edit  and change status
     * @param array $request
     * @return json $result_arr
     */
    
    public function PostAjaxData(Request $request) {
        $records = $data = $image =  $result_arr=array();
        $records["data"] =$errorCnt= $mail= array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
        if (isset($post['customActionName'])) {
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
                $result_arr[0]['msg'] = $records["customActionMessage"];
                return Response::json($result_arr);
            }
            else{

                // ## CHECK FIELD VALIDATION
                $validator = $this->validateFields($post);
                if ($validator->fails()) {

                    $validate_msg = setValidationErrorMessageForPopup($validator);
                    if (isset($validate_msg) && $validate_msg != "") {
                        $result[0]['isError'] = 1;
                        $result[0]['msg'] = $validate_msg;
                        return Response::json($result);
                    }
                }
                
                ## Check Validate Email Address
                if (isset($post['from']) && !empty($post['from']))
                {
                    foreach ($post['from'] as $email) { 
                       if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                         
                            $from = $email;
                        }else{
                            $errorCnt[0] = "Enter valid email in from.";
                        }
                    }
                } 
                if (isset($post['cc']) && !empty($post['cc']))
                {
                    foreach ($post['cc'] as $email) { 
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $mail['cc'][] =  $email;
                        }else{
                            $errorCnt[1] = "Enter valid email in cc.";
                        }  
                    }
                } 
                if (isset($post['reply_to']) && !empty($post['reply_to']))
                {
                    foreach ($post['reply_to'] as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                      
                            $reply_to = $email;
                        }else{
                            $errorCnt[2] = "Enter valid email in reply to.";
                        }
                    }
                } 
                if(!empty($errorCnt)){
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = implode("<br>", $errorCnt);
                    return Response::json($result_arr);

                }else{          
                    if ($post['customActionName'] == "Update") 
                    {
                        $post['from'] = $from;
                        $post['cc'] = implode(",", $mail['cc']);
                        $post['reply_to'] =  $reply_to;
                        $update = $this->updateRecord($post);
                    }else{

                        $post['cc'] = implode(",", $mail['cc']);
                        $post['reply_to'] =  $reply_to;
                        $update = $this->insertRecord($post);
                    } 
                    if (!empty($update) && $update['isError'] == 1) {
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = $update[0]['msg'];
                    } else {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                    }
                     return Response::json($result_arr);
                }
               
            }
        }
    }


      /**
    * Validation for form
    * @return validator object
    */
    private function validateFields($data = array()) {
       
        $rules = array(           
            'from'=> 'required',
            'reply_to'=> 'required',
            'cc'=> 'required',
            'mime'=> 'required'
        );
        $messages = array(
            'from.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'from'),
            'reply_to.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'reply to'),
            'cc.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'cc'),
            'mime.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'content type')
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function : Load form to Add/Edit Email Templates
     * @param  string $mode 
     * @param  int $id 
     * @return template
     */
    public function add($mode = NULL,$id = NULL) { 
        $result_arr = array();
        $data = $language = array();
        $email_lang_arr = $cc =array();
        
        if(isset($mode) && $mode != ""){
            if (isset($id) && $id != "") {
                ## update 
               $emailId = base64_decode($id);
                $data = EmailTemplate::getEmailTemplate($emailId);
                if(!empty($data) && !empty($data[0]['email_language'])){
                    foreach($data[0]['email_language'] as $rowLanguage){
                        $email_lang_arr[$rowLanguage['lang_code']]['from_name'] = (isset($rowLanguage['from_name'])?$rowLanguage['from_name']:"");
                        $email_lang_arr[$rowLanguage['lang_code']]['from_name'] = (isset($rowLanguage['from_name'])?$rowLanguage['from_name']:"");
                        $email_lang_arr[$rowLanguage['lang_code']]['reply_to_name'] = (isset($rowLanguage['reply_to_name'])?$rowLanguage['reply_to_name']:"");
                        $email_lang_arr[$rowLanguage['lang_code']]['subject'] = (isset($rowLanguage['subject'])?$rowLanguage['subject']:"");
                        $email_lang_arr[$rowLanguage['lang_code']]['body'] = (isset($rowLanguage['body'])?$rowLanguage['body']:"");
                    }
                }

                if(!empty($data) && $data[0]['cc'] !=""){
                    $cc = $data[0]['cc'];
                   $cc  = explode(",",$data[0]['cc']);

                } 
                //$tot_cnt = count($data);
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin/tools/email_template_add')->with(['mode' => $mode, 'id' => $emailId, 'data' => $data, 'cc' => $cc, 'email_lang_arr' => $email_lang_arr]);
                }elseif($mode == 'view'){
                    $mode = 'View';
                    return view('admin/tools/email_template_view')->with(['mode' => $mode, 'id' => $emailId, 'data' => $data, 'email_lang_arr' => $email_lang_arr]);
                }
            }
            elseif($mode == 'add'){
                $mode = "Add";
                return view('admin.tools.email_template_add')->with(['mode' => $mode]);
            } 
        }
        abort(404);
    }

    /**
     * Function : Validation for update record
     * @param  array $data 
     */
 
    private function validateEdit($data = array()) {
        $rules = array(
            'from' => 'required|email',
            'cc' => 'required|email',
            'reply_to' => 'required|email',
            'mime' => 'required',
        );
        $messages = array(
            'from.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'From'),
            'from.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'From'),
            'cc.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'CC'),
            'cc.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'CC'),
            'reply_to.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Reply To'),
            'reply_to.email' => sprintf(Config::get('messages.validation_msg.email_field'), 'Reply To'),
            'mime.required' => sprintf(Config::get('messages.validation_msg.required_field'), 'Email Content Type'),
            
        );
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Function : insert email template records  
     * @param  array $result 
     */

    private function insertRecord($data = array()) {
       
        $lang_arr = count($data['lang_code']);
        $email_lang_array = array();

        $id            = (isset($data['id']) ? $data['id'] : ""); 
        $from          = (isset($data['from']) ? $data['from'][0] : "");
        $cc            = (isset($data['cc']) ? $data['cc'] : "");
        $reply_to      = (isset($data['reply_to']) ? $data['reply_to'] : "");
        $type          = (isset($data['type']) ? $data['type'] : "");
        $section       = (isset($data['sectionName']) ? $data['sectionName'] : "");
        $mime          = (isset($data['mime']) ? $data['mime'] : "0");
        $status        = (isset($data['status']) ? $data['status'] : "2");
        $update_array = array(
            'from'     => $from,
            'cc'       => $cc,
            'reply_to' => $reply_to,
            'mime'     => $mime,
            'type'     => $type,
            'section'  => $section,
            'status'   => $status,
        );
        $insert = EmailTemplate::insertEmailTemplate( $update_array);
        $id = $insert->id;
        if (isset($insert)) {  
            ## Insert Email in different Languages
           // $cnt = count($data['lang_code']);
          //  $email_lang =$data['email_lang'];
           // if (!empty($data['lang_code'])) {
                $result['isError'] = 0;
                $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED'); 
                  
                 ## Start Add Activity
                $reference_id = $id;
                $module_id = Config::get('constants.module_id.email_template');
                $log_text = "Email Template " . $data['type'] . " -  " .$data['sectionName']  . " edited";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
                ## End Add Activity
           /* } else {
                $result[0]['isError'] = 1;
                $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
            }*/

        }
        return $result;
    }

    /**
     * Function : Update Record
     * @param  array $data 
     * @return array $result
     */ 

    private function updateRecord($data = array()) {
        $lang_arr = array();
        
        //$lang_arr = count($data['lang_code']);
        $email_lang_array = array();

        $id            = (isset($data['id']) ? $data['id'] : ""); 
        $from          = (isset($data['from']) ? $data['from'] : "");
        $cc            = (isset($data['cc']) ? $data['cc'] : "");
        $reply_to      = (isset($data['reply_to']) ? $data['reply_to'] : "");
        $mime          = (isset($data['mime']) ? $data['mime'] : "0");
        $content       = (isset($data['content']) ? $data['content'] : "");
        $status          = (isset($data['status']) ? $data['status'] : "2");
        $update_array = array(
            'from'     => $from,
            'cc'       => $cc,
            'reply_to' => $reply_to,
            'mime'     => $mime,
            'content'  => $content,
            'status'   => $status,
        );
        $update = EmailTemplate::updateEmailTemplate($id, $update_array);

        if (isset($update)) {
            ## Insert Email in different Languages
           // $cnt = count($data['lang_code']);
          //  $email_lang =$data['email_lang'];
            //if (!empty($data['lang_code'])) {
                $result['isError'] = 0;
                $result['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED'); 
                  
                 ## Start Add Activity
                $reference_id = $id;
                $module_id = Config::get('constants.module_id.email_template');
                $log_text = "Email Template " . $data['type'] . " -  " .$data['sectionName']  . " edited";
                $activity_arr = array("admin_id" =>Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
                ## End Add Activity
           /* } else {
                    $result[0]['isError'] = 1;
                    $result[0]['msg'] = Config::get('messages.msg.MSG_SOMETHING_WRONG');
                }*/

        }
        return $result;
    }

    /**
     * Function : Update status
     * @param  int $id
     * @param  int $status
     * @return bolean 
     */
    
    private function updateStatus($id = array(), $status) {
        if (!empty($id)) {
            if (isset($status) && $status == "Active") {
                $status = 1;
            } elseif (isset($status) && $status == "Inactive") {
                $status = 2;
            }
            $update = EmailTemplate::updateEmailTemplateStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }
}