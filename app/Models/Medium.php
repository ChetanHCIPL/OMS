<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
    protected $table = 'mas_medium';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','board_id', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     
    /**
    * Add Medium Single
    * @param array Medium Data Array
    * @return array Respose after insert
    */
    public static function addMedium($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Medium Single
     * @param integer Medium Id
     * @param array Medium Data Array
     * @return array Respose after Update
    */
    public static function updateMedium($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Medium  Status
     * @param array Medium Ids Array
     * @param string Medium Status
     * @return array Medium after Update
    */
    public static function updateMediumStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Medium Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Medium data array
     */

    public static function getMediumData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_medium as mi');
        $query->join('mas_board as mb', 'mi.board_id', '=', 'mb.id');
        $query->select('mi.*','mb.name as bname');
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
            if(isset($search_arr['board_id']) && $search_arr['board_id'] != '')
                $query->Where('mi.board_id', $search_arr['board_id']);
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
     * Get Single Medium data
     * @param int Medium Id 
     * @return array Medium data
    */
    public static function getMediumDataFromId($id) {
        $query = self::select('mas_medium.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_medium.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Medium
     * @param array Medium Ids Array
     * @return array Respose after Delete
    */
    public static function deleteMediumData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
     * Get Medium data
     * @param array Medium ID passed
     * @return array Respose Medium Array
     */
    public static function getMediumDataList($medium_id = NULL) {
        $query = DB::table('mas_medium');
        
        $query->select('id','name');
        if ($medium_id){
            $query->where(['id' => $medium_id]);
        }
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Medium data with Board Name
     * @param int Medium ID passed
     * @return array Respose Medium Array
     */
    public static function getMediumDataWithBoad($medium_id = NULL) {
        $query = DB::table('mas_medium AS mm');
        
        $query->select('mm.id', 'mm.name', 'mb.name as board_name');
        $query->join('mas_board as mb', 'mm.board_id', '=', 'mb.id');
        if ($medium_id){
            $query->where(['mm.id' => $medium_id]);
        }
        $query->where(['mm.status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Medium vise Board 
     */
    public function getMediumBoard(){
        $query = self::from('mas_medium AS mm');
        $query->leftJoin('mas_board AS mb', 'mb.id', '=', 'mm.board_id');
        
        $query->select('mm.id', 'mm.name', 'mb.name as board_name');
        $query->where(['mb.status' => self::ACTIVE]);
        // $query->groupBy('mm.id');
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Medium data with board id
     * @param array Medium ID passed
     * @return array Respose Medium Array
     */
    public static function getMediumDataFromBoardId($board_id = NULL) {
        $query = DB::table('mas_medium');
        
        $query->select('id','name');
        if (isset($board_id) && is_array($board_id)){
            $query->whereIn('board_id', $board_id);
        }else{
            $query->where(['board_id' => $board_id]);
        }
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return $result;
    }
}