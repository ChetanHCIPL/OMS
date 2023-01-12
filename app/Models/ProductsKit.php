<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ProductsKit extends Model
{
    protected $table = 'products_kit';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'quantity', 'kit_product_id'
    ];

    public $timestamps = false; 
     
    /**
    * Add Product ID & Quantity in products_kit Table
    * @param array product id & Quantity.
    * @return array Respose after insert
    */
    public static function addProductKitMapping($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Add Product ID & Quantity in products_kit Table
    * @param array Bulk Entry For Mapping 
    */
    public static function addProductsKitMapping($insert_array = array()) {
        return self::insert($insert_array);
    }

    /**
    * Delete Mapping
    * @param array Product Head id Array
    * @return array Respose after Delete
    */
    public static function deleteProductKit($id, $exist_ids) {
        // echo '<pre>'; print_r($id); echo '</pre>';
        // echo '<pre>'; print_r($exist_ids); echo '</pre>'; exit();
        return self::whereNotIn('id', $exist_ids)->where('kit_product_id', $id)->delete();        
    }

    /**
     * Check if Data already Exist with Same Entry or Not.
     * if Exist return ID
     */
    public function getMappingData($kit_id, $product_id, $quantity){
        $query = self::select('products_kit.id');
        $query->where('kit_product_id' , $kit_id);
        $query->where('product_id' , $product_id);
        $query->where('quantity' , $quantity);
        return $result =  $query->get()->toArray();
    }

    /* Get Kit Vise Products 
    * Return @array : List of Products
    */
    public function getProductKitMappedData($kit_product_id){
        $query = self::select('products_kit.*');
        $query->where('kit_product_id' , $kit_product_id);

        return $result =  $query->get()->toArray();
    }
}