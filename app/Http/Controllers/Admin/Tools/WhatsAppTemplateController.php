<?php
namespace App\Http\Controllers\Admin\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Variable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Validator;
use Config;
use App;
use Image;
use Intervention\Image\File;
use App\GlobalClass\Design;
use App\Models\WhatsAppTemplate;


class WhatsAppTemplateController extends Controller { 

	/**
     * Function : Load List Page
     *
     * @return void
    **/
	public function index(){
		$section_arr = Config::get('constants.whatsapp_template_section');
		return view('admin.tools.whatsapp_template_list')->with(["section_arr" => $section_arr]);
	}


	/**
     * Function : Get whatsapp data 
     * @param array $request 
     *
     * @return json response 
    **/
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
        if(isset($post['columns']) && count($post['columns']) > 0) {
            $searchFilterData = objToArray(json_decode($post['columns'][0]['search']['value']));
            if(isset($searchFilterData)){
            foreach($searchFilterData as $searchData)
                $search_arr[$searchData['key']] = $searchData['val'];
            }
        }
        if(isset($post['sectionid']) && !isset($search_arr['sectionid'])){
            $search_arr['sectionid'] = $post['sectionid'];
        }

        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = WhatsAppTemplate::getWhatsAppTemplateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
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
        $data = WhatsAppTemplate::getWhatsAppTemplateData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        $cnt = count($data);
        
        for ($i = 0; $i < $cnt; $i++) {

            $section = Config::get('constants.whatsapp_template_section.' . $data[$i]->section);

            
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');

            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status); 
            $status = Design::blade('status',$status,$status_color);

            $edit = '---';
            $view = '';
            if (per_hasModuleAccess('WhatsappTemplate', 'Edit')) {
                $edit = ' <a href="' . route('whatsapp-template',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit"><span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span></a>';
            }
            if (per_hasModuleAccess('WhatsappTemplate', 'View')) {
                $view = '<a href="' . route('whatsapp-template',['mode' => 'view',  'id' => $encoded_id]) . '" title="View"><span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span></a>';
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
     * Function :  Post whatsapp data
     * @param array $request 
     *
     * @return json response 
    **/
    public function PostAjaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();
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
            }else if ($post['customActionName'] == "Update") {
                $update = $this->updateRecord($post);
               
                return Response::json($update);
            }
        }
    }

    /**
     * Function : Update status
     * @param array $request 
     *
     * @return json response 
    **/
    private function updateStatus($id = array(), $status) {
        if (!empty($id)) {
            if (isset($status) && $status == "Active") {
                $status = 1;
            } elseif (isset($status) && $status == "Inactive") {
                $status = 2;
            }
            $update = WhatsAppTemplate::updateWhatsAppTemplateStatus($id, $status);
        }
        if (isset($update)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Function : Load Edit/View Whatsapp template
     * @param string mode
     * @param int id
     *
     * @return view
    **/
    public function add($mode = NULL,$id = NULL) {
        $whatsapp_lang_arr = $language= $data = array();

        if(isset($mode) && $mode != ""){
            if (isset($id) && $id != "") {
                $whatsappId = substr($id, 3, -3);
                $data = WhatsAppTemplate::getWhatsAppTemplate($whatsappId);
                if(!empty($data) && !empty($data[0]['whatsapp_language'])){
                    foreach($data[0]['whatsapp_language'] as $rowLanguage){
                        $whatsapp_lang_arr[$rowLanguage['lang_code']]['content'] = (isset($rowLanguage['content'])?$rowLanguage['content']:"");
                    }
                }
                if($mode == 'edit'){
                    $mode = 'Update';
                    return view('admin/tools/whatsapp_template_add')->with(['mode' => $mode, 'id' => $whatsappId, 'data' => $data, 'whatsapp_lang_arr' => $whatsapp_lang_arr]);
                }elseif($mode == 'view'){
                    $mode = 'View';
                    return view('admin/tools/whatsapp_template_view')->with(['mode' => $mode, 'id' => $whatsappId, 'data' => $data, 'whatsapp_lang_arr' => $whatsapp_lang_arr]);
                }
            }
        }
        abort(404);
    }
 
    /**
     * Function : Update Record
     * @param array data
     *
     * @return array result
    **/
    private function updateRecord($data = array()) {

        ## Variable Declaration
        $result_arr = array();
        $whatsapp_lang_array = array();

        $type = (isset($data['type']) ? $data['type'] : "");
        $id = (isset($data['id']) ? $data['id'] : "");
        $type = (isset($data['type']) ? $data['type'] : "");
        $section = (isset($data['section']) ? $data['section'] : "");
        $content = (isset($data['content']) ? $data['content'] : "");
        $status = (isset($data['status']) ? $data['status'] : "2");
    
        $update_array = array(
            'content'  =>$content,
            'status' => $status,
            'updated_at' => date_getSystemDateTime(),
        );

        $update = WhatsAppTemplate::updateWhatsAppTemplate($id, $update_array);
        if (isset($update)) {            
                $result_arr[0]['isError'] = 0;
                $result_arr[0]['msg'] = Config::get('messages.msg.MSG_RECORD_UPDATED');
                $result_arr[0]['section_id'] = $data['section'];

                ## Start Add Activity
                $reference_id = $id;
                $module_id = Config::get('constants.module_id.whatsapp_template');
                $log_text = "WhatsApp Template " . $data['type'] . " -  " .$data['sectionName']  . " edited";
                $activity_arr = array("admin_id" => Auth::guard('admin')->user()->id, "module_id" => $module_id, "reference_id" => $reference_id, "no" => '', "log_text" => $log_text, "req_data" => $data);
                usr_addActivityLog($activity_arr);
                ## End Add Activity
            
        }else {
        	$result_arr[0]['isError'] = 1;
            $result_arr[0]['msg'] = config('messages.msg.MSG_SOMETHING_WRONG');
        }
        return $result_arr;
    }
}
