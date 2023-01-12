<?php ## Created by Vikas as on 14th Dec 2022

namespace App\Http\Controllers\API\V1\Client;

use App\Models\State;
use App\Models\Taluka;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction; 
use App\Traits\Client\Client;
use Exception;
use Hash;
use Config;
use App\Models\Admin;
use App\Models\Clients;
use App\Models\ClientsAddress;
use App\Models\ClientContactPerson;
use App\Models\Districts;
use App\Models\ClientType;
use App\Models\ContactUs;
use DB;

class ClientController extends Controller
{
	use ApiFunction,Client;

    public function __construct(){
        $this->destinationPath      = config('path.client_path');
        $this->AWSdestinationPath   = config('path.AWS_CLIENT_PATH');
        $this->size_array           = config('constants.user_image_size');
        $this->customer_identification_path        = config('path.client_identification_path');
    }

    /**
     * Get All Client List API
     * Request URL : member/get-client-list-with-detail
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getAllClients(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id      = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $pagination = (isset($request->pagination) && $request->pagination != "" ? (string)$request->pagination : 0);
            if($user_id > 0){

                ## Set Filtered Data If Applied
                $where_arr['type'] = "total_count";

                if(isset($post['filter']['state_id'])) $where_arr['state_id']=$post['filter']['state_id'];
                if(isset($post['filter']['district_id'])) $where_arr['district_id']=$post['filter']['district_id'];
                if(isset($post['filter']['taluka_id'])) $where_arr['taluka_id']=$post['filter']['taluka_id'];
                if(isset($post['filter']['sales_user_id'])) $where_arr['sales_user_id']=$post['filter']['sales_user_id'];
                if(isset($post['filter']['status']))$where_arr['status']=$post['filter']['status'];
                if(isset($post['filter']['search_keyword']))$where_arr['search_keyword']=$post['filter']['search_keyword'];

                //echo "<pre>"; print_r($where_arr);exit();
                // Get Data Filtered vise Data with Counters
                $client_total = Clients::getClientsList($user_id, $where_arr);
                // If Pagination is True then Return Client list with Pagination
                if ($pagination == 1){
                    $where_arr['type'] = "total_count";
                    $RECORDS_PER_PAGE = 1;
                    $records_per_page = ($request->has('records_per_page') && $request->input('records_per_page') > 0)?$request->input('records_per_page'):$RECORDS_PER_PAGE;
                    $page = ($request->has('page') && $request->input('page') > 0)?$request->input('page'):1;
                    if($client_total > 0){
                        ## Pagination Parameters
                        extract(api_setPaginationParameters($client_total, $page, $records_per_page));

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

                // Get Client List Data

                $client_data = Clients::getClientsList($user_id, $where_arr);
              //  echo "<pre>"; print_r($client_data);exit();
                // If Client List are Not Empty then Prepare Data
                if(!empty($client_data)){
                    // Initialize Empty Array To Fill Client Data
                    $client_data_array = array();

                    foreach($client_data as $client){

                        $sales = "SELF";
                        if (isset($client['sales_user_id'])) {
                            $sales_user = Admin::getUserDataFromId($client['sales_user_id']);
                            $district_name = '';
                            $state_name = '';


                            // Set Sales User
                            if (isset($sales_user[0]) && ($sales_user[0]['id'] != $user_id)) {
                                if (isset($sales_user[0]['first_name']) && isset($sales_user[0]['last_name']))
                                    $sales = ucfirst($sales_user[0]['first_name'] ? $sales_user[0]['first_name'] : '') . " " . ucfirst($sales_user[0]['last_name'] ? $sales_user[0]['last_name'] : '');
                            }
                        }
                        // Ser State Name
                        if ($client['state_id'] > 0){
                            $state = State::getStateDataFromId($client['state_id']);
                            $state_name = $state[0]['state_name'] ? $state[0]['state_name'] : '';

                        }
                        // Ser District Name
                        if ($client['district_id'] > 0){
                            $district = Districts::getDistrictsDataFromId($client['district_id']);
                            $district_name = $district[0]['district_name'] ? $district[0]['district_name'] : '';
                        }

                        $client_data_array[] = array(
                            'client_id' => $client['id'] ? $client['id'] : '',
                            'client_code' => $client['client_code'] ? $client['client_code'] : '',
                            'client_full_name' => $client['client_name'] ? $client['client_name'] : '',
                            'date_added'    => $client['created_at'] ? $client['created_at'] : '',
                            'date_visible'  => $client['created_at'] ? date_getDateFull($client['created_at']) : '',
                            'sales_person_id'   => $client['sales_user_id'],
                            'sales_person_name' => $sales,
                            'state'         => $client['state_id'],
                            'state_name'    => $state_name,
                            'district_id'   => $client['district_id'],
                            'district_name' => $district_name,
                            'email'     => $client['email'] ? $client['email'] : '',
                            'mobile_number' => $client['mobile_number'] ? $client['mobile_number'] : '',
                            'zip_code'           => $client['zip_code'],
                            'verified_date' => $client['verified_date'] ? $client['verified_date'] : '',
                            'verified_date_visible' => $client['verified_date'] ? date_getFullDate($client['verified_date']) : '',
                            'status' => array(
                                    'status_code' => $client['status'], 
                                    'status_label' => Config::get('constants.client_status.' . $client['status']),
                                    'status_color' => Config::get('constants.client_status_color_code.' . $client['status'])
                                )
                        );
                    }

                    // Add Clients Statuses array in Response
                    $clienst_status = Config::get('constants.client_status');
                    $clienst_status_color = Config::get('constants.client_status_color_code');
                    $clients_statuses = array();
                    if (is_array($clienst_status)){
                        foreach ($clienst_status as $key => $value) {
                            $clients_statuses[] = array(
                                'status_code' => $key,
                                'status_name' => $value,
                                'status_color_code' => $clienst_status_color[$key]
                            );
                        }
                    }

                    ## Success
                    $lastpage=ceil($client_total/$records_per_page);
                    $data["client_list"] = $client_data_array;
                    $data["client_status"] = $clients_statuses;
                    $data["total_records"] = $client_total;
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
     * Get All Client List API
     * Request URL : member/get-client-list
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getClientsBasicData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id      = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $pagination = (isset($request->pagination) && $request->pagination != "" ? (string)$request->pagination : 0);
            if($user_id > 0){

                // Get Client List Data
                $client_data = Clients::getClientDataList();
              //  echo "<pre>"; print_r($client_data);exit();
                // If Client List are Not Empty then Prepare Data
                if(!empty($client_data)){

                    ## Success
                    $data["client_list"] = $client_data;
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
     * Get Client Data By Client ID
     * Request URL : user/get-client-details
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getClientsDetailsById(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $client_id      = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);
            $user_id        = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            // If Client ID is Exist then Get Data About Client
            if(($client_id > 0) && ($user_id > 0)){
                ## Get Client Data
                $client_data = Clients::getClientsDataFromIdWithAllDetails($client_id);
                
                // Check if data is in DB then Prepare Array for Response
                if(!empty($client_data)){

                    $client=$client_data[0];

                    //Initialize Client Array
                    $client_data_array = array();

                    // Initialize Sub array of Client Array
                    $client_statistics = array();
                    $client_detail = array();
                    $client_information = array();
                    $client_address = array();
                    $client_contact = array();

                    // Client Statistics For Now It's has been Static
                    $client_statistics[] = array(
                        'label' => 'Orders',
                        'counter' => '54'
                    );
                    $client_statistics[] = array(
                        'label' => 'Invoices',
                        'counter' => '50'
                    );
                    $client_statistics[] = array(
                        'label' => 'Collection',
                        'counter' => '34'
                    );
                    $client_statistics[] = array(
                        'label' => 'Credit Note',
                        'counter' => '30'
                    );

                    // Prepare Client General Details
                    $client_detail['client_id'] = $client['id'] ? $client['id'] : '';
                    $client_detail['client_code'] = $client['client_code'] ? $client['client_code'] : '';
                    $client_detail['client_name'] = $client['client_name'] ? $client['client_name'] : '';
                    $client_detail['email'] = $client['email'] ? $client['email'] : '';
                    $client_detail['mobile_number'] = $client['mobile_number'] ? $client['mobile_number'] : '';
                    $client_detail['sales_user_id'] = $client['sales_user_id'] ? $client['sales_user_id'] : 0;
                    $client_detail['client_type'] = $client['client_type'] ? $client['client_type'] : '';
                    $client_detail['state_id'] = $client['state_id'] ? $client['state_id'] : 0;
                    $client_detail['district_id'] = $client['district_id'] ? $client['district_id'] : 0;
                    $client_detail['zip_code'] = $client['zip_code'] ? $client['zip_code'] : '';

                    //Prepare Client Information Object
                    $client_information[] = array(
                        'key'   => 'Code:',
                        'value' => $client['client_code'] ? $client['client_code'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'Client Name:',
                        'value' => $client['client_name'] ? $client['client_name'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'Billing Name:',
                        'value' => $client['client_name'] ? $client['client_name'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'Registered Email:',
                        'value' => $client['email'] ? $client['email'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'Registered Mobile No:',
                        'value' => $client['mobile_number'] ? $client['mobile_number'] : '',
                        'icon'  => 1
                    );
                    $client_information[] = array(
                        'key'   => 'WhatsApp No:',
                        'value' => $client['whatsapp_number'] ? $client['whatsapp_number'] : '',
                        'icon'  => 0
                    );

                    if (isset($client['client_type'])){
                        $type = ClientType::getClientTypeDataFromId($client['client_type']);
                    }
                    $client_information[] = array(
                        'key'   => 'Type:',
                        'value' => $client['client_type'] ? $type[0]['name'] : '',
                        'icon'  => 0
                    );

                    if (isset($client['sales_user_id'])){
                        $sales_person = Admin::getAdminNameFromId($client['sales_user_id']);
                    }
                    $client_information[] = array(
                        'key'   => 'Sales Person:',
                        'value' => $client['sales_user_id'] ? $sales_person[0]['name'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'PAN No.:',
                        'value' => $client['pan_no'] ? $client['pan_no'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'GST No.:',
                        'value' => $client['gst_no'] ? $client['gst_no'] : '',
                        'icon'  => 0
                    );
                    if (isset($client['district_id'])){
                        $dist = Districts::getDistrictsDataFromId($client['district_id']);
                    }
                    $client_information[] = array(
                        'key'   => 'District:',
                        'value' => $client['district_id'] ? $dist[0]['district_name'] : '',
                        'icon'  => 0
                    );
                    if (isset($client['taluka_id'])){
                        $taluka = Taluka::getTalukaDataFromId($client['taluka_id']);
                    }
                    $client_information[] = array(
                        'key'   => 'Taluka:',
                        'value' => $client['taluka_id'] ? $taluka[0]['taluka_name'] : '',
                        'icon'  => 0
                    );
                    $client_information[] = array(
                        'key'   => 'Zip:',
                        'value' => $client['zip_code'] ? $client['zip_code'] : '',
                        'icon'  => 0
                    );

                    //Prepare Client Address Object
                    $client_addresses = ClientsAddress::getClientDataListWithStateDistrictTaluka($client_id);
                    
                    if (is_array($client_addresses) && !empty($client_addresses)){
                        foreach ($client_addresses as $address) {
                            $client_address[] = array(
                                'id' => $address['id'],
                                'client_id'     => $address['client_id'],
                                'full_name'     => $address['title'],
                                'address'       => get_formatted_address(array($address['address1'], $address['address2'], $address['taluka_name'], $address['district_name'], $address['state_name'], $address['zip_code'])),
                                'address1'      => $address['address1'],
                                'address2'      => $address['address2'],
                                'country_id'    => $address['country_id'],
                                'state_id'      => $address['state_id'],
                                'district_id'   => $address['district_id'],
                                'taluka_id'     => $address['taluka_id'],
                                'zip_code'      => $address['zip_code'],
                                'mobile_number' => $address['mobile_number'],
                                'email'         => $address['email'],
                                'is_deletable'  => 1,
                                'is_editable'   => 0,
                                'is_verified'   => 1,
                                'use_for_billing'   => $address['use_for_billing'],
                                'use_for_shipping'  => $address['use_for_shipping'],
                                'is_default_billing'    => $address['is_default_billing'],
                                'is_default_shipping'   => $address['is_default_shipping'],
                                'is_locked'     => $address['is_locked'],
                                'is_approved'   => $address['is_approved'],
                                'approved_date' => $address['approved_date'],
                            );
                        }
                    }

                    //Prepare Client Contact object
                    $client_contacts = ClientContactPerson::getClientDataListWithDesignation($client_id);

                    if (isset($client_contacts) && !empty($client_contacts)) {
                        foreach ($client_contacts as $contact) {
                            $client_contact[] = array(
                                'id' => $contact['id'],
                                'client_id' => $contact['client_id'],
                                'full_name' => $contact['full_name'],
                                'mobile_number' => $contact['mobile_number'],
                                'whatsapp_number' => $contact['whatsapp_number'],
                                'designation_id' => $contact['designation_id'],                                
                                'designation' => $contact['desiname'],
                                'department' => $contact['department'],
                                'dob' => $contact['dob'],
                                'is_deletable' => 1,
                                'is_editable'  => 0,
                                'is_verified'  => 1,
                                'is_default'   => $contact['is_default'],
                                'created_at'   => $contact['created_at'],
                            );
                        }
                    }

                    ## Set Success Response
                    $data['statistics'] = $client_statistics;
                    $data['client_detail'] = $client_detail;
                    $data['client_information'] = $client_information;
                    $data['client_status'] = array(
                        'status_code' => $client['status'],
                        'status_label' => Config::get('constants.client_status.' . $client['status']),
                        'status_color' => Config::get('constants.client_status_color_code.' . $client['status'])
                    );
                    $data['client_address'] = $client_address;
                    $data['client_contact'] = $client_contact;
                    $data['client_address_message'] = empty($client_address) ? 'No Address Found' : '';
                    $data['client_contact_message'] = empty($client_contact) ? 'No Conatct Found' : '';
                    
                    
                    $this->setStatusCode(Response::HTTP_OK);
                    $message =__('message.msg_success'); //Config::get('messages.msg.msg_record_added');
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
                }
            }else{
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_user_id');
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
     * Get Client Addresses Data By Client ID
     * Request URL : user/get-client-addresses
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getClientAddressesByClientId(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $client_id      = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);
            $user_id        = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            // If Client ID is Exist then Get Client Addresses
            if(($client_id > 0) && ($user_id > 0)){

                //Prepare Client Address Object
                $client_addresses = ClientsAddress::getClientDataListWithStateDistrictTaluka($client_id);
                
                // Check if data is in DB then Prepare Array for Response
                if(is_array($client_addresses) && !empty($client_addresses)){

                    $billing_address = $shipping_address = array();

                    foreach ($client_addresses as $address) {

                        if( $address['use_for_billing'] == 1 ) {

                            $billing_address[] = array(
                                'id'            => $address['id'],
                                'client_id'     => $address['client_id'],
                                'full_name'     => $address['title'],
                                'address'       => get_formatted_address(array($address['address1'], $address['address2'], $address['taluka_name'], $address['district_name'], $address['state_name'], $address['zip_code'])),
                                'address1'      => $address['address1'],
                                'address2'      => $address['address2'],
                                'taluka'        => $address['taluka_name'],
                                'district'      => $address['district_name'],
                                'state'         => $address['state_name']
                            );
                        }
                        if( $address['use_for_shipping'] == 1 ) {

                            $shipping_address[] = array(
                                'id'            => $address['id'],
                                'client_id'     => $address['client_id'],
                                'full_name'     => $address['title'],
                                'address'       => get_formatted_address(array($address['address1'], $address['address2'], $address['taluka_name'], $address['district_name'], $address['state_name'], $address['zip_code'])),
                                'address1'      => $address['address1'],
                                'address2'      => $address['address2'],
                                'taluka'        => $address['taluka_name'],
                                'district'      => $address['district_name'],
                                'state'         => $address['state_name']
                            );
                        }
                    }

                    ## Set Success Response
                    $data['client_billing_addresses'] = $billing_address;
                    $data['client_shipping_addresses'] = $shipping_address;
                    
                    $this->setStatusCode(Response::HTTP_OK);
                    $message =__('message.msg_success'); //Config::get('messages.msg.msg_record_added');
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
                }
            }else{
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_user_id');
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
     * Get Client Contacts Data By Client ID
     * Request URL : user/get-client-addresses
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function getClientContactsByClientId(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $client_id      = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);
            $user_id        = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            // If Client ID is Exist then Get Client Addresses
            if(($client_id > 0) && ($user_id > 0)){

                //Prepare Client Contact object
                $client_contacts = ClientContactPerson::getClientDataList($client_id);
                
                // Check if data is in DB then Prepare Array for Response
                if(is_array($client_contacts) && !empty($client_contacts)){

                    $client_contact = array();

                    foreach ($client_contacts as $contact) {
                        $client_contact[] = array(
                            'id' => $contact['id'],
                            'client_id' => $contact['client_id'],
                            'full_name' => $contact['full_name'],
                            'mobile_number' => $contact['mobile_number'],
                            'whatsapp_number' => $contact['whatsapp_number'],
                            'department' => $contact['department'],
                            'dob' => $contact['dob']
                        );
                    }

                    ## Set Success Response
                    $data['client_contacts'] = $client_contact;
                    
                    $this->setStatusCode(Response::HTTP_OK);
                    $message =__('message.msg_success'); //Config::get('messages.msg.msg_record_added');
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
                }
            }else{
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_user_id');
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
     * Function: Add Client
     * Request URL : user/add-client-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function addClientData(Request $request){
        $event_data_array = array();
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            // Validate Request Object for Processed Further
            $validator = $this->validateClientAdd($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Insert Recored
                $data=$post;
                if(!empty($user_id)){

                    $insert_client = array();
                    $insert_client['client_code'] = 0;
                    $insert_client['client_name']       = $data['client_name'] ? $data['client_name'] : '';
                    $insert_client['mobile_number']     = $data['mobile_number'] ? $data['mobile_number'] : '';
                    $insert_client['whatsapp_number']   = $data['whatsapp_number'] ? $data['whatsapp_number'] : '';
                    $insert_client['email']     = $data['email'] ? $data['email'] : '';
                    $insert_client['sales_user_id']     = $user_id ? $user_id : '';
                    $insert_client['client_type']       = $data['type'] ? $data['type'] : '';
                    $insert_client['gst_no']       = $data['gst_no'] ? $data['gst_no'] : '';
                    $insert_client['pan_no']       = $data['pan_no'] ? $data['pan_no'] : '';
                    $insert_client['country_id']   = 1;
                    $insert_client['state_id']     = $data['state_id'] ? $data['state_id'] : '';
                    $insert_client['district_id']  = $data['district_id'] ? $data['district_id'] : '';
                    $insert_client['taluka_id']    = $data['taluka_id'] ? $data['taluka_id'] : '';
                    $insert_client['taluka_id']    = $data['taluka_id'] ? $data['taluka_id'] : '';
                    $insert_client['zip_code']     = $data['zip_code'] ? $data['zip_code'] : '';
                    $insert_client['created_by']   = $user_id;

                    // Insert Client in Respected Table
                    $insert = Clients::addClients($insert_client);

                    // Get Client Type
                    $client_type=ClientType::getClientDataList();

                    // Initialize Client Prefix
                    $client_code_prefix=[];

                    foreach($client_type as $d1){
                        $client_code_prefix[$d1['id']]=strtoupper(substr($d1['name'], 0, 2));
                    }

                    // Update Client Code With Unique Identifier
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
    
    /**
     * Summary of addClientAddressData
     * Create Address for Client
     * Request Endpoint : user/add-client-address-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function addClientAddressData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            // Validate Request Object for Processed Further
            $validator = $this->validateClientAddAddress($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Insert Recored
                $data=$post;
                if(!empty($user_id) && !empty($post['client_id'])){
                    // Get District Data
                    $district_data = Districts::getDistrictsDataFromId($data['district_id']);
                    
                    $email = empty($post['email']) ? NULL : $post['email'];

                    $bill=0;
                    $ship=0;

                    // Remove Default Address id Form Client Table
                    if($post['use_for_billing']==1 && $post['is_default_billing']==1){
                        $bill=1;
                        ClientsAddress::updateClientAddressDefultSet(['client_id'=>$post['client_id']],['is_default_billing'=>0]);
                    }
                    if($post['use_for_shipping']==1 && $post['is_default_shipping']==1){
                        $ship=1;
                        ClientsAddress::updateClientAddressDefultSet(['client_id'=>$post['client_id']],['is_default_shipping'=>0]);
                    }

                    // Prepare data for Create Client Address
                    $client_address = array(
                        'client_id'     => $data['client_id'] ? $data['client_id'] : '',
                        'title'         => $data['title'] ? $data['title'] : '',
                        'mobile_number' => $data['mobile_number'] ? $data['mobile_number'] :'',
                        'email'         => $data['email'] ? $data['email'] : '',
                        'address1'      => $data['address1'] ? $data['address1'] : '',
                        'address2'      => $data['address2'] ? $data['address2'] : '',
                        'country_id'    => $district_data[0] ? $district_data[0]['country_id'] : '',
                        'state_id'      => $data['state_id'] ? $data['state_id'] : '',
                        'district_id'   => $data['district_id'] ? $data['district_id']  :'',
                        'taluka_id'     => $data['taluka_id'] ? $data['taluka_id'] : '',
                        'zip_code'      => $data['zip_code'] ? $data['zip_code'] : '',
                        'use_for_billing'       => $data['use_for_billing'] ? $data['use_for_billing'] : 0,
                        'use_for_shipping'      => $data['use_for_shipping'] ? $data['use_for_shipping'] : 0,
                        'is_default_billing'    => $data['is_default_billing'] ? $data['is_default_billing'] : 0,
                        'is_default_shipping'   => $data['is_default_shipping'] ? $data['is_default_shipping'] : 0,
                    );

                    // Set Default Address id Form Client Table
                    $insert=ClientsAddress::addClientAddress($client_address);
                    if($insert){
                        if($bill==1){
                            ClientsAddress::updateClientAddressDefultSet(['id'=>$insert['id']],['is_default_billing'=>1]);
                        }
                        if($ship==1){
                            ClientsAddress::updateClientAddressDefultSet(['id'=>$insert['id']],['is_default_shipping'=>1]);
                        }
                        $this->setStatusCode(Response::HTTP_OK);
                        $message =__('message.msg_record_added');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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
    /**
     * Summary of addClientContactData
     * Create Contact Person for Client
     * Request Endpoint : user/add-client-contact-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function addClientContactData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);

            $validator = $this->validateClientAddContact($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Insert Recored
                $data=$post;
                if(!empty($user_id) && !empty($post['client_id'])) {
                    
                    $is_default=0;
                    if($post['is_default']==1){
                        $is_default=1;
                        ClientContactPerson::updateClientPersonDefultSet(['client_id'=>$post['client_id']],['is_default'=>0]);
                    }
                    $client_contact = array(
                        'client_id' => $data['client_id'] ? $data['client_id'] : '',
                        'full_name' => $data['full_name'] ? $data['full_name'] : '',
                        'mobile_number'     => $data['mobile_number'] ? $data['mobile_number'] : '',
                        'whatsapp_number'   => $data['whatsapp_number'] ? $data['whatsapp_number'] : '',
                        'designation_id'   => $data['designation_id'] ? $data['designation_id'] : '',
                        'department'    => $data['department'] ? $data['department'] : '',
                        'dob'   => $data['dob'] ? $data['dob'] : '',
                    );
                    $insert=ClientContactPerson::addClientContactPerson($client_contact);
                    if($insert){
                        if($is_default==1){
                            ClientContactPerson::updateClientPersonDefultSet(['id'=>$insert['id']],['is_default'=>1]);
                        }
                        $this->setStatusCode(Response::HTTP_OK);
                        $message = __('message.msg_record_added');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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
    
    /**
     * Function: Edit Profile
     * Request Endpoint : user/update-user-profile-details
     *
     * @param    string  $request
     * @return   json    
     */
    public function editProfile(Request $request){
		try{
	        ## Variable Declaration
	        $photo = '';
			 
	        ## Check Validation
	        $validator = $this->validateProfile($request->all());
	        if ($validator->fails()) {
	            ## Invalid request parameters
	            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	            $message = array_unique($validator->errors()->all());
	            return $this->prepareResult($message, $data = []);  
	        }

        	## Get old image from db
        	$cust_data = Customer::getCustomerDataFromId($request->input('id'));
        	$old_photo = isset($cust_data[0]['image']) ? $cust_data[0]['image'] : '';

			## Image Upload
			if($request->has('image_url') && $request->filled('image_url')){
				$photo = getBaseNameFromImageURL($request->input('image_url'));
				/*if(config('settings.SITE_IMAGES_STORAGE') == config('constants.SITE_IMAGES_STORAGE_AWS')){
					## Store image to s3 server
					storeImageinAWS($request->input('image_url'), $this->AWSdestinationPath, $photo, $this->size_array);
				}else{*/
					## Store image to local storage
					storeImageinFolder($request->input('image_url'), $this->destinationPath, $photo, $this->size_array);
				// }
				## Remove old image from folder
				if ($old_photo != "") {

					// if(config('settings.SITE_IMAGES_STORAGE') == config('constants.SITE_IMAGES_STORAGE_AWS')){
					// 	deleteImageFromAWS($old_photo, $this->AWSdestinationPath,$this->size_array);
					// }else{
						deleteImageFromFolder($old_photo, $this->destinationPath, $this->size_array);
					// }
				}
			}else{
				$photo = $old_photo;
			}

			$customer_type = ($request->has('customer_type'))?$request->input('customer_type'):Customer::INDIVIDUAL;
			
	        ## Edit Profile
	        $update_array = array(
	            	'first_name'    => $request->input('first_name', ''),
                    'last_name'     => $request->input('last_name', ''),
                    'mobile'        => $request->input('mobile', ''),
                    'dob'           => date('Y-m-d',strtotime($request->input('dob'))),
                    'lang_code'     => $request->input('language', ''),
                    'email'         => $request->input('email', ''),
                    'image' 		=> $photo,
                    'updated_at'    => date_getSystemDateTime(),
                    'type'			=>$customer_type,
                    'company_name'	=>$request->input('company_name', ''),
	        ); 
	        $update = Customer::updateCustomer($request->input('id'), $update_array);

	        if(isset($update)){
	        	## Delete Customer Address
	        	CustomerAddress::deleteCustomerAddress($request->input('id'));
	        	## Add Customer Address
	        	if($request->has('address') && !empty($request->input('address'))){
	        		CustomerAddress::addCustomerAddress($request->input('address'));
	        	}
	        	## Upload Identification Images
                    if($customer_type == Customer::CORPORATE){
                        if($request->has('identityfile') && !empty($request->input('identityfile'))){
                            if($request->input('device_type') == '2'){
                                
                            }else{
                                foreach ($request->input('identityfile') as $key => $value) 
                                {
                                	if($value != ""){
	                                    $identImage = basename($value);
	                                    saveFileFromURL($value, $this->customer_identification_path, $identImage);
	                                    $insertCusIdentArr = array(
	                                        'customer_id'=>$request->input('id'),
	                                        'file'=>$identImage
	                                    );
	                                    CustomerIdentification::insertFile($insertCusIdentArr);
	                                }
                                }
                                
                            }
                        }
                    }

	            ## Success
	            $this->setStatusCode(Response::HTTP_OK);
	            $message[] = __('message.msg_profile_edited_successfully');
	        }else{
	            ## Error in editing profile
	            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	            $message[] = __('message.msg_error_profile');
	        }   
	    }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message[] = $e->getMessage();
        }

