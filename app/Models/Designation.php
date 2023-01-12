<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'mas_designation';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     
    /**
    * Add Designation Single/Multiple
    * @param array Designation Data Array
    * @return array Respose after insert
    */
    public static function addDesignation($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Designation Single
     * @param integer Id
     * @param array Designation Data Array
     * @return array Respose after Update
    */
    public static function updateDesignation($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Designation  Status
     * @param array Designation Ids Array
     * @param string Designation Status
     * @return array Designation after Update
    */
    public static function updateDesignationStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Designation Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Designation data array
    */

    public static function getDesignationData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_designation as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['name']) && $search_arr['name'] != '')
                $query->Where('name', 'like', ''.$search_arr['name'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('mi.status', $search_arr['status']);
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
        
        $result = $query->get();

        return $result;
    }

    /**
     * Get Single Designation data
     * @param int Designation Id 
     * @return array Designation data
    */
    public static function getDesignationDataFromId($id) {
        $query = self::select('mas_designation.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_designation.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Designation
     * @param array Designation Ids Array
     * @return array Respose after Delete
    */
    public static function deleteDesignationData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /** Get Active Designation
    * @param integer id
    * @return Array Designation Array
    */
    public static function getAllActiveDesignation($id = NULL){
        if($id != '')
        {   
            $id = explode(',',$id);
            $query = self::select('*');
            if(is_array($id)){

                $query->whereIn('mas_designation.id', $id);
            }else {
               
                $query->where(['mas_designation.id' => $id]);
            } 
            $result =  $query->get()->toArray();

            return $result;

        }else{
            return self::where(['status' =>'1'])->get()->toArray();    
        }
    }
}