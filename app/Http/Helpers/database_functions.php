<?php

/**
* Get Associative array for each module
*/
function per_getAssocArray($access_group_id)
{
    $assoc_array = array();
    if(!empty($access_group_id)){
        $query = App\Models\AccessModule::where('admin_access_module.module_status', 1);
        $query->whereIn('amr.access_group_id', $access_group_id);
        $query->select('admin_access_module.access_module_id','amr.access_group_id', 'admin_access_module.access_module', 'amr.list','amr.view', 'amr.add', 'amr.edit', 'amr.delete', 'amr.status','amr.print');
        $query->leftjoin('admin_access_group_module_role as amr', 'admin_access_module.access_module_id', '=', 'amr.access_module_id');
        $result = $query->get()->toArray();
        $cnt = count($result);
        for ($i = 0; $i < $cnt; $i++) {
            $assoc_array[$result[$i]['access_module']]['list'] = $result[$i]['list'];
            $assoc_array[$result[$i]['access_module']]['view'] = $result[$i]['view'];
            $assoc_array[$result[$i]['access_module']]['add'] = $result[$i]['add'];
            $assoc_array[$result[$i]['access_module']]['edit'] = $result[$i]['edit'];
            $assoc_array[$result[$i]['access_module']]['delete'] = $result[$i]['delete'];
            $assoc_array[$result[$i]['access_module']]['print'] = $result[$i]['print'];
            $assoc_array[$result[$i]['access_module']]['status'] = $result[$i]['status'];
        }
    }
    return $assoc_array;
}

/**
* Check permission
*/
function per_hasModuleAccess($mod_name = '', $mod_access_name = '') {
    $access_group_id = array();
    $user_type=0;
    // return true;
	// Check Access Group Id are active or not
	$query_check = App\Models\Admin::where(['admin.id' => Auth::guard('admin')->user()->id, 'admin_access_group.status' => 1, 'admin.status' => 1])
                   ->join('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'admin.id')
                    ->join('admin_access_group', 'admin_access_group.id', '=', 'admin_access_group_permission.access_group_id')
                    ->select(DB::Raw('GROUP_CONCAT(admin_access_group_permission.access_group_id) AS iAGroupId'),DB::Raw('admin.user_type AS user_type'))
					->groupBy('admin_access_group_permission.admin_id')
                    ->get()->toArray();
                    $act_group_id = array();
                    if(!empty($query_check))
                    {
	                   $act_group_id =  explode(',',$query_check[0]['iAGroupId']);
                       $user_type=$query_check[0]['user_type'];
                    }
	       
            	$old_group_id =  explode(',', session('sess_access_group_id'));
            	$old_group_id = array_intersect($old_group_id,$act_group_id);
            	for($i=0;$i<count($act_group_id);$i++){
            		if(!in_array($act_group_id[$i],$old_group_id)){
            			$old_group_id[$i] = $act_group_id[$i];
            		}
            	}
	session(['sess_access_group_id' => implode(',',$old_group_id)]);
    //add for user type for access pages 
    session(['user_type' => $user_type]);
    
    if (session('sess_access_group_id') != "") {
        $access_group_id = explode(',', session('sess_access_group_id'));
    }
    if(!isset($GLOBALS['mod_access_arr'])){
        $GLOBALS['mod_access_arr'] = per_getAssocArray($access_group_id); 
    }
    if (!empty($GLOBALS['mod_access_arr'][$mod_name])) {
        if ($mod_access_name == 'List') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['list']) && $GLOBALS['mod_access_arr'][$mod_name]['list'] == '1')
                return 1;
        }
        if ($mod_access_name == 'View') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['view']) && $GLOBALS['mod_access_arr'][$mod_name]['view'] == '1')
                return 1;
        }
        else if ($mod_access_name == 'Add') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['add']) && $GLOBALS['mod_access_arr'][$mod_name]['add'] == '1')
                return 1;
        }
        else if ($mod_access_name == 'Edit') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['edit']) && $GLOBALS['mod_access_arr'][$mod_name]['edit'] == '1')
                return 1;
        }
        else if ($mod_access_name == 'Print') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['print']) && $GLOBALS['mod_access_arr'][$mod_name]['print'] == '1')
                return 1;
        }
        else if ($mod_access_name == 'Status') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['status']) && $GLOBALS['mod_access_arr'][$mod_name]['status'] == '1' || $GLOBALS['mod_access_arr'][$mod_name]['edit'] == '1')
                return 1;
        }
        else if ($mod_access_name == 'Delete') {
            if (isset($GLOBALS['mod_access_arr'][$mod_name]['delete']) && $GLOBALS['mod_access_arr'][$mod_name]['delete'] == '1')
                return 1;
        }else {
            return 0;
        }
    } else {
        return 0;
    }
}


