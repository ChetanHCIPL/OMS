<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class LoginLog extends Model {

    protected $table = 'login_log';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'ip', 'login_date', 'logout_date'
    ];
    public $timestamps = false;
    

    /**
     * Get the Login Log Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Login Log Data array
     */
    public static function getLoginLogData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()){

            $query = DB::table('login_log as  ll');
            $query->select('ll.*', DB::raw('CONCAT(a.first_name, " ", a.last_name) AS name'));

            if (isset($search) && $search != "") {
                $query->orWhere('a.first_name', 'like', $search.'%');
                $query->orWhere('a.last_name', 'like', $search.'%');
                $query->orWhere('ll.ip', 'like', $search.'%');
            }

            if(isset($search_arr) && count($search_arr) > 0){
                if(isset($search_arr['name']) && $search_arr['name'] != '')
                    $query->where(DB::Raw('CONCAT(first_name, " ", last_name)'), 'like', '' .$search_arr['name']. '%'); 
                if(isset($search_arr['ip']) && $search_arr['ip'] != '')
                   $query->where('ll.ip', 'like', '' .$search_arr['ip']. '%');            

                if (isset($search_arr['login_date_from']) && $search_arr['login_date_from'] != "") {
                    $login_date_from = date('Y-m-d', strtotime($search_arr['login_date_from'])) . " " . "00:00:01";
                    $query->where('ll.login_date', '>=', $login_date_from);
                }
                if (isset($search_arr['login_date_to']) && $search_arr['login_date_to'] != "") {
                    $login_date_to = date('Y-m-d', strtotime($search_arr['login_date_to'])) . " " . "23:59:59";
                    $query->where('ll.login_date', '<=', $login_date_to);
                }            
                if (isset($search_arr['logout_date_from']) && $search_arr['logout_date_from'] != "") {
                    $logout_date_from = date('Y-m-d', strtotime($search_arr['logout_date_from'])) . " " . "00:00:01";
                    $query->where('ll.logout_date', '>=', $logout_date_from);
                }
                if (isset($search_arr['logout_date_to']) && $search_arr['logout_date_to'] != "") {
                    $logout_date_to = date('Y-m-d', strtotime($search_arr['logout_date_to'])) . " " . "23:59:59";
                    $query->where('ll.logout_date', '<=', $logout_date_to);
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

            $query->leftjoin('admin AS a', 'll.admin_id', '=', 'a.id');
            $result = $query->get()->toArray();
            return $result;
    }


    /** 
     * Delete Login log  
     * @param array Login log Ids Array
     * @return array Respose after Delete
     */
    public static function deleteLoginlogData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
     * Update Login log Single
     * @param integer Login log Id
     * @param array Login log Data Array
     * @return array Respose after Update
     */
    public static function updateLoginLog($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

}
