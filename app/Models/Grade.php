<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'mas_user_grade';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'auto_approve_limit', 'status', 'created_at', 'updated_at', 'created_by',
    ];

    public $timestamps = true;
    
    /**
    * Get the Grade Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array Grade data array
    */

    public static function getGradeData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_user_grade as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                $query->orWhere('auto_approve_limit', 'like', ''.$search.'%');
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
            if(isset($search_arr['auto_approve_limit']) && $search_arr['auto_approve_limit'] != '')
                $query->Where('auto_approve_limit', 'like', ''.$search_arr['auto_approve_limit'].'%');
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
    * Add Grade Single
    * @param array Grade Data Array
    * @return array Respose after insert
    */
    public static function addGrade($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Grade Single
    * @param integer SerGradeies Id
    * @param array Grade Data Array
    * @return array Respose after Update
    */
    public static function updateGrade($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Grade data
     * @param int Grade Id 
     * @return array Grade data
    */
    public static function getGradeDataFromId($id){
        $query = self::select('mas_user_grade.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_user_grade.id' => $id]);
        }
        
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     *  @return array Respose Array
     */
    public static function getAllActiveGradeData() {
        $query = self::select('mas_user_grade.*');
        $query->where(['status' => self::ACTIVE]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Delete Grade
     * @param array Grade Ids Array
     * @return array Respose after Delete
    */
    public static function deleteGradeData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Grade Status
    * @param array Grade Ids Array
    * @param string Grade Status
    * @return array Grade after Update
    */
    public static function updateGradeStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    
}