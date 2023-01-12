<?php

namespace App\Http\Controllers\Admin\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Settings;
use Illuminate\Support\Facades\Response;
use Config;
use Image;
use File;
use Illuminate\Support\Facades\Input;

class SettingsController extends Controller {

    public function __construct(){
        $this->middleware('accessrights');
        $this->setting_display_type = Config::get('constants.setting_display_type');
    }


    /**
    * Function  to Load view for Site Setting
    *
    * @param    void
    * @return   view
    */
    public function siteSetting() {
        $language = $data_lang =$data= $setting_config_type =$settings_language=  $site_data_lang = $setting_display_type = array();
        $setting_data_lang  = array();

        $setting_config_type = Config::get('constants.setting_config_type');
        $setting_display_type = Config::get('constants.setting_display_type');

        $data = Settings::getAllSettingsData(['status' => '1'], $orderby = 'order_by', $orderdir = 'ASC');

        $cnt_data = count($data);
        for($i=0;$i<$cnt_data;$i++){
            if($data[$i]['language_input'] == '1'){
                $setting_lang_data[$data[$i]['config_type']][] =$data[$i];
                //if language 1 set config id in setting_data_lang

            }
            if(!empty($data[$i]['settings_language'])){
                foreach($data[$i]['settings_language'] as $rowLanguage){
                    if ($rowLanguage['setting_type'] == $data[$i]['config_type']) {
                        $settings_language[$setting_config_type[$data[$i]['config_type']]['name']][$rowLanguage['name']][$rowLanguage['lang_code']] = (isset($rowLanguage['value'])?$rowLanguage['value']:"");
                    } 
                }
            }

        }        
        return view('admin/tools/site_settings')->with(['fields'=>$data, 'setting_config_type' => $setting_config_type, 'setting_display_type' => $setting_display_type]);
    }


    /**
    * Function  toSave Site Setting Data
    *
    * @param   array $request
    * @return  json $records
    */
    public function saveSiteSetting(Request $request) {
        
        $records = $records["data"] = $configArr=$settings_language= array();
        $requestData =$request->All();
        //p($requestData);
        if (isset($requestData["groupActionName"]) && $requestData["customActionType"] == "group_action") {
            $mode = $requestData["groupActionName"];

            if ($mode == "Update") {
                $where = ['status' => '1'];
                $db_setting_rs = Settings::getAllSettingsData($where, $orderby = 'order_by', $orderdir = 'ASC');

                $n = count($db_setting_rs);
                for($i=0;$i<$n;$i++){
                    if(!empty($db_setting_rs[$i]['settings_language'])){
                        foreach($db_setting_rs[$i]['settings_language'] as $rowLanguage){
                            if ($rowLanguage['setting_type'] == $db_setting_rs[$i]['config_type']) {
                                $settings_language[$db_setting_rs[$i]['config_type']][$rowLanguage['name']][$rowLanguage['lang_code']] = (isset($rowLanguage['value'])?$rowLanguage['value']:"");
                            } 
                        }
                    }
                }

                for ($i = 0; $i < $n; $i++) {
                    $field_name = $db_setting_rs[$i]["name"];
                    $field_desc = $db_setting_rs[$i]["desc"];
                    $default_value = $db_setting_rs[$i]["default_value"];
                    $config_type =$db_setting_rs[$i]["config_type"];
                    $display_type = $db_setting_rs[$i]["display_type"] ;

                        if ($this->setting_display_type[$db_setting_rs[$i]["display_type"]] == 'selectbox') {
                            if (is_array($requestData["$field_name"])) {
                                $value = implode("|", $requestData["$field_name"]);
                            } else {
                                $value = $requestData["$field_name"];
                            }
                        } else if($this->setting_display_type[$db_setting_rs[$i]["display_type"]] == 'checkbox') {
                            if (isset($requestData["$field_name"]) && $requestData["$field_name"] != "")
                                $value = "Y";
                            else
                                $value = "N";
                        }else {
                            $value = $requestData["$field_name"];
                        }
                        if ($value != "" && $value != "-9") {
                            $value = $value;
                        } else {
                            $value = $default_value;
                        }

                        $update_array = array(
                            'value' => trim($value),
                        );

                        $db_update = Settings::updateSettingsByName(trim($field_name), $update_array);
                        
                        ## Create config array to set variables in settings.php
                        if (isset($db_update)) {
                            if($db_setting_rs[$i]["language_input"] != 1){
                                $configArr[$field_name] = $value;
                            }else{
                                $configArr[$field_name]['default'] = $value;
                            }
                        }
                        //if field name language data available
                        if(!empty($requestData[$config_type][$field_name])){
                            foreach ($requestData[$config_type][$field_name] as $langcode => $langvalue) {

                               if(isset($settings_language[$config_type][$field_name][$langcode]))
                               {    #if langcode available for field_name  in settings_language     

                                    if ($this->setting_display_type[$display_type] == 'selectbox') {
                                        if (is_array($langvalue)) {
                                                $value = implode("|", $langvalue);
                                        } else {
                                            $value = $langvalue;
                                        }
                                    } else if($this->setting_display_type[$display_type] == 'checkbox') {
                                        if (isset($langvalue) && $langvalue != "")
                                            $langvalue = "Y";
                                        else
                                            $langvalue = "N";
                                    }else {
                                        $value = $langvalue;
                                    }
                                    if ($value != "" && $value != "-9") {
                                        $value = $value;
                                    } else {
                                        $value = $default_value;
                                    }

                                    $update_array = array(
                                        'value' => trim($value),
                                    );

                                    $where= ['name'=>trim($field_name),'lang_code'=>trim($langcode)];


                               }

                                if (isset($db_update)) {
                                    $configArr[$field_name][$langcode] = $value;
                                       
                                }
                            }
                        }
                }
                
                ## Set Settings - Config/settings.php file
                if(!empty($configArr)){
                    $config_arr = var_export($configArr, 1);
                    if(File::put(base_path() . '/config/settings.php', "<?php\n return $config_arr ;")) {
                        $result_arr[0]['isError'] = 0;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_UPDATE');
                    }else{
                        $result_arr[0]['isError'] = 1;
                        $result_arr[0]['msg'] = Config::get('messages.msg.MSG_UPDATE_ERROR');
                    }
                }else{
                    $result_arr[0]['isError'] = 1;
                    $result_arr[0]['msg'] = Config::get('messages.msg.MSG_UPDATE_ERROR');
                }
                echo json_encode($result_arr);
                exit;
            }
            $records["customActionMessage"] = '';
        }
        echo json_encode($records);
    }

}
