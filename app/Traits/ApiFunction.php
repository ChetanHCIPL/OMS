<?php 

namespace App\Traits;

use Illuminate\Http\Response;
use App\Models\EntityType;

trait ApiFunction
{
    private $_status_code = Response::HTTP_OK;

	/**
     * Function: Get All HTTP Status
     *
     * @return   array $status
     */
	private function allHTTPStatus(){
		$status = array(
				100 => 'Continue',  
				101 => 'Switching Protocols',  
				200 => 'OK',
				201 => 'Created',  
				202 => 'Accepted',  
				203 => 'Non-Authoritative Information',  
				204 => 'No Content',  
				205 => 'Reset Content',  
				206 => 'Partial Content',  
				300 => 'Multiple Choices',  
				301 => 'Moved Permanently',  
				302 => 'Found',  
				303 => 'See Other',  
				304 => 'Not Modified',  
				305 => 'Use Proxy',  
				306 => '(Unused)',  
				307 => 'Temporary Redirect',  
				400 => 'Bad Request',  
				401 => 'Unauthorized',  
				402 => 'Payment Required',  
				403 => 'Forbidden',  
				404 => 'Not Found',  
				405 => 'Method Not Allowed',  
				406 => 'Not Acceptable',  
				407 => 'Proxy Authentication Required',  
				408 => 'Request Timeout',  
				409 => 'Conflict',  
				410 => 'Gone',  
				411 => 'Length Required',  
				412 => 'Precondition Failed',  
				413 => 'Request Entity Too Large',  
				414 => 'Request-URI Too Long',  
				415 => 'Unsupported Media Type',  
				416 => 'Requested Range Not Satisfiable',  
				417 => 'Expectation Failed', 
				422 => 'Unprocessable Entity', 
				424 => 'Quantity Exceeded',
				500 => 'Internal Server Error',  
				501 => 'Not Implemented',  
				502 => 'Bad Gateway',  
				503 => 'Service Unavailable',  
				504 => 'Gateway Timeout',  
				505 => 'HTTP Version Not Supported'
			);
		return $status;
	}

	/**
     * Function: Set Status Code for Current Request
     *
     * @param   string 	$status_code
     * @return  void
     */
	private function setStatusCode($status_code){
        $this->_status_code = $status_code;
    }

    /**
     * Function: Get Status Code for Current Request
     *
     * @return   string (status code)
     */
	private function getStatusCode(){
        return $this->_status_code;
    }

    /**
     * Function: Get Server Messages
     *
     * @return   string (HTTP Message based on status code)
     */
	private function getHTTPMessage(){
		$status = $this->allHTTPStatus();
		return ($status[$this->_status_code])?$status[$this->_status_code]:$status[Response::HTTP_INTERNAL_SERVER_ERROR];
	}

	/**
     * Function: Get Request Header
     *
     * @param    string 	$request
     * @param    string 	$param
     * @return   array/string based on $param
     */
	public function getRequestHeader($request, $param = ''){
		if($param == ''){
			return $request->header();			
		}else{
			return $request->header($param);			
		}
	}
	
	/**
     * Function: Prepare JSON Response
     *
     * @param    string 	$message
     * @param    array 		$data
     * @return   json 		(Response of API)
     */
	private function prepareResult($message = '', $data = array()){
		/* if(empty($data)){
			$data = (object)$data;
		}
		if(!empty($data['banners'])){
			$data = (array)$data['banners'];
		} */
		if (version_compare(phpversion(), '7.1', '>=')) {
		    ini_set( 'serialize_precision', -1 );
		}
		$response = [
			'status_code' => $this->_status_code, 
			'status_message' => $this->getHTTPMessage(), 
			'message' => $message, 
			'data' => $data
		];
	   	return response()->json($response, $this->_status_code);	
	}
}
