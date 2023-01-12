<?php
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Config;
use App;

class LoginLogController extends Controller {

	public function __construct(){
        $this->middleware('accessrights');
    }


    /**
    * Function : Load language List Page
    *
    * @return void
    */

    public function index() {
        return view('admin/user/login_log_list');
    }

    /**
    *Function : Get Login 
    * @param  string  $request
    * @return json    
    */
    public function ajaxData(Request $request) {
    	$records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;
        $post = $request->All();
       
         $search_coulmn_arr = array(
            "0" => "name",
            "1" => "ip",
            "2" => "login_date_from",
            "3" => "login_date_to",
            "4" => "logout_date_from",
            "5" => "logout_date_to",
        );
        $search_arr = array();
        if(isset($post['columns']) && count($post['columns']) > 0) {
            foreach ($post['columns'] as $key => $field) {
                if($field['search']['value'] != '')
                    $search_arr[$search_coulmn_arr[$key]] = $field['search']['value'];
            }
        }

        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");
              
        $tot_records_data = LoginLog::getLoginLogData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr);
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
                    $sort = "ip";
                    break;
                case "3":
                    $sort = "login_date";
                    break;
                case "4":
                    $sort = "logout_date";
                    break;
                default:
                    $sort = "login_date";
            }
        } else {
            $sort = "login_date";
        }
        $data = LoginLog::getLoginLogData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr);
        //p($data);
        $cnt = count($data);
        
        for ($i = 0; $i < $cnt; $i++) {
         
            $login_date = date_getDateTimeAll($data[$i]->login_date);
           
            $logout_date = date_getDateTimeAll($data[$i]->logout_date);
           
            $duration = '---';
            if ($login_date != "" && $login_date != "---" && $logout_date != "" && $logout_date != "---") {
                $duration = timeBetween($login_date, $logout_date);
                $duration = ($duration != ''?$duration:"---");
            }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->name,
                $data[$i]->ip,
               	$login_date,
                $logout_date,
                $duration,
				ip_info($data[$i]->ip,'country')
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }

   	/**
    *Function : Post login log data 
    * @param  string $request
    * @return json    
    */
    public function PostAjaxData(Request $request) {
        $records = $data = $image = array();
        $records["data"] = array();

        $post = $request->All();
       
        if (isset($post['customActionName'])) {
            if ($post['customActionName'] == "Delete") {
                $delete = $this->deleteRecord($post['id']);
                if ($delete == true) {
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
    *Function : Delete login log data
    * @param  array $id
    * @return boolean    
    */
    private function deleteRecord($id = array()) {
        if (!empty($id)) {
            $delete = LoginLog::deleteLoginlogData($id);
            if (isset($delete)) {
                return true;
            } else {
                return false;
            }
        }
    }
}