<?php

namespace App\Http\Controllers\Admin\ActivityLog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Admin;
use App\Models\AccessModule;
use App\Models\AdminActivityLog;
use App\Models\AdminActivityLogDetail;
use Illuminate\Support\Facades\Response;

class ActivityLogController extends Controller {

    public function __construct(){
        
        $this->middleware('accessrights');
    }

    ## Load Activity Log List Page
    public function index() {
        
        $admin = Admin::getAllActiveUser();
        $module = AccessModule::getModuleData();
        for ($i = 0, $mo = count($module); $i < $mo; $i++) {
            $acc_mod_assoc_arr[$module[$i]['parent_id']][] = $module[$i];
        }
        $par_am_array = am_getChildAccessModuleList(0, "", "", 1, 5, $acc_mod_assoc_arr);
        // echo "<pre>";print_r($_REQUEST);exit();
        return view('admin/activitylog/activity_log')->with(['admin' => $admin, 'par_am_array' => $par_am_array]);
    }

    /**
    * Function : Get Activity Log Data
    *
    * @param    string  $request
    * @return   json    
    */
    public function getActivityLog(Request $request) {
        
        ## Variable Declaration
        $date_from = $date_to = NULL;
        //p($request->input());
        ## Request Parameter
        $startlimit = $request->input('startlimit', '');
        $endlimit = $request->input('endlimit', '');
        $module_id = $request->input('module_id', '');
        $first_name = $request->input('first_name', '');
        $last_name = $request->input('last_name', '');
        $username = $request->input('username', '');
        if ($request->filled('date_from')) {
            $date_from = strtotime(date('Y-m-d', strtotime($request->input('date_from'))) . " " . "00:00:01");
        }
        if ($request->filled('date_to')) {
            $date_to = strtotime(date('Y-m-d', strtotime($request->input('date_to'))) . " " . "23:59:59");
        }
        
        ## Get Activity log data
        $where_arr = array('module_id' => $module_id, 'date_from' => $date_from, 'date_to' => $date_to, 'first_name' => $first_name, 'last_name' => $last_name, 'username' => $username);
      
        $result = AdminActivityLog::getActivityLogData($startlimit, $endlimit, $where_arr)->get()->toArray();
        // echo "<pre>";print_r($result);exit;
        //p($result);
        ## Prepare response array
        $act_arr = array();
        if(!empty($result)){
            foreach($result as $rowData){
                $dAddedDate = date('d-M-y', $rowData['added_date_time']);
                if (isset($rowData['admin_name']) && $rowData['admin_name'] != ""){
                    $log_text = $rowData['log_text'] . " (" . $rowData['admin_name'] . ")";
                }else{
                    $log_text = $rowData['log_text'];
                    $act_arr[$dAddedDate][] = array(
                        'id'                => $rowData['id'],
                        'module_id'         => $rowData['module_id'],
                        'admin_id'          => $rowData['admin_id'],
                        'log_text'          => $log_text,
                        'access_module'     => $rowData['access_module'],
                        'AddedDate'         => $dAddedDate,
                        'AddedTime'         => date('H:i', $rowData['added_date_time']),
                        'date_diff'         => date_getTimeDifference(date_getUnixDate($rowData['added_date_time']), date_getSystemDate()),
                        'reference_id'      => $rowData['reference_id'],
                     );
                }
            }
        }

        //p($act_arr);
        ## Return Response
        return Response::json($act_arr);
    }


    /**
    * Function : Get Admin Activity Log Details
    *
    * @param    string  $request
    * @return   string $str  
    */
    public function getActivityLogDetail(Request $request) {
        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : "");
        $str = NULL;
        if ($id != "") {
            $data = AdminActivityLogDetail::getActivityLogDetailFromId($id);
            //echo "<pre>";print_r($data);exit();
            $tData = json_decode($data[0]['data']);
            //echo "<pre>";print_r($tData);exit();
            $str = self::do_dump($tData);
        }
        echo $str;
        exit;
    }

    /**
    * Function : Module to get activity log entries
    * 
    */
    private static function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
        $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
        $reference = $reference . $var_name;
        $keyvar = 'the_do_dump_recursion_protection_scheme';
        $keyname = 'referenced_object_name';
        $str = $type_color = $type = NULL;
        ## So this is always visible and always left justified and readable
        echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

        if (is_array($var) && isset($var[$keyvar])) {
            $real_var = &$var[$keyvar];
            $real_name = &$var[$keyname];
            $type = ucfirst(gettype($real_var));
            echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
        } else {
            $var = array($keyvar => $var, $keyname => $reference);
            $avar = &$var[$keyvar];

            $type = ucfirst(gettype($avar));

            if ($type == "String")
                $type_color = "<span style='color:green'>";
            elseif ($type == "Integer")
                $type_color = "<span style='color:red'>";
            elseif ($type == "Double") {
                $type_color = "<span style='color:#0099c5'>";
                $type = "Float";
            } elseif ($type == "Boolean")
                $type_color = "<span style='color:#92008d'>";
            elseif ($type == "NULL")
                $type_color = "<span style='color:black'>";

            if (is_array($avar)) {

                $count = count($avar);
                echo "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
                $keys = array_keys($avar);
                foreach ($keys as $name) {
                    $value = &$avar[$name];
                    self::do_dump($value, "['$name']", $indent . $do_dump_indent, $reference);
                }
                echo "$indent)<br>";
            } elseif (is_object($avar)) {
                echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
                foreach ($avar as $name => $value)
                    self::do_dump($value, "$name", $indent . $do_dump_indent, $reference);
                echo "$indent)<br>";
            } elseif (is_int($avar))
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
            elseif (is_string($avar))
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color\"" . htmlentities($avar) . "\"</span><br>";
            elseif (is_float($avar))
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
            elseif (is_bool($avar))
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
            elseif (is_null($avar))
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> {$type_color}NULL</span><br>";
            else
                echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> " . htmlentities($avar) . "<br>";

            $var = $var[$keyvar];
        }
        echo "</div>";
    }
}