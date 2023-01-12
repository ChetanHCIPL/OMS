<?php

namespace App\Http\Controllers\API\V1\Sales;

use App\Http\Controllers\Controller;
use App\Traits\General\ApiFunction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Hash;
use Exception;
use DB;
use Config;
use App\Models\Admin;

class SalesUserController extends Controller
{
    use ApiFunction;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Summary of getSalesUsersList
     * get Sales Users List
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getSalesUsersList(Request $request){

        $data = array();
        try{
            $post = $request->all();

            $data = array();
            
            ## Get sales users list
            $data['sales_users'] = Admin::getSalesUsersList();

            ## Success
            $this->setStatusCode(Response::HTTP_OK);
            $message = __('label.lbl_sucess');
            
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