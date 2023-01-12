<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB; 

class Settings extends Model {

    protected $table = 'setting';
    
    const ACTIVE = 1;
    const INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'desc', 'value', 'order_by', 'config_type', 'display_type', 'source', 'source_value', 'select_type', 'default_value', 'status','language_input','sub_config_type'
    ];
    public $timestamps = false;



    /**
    * Function to return Status array
    * @return array
    */
    public static function renderStatus()
    {
        return [
            self::ACTIVE => ['label' => 'Active', 'code' => self::ACTIVE], 
            self::INACTIVE => ['label' => 'Inactive', 'code' => self::INACTIVE]
        ];
    }

    /** 
     * Get All Settings Data
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @return array
     */
    public static function getAllSettingsData($where = array(), $orderby = NULL, $orderdir = 'ASC') {
        //$query = DB::table('setting');
        $query = self::select('setting.*');
        if (!empty($where)) {
            $query->where($where);
        }
        if (isset($orderby) && $orderby != "") {
            $query->orderBy($orderby, $orderdir);
        }
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /** Update Settings Single
     * @param string Settings field name
     * @param array Settings Data Array
     * @return array Respose after Update
     */
    public static function updateSettingsByName($field_name, $update_array = array()) {
        return self::where('name', $field_name)->update($update_array);
    }

     /**
     * Function: Get Settings in user's preferred language (For Supplier/Store/DA)
     *
     * @param    array  $where_arr
     * @return   array  $data     
     */
    public static function getSettingData($where_arr = array()) {
        $query = self::from('setting as s');
        $query->select('s.*');
        if(!empty($where_arr)){
            if(isset($where_arr['status']) && $where_arr['status'] != '')
                $query->where('s.status', $where_arr['status']);
            if(isset($where_arr['lang_code']) && $where_arr['lang_code'] != ""){
                $query->leftjoin('setting_lang AS sl', function($settings_lang_join) use($where_arr){
                    $settings_lang_join->on('sl.name', "=", 's.name');
                    $settings_lang_join->where('sl.lang_code', "=", $where_arr['lang_code']);
                    if(isset($where_arr['setting_type']) && $where_arr['setting_type'] != ''){
                        $settings_lang_join->where('sl.setting_type', $where_arr['setting_type']);
                    }
                });                 
                $query->addSelect(DB::raw("(CASE WHEN sl.value IS null OR sl.value = '' THEN s.value ELSE sl.value END) AS value"));
            }
        }
        $data = $query->get()->toArray();
        return $data;
    }

      /** 
     * Get  Settings Data as per where condition wise
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @return array
     */
    public static function getSettingsData($where = array(), $orderby = NULL, $orderdir = 'ASC') {
        //$query = DB::table('setting');
        $query = self::select('setting.*');

        if (!empty($where)) {
            
            $query->whereIn('name',$where);
        }
        if (isset($orderby) && $orderby != "") {
            $query->orderBy($orderby, $orderdir);
        }
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}
