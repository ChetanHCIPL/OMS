<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ContactUs extends Model
{
	protected $table = 'app_contact_us';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'question_type', 'message', 'created_at'
    ];
    public $timestamps = false;

    /**
     * Get the contact us Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array  data array
     */
    public static function getContactUsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {

    	$query = DB::table('app_contact_us');
        $query->select('app_contact_us.*',DB::raw("CONCAT(m.first_name,' ',m.last_name,' ( ',m.mobile,' ) ' ) AS member_name"));
        $query->leftjoin('admin AS m', 'app_contact_us.user_id', '=', 'm.id');
         if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('m.first_name', 'like', $search.'%');
                $query->orWhere('m.last_name', 'like', $search.'%');                
                $query->orWhere('m.mobile', 'like', $search.'%');                
                $query->orWhere('question_type', 'like', $search.'%');                
                $query->orWhere('message', 'like', $search.'%');   
                $query->orWhere('app_contact_us.created_at', 'like', '%'.date('Y-m-d',strtotime($search)).'%');
                if (strtolower($search) == 'open' || strtolower($search) == 'opened') {
                    $query->orWhere('app_contact_us.status', '=', '1');
                } else if (strtolower($search) == 'closed') {
                    $query->orWhere('app_contact_us.status', '=', '2');
                } else if (strtolower($search) == 'cancelled') {
                    $query->orWhere('app_contact_us.status', '=', '3');
                }
            });
        }
        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['member']) && $search_arr['member'] != '')
                $query->Where('m.id', '=' , $search_arr['member']);
            if(isset($search_arr['question_type']) && $search_arr['question_type'] != '')
                $query->Where('app_contact_us.question_type', '=' , $search_arr['question_type']);
             if(isset($search_arr['created_at']) && $search_arr['created_at'] != '')
             $query->Where('app_contact_us.created_at', 'like', '%'.date('Y-m-d',strtotime($search_arr['created_at'])).'%');
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
        
        $result = $query->get()->toArray();
        return $result;

   }

   /** 
     * Add Contact Us Single
     * @param array Contact Us Data Array
     * @return array Respose after insert
     */
    public static function addContactUs($insert_array = array()) {
        return self::create($insert_array);
    }

     /** Update Contact Single
     * @param integer  Id
     * @param array Contact Data Array
     * @return array Respose after Update
    */
    public static function updateContactUs($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }


    /** 
     * Get Contact Us  Single Data By Id
     * @param int Id
     * @return array data
     */
    public static function getContactUsDataFromId($id){
        $query = DB::table('app_contact_us');
        $query->select('app_contact_us.*','m.email as email_to',DB::raw("CONCAT(m.first_name,' ',m.last_name,' ( ',m.mobile_number,' ) ' ) AS member_name"));
        $query->leftjoin('member AS m', 'app_contact_us.member_id', '=', 'm.id');
        $query->where('app_contact_us.id','=',$id);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /** 
     * Delete contact us 
     * @param int | array Id
     * @return response after delete
    **/
    public static function deleteContactUs($id){
        if(is_array($id)){
            return self::whereIn('id',$id)->delete();
        } else {
            return self::where('id',$id)->delete();
        }
    }

    
}