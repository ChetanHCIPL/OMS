<?php ## Created by Radhika Mangrola as on 9th Aug 2019

/**
 * Function: Generate User Login Device Token
 *
 * @param    integer    $length
 * @return   string     $key
 */
function app_generate_login_device_token($length){
	$key = '';
    $keys = array_merge(range(0, 9), range(0, 9));
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;	
}

/**
 * Function: Set format of given date
 *
 * @param    string     $text
 * @return   string     Formatted date
 */
function api_getDateFormat($text) {
	if($text=="" || $text=="0000-00-00") return "";
	else return date("Y-m-d", strtotime($text));
}

/**
 * Function: Set format of given datetime
 *
 * @param    string     $text
 * @return   string     Formatted date
 */
function api_getDateTimeFormat($text) {
	if($text=="" || $text=="0000-00-00 00:00:00") return "";
	else return date("Y-m-d H:i:s", strtotime($text));
}

/**
 * Function: Check format of date
 *
 * @param    string  $date
 * @return   boolean
 */
function api_validDateFormat($date){
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date))
    {
        return true;
    }else{
        return false;
    }
}

/**
 * Function: Check format of datetime
 *
 * @param    string  $date
 * @return   boolean
 */
function api_validDateTimeFormat($date){
	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-4]):([0-5][0-9])$/",$date))
    {
        return true;
    }else{
        return false;
    }
}

/**
 * Function: Set pagination parameters (Total Pages, Start Index, Next page, Previous page)
 *
 * @param    integer  $total_records
 * @param    integer  $page
 * @param    integer  $records_per_page
 * @return   array    $result
 */
function api_setPaginationParameters($total_records, $page, $records_per_page){
    ## Variable Declaration
    $next = $previous = "";
    $start = $total_pages = 0;
    if ($records_per_page > 0) {
        if($page != "" && $page > 0 && $total_records > 0){
            ## Total Pages
            $total_pages = ceil($total_records/$records_per_page);
            ## Start Index
            $start = ($page-1) * $records_per_page;
            ## Next Page
            if($page != $total_pages){
                $next = $page+1;
            }     
            ## Previous Page                   
            if($page != 1){
                $previous = $page-1;
            }
        }
    }
    ## Return Response
    $result = array('total_pages' => $total_pages, 'start' => $start, 'next' => $next, 'previous' => $previous);
    return $result;
}

/**
 * Function: Set validation error message
 *
 * @param    array      $errors
 * @return   string     $msg
 */
function setValidationErrorMessageForAPI($errors) {
    $msg = '';
    if (!empty($errors)) {
        $i = 0;
        foreach ($errors as $row) {
            if($i > 0){
                $msg .= '</br>';
            }
            $msg .= $row;
            $i++;
        }
    }
    return $msg;
}

/**
 * fuction for Add CSS for Android device
 *
 * @param string $page
 * @return string $css
 */
function getMobileCSS($page=""){
    $css = "";
    if(!empty(request()->get('device_type'))){
        $device_type = request()->get('device_type');
        // $device_type -> 1 = Andtoid | 2=IOS
        $css_file_name = "";
        if($device_type == 1){
            $css_file_name = "android.css";
        }else if($device_type == 2){
            $css_file_name = "ios.css";
        }
        if($css_file_name != ""){
            switch ($page) {
                case 'static':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;
                case 'course':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;
                case 'instructor':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;        
                case 'module':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;        
                case 'topic':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;        
                case 'resources':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;
                case 'help':
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;      
                default:
                    $mobile_css_url = public_path() . "/assets/pages/css/".$css_file_name; // Done
                    break;
            }
            // $android_css_url = public_path() . "/assets/pages/css/android.css";
            $mobile_css = "";
            $mobile_css = file_get_contents($mobile_css_url);
                    
            if(!empty($mobile_css)){
                $css = '<style type="text/css">'.$mobile_css.'</style>';
            }
        }
    }
    return $css;
}


function generateMemberAPIDebugLog(){

    $current_date = date("Y-m-d");
    
    $log_arr = array();
    $log_arr["log_time"] = date("Y-m-d H:i:s");
    $log_arr["url"] = url()->current();
    $log_arr["headers"] = \Request::header();
    $req_param = request()->all();
    $member_id = (!empty($req_param["member_id"]))?$req_param["member_id"]:"";
    if(!empty($req_param["password"])){
        $req_param["password"] = Hash::make($req_param["password"]);
    }
    if(!empty($req_param["current_password"])){
        $req_param["current_password"] = Hash::make($req_param["current_password"]);
    }
    if(!empty($req_param["new_password"])){
        $req_param["new_password"] = Hash::make($req_param["new_password"]);
    }
    if(!empty($req_param["confirm_password"])){
        $req_param["confirm_password"] = Hash::make($req_param["confirm_password"]);
    }
    $log_arr["parameters"] = $req_param;

    // \Storage::disk('local')->makeDirectory('/logs/'.$current_date,  0775);
    $log_folder_path = storage_path('logs/'.$current_date);
    if (!file_exists($log_folder_path)) {
        File::makeDirectory($log_folder_path);
    }
    if($member_id > 0){
        $log_folder = $log_folder_path."/".$member_id.'.json';
    }else{
        $log_folder = $log_folder_path."/other.json";
    }

    $log_arr_str = json_encode($log_arr, JSON_PRETTY_PRINT)."\n";
    file_put_contents($log_folder, $log_arr_str, FILE_APPEND);
}
/**
 * Summary of get_formatted_address
 * Pass as Array & Retuen As String For Client Address
 * 
 * @param mixed $data
 * @return void
 */
function get_formatted_address($data = array()){
    return implode(', ', $data);
}