<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AccessModule extends Model {

	protected $table = 'admin_access_module';
	protected $primaryKey = 'access_module_id';


	/**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'access_module_id', 'parent_id', 'title', 'access_module','list', 'view' ,'add', 'edit', 'delete', 'status', 'export', 'print', 'date_period', 'display_order', 'module_status'
    ];

    /**
         * Get the Access Group Module Id Data
        * @return array Access Group Module Id array
    */
    public static function getModuleIdData(){
        $query =  self::select('admin_access_module.access_module_id')
                        ->where('admin_access_module.module_status' , '=',1)
                        ->orderBy('admin_access_module.parent_id', 'ASC')
                        ->orderBy('admin_access_module.display_order', 'ASC')
                        ->orderBy('admin_access_module.access_module', 'ASC');
        return $result = $query ->get()->toArray();
                              
    }

    /**
    * Get Access Module Count
    * @param integer Access Module Data
    * @return array Data
    */
    public static function getModuleData()
    {
        $query = self::select('*')
                 ->where('module_status','=','1')
                ->orderBy('admin_access_module.parent_id', 'ASC')
                ->orderBy('admin_access_module.display_order', 'ASC')
                ->orderBy('admin_access_module.access_module', 'ASC');

        $result = $query->get()->toArray();
        return $result;

    }   

    /**
    * Get Parent Data of Access Module 
    * @return array Data
    */
    public static function getParentData(){
        return self::where('admin_access_module.parent_id', '>', 0)
                    ->select(DB::raw('DISTINCT parent_id'))
                    ->get()->toArray();
    }


    /**
    * Get the Access Group Module Id Data by Module Id
    * @return array Access Group Module Id array
    */
    public static function getModuleDataFromId($moduleId  = array()){
        $query =  self::select('admin_access_module.access_module_id')
                        ->where('admin_access_module.module_status' , '=',1)
                        ->whereIn('admin_access_module.access_module_id',$moduleId)
                        ->orderBy('admin_access_module.parent_id', 'ASC')
                        ->orderBy('admin_access_module.display_order', 'ASC')
                        ->orderBy('admin_access_module.access_module', 'ASC');
        return $result = $query ->get()->toArray();
                              
    }


    

}