/**
* set logged in user data in session 
*/
function updateLoginCount($email) {

    //$userData = App\Models\Admin::where(['email'=>$email])->get()->toArray();
    $userData = App\Models\Admin::where(['username'=>$email])->get()->toArray();
    $count = 1;
    if(!empty($userData))
    {
        $count = isset($userData[0]['tot_login']) ? $userData[0]['tot_login'] + 1  : 1;
    }
    $id = $userData[0]['id'];
    $updateArr = array('tot_login'=>$count,'last_access'=>date('Y-m-d H:i:s'));
    $userData = App\Models\Admin::where(['id'=>$id])->update($updateArr); 
    
}

/**
* Admin Activity
*/
function usr_addActivityLog($act_arr = array()) {
    $module_id = (isset($act_arr['module_id']) ? $act_arr['module_id'] : "");
    $admin_id = (isset($act_arr['admin_id']) ? $act_arr['admin_id'] : "");
    $reference_id = (isset($act_arr['reference_id']) ? $act_arr['reference_id'] : "");
    $remark = '';
    $no =  (isset($act_arr['no']) ? $act_arr['no'] : "");;
    $log_text = addslashes((isset($act_arr['log_text']) ? $act_arr['log_text'] : ""));
    if ($module_id != "" && $module_id > 0) {
        if ($log_text != "" && isset($admin_id)) {
            $added_date_time = strtotime(date_getSystemDateTime());
            $act_log_array = array(
                'module_id' => $module_id,
                'admin_id' => $admin_id,
                'reference_id' => $reference_id,
                'log_text' => $log_text,
                'remark' => $remark,
                'no' => $no,
                'added_date_time' => $added_date_time,
            );
            
            $AdminActivityLog = App\Models\AdminActivityLog::addActivityLog($act_log_array);
            if (!empty($AdminActivityLog) && isset($AdminActivityLog['id']) && $AdminActivityLog['id'] != "" && is_array($act_arr['req_data'])) {
                $act_log_detail_array = array(
                    'activity_log_id' => $AdminActivityLog['id'],
                    'data' => json_encode($act_arr['req_data']),
                );
                $AdminActivityLogDetail = App\Models\AdminActivityLogDetail::addActivityLogDetail($act_log_detail_array);
            }
        }
    }
}


