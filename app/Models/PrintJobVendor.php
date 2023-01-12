<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class PrintJobVendor extends Model
{
    protected $table = 'mas_book_press';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true;
    
    /**
    * Get the Print Job Vendor Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array Print Job Vendor data array
    */

    public static function getPrintJobVendorData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_book_press as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                $query->orWhere('address', 'like', '%'.$search.'%');
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
            if(isset($search_arr['address']) && $search_arr['address'] != '')
                $query->Where('mi.address', $search_arr['address']);
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
    * Add Print Job Vendor Single
    * @param array Print Job Vendor Data Array
    * @return array Respose after insert
    */
    public static function addPrintJobVendor($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Print Job Vendor Single
    * @param integer Print Job Vendor Id
    * @param array Print Job Vendor Data Array
    * @return array Respose after Update
    */
    public static function updatePrintJobVendor($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Print Job Vendor data
     * @param int Print Job Vendor Id 
     * @return array Vendor data
    */
    public static function getPrintJobVendorDataFromId($id) {
        $query = self::select('mas_book_press.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_book_press.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Print Job Vendor
     * @param array Print Job Vendor Ids Array
     * @return array Respose after Delete
    */
    public static function deletePrintJobVendorData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Print Job Vendor Status
    * @param array Print Job Vendor Ids Array
    * @param string Print Job Vendor Status
    * @return array Print Job Vendor after Update
    */
    public static function updatePrintJobVendorStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    
}