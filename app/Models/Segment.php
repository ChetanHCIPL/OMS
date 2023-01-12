<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $table = 'mas_segment';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE  = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'medium_id', 'semester_id', 'status', 'created_at', 'created_by', 'updated_at'
    ]; 
     
    /**
     * Get the Segment Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Segment data array
     */

    public static function getSegmentData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_segment');
        $query->select('mas_segment.*');

        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', '2');
                }
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['couName']) && $search_arr['couName'] != '')
                $query->Where('name', 'like', ''.$search_arr['couName'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('status', $search_arr['status']);
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
    * Add Segment Single
    * @param array Segment Data Array
    * @return array Respose after insert
    */
    public static function addSegment($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Segment Single
     * @param integer Segment Id
     * @param array Segment Data Array
     * @return array Respose after Update
    */
    public static function updateSegment($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Segment  Status
     * @param array Segment Ids Array
     * @param string Segment Status
     * @return array Respose after Update
    */
    public static function updateSegmentStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
    
    /**
     * Delete Segment  
     * @param array Segment Ids Array
     * @return array Respose after Delete
    */
    public static function deleteSegments($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
    
    /**
     * Get Single Segment data
     * @param int Segment Id 
     * @return array Segment data
    */
    public static function getSegmentDataFromId($id) {
        $query = self::select('mas_segment.*');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_segment.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
 /**
     * Get All Active Segment data 
     * @param int Segment Id 
     * @return array Segment data
    */
    public static function getAllActiveSegmentData() {
        $query = self::select('*')->where(['status' => self::ACTIVE]);;
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
}