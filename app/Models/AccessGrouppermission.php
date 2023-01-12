<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccessGrouppermission extends Model {

    protected $table = 'admin_access_group_permission';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'admin_id', 'access_group_id'
    ];
    public $timestamps = false;

    /**
    * Get Admin Count
    * @param integer Access Group Id
    * @return integer Admin Count
    */
	public static function getAdminCount($iAGroupId)
    {
		return self::where('access_group_id', $iAGroupId)->count('id');
	}

    /**
    *
    * User Roles count with Admin user existed or not
    * Count Admin access group permission with user existed or not
    * @param integer Access Group Id
    * @return integer Admin count
    */
    public static function getAdminCountWithAdminChecking($iAGroupId)
    {
        $query = self::where('access_group_id', $iAGroupId);
        $query->rightJoin('admin', 'admin_access_group_permission.admin_id', '=', 'admin.id');
        return $query->count('admin_access_group_permission.id');
    }
    /** 
    * delete accessgroup
    *
    */
    public static function accessGroupDelete($id)
    {
        if(is_array($id)){
            return self::whereIn('admin_id',$id)->delete();
        }else{
            return self::where('admin_id','=',$id)->delete();
        }
    }

    /**
    * Add Access Group Premission  Single
    * @param array Access Grouppermission Data Array
    * @return array Respose after insert
    */
    public static function addAccessGrouppermission($insert_array = array()) {
        return self::insert($insert_array);
    }
}   