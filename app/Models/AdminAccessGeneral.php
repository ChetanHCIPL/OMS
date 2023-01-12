<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessGeneral extends Model {

    protected $table = 'admin_access_general';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'code', 'name', 'status'
    ];
    public $timestamps = false;


    /**
    * Get the General Data
    * @param integer iAGroupId
    * @return array General Data
    */
	public static function getGeneralData($iAGroupId){
		return self::from('admin_access_general AS aag')
							  ->where('aag.status', 1)
							  ->select('aag.id','aag.name','agr.access_group_id')
							  ->leftjoin('admin_access_general_role AS agr', function($join) use($iAGroupId){
								$join->on('agr.access_general_id', '=', 'aag.id');
								$join->where('agr.access_group_id', $iAGroupId);
							})->get()->toArray();
	}

	/**
    * Get the General Id
    * @return array General Id Data
    */
	public static function getGeneralId(){
		return self::where(['status' => 1])->select('id')->get()->toArray();
	}
}
?>