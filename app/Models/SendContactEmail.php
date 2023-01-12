<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SendContactEmail extends Model
{
	protected $table = 'app_contact_email_log'; //app_contact_us_email_log
    protected $primaryKey = 'id';

    const OPENED = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'contact_id', 'member_id', 'email_to', 'email_from', 'email_subject', 'email_message', 'status', 'created_at'
    ];
    public $timestamps = false;

    /**
     * Get the contact us email log data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array  data array
     */
    public static function getcontactMailLogsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {

        $query = DB::table('app_contact_email_log');
        $query->select('app_contact_email_log.*',DB::raw("CONCAT(m.first_name,' ',m.last_name,' ( ',m.mobile_number,' ) ' ) AS member_name"));
        $query->leftjoin('member AS m', 'app_contact_email_log.member_id', '=', 'm.id');
        // $query->Where('app_contact_email_log.member_id', '=', $member_id);

         if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('app_contact_email_log.email_subject', 'like', $search.'%');   
                $query->orWhere('app_contact_email_log.email_message', 'like', $search.'%');   
                $query->orWhere('app_contact_email_log.email_from', 'like', $search.'%');   
                $query->orWhere('app_contact_email_log.email_to', 'like', $search.'%');   
                $query->orWhere('app_contact_email_log.created_at', 'like', '%'.date('Y-m-d',strtotime($search)).'%');
                if (strtolower($search) == 'open' || strtolower($search) == 'opened') {
                    $query->orWhere('app_contact_email_log.status', '=', '1');
                } else if (strtolower($search) == 'closed') {
                    $query->orWhere('app_contact_email_log.status', '=', '2');
                } else if (strtolower($search) == 'cancelled') {
                    $query->orWhere('app_contact_email_log.status', '=', '3');
                }
            });
        }
        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['member']) && $search_arr['member'] != '')
                $query->Where('m.id', '=' , $search_arr['member']);
             if(isset($search_arr['created_at']) && $search_arr['created_at'] != '')
             $query->Where('app_contact_email_log.created_at', 'like', '%'.date('Y-m-d',strtotime($search_arr['created_at'])).'%');

            if(isset($search_arr['contact_id']) && $search_arr['contact_id'] != '')
                $query->Where('app_contact_email_log.contact_id', '=' , $search_arr['contact_id']);
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


    /** Update Email Log Single
     * @param integer  Id
     * @param array Email Log Data Array
     * @return array Respose after Update
    */
    //updateContactEmailLog
    public static function updateContactEmailLog($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

   /** 
     * Add Contact Us Single
     * @param array Contact Us Data Array
     * @return array Respose after insert
     */
   //addContactEmailLog
    public static function addContactEmailLog($insert_array = array()) {
        return self::create($insert_array);
    }

    /** 
     * Get Email logs from contact Id
     * @param int Id
     * @return array data
     */
    public static function getPreviousEmailDetails($id){
        $query = DB::table('app_contact_email_log');
        $query->select('app_contact_email_log.*');
        $query->where('app_contact_email_log.contact_id','=',$id);
        $query->orderBy('app_contact_email_log.id', 'DESC');
        // $query->limit($limit);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}