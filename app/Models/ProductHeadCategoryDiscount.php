<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ProductHeadCategoryDiscount extends Model
{
    protected $table = 'product_head_category_discount';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_head_id', 'discount_category_id', 'max_discount', 'status'
    ];

    public $timestamps = false; 
     
    /**
    * Add Product Head category Discount Single
    * @param array Product Head Category Discount Data Array
    * @return array Respose after insert
    */
    public static function addProductHeadCategoryDiscount($insert_array = array()) {
        return self::insert($insert_array);
    }
    
    /**
     * Update Product Head category Discount Single
     * @param array Product Head Category Disocunt Data Array
     * @return array Respose after Update
    */
    public static function updateProductHeadCategoryDiscount($update_array = array()) {

        $query = self::select('product_head_category_discount.*');
        $query->where('product_head_id' , $update_array['product_head_id'])
        ->where('discount_category_id', $update_array['discount_category_id']);
        $result =  $query->get()->toArray();

        // Check if mapping is not exist in DB
        if (empty($result)) {
            return self::create($update_array);
        }


        return self::where('product_head_id' , $update_array['product_head_id'])
        ->where('discount_category_id', $update_array['discount_category_id'])
        ->update($update_array);
    }

    /**
     * Get Product Head Category Discount based on product_head_id and category_id
     * @param array Product Head Category Disocunt Data Array
     * @return array Respose Product Head Category Discount Array
     */
    public static function getProductheadCategoryDiscount($product_head_id = NULL) {
        $query = DB::table('product_head_category_discount');
        
        $query->select('product_head_id','discount_category_id','max_discount');
        if ($product_head_id){
        $query->where(['product_head_id' => $product_head_id]);
        }
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
    * Delete Mapping
    * @param array Product Head id Array
    * @return array Respose after Delete
    */
    public static function deleteProductheadCategoryDiscount($product_head_id) {
        if(is_array($product_head_id)){
            return self::whereIn('product_head_id', $product_head_id)->delete();
        }else {
            return self::where('product_head_id', $product_head_id)->delete();
        }
        
    }
}