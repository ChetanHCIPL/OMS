<?php

namespace App\Http\Controllers\Admin\Orders;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Support\Facades\Response;
use Validator;
use Config;
use App;
use Image;
use DB;
use Intervention\Image\File;
use App\GlobalClass\Design;
use App\Models\Orders;
use App\Models\Series;
use App\Models\Segment;
use App\Models\Medium;
use App\Models\Semester;
use App\Models\Board;
use App\Models\OrderStatus;
use App\Models\Clients;
use App\Models\ClientContactPerson;
use App\Models\ClientsAddress;
use App\Models\Admin;
use App\Models\PaymentTerms;
use App\Models\ProductHead;
use App\Models\Products;
use App\Models\Transporter;
class OrdersControllerChallan1 extends AbstractController
{
	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     //   $this->middleware('admin');
        //$this->middleware('accessrights'); 
        $this->destinationPath = Config::get('path.order_path');
        $this->AWSdestinationPath = Config::get('path.AWS_COUNTRY');
        $this->size_array = Config::get('constants.order_image_size');
        $this->img_max_size = Config::get('constants.IMG_MAX_SIZE');
        $this->img_ext_array = Config::get('constants.image_ext_array');
        $this->usertype=Config::get('constants.usertype');
    }

    /**
     * Show the challan orders records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $SalesUsers=Admin::getSalesUsersList();
        return view('admin.orders.create_challan')->with(['SalesUsers'=>$SalesUsers]);
    }

    /**
     * Get challan orders data and pass json response to data table
     *
     * @param  array $request
     * @return json $records
    */
    public function ajaxData(Request $request) {
        
        $Transport=[1=>'Patel Transport',2=>'Shree Ganesh',3=>'Mahashager',4=>'Geeta Transport'];
        $records = $data = $image = array();
        $records["data"] = array();
        $pattern = $err_msg = NULL;

        ## Request Parameters
        $post =$request->All();
       
        $search_arr = array();

        ## Advance Search Filter Params
        if(isset($post['columns'][0]['search']['value'])){
            $search_arr = getAdvanceSearchFilterColsData($post['columns'][0]['search']['value']);
        }

        // Get orders where status = order placed
        $search_arr['selected_status_order'] = [1,2];

        $PheadDetails=[];
        $search = (isset($post['search']['value']) ? ($post['search']['value']) : "");       
        $tot_records_data = Orders::getOrdersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search, $search_arr)->toArray();
        $iTotalRecords = count($tot_records_data);

        $iDisplayLength = (isset($post['length']) ? intval($post['length']) : 50);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = (isset($post['start']) ? intval($post['start']) : 1);
        $sEcho = (isset($post['draw']) ? intval($post['draw']) : 1);

        $sorton = (isset($post['order'][0]['column']) ? $post['order'][0]['column'] : "");
        $sortdir = (isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC');

        if (isset($sorton) && $sorton != "") {
            switch ($sorton) {
                case "1":
                    $sort = "om.order_no";
                    break;
                case "2":
                    $sort = "om.order_date";
                    break;
                case "4":
                    $sort = "om.client_name";
                    break;
                case "5":
                    $sort = "a.first_name";
                    break;
                case "6":
                    $sort = "om.order_total";
                    break;
                case "7":
                    $sort = "om.id";
                    break;
                case "8":
                    $sort = "om.order_expected_dispatched_date";
                    break;
                case "9":
                    $sort = "om.status";
                    break;
                default:
                    $sort = "om.order_no";
            }
        } else {
            $sort = "id";
        }
        $data = Orders::getOrdersData($iDisplayLength, $iDisplayStart, $sort, $sortdir, $search, $search_arr)->toArray();
        
        $cnt = count($data);
         
        for ($i = 0; $i < $cnt; $i++) {
            $j = $i + 1;
            $status = ($data[$i]->status == 1) ? "Active" : "Inactive";
            $status_color = Config::get('constants.status_color.' . $status);
            //$encoded_id = base64_encode($data[$i]->id);
            $encoded_id = gen_generate_encoded_str($data[$i]->id, '3', '3', '');
            $edit = '---';
            $view = '';
            if (per_hasModuleAccess('Orders', 'Edit')) {
                $edit = ' <a href="' . route('orders',['mode' => 'edit', 'id' => $encoded_id]) . '" title="Edit">'.Design::button('edit').'</a>';
                
            }
            if (per_hasModuleAccess('Orders', 'View')) {

                $disabled = '';
                $href = route('order/challan',['mode' => 'add','id' => $encoded_id]);
                $title = "Create Challan";
                $challan="";
                if($data[$i]->client_status == 1) {
                    $disabled = 'challan-disabled';
                    $href = "javascript:void(0);";
                    $title = "Need to verify client";
                }
                $view = '<span title="'.$title.'"><a href="' . $href . '" class="'.$disabled.' btn btn-info btn-sm" title="'.$title.'">Create Challan</a></span>';
            }
            
            $status = Design::blade('status',$status,$status_color);
            $pdfimage="No uploaded";
            if(!empty($data[$i]->order_form_photo)){
                $pdfimage='<a href="http://192.168.32.160/ideal_oms/public/images/product/'.$data[$i]->order_form_photo.'" target="_blank">';
                $pdfimage.=(!empty(strpos($data[$i]->order_form_photo,'.pdf'))) ? 'pdf':'image';
                $pdfimage.='</a>';
            }
            $print="";
            if($data[$i]->status==1){
                $print=' <a href="' . route('order/challan',['mode'=>'add','id' => $encoded_id]) . '" title="Edit">'.Design::button('print').'</a>';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="row_' . $i . '" value="' . $data[$i]->id . '" >',
                $data[$i]->order_no,
                (!empty($data[$i]->order_date)) ? date('d-M-Y',strtotime($data[$i]->order_date)):'',
                $pdfimage,
                $data[$i]->client_name,
                $data[$i]->sales_user_name,
                '₹ '. number_format($data[$i]->order_total,2),
                $data[$i]->id.'.000',
                (!empty($data[$i]->order_expected_dispatched_date)) ? date('d-M-Y',strtotime($data[$i]->order_expected_dispatched_date)):'',
                $view
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return Response::json($records);
    }
}
