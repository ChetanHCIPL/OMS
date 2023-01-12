<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLogDetail extends Model {

    protected $table = 'admin_activity_log_detail';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'activity_log_id', 'data'
    ];
    public $timestamps = false;

    ## Add Activity Log Detail
    public static function addActivityLogDetail($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Get the Activity Log Detail Data
    * @param integer Activity Log Id
    * @return array  Activity Log Detail Data
    */
    ## get Activity Log Detail from specific id
    public static function getActivityLogDetailFromId($id) {
        $query = self::select('admin_activity_log_detail.*')->where(['activity_log_id' => $id]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

}
