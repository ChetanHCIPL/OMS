<?php ## Created by Pallavi as on 12th Jan 2023

namespace App\Http\Controllers\API\V1\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction; 
use Exception;
use Hash;
use Config;
use DB;
use App\Models\Products;

class ProductController extends Controller
{
	use ApiFunction;

    public function __construct(){
        
    }

    /**
     * Get Filtered Products List API
     * Request URL : user/get-filtered-products-list
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getFilteredProductsData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $product_head = (isset($post['product_head']) && $post['product_head'] != "" ? $post['product_head'] : 0);
            $user_id = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            if($user_id > 0 && $product_head > 0){

                ## Set Filtered Data If Applied
                if(isset($post['medium_id'])) $where_arr['medium_id'] = $post['medium_id'];
                if(isset($post['product_head'])) $where_arr['product_head'] = $post['product_head'];
                if(isset($post['series'])) $where_arr['series'] = $post['series'];
                if(isset($post['segment'])) $where_arr['segment'] = $post['segment'];
                //if(isset($post['semester_id'])) $where_arr['semester_id'] = $post['semester_id'];

                //echo "<pre>"; print_r($where_arr);exit();
                // Get Filtered Products Data

                $products_data = Products::getFilteredProducts($where_arr);
                //echo "<pre>"; print_r($products_data);exit();
                // If Products List is Not Empty then Prepare Data
                if(!empty($products_data)){
                    // Initialize Empty Array To Fill Products Data
                    $products_data_array = array();

                    foreach($products_data as $order){

                        $products_data_array[] = array(
                            'product_id' => $order['id'] ? $order['id'] : '',
                            'product_name' => $order['name'] ? $order['name'] : '',
                            'product_mrp' => $order['mrp'] ? $order['mrp'] : '',
                            'product_sku' => $order['code'] ? $order['code'] : '',
                            'max_discount' => $order['max_discount'] ? $order['max_discount'] : '',
                            'max_order_qty' => $order['max_order_qty'] ? $order['max_order_qty'] : ''
                        );
                    }

                    // Flags array to define if qty and discount fields are editable or not for a sales person
                    $editable_array = array(
                        'is_qty_editable' => 1,
                        'is_disc_editable' => 1
                    );

                    ## Success
                    $data["products_list"] = $products_data_array;
                    $data["edit_access"] = $editable_array;
                    
                    $this->setStatusCode(Response::HTTP_OK);
                    $message =__('message.msg_success');
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_clients_not_found');
                }
            }else{
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_parameter_missing');
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }

        if(empty($data)){
            $data = (Object)$data;
        }
        ## Return Response
        return $this->prepareResult($message, $data);
    }
}