<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class EmailTemplate extends Model {

    protected $table = 'email_format';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',  'from', 'cc', 'reply_to', 'mime', 'type', 'section', 'status', 'content','created_at' , 'updated_at'
    ];
    public $timestamps = true;

    /**
     * Get the EmailTemplate Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array EmailTemplate Data array
     */
    public static function getEmailTemplateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('email_format');
        $query->select('email_format.*');

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
     * Function to get Email Templates details by Id
     *
     * @param    integer   $id
     * @return   array    
    */
    public static function getEmailTemplate($id) {
        //echo $id;
        return self::where('id', $id)->get()->toArray();
    }
    
    /** Update EmailTemplate Single
     * @param integer $id
     * @param array EmailTemplate Data Array
     * @return object|bool
     */
    public static function updateEmailTemplate($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }


    /** insert  EmailTemplate Single 
     * @param array EmailTemplate Data Array
     * @return object|bool
     */
    public static function insertEmailTemplate($insert_array = array()) {
        return self::create($insert_array);
    } 


    /**
     * Get single section with Multiple Type EmailTemplate's data
     * @param string type 
     * @param string section 
     * @return array
     */
    public static function getEmailTemplatTypesData($typeArr, $section,$vLangCode = NULL) {
        $query = self::with(array
                ('email_language' => function($query) use ($vLangCode)
                    {
                        if($vLangCode != ''){
                            $query->where('lang_code', $vLangCode);
                        }
                    }
                ));
        if(is_array($typeArr)){
            $query->whereIn('type',$typeArr);
        }else{
            $query->where('type',$typeArr);
        }
        return $query->where('section',$section)->where('status',1)->get()->toArray();
    }

    /**
     * Get single language and section with Multiple Type EmailTemplate's data
     * @param string type 
     * @param string section 
     * @param string vLangCode 
     * @return array
     */
    public static function getEmailTemplateDataByLangCode($typeArr, $section,$vLangCode = NULL){
        $query = self::from('email_format as ef')
                ->select('ef.*','efl.lang_code','efl.from_name','efl.reply_to_name','efl.subject','efl.body');
        if($vLangCode != ''){
            $query->where('efl.lang_code', $vLangCode);
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

    
    /** Update EmailTemplate status
     * @param integer $id
     * @param array EmailTemplate Data Array
     * @return object|bool
     */
    public static function updateEmailTemplateStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
}