<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;

class AccessGroupModuleRole extends Model {

	protected $table = 'admin_access_group_module_role';
	protected $primaryKey = 'id';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','access_module_id', 'access_group_id', 'list', 'view', 'add', 'edit', 'delete', 'status' ,'export', 'print', 'date_period'
    ];

    /*
	* Get Access Group Module Role Data
	* 
	* @param integer Access Group Id
    * @param array Access Group  Moudule Id 
    * @return array Respose after Update
    *
    */
    public static function getGroupModuleData($iAGroupId,$iAModuleId)
    {
        //if($iAGroupId != ""){
            $query = self::select('*')
                    ->where('access_group_id',$iAGroupId)
                    ->whereIn('access_module_id',$iAModuleId);
       /* } else {
            $query = self::select('*')
                    ->whereIn('access_module_id',$iAModuleId);
        }*/
    	
    	$result= $query->get()->toArray();
    	return $result;
    }

    /*
	* Add Data in Access Group Module Role
	* 
    * @param array Access Group  Moudule Role Data 
    * @return array Respose after insert
    *
    */
    public static function insertGroupModule($array = array() )
    {
    	return self::insert($array);
    }
    /*
	* Delete  Access Group Module Role Data
	* 
	* @param array Access Group Id
    * @param array Access Group Moudule Id 
    * @return array Respose after Delete
    *
    */
    public static function deleteGroupModuleData($iAGroupId)
    {  
    	if(is_array($iAGroupId)){
            return self::whereIn('access_group_id' ,$iAGroupId)->delete();
        }else {

            return self::where(['access_group_id' => $iAGroupId])->delete();
        }
		
    }
}
?>


 