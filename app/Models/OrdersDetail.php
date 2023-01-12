<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class OrdersDetail extends Model
{
    protected $table = 'order_detail';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'order_qty', 'bill_qty', 'price', 'final_amount', 'discount', 'bill_discount', 'status', 
        'is_completed', 'is_verified', 'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    public $timestamps = true; 
     
    /**
    * Add Orders Single/Multiple
    * @param array Orders Data Array
    * @return array Respose after insert
    */
    public static function addOrderProduct($insert_array = array()) {
        return self::insert($insert_array);
    }
    /**
    * Get Orders Details 
    * @param array Client Id 
    * @return array Respose Data Array
    */
    public static function getClientDetailsByOrderId($id) {
        $query = self::from('order_detail AS o');
        $query->select('o.*', 'p.stock', 'p.weight', 'p.max_order_qty');

        $query->leftjoin('products AS p','p.id','=','o.product_id');

        if(is_array($id)){
            $query->whereIn('o.order_id', $id);
        }else {
            $query->where(['o.order_id' => $id]);
        }

        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}