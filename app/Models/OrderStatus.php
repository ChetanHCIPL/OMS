<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'mas_order_status';
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
    * Add Order Single/Multiple
    * @param array Order Data Array
    * @return array Respose after insert
    */
    public static function addOrder($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Order Single
     * @param integer Id
     * @param array Order Data Array
     * @return array Respose after Update
    */
    public static function updateOrder($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Order  Status
     * @param array Order Ids Array
     * @param string Order Status
     * @return array Order after Update
    */
    public static function updateOrderStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Order Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Order data array
    */

    public static function getOrderData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_order_status as mi');
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

        $query->leftjoin('mas_medium as m', 'm.order_id', '=', 'mi.id');

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
     * Get Single Order data
     * @param int Order Id 
     * @return array Order data
    */
    public static function getOrderDataFromId($id) {
        $query = self::select('mas_order_status.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_order_status.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get Single Order data
     * @param int Order Id 
     * @return array Order data
    */
    public static function getListOrderData() {
        $query = self::select('mas_order_status.*');
        $result =  $query->get()->toArray();
        $query->where('status', self::ACTIVE);
        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get All Active Status data
     * @return array Order status array
    */
    public static function getAllActiveOrderStatus() {
        $query = self::from('mas_order_status');
        
        $query->addSelect('name AS status_label', 'color_code AS status_color', 'id AS status_code');

        $result = $query->get()->toArray();
        $query->where('status', self::ACTIVE);
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Order
     * @param array Order Ids Array
     * @return array Respose after Delete
    */
    public static function deleteOrderData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
}