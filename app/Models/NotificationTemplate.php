<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class NotificationTemplate extends Model {

	protected $table = 'notification_template';
	const ACTIVE = 1;
	const INACTIVE = 2;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'type', 'section', 'status', 'created_at', 'updated_at'
	];

	/**
     * Get the Notification Template Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Notification Template Data array
    **/
    public static function getNotificationTemplateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        
        $query = DB::table('notification_template');
        $query->select('*');
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('type', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', '2');
                }
            });
        }
        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['search_type']) && $search_arr['search_type'] != '')
                $query->Where('type', 'like', ''.$search_arr['search_type'].'%');
            if(isset($search_arr['search_status']) && $search_arr['search_status'] != '')
                $query->Where('status', $search_arr['search_status']);
            if(isset($search_arr['sectionid']) && $search_arr['sectionid'] != ''){
                $query->Where('section', $search_arr['sectionid']);            
            }
        }
        if (isset($iDisplayLength) && $iDisplayLength != "") {
            $query->limit($iDisplayLength);
        }
        if (isset($iDisplayStart) && $iDisplayStart != "") {
            $query->offset($iDisplayStart);
        }
        if (isset($sort) && $sort != "" && isset($sortdir) && $sortdir != "") {
            $query->orderBy($sort, $sortdir);
        }
        $result = $query->get();
        return $result;
    }

    /**
     * Update Notification Template  Status
     * @param array email Template Ids Array
     * @param string email Template Status
     * @return array Respose after Update
     */
    public static function updateNotificationTemplateStatus($id,$status){
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /** 
     * Get the languages associated with the id.
    **/
    public function notification_language()
    {
        return $this->hasMany('App\Models\NotificationTemplateLanguage', 'notification_template_id', 'id');
    }

    /** 
     * Get Notification Template's data
     * @param string type 
     * @param string section 
     * @return array
    **/
    public static function getNotificationTemplate($id) {
        return self::where('id', $id)->with('notification_language')->get()->toArray();
    }

   	/** Update Notification Template
     * @param integer template Id
     * @param array template Data Array
     * @return array Respose after Update
    **/
    public static function updateNotificationTemplate($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get single language and section with Multiple Type NotificationTemplate's data
     * @param string type 
     * @param string section 
     * @param string vLangCode 
     * @return array
     */
    public static function getNotificationTemplateDataByLangCode($typeArr,$section,$vLangCode = NULL){
        $query = self::from('notification_template as nt')
                ->select('nt.*','ntl.lang_code','ntl.content','ntl.title')
                ->join('notification_template_language as ntl','ntl.notification_template_id','=','nt.id');
        if($vLangCode != ''){
            $query->where('ntl.lang_code', $vLangCode);
        }
        if(is_array($typeArr)){
            $query->whereIn('type',$typeArr);
        }else{
            $query->where('type',$typeArr);
        }
        if(is_array($section)){
            $query->whereIn('section',$section);
        }else{
            $query->where('section',$section);
        }
        return $query->where('status',1)->get()->toArray();
    }
}
?>