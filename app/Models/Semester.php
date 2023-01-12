<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'mas_semester';
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
     * Get Semester data
     * @param array Semester ID passed
     * @return array Respose Semester Array
     */
    public static function getSemesterDataList($semester_id = NULL) {
        $query = DB::table('mas_semester');
        
        $query->select('id','name');
        if ($semester_id){
            $query->where(['id' => $medium_id]);
        }
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get the Semester Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Semester data array
     */

    public static function getSemesterData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_semester as mi');
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
    * Add Semester Single
    * @param array Semester Data Array
    * @return array Respose after insert
    */
    public static function addSemester($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Semester Single
    * @param integer Semester Id
    * @param array Semester Data Array
    * @return array Respose after Update
    */
    public static function updateSemester($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Semester data
     * @param int Semester Id 
     * @return array Semester data
    */
    public static function getSemesterDataFromId($id) {
        $query = self::select('mas_semester.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_semester.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
   
    /**
     * Delete Semester
     * @param array Semester Ids Array
     * @return array Respose after Delete
    */
    public static function deleteSemesterData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Semester Status
    * @param array Semester Ids Array
    * @param string Semester Status
    * @return array Semester after Update
    */
    public static function updateSemesterStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
     
}