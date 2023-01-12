<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'order_management';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_no', 'user_id', 'user_type', 'parrent_id', 'order_date', 'order_expected_dispatched_date', 
        'order_subtotal', 'order_discount', 'order_adjusment' , 'order_total', 'transport_id', 'order_payment_due_days', 'order_payment_due_date', 'order_remark', 'client_contact_person_id', 'order_responsible_person_name', 'order_responsible_person_number', 'client_id', 'client_name', 'client_number', 'sales_user_id', 'order_form_photo', 'bill_number', 'tally_bill_number', 'tally_client_name', 'updated_at', 'created_at', 'created_by', 'status', 'billing_address_id', 'shipping_address_id'
    ];

    public $timestamps = true; 
     
    /**
    * Add Orders Single/Multiple
    * @param array Orders Data Array
    * @return array Respose after insert
    */
    public static function addOrders($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Orders Single
     * @param integer Id
     * @param array Orders Data Array
     * @return array Respose after Update
    */
    public static function updateOrders($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Orders  Status
     * @param array Orders Ids Array
     * @param string Orders Status
     * @return array Orders after Update
    */
    public static function updateOrdersById($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the Orders Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Orders data array
    */

    public static function getOrdersData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array(),$groupby=NULL) {
        $query = DB::table('order_management as om');
        $query->select('om.*', 'c.status AS client_status', DB::raw("CONCAT(a.first_name,' ', a.last_name) AS sales_user_name"));
        if(!empty($groupby)){
            $query->addSelect(DB::raw("count(*) as numrow"));
        }
        $query->leftjoin('clients AS c','c.id','=','om.client_id');
        $query->leftjoin('admin AS a','a.id','=','om.sales_user_id');
        
        if(isset($search) && $search != ""){
            $query->where(function ($query) use ($search) {
                $query->orWhere('order_no', 'like', ''.$search.'%');
                 
            });
        }        
        if(isset($search_arr) && count($search_arr) > 0){
            
            if(isset($search_arr['orderNo']) && $search_arr['orderNo'] != ''){
                $query->Where('om.order_no', 'like', ''.$search_arr['orderNo'].'%');
            }
            
            if (isset($search_arr['fromDate']) && $search_arr['fromDate'] != "") {
                $query->where('om.order_date', '>=', $search_arr['fromDate']);
            }
            if (isset($search_arr['toDate']) && $search_arr['toDate'] != "") {
                $query->where('om.order_date', '<=', $search_arr['toDate']);
            }

            /*if(isset($search_arr['clientName']) && $search_arr['clientName'] != ''){
                $query->where('om.id', '=',$search_arr['clientName']);
            }*/
            if(isset($search_arr['clientName']) && $search_arr['clientName'] != ''){
                $query->where('om.client_id', '=', $search_arr['clientName']);
            }
            if(isset($search_arr['salesUsers']) && $search_arr['salesUsers'] != ''){
                $query->Where('om.sales_user_id', '=', $search_arr['salesUsers']);
            }/*
            if(isset($search_arr['amount_to']) && $search_arr['amount_to'] != ''){
                $query->Where('order_total', '<=', $search_arr['amount_to']);
            }*/
            if(isset($search_arr['couStatus']) && $search_arr['couStatus'] != ''){
                $query->Where('om.status', $search_arr['couStatus']);
            }
            if(isset($search_arr['orderstatus']) && $search_arr['orderstatus'] != ''){
                $query->Where('om.status', $search_arr['orderstatus']);
            }
            if(isset($search_arr['selected_status_order']) && $search_arr['selected_status_order'] != ''){
                $query->whereIn('om.status', $search_arr['selected_status_order']);
            }
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
        if(!empty($groupby)){
            $query->groupBy($groupby);
        }
        $result = $query->get();
        return $result;
    }

    /**
     * Get Single Orders data
     * @param int Orders Id 
     * @return array Orders data
    */
    public static function getOrdersDataFromId($id) {
        $query = self::select('order_management.*', DB::raw('CONCAT(a.first_name, " ", a.last_name) AS sales_user_name'), 'mt.name AS transporter');

        $query->leftjoin('admin AS a','a.id','=','order_management.sales_user_id');
        $query->leftjoin('mas_transporter AS mt','mt.id','=','order_management.transport_id');

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['order_management.id' => $id]);
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get Single Orders data
     * @param int Orders Id 
     * @return array Orders data
    */
    public static function getListOrdersData() {
        $query = self::select('order_management.*');
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
/**
     * Get Orders sum data by status 
     * @return array Orders data
    */
    public static function getListOrdersStatusData() {
        $query = self::select('order_management.status',DB::raw('count(*) AS numrow'));
        $result =  $query->groupBy('status')->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Get Order Client list with search
     */
    public static function getClientsDroupDownBySearch($name,$status=""){
        $query = self::select('order_management.id','order_management.client_name');
        if(!empty($name)){
            $query->where('order_management.client_name','Like',''.$name.'%');
        }
        if(!empty($status)){
            $query->where('order_management.status','=',$status);
        }
        $query->limit(10);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Delete Orders
     * @param array Orders Ids Array
     * @return array Respose after Delete
    */
    public static function deleteOrdersData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /** 
     * Update Order Number
     * @param integer Last inserted Id
     * @return array Respose after Update
     */

     public static function updateOrderNumber($id, $code)
     {
        return self::where('id', $id)->update(['order_no' => DB::raw("CONCAT('".$code."', LPAD(".$id.", 7, '0'))")]);
     }

    /**
    * Get Orders by admin Id
    * @param int admin ID passed
    * @param array filters array passed
    * @return array Respose Orders List Array
    */
    public static function getOrdersList($user_id = NULL, $where_arr = array()) {
        $query = self::from('order_management AS om');
        
        $query->leftjoin('mas_order_status AS mos','mos.id','=','om.status');
        $query->leftjoin('clients AS c','c.id','=','om.client_id');
        $query->leftjoin('admin AS a','a.id','=','om.sales_user_id');
        $query->leftjoin('order_detail AS od','od.order_id','=','om.id');
        
        $query->addSelect('om.*', 'c.client_name AS client_full_name', 'mos.name AS status_label', 'mos.color_code AS status_color', DB::raw("CONCAT(a.first_name,' ', a.last_name) AS sales_user_name"), DB::raw('SUM(order_qty) AS order_total_qty'));
        
        $search_keyword = isset($where_arr['search_keyword']) ? $where_arr['search_keyword'] : '';

        if (isset($search_keyword) && $search_keyword != "") {
            $query->where(function ($query) use ($search_keyword) {
                $query->orWhere('om.order_no', 'like', '%' . $search_keyword . '%');
            });
        }

        if (isset($where_arr['fromDate']) && $where_arr['fromDate'] != "") {
            $query->where('om.order_date', '>=', $where_arr['fromDate']);
        }
        if (isset($where_arr['toDate']) && $where_arr['toDate'] != "") {
            $query->where('om.order_date', '<=', $where_arr['toDate']);
        }
        if (isset($where_arr['sales_user_id'])){
            $query->where('om.sales_user_id','=', $where_arr['sales_user_id']);
        }
        if (isset($where_arr['client_id'])){
            $query->where('om.client_id','=', $where_arr['client_id']);
        }
        if (isset($where_arr['status'])){
            $query->where('om.status','=', $where_arr['status']);
        }

        $query->groupBy('om.id');
        
        if (isset($where_arr['type'])){
            if($where_arr['type'] == 'total_count'){

                $result = count($query->get()->toArray());
            }else{
                if($where_arr['type'] == 'paginated_data'){
                    if (isset($where_arr['records_per_page']) && $where_arr['records_per_page'] != "") {
                        $query->limit($where_arr['records_per_page']);
                    }
                    if (isset($where_arr['start']) && $where_arr['start'] != "") {
                        $query->offset($where_arr['start']);
                    }
                }     
                $query->orderBy('om.id', 'desc'); 
                $result = $query->get()->toArray();
            }
        }else{
            $query->orderBy('om.id', 'desc'); 
            $result = $query->get()->toArray();
        }

        return json_decode(json_encode($result), true);
    }
}