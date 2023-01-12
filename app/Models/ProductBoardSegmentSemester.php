<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ProductBoardSegmentSemester extends Model
{
    protected $table = 'product_board_segment_semester';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'medium_board_id', 'segment_id', 'semester_id'
    ];

    public $timestamps = false; 
     
    /**
    * Add Product board segment Semester mapping
    * @param array Product Head Category Discount Data Array
    * @return array Respose after insert
    */
    public static function addProductBoardSegmentSemesterMapping($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Add Product board segment Semester mapping
    * @param array Buld Entry For Mapping 
    */
    public static function addProductMapping($insert_array = array()) {
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
    * Delete Mapping
    * @param array Product Head id Array
    * @return array Respose after Delete
    */
    public static function deleteProductMapping($id, $exist_ids) {
        return self::whereNotIn('id', $exist_ids)->where('product_id', $id)->delete();        
    }

    /**
     * Check if Data already Exist with Same Entry or Not.
     * if Exist return ID
     */
    public function getMappingData($product_id, $medium_id, $segment_id, $semester_id){
        $query = self::select('product_board_segment_semester.id');
        $query->where('product_id' , $product_id);
        $query->where('medium_board_id' , $medium_id);
        $query->where('segment_id' , $segment_id);
        $query->where('semester_id' , $semester_id);
        return $result =  $query->get()->toArray();
    }

    /* Check if Data already Exist with Same Entry or Not.
    * if Exist return ID
    */
    public function getProductMappedData($product_id){
        $query = self::select('product_board_segment_semester.*');
        $query->where('product_id' , $product_id);

        return $result =  $query->get()->toArray();
    }
}