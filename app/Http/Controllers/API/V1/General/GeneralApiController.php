<?php 

namespace App\Http\Controllers\API\V1\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\ApiFunction;
use App\Traits\GeneralContactUs;
use App\Models\Language; 
use App\Models\Country; 
use App\Models\State; 
use App\Models\Districts; 
use App\Models\Taluka; 
use App\Models\Designation; 
use App\Models\Medium; 
use App\Models\Series; 
use App\Models\ClientType; 
use App\Models\ProductHead; 
use App\Models\Segment; 
use App\Models\Semester; 
use App\Models\PaymentTerms; 
use App\Models\Transporter; 
use Exception;
use DB;
use Arr;

class GeneralApiController extends Controller
{
    use ApiFunction;

    public $COUNTRY_FOLDER  = "";
    public $LANGUAGE_FOLDER  = "";
    public $language_path  = "";
 
        
    public function __construct(){
        $this->COUNTRY_FOLDER = config('path.AWS_COUNTRY');
        $this->LANGUAGE_FOLDER = config('path.AWS_LANGUAGE');
        $this->language_path = config('path.language_path');
        $this->aws_bucket_array = config('constants.aws_bucket_array');
    }

    /**
     * Function: Get master data based on data request
     *
     * @param    array $request
     * @return   json    
     */
    public function getMasterdata(Request $request){
        try{

            ## Variable Declaration
            $data = array();
            ## Check for data request
            if($request->has('data-request') && !empty(array_filter($request->input('data-request')))){
                
                $data_request_array = array_filter($request->input('data-request'));
                $lang_code = $request->input('lang_code', '');
                 
                ## Get country data 
                if(in_array('country', $data_request_array)){
                    $where_country_arr = array('status' => Country::ACTIVE, 'lang_code' => $lang_code);
                    $data['country'] = Country::getAllActiveCountries($where_country_arr); 
                    // echo "<pre>"; print_r($data['country']);exit();
                }

                ## Get state data 
                if(in_array('state', $data_request_array)){
                    $state_data = array();
                    if($request->has('country_id') && $request->filled('country_id')){
                        $where_state_arr = array('status' => State::ACTIVE, 'lang_code' => $lang_code, 'country_id' => $request->input('country_id'));
                        $state_data = State::getStateDataFromCountryId($where_state_arr); 
                    }
                    $data['state'] = $state_data;
                    // echo "<pre>"; print_r($data['state']);exit();
                }

                ## Get ditrict data 
                if(in_array('district', $data_request_array)){
                    $district_data = array();
                    if($request->has('state_id') && $request->filled('state_id')){
                        $where_district_arr = array('status' => Districts::ACTIVE, 'lang_code' => $lang_code, 'country_id' => $request->input('country_id'), 'state_id' => $request->input('state_id'));
                        $district_data = Districts::getDistrictsFromStateAndCountryId($where_district_arr);
                    }
                    $data['district'] = $district_data;
                    // echo "<pre>"; print_r($data['district']);exit();
                }

                ## Get Taluka data 
                if(in_array('taluka', $data_request_array)){
                    $taluka_data = array();
                    if($request->has('state_id') && $request->filled('state_id')){
                        $where_taluka_arr = array('status' => Taluka::ACTIVE, 'lang_code' => $lang_code, 'country_id' => $request->input('country_id'), 'state_id' => $request->input('state_id'), 'district_id' => $request->input('district_id'));
                        $taluka_data = Taluka::getTalukaFromCSDId($where_taluka_arr);
                    }
                    $data['taluka'] = $taluka_data;
                    // echo "<pre>"; print_r($data['taluka']);exit();
                }

                ## Get designation data with lang code
                if(in_array('designation', $data_request_array)){
                    $designation_data = array();
                    $designation_data = Designation::getAllActiveDesignation();
                    $data['designation'] = $designation_data;
                    // echo "<pre>"; print_r($data['designation']);exit();
                }

                ## Get series data 
                if(in_array('series', $data_request_array)){
                    $where_series_arr = array('status' => Series::ACTIVE, 'lang_code' => $lang_code);
                    $data['series'] = Series::getAllActiveSeries($where_series_arr); 
                    // echo "<pre>"; print_r($data['series']);exit();
                }

                ## Get board-medium data with lang code
                if(in_array('board_medium', $data_request_array)){
                    $board_medium_data = array();
                    $data['board_medium'] = Medium::getMediumDataWithBoad();
                    // echo "<pre>"; print_r($data['board_medium']);exit();
                }

                ## Get Client Type data 
                if(in_array('client_type', $data_request_array)){
                    $data['client_type'] = ClientType::getClientDataList(); 
                    // echo "<pre>"; print_r($data['client_type']);exit();
                }

                ## Get Product Head data 
                if(in_array('product_head', $data_request_array)){
                    $data['product_head'] = ProductHead::getProductHeadAllList(); 
                    // echo "<pre>"; print_r($data['product_head']);exit();
                }

                ## Get segment data 
                if(in_array('segment', $data_request_array)){
                    $data['segment'] = Segment::getAllActiveSegmentData(); 
                    // echo "<pre>"; print_r($data['segment']);exit();
                }

                ## Get semester data 
                if(in_array('semester', $data_request_array)){
                    $data['semester'] = Semester::getSemesterDataList(); 
                    // echo "<pre>"; print_r($data['semester']);exit();
                }

                ## Get Payment Terms data 
                if(in_array('payment_terms', $data_request_array)){
                    $data['payment_terms'] = PaymentTerms::getAllPaymentTermsData(); 
                    // echo "<pre>"; print_r($data['payment_terms']);exit();
                }

                ## Get Transporter data 
                if(in_array('transporter', $data_request_array)){
                    $data['transporter'] = Transporter::getAllTransporters();
                    // echo "<pre>"; print_r($data['transporter']);exit();
                }

                ## Get Route Area data 
                if(in_array('route_area', $data_request_array)){
                    $data['route_area'] = array(
                        array(
                            'id' => '1',
                            'name' => 'Ahmedabad-morbi',
                        ),
                        array(
                            'id' => '2',
                            'id' => '3',
                            'name' => 'Ahmedabad-rajkot-jamnager',
                        ),
                        array(
                            'id' => '3',
                            'name' => 'Jetpur-shomnath',
                        ),
                        array(
                            'id' => '4',
                            'name' => 'Bhuj'
                        )
                    );
                    // echo "<pre>"; print_r($data['route_area']);exit();
                }

                ## Success
                $this->setStatusCode(Response::HTTP_OK);
                $message =  __('message.msg_success');
                // $message = __('message.msg_success');
            }else{
                ## Invalid Request Data Type
                $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                $message = __('message.msg_invalid_request_data_type');
            }
        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        
        ## Return Response
        return $this->prepareResult($message, $data);
    }

    /**
     * Function: Get master data based on data request
     *
     * @param    array $request
     * @return   json    
     */
    public function getContactUsData(Request $request){
        $data = array();
        try{
            $contact_us_question_type_arr = $question_type_arr = array();
            $post = $request->all();

           
            ########### Changes given by TL - Riddhi 28/07/2022 [for add dynamic lable data]
            $data['contact_info'] = array(
                        array(
                            'key' => 'Missed Call on',
                            'value' => '917458961023'
                        ),
                        array(
                            'key' => "Say 'Hello' On",
                            'value' => "919033239582"
                        ),
                        array(
                            'key' => "Email Us",
                            'value' => "info@idealoms.com"
                        ),
                        array(
                            'key' => "Website",
                            'value' => "https://www.ideal.ind.in/"
                        ),
                        array(
                            'key' => 'Location',
                            'value' => 'Jain International Organization Panchshil Plaza, A Wing Basement, Hughes Road, Near Dharam Palace, Mumbai, Maharashtra-400007 GSTIN : 27AAECL0G1Z9'
                        ),array(
                            'key' => 'PAN',
                            'value' => 'AAECL8470G'
                        )
                    );
            $data['follow_links'] = array(
                                    'facebook_link'=>config('settings.FACEBOOK_LINK'),
                                    'instagram_link'=>config('settings.INSTAGRAM_LINK'),
                                    'youtube_link'=>config('settings.YOUTUBE_LINK'),
                                );

            $this->setStatusCode(Response::HTTP_OK);
            $message = __('label.lbl_sucess');


        }catch(Exception $e){
            ## Handle Exception
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $message = $e->getMessage();
        }
        
        ## Return Response
        return $this->prepareResult($message, $data);
    }
}
