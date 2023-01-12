<?php

namespace App\Traits\General;

trait GeneralFunction
{
    
    private $_is_error 	= '0';
    private $_message 	= '';

    /**
     * Function: Set Error Status for Current Request
     *
     * @param   string 	$is_error
     * @return  void
     */
	private function setErrorStatus($is_error){
        $this->_is_error = $is_error;
    }

    /**
     * Function: Set Message for Current Request
     *
     * @param   string 	$message
     * @return  void
     */
	private function setMessage($message){
        $this->_message = $message;
    }

    /**
     * Function: Prepare JSON Response
     *
     * @param    array 		$result
     * @return   json 		$response
     */
	private function prepareJSONResponse($result = array()){
		$response = [
			'is_error' 	=> $this->_is_error, 
			'message' 	=> $this->_message, 
			'data' 		=> $result
		];
	   	return response()->json($response);
	}
}