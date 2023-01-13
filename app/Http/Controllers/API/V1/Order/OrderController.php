<?php ## Created by Pallavi as on 10th Jan 2023

namespace App\Http\Controllers\API\V1\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction; 
use App\Traits\Order\Order;
use Exception;
use Hash;
use Config;
use DB;
use App\Models\Orders;
use App\Models\OrderStatus;

class OrderController extends Controller
{
    use ApiFunction, Order;

    public function __construct(){
        
    }

    /**
     * Get All Orders List API
     * Request URL : user/get-order-list
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getAllOrders(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id      = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $pagination = (isset($request->pagination) && $request->pagination != "" ? (string)$request->pagination : 0);
            if($user_id > 0){

                ## Set Filtered Data If Applied
                $where_arr['type'] = "total_count";

                if(isset($post['filter']['fromDate'])) $where_arr['fromDate']=$post['filter']['fromDate'];
                if(isset($post['filter']['toDate'])) $where_arr['toDate']=$post['filter']['toDate'];
                if(isset($post['filter']['client_id'])) $where_arr['client_id']=$post['filter']['client_id'];
                if(isset($post['filter']['sales_user_id'])) $where_arr['sales_user_id']=$post['filter']['sales_user_id'];
                if(isset($post['filter']['status']))$where_arr['status']=$post['filter']['status'];
                if(isset($post['filter']['search_keyword']))$where_arr['search_keyword']=$post['filter']['search_keyword'];

                //echo "<pre>"; print_r($where_arr);exit();
                // Get Data Filtered vise Data with Counters
                $order_total_records = Orders::getOrdersList($user_id, $where_arr);
                // If Pagination is True then Return Order list with Pagination
                if ($pagination == 1){
                    $where_arr['type'] = "total_count";
                    $RECORDS_PER_PAGE = 1;
                    $records_per_page = ($request->has('records_per_page') && $request->input('records_per_page') > 0)?$request->input('records_per_page'):$RECORDS_PER_PAGE;
                    $page = ($request->has('page') && $request->input('page') > 0)?$request->input('page'):1;
                    if($order_total_records > 0){
                        ## Pagination Parameters
                        extract(api_setPaginationParameters($order_total_records, $page, $records_per_page));

                        ## Prepare Where array to get paginated result
                        $where_arr['type'] = "paginated_data";
                        $where_arr['records_per_page'] = $records_per_page;
                        $where_arr['start'] = $start;
                    }
                }
                //Else Return All Clients With Counter.
                else{
                    $RECORDS_PER_PAGE = 1;
                    $where_arr = array();
                }

                // Get Order List Data

                //echo "<pre>"; print_r($where_arr);exit();
                $order_data = Orders::getOrdersList($user_id, $where_arr);
                //echo "<pre>"; print_r($order_data);exit();
                // If Order List are Not Empty then Prepare Data
                if(!empty($order_data)){
                    // Initialize Empty Array To Fill Order Data
                    $order_data_array = array();

                    foreach($order_data as $order){

                        $order_data_array[] = array(
                            'order_id' => $order['id'] ? $order['id'] : '',
                            'order_no' => $order['order_no'] ? $order['order_no'] : '',
                            'client_id' => $order['client_id'],
                            'client_full_name' => $order['client_full_name'] ? $order['client_full_name'] : '',
                            'date_added' => $order['created_at'] ? $order['created_at'] : '',
                            'date_visible' => $order['created_at'] ? date_getDateFull($order['created_at']) : '',
                            'sales_person_id' => $order['sales_user_id'],
                            'sales_person_name' => $order['sales_user_name'],
                            'order_date' => $order['order_date'] ? $order['order_date'] : '',
                            'order_qty' => $order['order_total_qty'] ? $order['order_total_qty'] : '',
                            //'order_qty' => '5',
                            'order_date_visible' => $order['order_date'] ? date_getDateFull($order['order_date']) : '',
                            'order_expected_dispatched_date' => $order['order_expected_dispatched_date'] ? $order['order_expected_dispatched_date'] : '',
                            'order_expected_dispatched_date_visible' => $order['order_expected_dispatched_date'] ? date_getDateFull($order['order_expected_dispatched_date']) : '',
                            'billing_address_id' => $order['billing_address_id'] ? $order['billing_address_id'] : '',
                            'billing_address' => $order['billing_address'] ? $order['billing_address'] : '',
                            'shipping_address_id' => $order['shipping_address_id'] ? $order['shipping_address_id'] : '',
                            'shipping_address' => $order['shipping_address'] ? $order['shipping_address'] : '',
                            'bill_number' => $order['bill_number'] ? $order['bill_number'] : '',
                            'order_subtotal' => $order['order_subtotal'] ? $order['order_subtotal'] : '',
                            'order_discount' => $order['order_discount'] ? $order['order_discount'] : '',
                            'order_total' => $order['order_total'] ? $order['order_total'] : '',
                            'order_total_visible' => $order['order_total'] ? '₹ '. number_format($order['order_total'],2) : '',
                            'transport_id' => $order['transport_id'] ? $order['transport_id'] : '',
                            'order_payment_due_date' => $order['order_payment_due_date'] ? $order['order_payment_due_date'] : '',
                            'order_payment_due_date_visible' => $order['order_payment_due_date'] ? date_getDateFull($order['order_payment_due_date']) : '',
                            'status' => array(
                                'status_code' => $order['status'], 
                                'status_label' => $order['status_label'],
                                'status_color' => $order['status_color']
                            ),
                            'is_editable' => ($order['id'] % 5 == 0) ? 1 : 0,
                            'is_deletable' => ($order['id'] % 10 == 0) ? 1 : 0,
                            'is_viewable' => ($order['id'] % 2 == 0) ? 1 : 0,
                            'is_sharable' => ($order['id'] % 3 == 0) ? 1 : 0
                        );
                    }

                    $order_status_array = OrderStatus::getAllActiveOrderStatus();

                    ## Success
                    $lastpage = ceil($order_total_records/$records_per_page);
                    $data["order_list"] = $order_data_array;
                    $data["order_status"] = $order_status_array;
                    $data["total_records"] = $order_total_records;
                    $data["total_records_str"] = "Total ".$order_total_records." Orders";
                    $data["total_page"] = $lastpage;
                    $data["current_page"] = $page;
                    $data["next_page"] = ($page+1<=$lastpage)? $page+1:$lastpage;
                    $data["previous_page"] = ($page-1>=1)? $page-1:1;
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

    /**
     * Function: Add Order
     * Request URL : user/add-client-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function addOrderData(Request $request){
        $event_data_array = array();
        $data = $where_arr = array();
        try{
            $data = $request->all();
            $user_id = (isset($data['user_id']) && $data['user_id'] != "" ? $data['user_id'] : 0);

            //echo "<pre>"; print_r($data); exit;

            // Validate Request Object for Processed Further
            $validator = $this->validateOrderAdd($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Insert Recored
                if(!empty($user_id)){

                    echo ">> ".date_convertDBDateFormat($data['order_date']); exit;

                    $insert_array = array(
                        'user_id'       => $data['user_id'],
                        'user_type'     => $data['user_type'], 
                        'parrent_id'    => $data['parrent_id'], 
                        'order_date'    => date_convertDBDateFormat($data['order_date']),
                        'order_expected_dispatched_date'    => $data['dispatch_date'], 
                        'order_total'  => $data['order_total'],
                        'order_subtotal' => $data['sub_total_value'], 
                        'order_discount' => $data['dis_total_value'],
                        'transport_id' => $data['transporter'],
                        'order_payment_due_days' => $paymentTerms[0] ? $paymentTerms[0]['due_type_value'] : '', 
                        'order_payment_due_date' => $data['due_date'], 
                        'order_remark' => $data['order_remark'], 
                        'client_contact_person_id' => $data['client_address_id'],
                        'order_responsible_person_name' => $clientContactPerson[0] ? $clientContactPerson[0]['full_name'] : '',
                        'order_responsible_person_number' => $clientContactPerson[0] ? $clientContactPerson[0]['mobile_number'] : '',
                        'client_id' => $data['client_id'],
                        'client_name' => $clientData[0] ? $clientData[0]['client_name'] : '',
                        'client_number' => $clientData[0] ? $clientData[0]['mobile_number'] : '',
                        'sales_user_id' => $data['sales_user_id'], 
                        'status' => 1,
                        'billing_address_id' => $data['client_address_id'], 
                        'billing_address_name' => $bill_address['title'],
                        'billing_address' => $bill_address['address'],
                        'shipping_address_id' => $data['client_ship_address_id'],
                        'shipping_address_name' => $ship_address['address'],
                        'shipping_address' => $ship_address['title'],
                        'order_form_photo' => $file,
                        'created_by' => Auth::guard('admin')->user()->id
                    );

                    // Insert Order in Respected Table
                    $insert = Orders::addOrders($insert_array);

                    // Get Order Type
                    $client_type=ClientType::getClientDataList();

                    // Initialize Order Prefix
                    $client_code_prefix=[];

                    foreach($client_type as $d1){
                        $client_code_prefix[$d1['id']]=strtoupper(substr($d1['name'], 0, 2));
                    }

                    // Update Order Code With Unique Identifier
                    Clients::updateClientCode($insert['id'], $client_code_prefix[$data['type']]);            
                    if($insert){
                        $this->setStatusCode(Response::HTTP_OK);
                        $message =__('message.msg_record_added');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_user_id_required');
                }
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