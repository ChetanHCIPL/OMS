<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Search extends Authenticatable {

    protected $table = 'app_search_keywords';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'keyword','total_search', 'created_at','updated_at'
    ];
 
    public $timestamps = false;

    /**
     * Get the App Search Keyword Data
     * @return array App Search Keyword Data
     */

    public static function getAppSearchKeywordData($where) {

        $query = self::select('app_search_keywords.*');
        
        if (isset($where['DisplayLength']) && $where['DisplayLength'] != "") {
            $query->limit($where['DisplayLength']);
        }
        if (isset($where['iDisplayStart']) && $where['iDisplayStart'] != "") {
            $query->offset($where['iDisplayStart']);
        }
        if (isset($where['sort']) && $where['sort'] != "" && isset($where['sortdir']) && $where['sortdir'] != "") {
            $query->orderBy($where['sort'], $where['sortdir']);
        }
        $result = $query->get()->toArray();;
        return json_decode(json_encode($result), true); 
    }

    /**  
     * Add App Search Keyword Single
     * @param array App Search Keyword Data Array
     * @return array Respose after insert
    */
    public static function addAppSearchKeyword($insert_array = array()) {
        return self::insert($insert_array);
    }

    /** Update App Search Keyword Single
     * @param integer  Id
     * @param array App Search Keyword Data Array
     * @return array Respose after Update
    */
    public static function updateAppSearchKeyword($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
       
    /** 
     * Delete App Search Keyword 
     * @param array  App Search Keyword Ids Array
     * @return array Respose after Delete
    */
    public static function deleteAppSearchKeyword($id = array()) {
        if(is_array($id)){
            return self::whereIn('id', $id)->delete();
        }else {
           return self::where(['id' => $id])->delete();
        }
    }

    /**
     * Get the App Search Keyword Data
     * @return array App Search Keyword Data
     */

    public static function getExistingKeywordByKeyword($keyword) {

        $query = self::select('app_search_keywords.*');
        $query->where("keyword","=",$keyword);        
        $query->limit(1);
        $result = $query->get()->toArray();;
        return json_decode(json_encode($result), true); 
    }

    /**
     * Add keyword Single
     * @param array keyword Data Array
     * @return array Respose after insert
     */
    public static function addSearchedKeyword($insert_array = array()) {
        return self::insert($insert_array);
    }

     /**
     * Trucate keyword table
     */
    public static function truncateTable() 
    {
        return DB::table('app_search_keywords')->truncate();
    }
}
?>