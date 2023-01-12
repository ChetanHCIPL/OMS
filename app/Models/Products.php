<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE  = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_kit_product', 'product_head_id', 'series_id', 'name', 'product_number', 'code', 'hsn_number', 'qrcode', 'mrp', 'pages', 'badho', 'weight', 'image', 'description','max_order_qty','version_no', 'stock','row_stock', 'lock_for_order', 'cn_lock', 'created_at','status',
        'type_of_product'
    ]; 
     
    /**
    * Function :  Get All Products records for ajax
    * @return  json $userArray 
    */
    public static function getAllProductsData($productsId = NULL)
    {   
         $query = DB::table('products'); 
         if($productsId != ''){
            $query->where('id', $productsId);
         }
         $query->where(['status' => self::ACTIVE]);
         $productData =  $query->get()->toArray();
         return $productData;
    }   

    /**
    * Function :  Get All Kit Products records for ajax
    * @return  json $userArray 
    */
    public static function getAllKitProductsData($productsId = NULL)
    {   
         $query = DB::table('products'); 
         if($productsId != ''){
            $query->where('id', $productsId);
         }
         $query->where(['type_of_product' => 1]);
         $query->where(['status' => self::ACTIVE]);
         $productData =  $query->get()->toArray();
         return $productData;
    }   
    /**
     * Get Products ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getProductsISDCodeData($where_arr=array()) {
        $query = self::from('products AS mc');
        $query->select('mc.id','mc.isd_code','mc.flag');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        if(isset($where_arr['products_id']) && $where_arr['products_id'] != ""){
            $query->where('mc.id', $where_arr['products_id']);
        }
        $query->where('mc.isd_code', '!=', '');
        $data = $query->get()->toArray();
        return $data;
    }
    

    /**
     * Get the Products Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Products data array
     */

    public static function getProductsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('products');
        $query->select('products.*');
       
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                $query->orWhere('code', 'like', ''.$search.'%');
                $query->orWhere('hsn_number', 'like', ''.$search.'%');
                $query->orWhere('stock', 'like', ''.$search.'%');
                $query->orWhere('mrp', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', '2');
                }
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['couName']) && $search_arr['couName'] != '')
                $query->Where('name', 'like', ''.$search_arr['couName'].'%');
            if(isset($search_arr['couCode']) && $search_arr['couCode'] != '')
                $query->Where('code', 'like', ''.$search_arr['couCode'].'%');
            if(isset($search_arr['couStatus']) && $search_arr['couStatus'] != '')
                $query->Where('status', $search_arr['couStatus']);
            if(isset($search_arr['filer_product_head']) && $search_arr['filer_product_head'] != "")
                $query->where('product_head_id', $search_arr['filer_product_head']);
            if(isset($search_arr['filter_product_type']) && $search_arr['filter_product_type'] != "")
                $query->where('type_of_product', $search_arr['filter_product_type']);
            if(isset($search_arr['couHSN']) && $search_arr['couHSN'] != "")
                $query->where('hsn_number', $search_arr['filter_product_type']);
            if(isset($search_arr['couStockFrom']) && $search_arr['couStockFrom'] != "")
                $query->where('stock', '>=', (int) $search_arr['couStockFrom']);
            if(isset($search_arr['couStockTo']) && $search_arr['couStockTo'] != "")
                $query->where('stock', '<=', (int) $search_arr['couStockTo']);                
            if(isset($search_arr['couMRPFrom']) && $search_arr['couMRPFrom'] != "")
                $query->where('mrp', '>=', (int) $search_arr['couMRPFrom']);
            if(isset($search_arr['couMRPTo']) && $search_arr['couMRPTo'] != "")
                $query->where('mrp', '<=', (int) $search_arr['couMRPTo']);
            
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
        //echo $result = $query->toSql();exit;
        $result = $query->get();
        return $result;
    }
    
    /**
    * Add Products Single
    * @param array Products Data Array
    * @return array Respose after insert
    */
    public static function addProducts($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Products Single
     * @param integer Products Id
     * @param array Products Data Array
     * @return array Respose after Update
    */
    public static function updateProducts($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Product  Status
     * @param array Products Ids Array
     * @param string Products Status
     * @return array Respose after Update
    */
    public static function updateCountriesStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
    
    /**
     * Delete Product  Status
     * @param array Products Ids Array
     * @return array Respose after Delete
    */
    public static function deleteCountries($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
    
    /**
     * Get Single Products data
     * @param int Products Id 
     * @return array Products data
    */
    public static function getProductsDataFromId($id, $client_cat_id = '') {
        
        $query = self::from('products AS p');
        $query->select('p.*', 'pcd.max_discount');
        $query->leftjoin('product_head_category_discount AS pcd','pcd.product_head_id','=','p.product_head_id');
        if(is_array($id)){
            $query->whereIn('p.id', $id);
        }else {
            $query->where(['p.id' => $id]);
        }
        if ($client_cat_id) {
            $query->where('pcd.discount_category_id', $client_cat_id);
        }
        $query->groupBy('p.id');
        $result =  $query->get()->toArray();
        
        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get All Active Products data
     * @param   array   $where_arr
     * @return  array   $result
    */
    public static function getAllActiveCountries($where_arr=array()) {
        $query = self::from('products AS mc');
        $query->select('mc.id','mc.name');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        $result = $query->orderBy('mc.name', 'ASC')->get()->toArray();
        return $result;
    }

     /**
     * Function to get associated array of name 
     * @param  array $whereArr
     * @return array $data
    */
    public static function getProductsAssocArr($whereArr=array()) {
        $query = self::from('products AS mc');
        if(isset($whereArr['status']) && $whereArr['status'] != ""){
            $query->where('mc.status', $whereArr['status']);
        }
        $data = $query->pluck('mc.name', 'mc.id')->toArray();
        return $data;
    }

    /**
     * Get Products Name
     *
     * @param string products Code
     * @return array Products Name Data   
    */
    public static function getProductsNameById($productsId)
    {
        $query = self::select('name');
        $query->where('id','=', $productsId);
        $data = $query->get()->toArray();
        return $data;
    }

    /**
     * Get Products Name
     *
     * @param string products Code
     * @return array Products Name Data   
    */
    public static function getProductsHeadById($productsId)
    {
        $query = self::select('product_head_id');
        $query->where('id','=', $productsId);
        $data = $query->get()->toArray();
        return $data;
    }
    
    /**
     * Get Products ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getProductsISDCode($where_arr=array()) {
        $query = self::from('products AS mc');
        $query->select('mc.id','mc.isd_code','mc.flag');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        $query->where('mc.isd_code', '!=', '');
        $data = $query->orderBy('mc.id', 'ASC')->get()->toArray();
        return $data;
    }
    
    /**
     * Get Products with filters
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getFilteredProducts($where_arr=array(), $client_category_id = '') {
        $query = self::from('products AS p');
        $query->select('p.id','p.name', 'pcd.max_discount', 'p.max_order_qty', 'p.mrp', 'p.code');

        $query->leftjoin('product_board_segment_semester AS ps','ps.product_id','=','p.id');
        $query->leftjoin('product_head_category_discount AS pcd','pcd.product_head_id','=','p.product_head_id');

        $query->where(['p.status' => self::ACTIVE]);

        if(isset($where_arr['product_head']) && $where_arr['product_head'] != ""){
            $query->where('p.product_head_id', $where_arr['product_head']);            
        }

        if(isset($where_arr['medium_id']) && is_array($where_arr['medium_id']) && $where_arr['medium_id'] != ""){
            $query->whereIn('ps.medium_board_id', $where_arr['medium_id']);
        }else if (isset($where_arr['medium_id']) && $where_arr['medium_id'] != "") {
            $query->where(['ps.medium_board_id' => $where_arr['medium_id']]);
        }

        if(isset($where_arr['segment']) && is_array($where_arr['segment']) && $where_arr['segment'] != ""){
            $query->whereIn('ps.segment_id', $where_arr['segment']);
        }else if (isset($where_arr['segment']) && $where_arr['segment'] != "") {
            $query->where(['ps.segment_id' => $where_arr['segment']]);
        }
        
        if(isset($where_arr['series']) && $where_arr['series'] != ""){
            $query->where('p.series_id', $where_arr['series']);
        }

        if(isset($client_category_id) && $client_category_id != "" ){
            $query->where('pcd.discount_category_id', $client_category_id);
            // $query->where('pcd.product_head_id', $where_arr['product_head']);            
        }

        $query->where(['type_of_product' => 0]);
        $query->where('p.stock', '>', '0');
        $query->where('p.max_order_qty', '>', '0');
        $query->groupBy('p.id');
        
        $data = $query->orderBy('p.name', 'ASC')->get()->toArray();
        return $data;
    }   

    /**
     * Get Kit Product By Parent Product.
     * Request @int  : Product ID
     * Return @array : Kit Product List
     */
    public static function getKitsProductData($productId = NULL)
    {   
        $query = self::from('products AS p');
        $query->select('p.id','p.name', 'pk.kit_product_id');

        $query->leftjoin('products_kit AS pk','pk.kit_product_id','=','p.id');

        $query->where(['p.status' => self::ACTIVE]);
        $query->where(['pk.product_id' => $productId]);

        $data = $query->orderBy('p.name', 'ASC')->get()->toArray();
        return $data;
    }   

    /**
    * Function :  Get All Kit Products records for ajax
    * @return  json $userArray 
    */
    public static function getAllNormalProductsData($productsId = NULL)
    {   
         $query = DB::table('products'); 
         if($productsId != ''){
            $query->where('id', $productsId);
         }
         $query->where(['type_of_product' => 0]);
         $query->where('stock', '>', '0');
         $query->where('max_order_qty', '>', '0');
         $query->where(['status' => self::ACTIVE]);
         $productData =  $query->get()->toArray();
         return $productData;
    }   
}