        ## Return Response
	    return $this->prepareResult($message, $data = []);
    }

    /**
     * Function: change password
     * Request Endpoint : change-password
     *
     * @param    string  $request
     * @return   json    
    **/
    public function changePassword(Request $request) {
    	try{

	        ## Check Validation
	        $validator = $this->validatePassword($request->all());
	        if ($validator->fails()) {
	            ## Invalid request parameters
	            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	            $message = array_unique($validator->errors()->all());
	            return $this->prepareResult($message, $data = []);  
	        }
 			#get customer data
        	$cust_data = Customer::getCustomerDataFromId($request->input('buyer_id'));
        	if(!empty($cust_data)){
                    
                    ## Details of requested customer
                    $password  = $cust_data[0]['password'];
                
                        
                    if(Hash::check(trim($request->input('current_password')), $password)){
                        
                        $update_array = array (
                        		'password'      => bcrypt($request->input('password')),
                				'password_hash' => encryptByHC($request->input('password')),
                        	);
                       	$update = Customer::updateCustomer($request->input('buyer_id'), $update_array);
                        if(isset($update)){
					            ## Success
					            $this->setStatusCode(Response::HTTP_OK);
					            $message[] = __('message.msg_password_change_sucess');
				        }else{
				            ## Error in editing profile
				            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
				            $message[] = __('message.msg_error_password_change');
				        } 
                    }else{
                        ## Invalid credentials
                        $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $message[] = __('message.msg_password_incorrect'); 
                    }
            }else{
               ## Invalid credentials
                $this->setStatusCode(Response::HTTP_BAD_REQUEST);
                $message[] = __('message.msg_credential_not_match');        
            }

	    }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message[] = $e->getMessage();
        }

        ## Return Response
	    return $this->prepareResult($message, $data = []);
    }

    /**
     * Summary of updateClientAddressData
     * update Address for Client
     * Request Endpoint : user/edit-client-address-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function editClientAddressData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            
            $user_id    = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $address_id = (isset($post['address_id']) && $post['address_id'] != "" ? $post['address_id'] : 0);
            $client_id  = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);

            // Validate Request Object for Processed Further
            $validator = $this->validateClientEditAddress($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Update Recored
                $data=$post;
                if(!empty($address_id) && !empty($user_id) && !empty($client_id)){
                    // Get District Data
                    $district_data = Districts::getDistrictsDataFromId($data['district_id']);
                    $bill=0;
                    $ship=0;

                    // Remove Default Address id Form Client Table
                    if($post['use_for_billing']==1 && $post['is_default_billing']==1){
                        $bill=1;
                        ClientsAddress::updateClientAddressDefultSet(['client_id'=>$post['client_id']],['is_default_billing'=>0]);
                    }
                    if($post['use_for_shipping']==1 && $post['is_default_shipping']==1){
                        $ship=1;
                        ClientsAddress::updateClientAddressDefultSet(['client_id'=>$post['client_id']],['is_default_shipping'=>0]);
                    }

                    // Prepare data for Update Client Address
                    $update_address = array(
                        'title'         => $data['title'] ? $data['title'] : '',
                        'mobile_number' => $data['mobile_number'] ? $data['mobile_number'] :'',
                        'email'         => $data['email'] ? $data['email'] : '',
                        'address1'      => $data['address1'] ? $data['address1'] : '',
                        'address2'      => $data['address2'] ? $data['address2'] : '',
                        'country_id'    => $district_data[0] ? $district_data[0]['country_id'] : '',
                        'state_id'      => $data['state_id'] ? $data['state_id'] : '',
                        'district_id'   => $data['district_id'] ? $data['district_id']  :'',
                        'taluka_id'     => $data['taluka_id'] ? $data['taluka_id'] : '',
                        'zip_code'      => $data['zip_code'] ? $data['zip_code'] : '',
                        'use_for_billing'       => $data['use_for_billing'] ? $data['use_for_billing'] : 0,
                        'use_for_shipping'      => $data['use_for_shipping'] ? $data['use_for_shipping'] : 0,
                        'is_default_billing'    => $data['is_default_billing'] ? $data['is_default_billing'] : 0,
                        'is_default_shipping'   => $data['is_default_shipping'] ? $data['is_default_shipping'] : 0,
                    );

                    // Set Default Address id Form Client Table
                    $insert=ClientsAddress::updateClientAddress($address_id, $update_address);
                    if($insert){
                        if($bill==1){
                            ClientsAddress::updateClientAddressDefultSet(['id'=>$insert['id']],['is_default_billing'=>1]);
                        }
                        if($ship==1){
                            ClientsAddress::updateClientAddressDefultSet(['id'=>$insert['id']],['is_default_shipping'=>1]);
                        }
                        $this->setStatusCode(Response::HTTP_OK);
                        $message =__('message.msg_your_address_updated_successfully');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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

    /**
     * Summary of Edit Client Contact Data
     * Create Contact Person for Client
     * Request Endpoint : user/edit-client-contact-data
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function editClientContactData(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();
            $user_id = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $contact_id = (isset($post['contact_id']) && $post['contact_id'] != "" ? $post['contact_id'] : 0);
            $client_id  = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);
            

            $validator = $this->validateClientEditContact($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Insert Recored
                $data=$post;
                if(!empty($user_id) && !empty($client_id) && !empty($contact_id)) {
                    
                    $is_default=0;
                    if($post['is_default']==1){
                        $is_default=1;
                        ClientContactPerson::updateClientPersonDefultSet(['client_id'=>$post['client_id']],['is_default'=>0]);
                    }
                    $client_contact = array(
                        'full_name' => $data['full_name'] ? $data['full_name'] : '',
                        'mobile_number'     => $data['mobile_number'] ? $data['mobile_number'] : '',
                        'whatsapp_number'   => $data['whatsapp_number'] ? $data['whatsapp_number'] : '',
                        'designation_id'   => $data['designation_id'] ? $data['designation_id'] : '',
                        'department'    => $data['department'] ? $data['department'] : '',
                        'dob'   => $data['dob'] ? $data['dob'] : '',
                        'is_default'   => $data['is_default'] ? $data['is_default'] : '',
                    );
                    $update = ClientContactPerson::updateClientContactPerson($contact_id, $client_contact);
                    
                    if($update){
                        if($is_default==1){
                            ClientContactPerson::updateClientPersonDefultSet(['id'=>$contact_id],['is_default'=>1]);
                        }
                    }
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_your_contact_updated_successfully');
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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
    /**
     * Summary of deleteClientAddressData
     *  Delete Client Address by Address ID 
     * Request URL : user/delete-client-address
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function deleteClientAddress(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();

            $user_id    = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $address_id = (isset($post['address_id']) && $post['address_id'] != "" ? $post['address_id'] : 0);
            $client_id  = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);

            // Validate Request Object for Processed Further
            $validator = $this->validateClientAddressForDelete($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Check
                
                if(!empty($address_id) && !empty($user_id) && !empty($client_id)){

                    // Check if Entry Is Exist in DB
                    $check = ClientsAddress::getClientAddressDataFromId($address_id);
                    
                    if(!empty($check)){

                        //Then Delete The Record
                        ClientsAddress::deleteClientAddressData(array($address_id));
                        $this->setStatusCode(Response::HTTP_OK);
                        $message =__('message.msg_record_delete');
                    }else{
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message =__('message.msg_record_not_found');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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

    /**
     * Summary of deleteClientContact
     * Delete Client Contact Person By Contact Id
     * Request URl : user/delete-client-contact
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function deleteClientContact(Request $request){
        $data = $where_arr = array();
        try{
            $post = $request->all();

            $user_id    = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $contact_id = (isset($post['contact_id']) && $post['contact_id'] != "" ? $post['contact_id'] : 0);
            $client_id  = (isset($post['client_id']) && $post['client_id'] != "" ? $post['client_id'] : 0);

            // Validate Request Object for Processed Further
            $validator = $this->validateClientContactForDelete($request->all());
            if($validator->fails()){
                ## Handle Exception
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $validator_message = $validator->errors()->all();
                $message = $validator_message;           
            }else{
                ## Check 
                if(!empty($contact_id) && !empty($user_id) && !empty($client_id)){

                    // Check if Entry Is Exist in DB
                    $check = ClientContactPerson::getClientContactPersonDataFromId($contact_id);
                    
                    if(!empty($check)){

                        // Then Delete the Record
                        ClientContactPerson::deleteClientContactPersonData(array($contact_id));
                        $this->setStatusCode(Response::HTTP_OK);
                        $message =__('message.msg_record_delete');
                    }else{
                        $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                        $message =__('message.msg_record_not_found');
                    }
                }else{
                    ## Event not found
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_record_not_found');
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

    /**
     * Summary of insertContactUsData
     * Create Enter About Contact Us
     * 
     * @param Request $request
     * @return \App\Traits\General\json
     */
    public function insertContactUsData(Request $request) {
		$data = array();
        try{
            $insertArr = array();
            $post = $request->all();
            
            $question_type           = (isset($post['question_type']) && $post['question_type'] != "" ? $post['question_type'] : 1);
            $user_id                 = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $contact_us_message      = (isset($post['message']) && $post['message'] != "" ? $post['message'] : '');
        
            if($user_id > 0){
                // Insert Into contact_us table
                $insertArr = array(
                        'user_id'		=> $user_id,
                        'question_type' => $question_type,
                        'message'		=> $contact_us_message,
                        'created_at'	=> date_getSystemDateTime()
                	);

                $insertData = ContactUs::addContactUs($insertArr);

                if($insertData){
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_contact_question_submit');
                }else{
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message =  _('message.msg_something_went_wrong');
                }
            }else{
                $data = (Object)$data;
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_parameter_missing');
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        
        ## Return Response
        return $this->prepareResult($message, $data);
	}

    public function getContactUsData (Request $request){
        $data = array();
        try{
            $insertArr = array();
            $post = $request->all();

            echo '<pre>'; print_r($post); echo '</pre>'; exit();
            
            $question_type           = (isset($post['question_type']) && $post['question_type'] != "" ? $post['question_type'] : 1);
            $user_id                 = (isset($post['user_id']) && $post['user_id'] != "" ? $post['user_id'] : 0);
            $contact_us_message      = (isset($post['message']) && $post['message'] != "" ? $post['message'] : '');
        
            if($user_id > 0){
                // Insert Into contact_us table
                $insertArr = array(
                        'user_id'		=> $user_id,
                        'question_type' => $question_type,
                        'message'		=> $contact_us_message,
                        'created_at'	=> date_getSystemDateTime()
                	);

                $insertData = ContactUs::addContactUs($insertArr);

                if($insertData){
                    $this->setStatusCode(Response::HTTP_OK);
                    $message = __('message.msg_contact_question_submit');
                }else{
                    $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                    $message =  _('message.msg_something_went_wrong');
                }
            }else{
                $data = (Object)$data;
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_request_parameter_missing');
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        
        ## Return Response
        return $this->prepareResult($message, $data);
    }
}
