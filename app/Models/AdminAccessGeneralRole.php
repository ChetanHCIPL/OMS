<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessGeneralRole extends Model {

    protected $table = 'admin_access_general_role';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'access_group_id', 'access_general_id'
    ];
    public $timestamps = false;

    /**
     * Delete Group General 
     * @param array Access Group Id Array
     * @param array Access Group General Ids Array
     * @return array Respose after Delete
    */
	public static function deleteAccessGeneral($iAGroupId,$iAGeneralId){
		return self::where(['access_group_id' => $iAGroupId])
						->whereIn('access_general_id', $iAGeneralId)
						->delete();
	}
    
	/**
     * Add Group General
     * @param array Group General Data Array
     * @return array Respose after insert
     */
	public static function insertAccessGeneral($insertgen_array){
		return self::create($insertgen_array);
	}

    /*
    * Delete Group General Data
    * 
    * @param array Access Group Id
    * @param array Acess General Id 
    * @return array Respose after Delete
    *
    */
    public static function deleteGroupGeneralData($iAGroupId  = array() , $iAGeneralId = array())
    {
        if($iAGroupId != '' && $iAGeneralId != ''){
            return self::where(['access_group_id' => $iAGroupId])
                        ->whereIn('access_general_id', $iAGeneralId)
                        ->delete();
        }else{
            return self::whereIn('access_group_id', $iAGroupId)->delete();
        }
    }
}