/**
* set logged in user data in session 
*/
function saveUserSessionData($id) {

    #=================================================================================
    # Set User data in session.....
    #=================================================================================
    ##Variable Declaration

    $tot_login = 0;
    $vFromIP = getIP();
    $dDate = date_getSystemDateTime();
    $AdminAccessGroup = App\Models\Admin::where('admin.id',$id)->where('admin_access_group.status',1)->where('admin.status',1)
                   ->join('admin_access_group_permission', 'admin_access_group_permission.admin_id', '=', 'admin.id')
                    ->join('admin_access_group', 'admin_access_group.id', '=', 'admin_access_group_permission.access_group_id')
                    //->select('admin.*, 1 as iAGroupId')
                    //->select('admin.*', DB::Raw('1 AS access_group_id'))
                    ->select('admin.tot_login', 'admin_access_group.access_group', DB::Raw('GROUP_CONCAT(admin_access_group_permission.access_group_id) AS iAGroupId'))
                    ->groupBy('admin.id')
                    ->get()->toArray();

    if (!empty($AdminAccessGroup)) {
        if (isset($AdminAccessGroup[0]["iAGroupId"]) && $AdminAccessGroup[0]["iAGroupId"] != "") {
            session(['sess_access_group_id' => $AdminAccessGroup[0]["iAGroupId"]]);
            /**
            * Set Access Group Name in Session
            */
            session(['access_group_name' => $AdminAccessGroup[0]["access_group"] ? $AdminAccessGroup[0]["access_group"] : '']);
        }
        if (isset($AdminAccessGroup[0]["tot_login"]) && $AdminAccessGroup[0]["tot_login"] != "") {
            $tot_login = $AdminAccessGroup[0]["tot_login"];
        } else {
            $tot_login = 0;
        }

        /**
         * Set User ID IN Session
         */
        session(['user_id' => $id]);

        #=================================================================================
        # Query for Inserting Login Details.....
        #=================================================================================

        $insert_array = array(
            'admin_id' => $id,
            'ip' => $vFromIP,
            'login_date' => $dDate,
        );
        $insert = App\Models\LoginLog::create($insert_array)->toArray();

        if (!empty($insert)) {
            if (isset($insert['id']) && $insert['id'] != "") {
                session(['sess_id_log' => $insert['id']]);
            }
        }
    } 

    #=================================================================================
    # FOR Last Login Details.....
    #=================================================================================
   

    $update_array = array(
        'last_access' => $dDate,
        'from_ip' => $vFromIP,
        'tot_login' => (int) ($tot_login + 1),
    );
    $update = App\Models\Admin::where(['admin.id' => $id, 'admin.status' => 1])->update($update_array);
}

/**
* set logged in client user data in session 
*/
function saveClientSessionData($id, $email) {

    #=================================================================================
    # Set Client data in session.....
    #=================================================================================
    ##Variable Declaration

    $tot_login = 0;
    $vFromIP = getIP();
    $dDate = date_getSystemDateTime();
    $userData = App\Models\Clients::where(['username'=>$email])->get()->toArray();
    if(!empty($userData))
    {
        $tot_login = isset($userData[0]['tot_login']) ? $userData[0]['tot_login'] : 0;

        /**
         * Set Client data in Session
         */
        session(['client_code' => $userData[0]['client_code']]);
        session(['client_name' => $userData[0]['client_name']]);
        session(['mobile_number' => $userData[0]['mobile_number']]);
        session(['email' => $userData[0]['email']]);
        session(['discount_category' => $userData[0]['discount_category']]);
        session(['client_type' => $userData[0]['client_type']]);
        session(['client_status' => $userData[0]['status']]);
    }

    /**
     * Set Client ID IN Session
     */
    session(['client_id' => $id]);

    #=================================================================================
    # Query for Inserting Login Details.....
    #=================================================================================

    $created_at = date_getSystemDateTime();
    $insert_array = array( 
        'client_id'         => $id,
        'ip'                => getIP(),
        'device_type'       => '3',
        'login_date'        => $created_at
    );

    $insert = App\Models\ClientLoginLog::create($insert_array)->toArray();

    if (!empty($insert)) {
        if (isset($insert['id']) && $insert['id'] != "") {
            session(['sess_client_id_log' => $insert['id']]);
        }
    }

    #=================================================================================
    # FOR Last Login Details.....
    #=================================================================================

    $update_array = array(
        'last_access' => $dDate,
        'from_ip' => $vFromIP,
        'tot_login' => (int) ($tot_login + 1),
    );

    $update = App\Models\Clients::where(['clients.id' => $id])->update($update_array);
}

/* Get  Access general role */

function getAccessGenralPermission($generalId) {
     $loginId = Auth::guard('admin')->user()->id;

     $adminAccessGroup = App\Models\AdminAccessGeneralRole::where(['access_general_id'=> $generalId,'p.admin_id'=>$loginId])->join('admin_access_group_permission as p', ['p.access_group_id'=>'admin_access_general_role.access_group_id'])->get()->toArray();
 
     return $adminAccessGroup;

}

