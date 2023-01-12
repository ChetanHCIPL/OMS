<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class AuditStockReason extends Model
{
    protected $table = 'mas_audit_stock_reason';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     
    /**
    * Add AuditStockReason Single/Multiple
    * @param array AuditStockReason Data Array
    * @return array Respose after insert
    */
    public static function addAuditStockReason($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update AuditStockReason Single
     * @param integer Id
     * @param array AuditStockReason Data Array
     * @return array Respose after Update
    */
    public static function updateAuditStockReason($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update AuditStockReason  Status
     * @param array AuditStockReason Ids Array
     * @param string AuditStockReason Status
     * @return array AuditStockReason after Update
    */
    public static function updateAuditStockReasonStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the AuditStockReason Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array AuditStockReason data array
    */

    public static function getAuditStockReasonData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_audit_stock_reason as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('title', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['title']) && $search_arr['title'] != '')
                $query->Where('title', 'like', ''.$search_arr['title'].'%');
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
     * Get Single AuditStockReason data
     * @param int AuditStockReason Id 
     * @return array AuditStockReason data
    */
    public static function getAuditStockReasonDataFromId($id) {
        $query = self::select('mas_audit_stock_reason.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_audit_stock_reason.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete AuditStockReason
     * @param array AuditStockReason Ids Array
     * @return array Respose after Delete
    */
    public static function deleteAuditStockReasonData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
}