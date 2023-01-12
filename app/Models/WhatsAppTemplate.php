<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class WhatsAppTemplate extends Model {

	protected $table = 'whatsapp_template';
	const ACTIVE = 1;
	const INACTIVE = 2;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'type', 'section', 'status', 'created_at','content', 'updated_at'
	];

	/**
     * Get the WhatsApp Template Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array WhatsApp Template Data array
    **/
    public static function getWhatsAppTemplateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        
        $query = DB::table('whatsapp_template');
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
     * Update WhatsApp Template  Status
     * @param array email Template Ids Array
     * @param string email Template Status
     * @return array Respose after Update
     */
    public static function updateWhatsAppTemplateStatus($id,$status){
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    

    /** 
     * Get WhatsApp Template's data
     * @param string type 
     * @param string section 
     * @return array
    **/
    public static function getWhatsAppTemplate($id) {
        return self::where('id', $id)->get()->toArray();
    }

   	/** Update WhatsApp Template
     * @param integer template Id
     * @param array template Data Array
     * @return array Respose after Update
    **/
    public static function updateWhatsAppTemplate($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get single language and section with Multiple Type WhatsAppTemplate's data
     * @param string type 
     * @param string section 
     * @param string vLangCode 
     * @return array
     */
    public static function getWhatsAppTemplateDataByLangCode($typeArr, $section,$vLangCode = NULL){
        $query = self::from('whatsapp_template as wt')
                ->select('wt.*','wtl.lang_code','wtl.content');
        if($vLangCode != ''){
            $query->where('wtl.lang_code', $vLangCode);
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