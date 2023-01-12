<?php 
namespace App\Traits;

use GuzzleHttp;
use Exception;
//use LaravelLocalization;
use Response;

trait GuzzleRequest
{
    /**
     * Function: HTTP client request with get method 
     *
     * @param   string  $route
     * @param   array   $params
     * @param   integer $is_json_request
     * @param   integer $is_master
     * @return  array   $response
     */
    private static function get($route = '', $params = array(), $is_json_request = 1, $is_master = 0){
        
        try {
            
            ## Prepare API URL
            $url = self::getApiURL().$route;

            ## Set Get api Parameters
            $data = self::setRequestParams($route, $params, $is_json_request, $is_master);
            
            ## Set api Headers
            $headers = self::setRequestHeaders($is_json_request);
            
            ## Call to Guzzle HTTP Client
            $client = new GuzzleHttp\Client(['headers' => $headers]);
            $response = $client->get($url, ['body' => $data]);
            
            ## Fetch Response Body
            $response = json_decode($response->getBody()); 
            $response = json_decode(json_encode($response), true);

            ## Return Response
            return $response;

        } catch (Exception $e) {
            
            ## Handle Exception
            $response = json_decode($e->getResponse()->getBody()); 
            $response = json_decode(json_encode($response), true);
            
            if(!empty($response['message'])){
                if(is_array($response['message'])){
                    $response = implode("</br>", $response['message']);                    
                }else{
                    $response = $response['message'];
                }
            }else{
                $response = $e->getMessage();                
            }
            
            abort(500, $response);
        }
    }

    /**
     * Function: HTTP client request with post method 
     *
     * @param   string  $route
     * @param   array   $params
     * @param   integer $is_json_request
     * @param   integer $is_master
     * @return  array   $response
     */
    private static function post($route = '', $params = array(), $is_json_request = 1, $is_master = 0){
        
        try {

            ## If _token is exist in post parameters, remove it. because we do not need to pass it in api
            if(isset($params['_token'])){
                unset($params['_token']);
            }

            ## Prepare API URL
            $url = self::getApiURL().$route;

            ## Set Post api Parameters
            $data = self::setRequestParams($route, $params, $is_json_request, $is_master);
           // print_r($data);exit();
            ## Set api Headers
            $headers = self::setRequestHeaders($is_json_request);

            ## Call to Guzzle HTTP Client
            $client = new GuzzleHttp\Client(['headers' => $headers,'allow_redirects' => false]);
            $response = $client->post($url, ['body' => $data]);
            
            ## Fetch Response Body
            $response = json_decode($response->getBody()); 

        } catch (Exception $e) {
            ## Handle Exception
            $response = json_decode($e->getResponse()->getBody()); 
        }
                        
        ## Return Response
        $response = json_decode(json_encode($response), true);

        return $response;
    }

    /**
     * Function: Get api url
     *
     * @return  string (URL of API)
     */
    private static function getApiURL(){
        return config('constants.API_URL');
    }

    /**
     * Function: Get autorization token
     *
     * @return  string (autorization token based on device type and entity)
     */
    private static function getAutorizationToken(){
        return config('constants.AUTHORIZATION_TOKEN');
    }

    /**
     * Function: Get device type
     *
     * @return  string (device type)
     */
    private static function getDeviceType(){
        return config('constants.DEVICE_TYPE');
    }

    /**
     * Function: Get entity type
     *
     * @return  string (entity type)
     */
    private static function getEntityType(){
        return config('constants.ENTITY_TYPE');
    }

    /**
     * Function: Get session prefix
     *
     * @return  string (session prefix)
     */
    private static function getSessionPrefix(){
        return config('constants.SESSION_PREFIX');
    }

    /**
     * Function: Set request headers
     *
     * @param   integer $is_json_request
     * @return  array   $headers
     */
    private static function setRequestHeaders($is_json_request = 1){
        $headers = array('Authorization' => self::getAutorizationToken());
        if($is_json_request == 1){
            $headers['Content-Type'] = 'application/json';
        }
        if(session()->has(self::getSessionPrefix().'.user_auth_token')){
            $headers['username'] = session(self::getSessionPrefix().'.user_auth_token');
        }
        return $headers;
    }

    /**
     * Function: Set request parameters
     *
     * @param   string      $route
     * @param   array       $params
     * @param   integer     $is_json_request
     * @param   integer     $is_master
     * @return  json/array  $params
     */
    private static function setRequestParams($route = '', $params = array(), $is_json_request = 1, $is_master = 0){
        //$params['lang_code'] = LaravelLocalization::getCurrentLocale();

		if(session()->has(self::getSessionPrefix().'.auth_data.id') && session()->get(self::getSessionPrefix().'.auth_data.id') > 0 ){
			$params['buyer_id'] = session(self::getSessionPrefix().'.auth_data.id');
		}
        if($is_master == 0){
            $params['device_type'] = self::getDeviceType();
            
            if($route == 'login'){
                $params['entity_type_id'] = self::getEntityType();
                $params['device_uuid'] = "123456";
                $params['device_no'] = "1234567";
                $params['device_name'] = "test";
                $params['device_platform'] = "test platform";
                $params['device_model'] = "test model";
                $params['device_version'] = "1.0";
                $params['app_version'] = "1.0.1";
            }
        }
        if($is_json_request == 1){
            $params = json_encode($params);
        }
        return $params;
    }
}