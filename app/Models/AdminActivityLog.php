<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
class AdminActivityLog extends Model {

    protected $table = 'admin_activity_log';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'module_id', 'admin_id', 'reference_id', 'no', 'log_text', 'remark', 'added_date_time'
    ];
    public $timestamps = false;

    /**
    * Add Activity Log Single
    * @param array Activity Log Data Array
    * @return array Respose after insert
    */
    public static function addActivityLog($insert_array = array()) {
        return self::create($insert_array);
    }

 
    /**
    * Get the Activity Log Data
    * @param    integer $startlimit (Display Start)
    * @param    integer $endlimit (Display Length)
    * @param    array   $where_arr
    * @return   object  $query
    */
    public static function getActivityLogData($startlimit = null, $endlimit = null, $where_arr){
        //echo "111";exit();
        $query = self::select('admin_activity_log.id', 'admin_activity_log.admin_id', 'admin_activity_log.module_id', 'admin_activity_log.log_text', 'admin_activity_log.added_date_time', 'admin_activity_log.reference_id', 'am.access_module');
        $query->join('admin as a', 'admin_activity_log.admin_id', '=', 'a.id');
        $query->leftjoin('admin_access_module as am', 'am.access_module_id', '=', 'admin_activity_log.module_id');
        $query->orderBy('admin_activity_log.added_date_time', 'DESC');
        if (isset($where_arr['module_id']) && $where_arr['module_id'] != "") {
            $query->Where('admin_activity_log.module_id', $where_arr['module_id']);
        }
        if (isset($where_arr['first_name']) && $where_arr['first_name'] != "") {
            $query->Where('a.first_name', 'like', ''.$where_arr['first_name'].'%');
        }
        if (isset($where_arr['last_name']) && $where_arr['last_name'] != "") {
            $query->Where('a.last_name', 'like', ''.$where_arr['last_name'].'%');
        }
        if (isset($where_arr['username']) && $where_arr['username'] != "") {
            
            $query->Where('a.username', 'like', $where_arr['username'].'%');
        }
        if (isset($where_arr['date_from']) && $where_arr['date_from'] != "") {
            $query->where('admin_activity_log.added_date_time', '>=', $where_arr['date_from']);
        }
        if (isset($where_arr['date_to']) && $where_arr['date_to'] != "") {
            $query->Where('admin_activity_log.added_date_time', '<=', $where_arr['date_to']);
        }
        if (isset($endlimit) && $endlimit != "") {
            $query->limit($endlimit);
        }
        if (isset($startlimit) && $startlimit != "") {
            $query->offset($startlimit);
        }
       // echo $query->toSql();exit();
        return $query;
    }
}
