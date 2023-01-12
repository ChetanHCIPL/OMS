<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $table = 'mas_series';
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
     * Get Series data
     * @param array Series ID passed
     * @return array Respose Series Array
     */
    public static function getSeriesDataList($series_id = NULL) {
        $query = DB::table('mas_series');
        
        $query->select('id','name');
        if ($series_id){
            $query->where(['id' => $medium_id]);
        }
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get the Series Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Series data array
     */

    public static function getSeriesData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_series as mi');
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
    * Add Series Single
    * @param array Series Data Array
    * @return array Respose after insert
    */
    public static function addSeries($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Series Single
    * @param integer Series Id
    * @param array Series Data Array
    * @return array Respose after Update
    */
    public static function updateSeries($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Series data
     * @param int Series Id 
     * @return array Series data
    */
    public static function getSeriesDataFromId($id){
        $query = self::select('mas_series.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_series.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Series
     * @param array Series Ids Array
     * @return array Respose after Delete
    */
    public static function deleteSeriesData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Series Status
    * @param array Series Ids Array
    * @param string Series Status
    * @return array Series after Update
    */
    public static function updateSeriesStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    

    /**
     * Get All Active Series data
     * @param   array   $where_arr
     * @return  array   $result
    */
    public static function getAllActiveSeries($where_arr=array()) {
        $query = self::from('mas_series AS ms');
        $query->select('ms.id','ms.name');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('ms.status', $where_arr['status']);
        }
        $result = $query->orderBy('ms.name', 'ASC')->get()->toArray();
        return $result;
    }
}