/**
* Check valid ip
*/
function checkValidIP($email) {
    $allowed_hosts = '';
    $IpData = $ip_id_arr = $ip_data = $ip_arr =  array();
    $userData = App\Models\Admin::where(['username'=>$email,'status'=>1])->get()->toArray();
    
    $is_ip_auth = $admin_id =  0;
    if(!empty($userData))
    {
        $is_ip_auth = isset($userData[0]['is_ip_auth']) ? $userData[0]['is_ip_auth'] : 0;
        $admin_id = isset($userData[0]['id']) ? $userData[0]['id'] : 0;
    }
    if($is_ip_auth !='' && $is_ip_auth == 1){
        if($admin_id > 0){
            $IpData = App\Models\AdminIpAccess::where(['admin_id'=>$admin_id])->get()->toArray();
            if(!empty($IpData)){
                foreach ($IpData as $ipkey => $ipvalue) {
                   $ip_id_arr[] = (isset($ipvalue['ip_id']))?$ipvalue['ip_id']:'';
                }
            }
            $ip_data = App\Models\Ip::getadminIp($ip_id_arr);
            if(!empty($ip_data)){
                foreach ($ip_data as $ipkey => $ipvalue) {
                   $ip_arr[] = (isset($ipvalue['ip']))?$ipvalue['ip']:'';
                    if(count($ip_arr)>0){
                        $allowed_hosts = implode("|", $ip_arr);
                    }
                }
            }
            // $current_ip = getIP(); 
            // if(!empty($ip_arr) && $current_ip !=''){
            //     if(in_array($current_ip, $ip_arr)){
            //         return 1;
            //     }else{
            //         return 0;
            //     }
            // }
            if($allowed_hosts != "")
            {
                //$allowed_hosts = "192.168.*|169.*.121.*";
                $allowed_hosts_array = explode('|', $allowed_hosts);

                $current_ip = getIP();

                // Comment Above & Uncomment Below for testing of various hosts
                //$current_ip = "192.168.*.*";
                $remote_ip_array = explode('.',$current_ip);
                // echo "<pre>";print_r($remote_ip_array);exit;
                foreach ($allowed_hosts_array as $host_spec) {
                  $allowed = false;
                  $allowed_split = explode('.',$host_spec);
                  foreach ($allowed_split as $key => $section) {
                    if (!is_numeric($section)) {
                      $hostname = explode('*',strtolower($section));
                      foreach ($hostname as $host) {
                        if (trim($host) != "") {
                          $allowed[] = (strtolower(substr($current_ip,0,strlen($host))) == $host) ? 1 : 0;
                        }
                      }
                    } else {
                      $allowed[] = (($remote_ip_array[$key] == $section) || $section == "*") ? 1: 0;
                    }
                  }
                  $allowed_spec[] = (!in_array(0,$allowed)) ? 1 : 0;
                }
                if (in_array(1,$allowed_spec)) {
                    return 1;
                } 
                else {
                    return 0;
                }
            }
            else{
                return 0;
            }
            
        }else{
            return 0;
        }
    }else{
        return 1;
    }
}

/**
* Check check Tot Login Attempt
*/
// function checkTotLoginAttempt($email) {
//     $allowed_hosts = '';
//     $userData = array();
//     $userData = App\Models\Admin::where(['username'=>$email,'status'=>1])->get()->toArray();
//     $is_mobile_auth = $admin_id =  0;
//     if(!empty($userData))
//     {
//         $is_mobile_auth = isset($userData[0]['is_mobile_auth']) ? $userData[0]['is_mobile_auth'] : 0;
//         $admin_id = isset($userData[0]['id']) ? $userData[0]['id'] : 0;
//         $mobile_auth_attempt = isset($userData[0]['mobile_auth_attempt']) ? $userData[0]['mobile_auth_attempt'] : '0';
//         if($is_mobile_auth == 1){
//             if($mobile_auth_attempt >= 5) {
//                 return 0;
//             }else{
//                 return 1;
//             }
//         }else{
//             return 1;
//         }
//     }else{
//         return 0;
//     }
// }
    
