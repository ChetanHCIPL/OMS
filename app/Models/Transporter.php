<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    protected $table = 'mas_transporter';
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
    * Add Product Head Single
    * @param array Product Head Data Array
    * @return array Respose after insert
    */
    public static function addTransporter($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Product Head Single
     * @param integer Product Head Id
     * @param array Product Head Data Array
     * @return array Respose after Update
    */
    public static function updateTransporter($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Product Head Status
     * @param array Product Head Ids Array
     * @param string Product Head Status
     * @return array Product Head after Update
    */
    public static function updateTransporterStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Product Head Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Product Head data array
     */

    public static function getTransporterData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_transporter as mi');
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
     * Get Single Product Head data
     * @param int Medium Id 
     * @return array Medium data
    */
    public static function getTransporterDataFromId($id) {
        $query = self::select('mas_transporter.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_transporter.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Product Head
     * @param array Product Head Ids Array
     * @return array Respose after Delete
    */
    public static function deleteTransporterData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
     * Return Transporters List.
     * @return mixed
     */
    public static function getAllTransporters() {
        $query = self::select('mas_transporter.*');
        $query->where(['mas_transporter.status' => self::ACTIVE]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}