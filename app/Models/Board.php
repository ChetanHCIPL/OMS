<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'mas_board';
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
    * Add Board Single/Multiple
    * @param array Board Data Array
    * @return array Respose after insert
    */
    public static function addBoard($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Board Single
     * @param integer Id
     * @param array Board Data Array
     * @return array Respose after Update
    */
    public static function updateBoard($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Board  Status
     * @param array Board Ids Array
     * @param string Board Status
     * @return array Board after Update
    */
    public static function updateBoardStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Board Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Board data array
    */

    public static function getBoardData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_board as mi');
        $query->select('mi.*',DB::raw("count(m.id) as mediums"));
        
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

        $query->leftjoin('mas_medium as m', 'm.board_id', '=', 'mi.id');

        if (isset($iDisplayLength) && $iDisplayLength != "") {
            $query->limit($iDisplayLength);
        }
        if (isset($iDisplayStart) && $iDisplayStart != "") {
            $query->offset($iDisplayStart);
        }
        if (isset($sort) && $sort != "" && isset($sortdir) && $sortdir != "") {
            $query->orderBy($sort, $sortdir);
        }

        
        $query->groupBy('mi.id');
        
        $result = $query->get();
    
        return $result;
    }

    /**
     * Get Single Board data
     * @param int Board Id 
     * @return array Board data
    */
    public static function getBoardDataFromId($id) {
        $query = self::select('mas_board.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_board.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get Single Board data
     * @param int Board Id 
     * @return array Board data
    */
    public static function getListBoardData() {
        $query = self::select('mas_board.*');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Board
     * @param array Board Ids Array
     * @return array Respose after Delete
    */
    public static function deleteBoardData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
}