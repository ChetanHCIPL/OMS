<?php
namespace App\GlobalClass;

class Connection {
  protected $curlObj;

  function __construct($merchantObj) {
    // initialise cURL object/options
    $this->curlObj = curl_init();

    // configure cURL proxy options by calling this function
    $this->ConfigureCurlProxy($merchantObj);

    // configure cURL certificate verification settings by calling this function
    $this->ConfigureCurlCerts($merchantObj);
  }

  function __destruct() {
    // free cURL resources/session
    curl_close($this->curlObj);
  }

  // Send transaction to payment server
  public function SendTransaction($merchantObj, $request) {
	
	## AUTHORIZE CURL
    curl_setopt($this->curlObj, CURLOPT_POST, 1);
    curl_setopt($this->curlObj, CURLOPT_POSTFIELDS, $request);
    curl_setopt($this->curlObj, CURLOPT_URL, $merchantObj->GetGatewayUrl());
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen($request)));
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
    curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, TRUE);
    // if ($merchantObj->GetDebug()) {
    //   curl_setopt($this->curlObj, CURLOPT_HEADER, TRUE);
    //   curl_setopt($this->curlObj, CURLINFO_HEADER_OUT, TRUE);
    // }
    $response = curl_exec($this->curlObj);

    if ($merchantObj->GetDebug()) {
      $requestHeaders = curl_getinfo($this->curlObj);
      $response = $requestHeaders["request_header"] . $response;
    }
    
    if (curl_error($this->curlObj))
      $response = "cURL Error: " . curl_errno($this->curlObj) . " - " . curl_error($this->curlObj);

    // respond with the transaction result, or a cURL error message if it failed
    return $response;
  }

  // Create Token 
  public function CreateToken($merchantObj,$session_id){
	$token_api_params_arr = array(
      'apiOperation' => urlencode('TOKENIZE'),
      'session.id' => urlencode($session_id),
      'sourceOfFunds.type' => urlencode('CARD'),
    );
    $token_api_params = "";
    foreach ($token_api_params_arr as $fieldName => $fieldValue) {
      if (strlen($fieldValue) > 0) {
        // replace underscores in the fieldnames with decimals
        for ($i = 0; $i < strlen($fieldName); $i++) {
          if ($fieldName[$i] == '_')
            $fieldName[$i] = '.';
        }
        $token_api_params .= $fieldName . "=" . $fieldValue . "&";
      }
    }
   // echo $token_api_params;exit();
    $token_api_params .= "merchant=" . urlencode($merchantObj->GetMerchantId()) . "&";
    $token_api_params .= "apiPassword=" . urlencode($merchantObj->GetPassword()) . "&";
    $token_api_params .= "apiUsername=" . urlencode($merchantObj->GetApiUsername());

    curl_setopt($this->curlObj, CURLOPT_POSTFIELDS, $token_api_params);
    curl_setopt($this->curlObj, CURLOPT_URL, $merchantObj->GetGatewayTokenUrl());
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen($token_api_params)));
    curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
    curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, TRUE);
    if ($merchantObj->GetDebug()) {
      curl_setopt($this->curlObj, CURLOPT_HEADER, TRUE);
      curl_setopt($this->curlObj, CURLINFO_HEADER_OUT, TRUE);
    }
    $token_api_response = curl_exec($this->curlObj);
	
    if ($merchantObj->GetDebug()) {
      $requestHeaders = curl_getinfo($this->curlObj);
      $token_api_response = $requestHeaders["request_header"] . $token_api_response;
    }
    if (curl_error($this->curlObj)){
      $token_api_response = "cURL Error: " . curl_errno($this->curlObj) . " - " . curl_error($this->curlObj);
	}
    // respond with the transaction result, or a cURL error message if it failed
    return $token_api_response;
  }
  
  //Parse curl response
  public function ParseResponse($response){
	    
		## Variable Declaration
	   $errorMessage = "";
		$errorCode = "";
		$gatewayCode = "";
		$result = "";
		$is_error = 0;
		$responseArray = $payData = array();
		// meaning there's a string cURL Error in the response
		if (strstr($response, "cURL Error") != FALSE) {
			$errorMessage = "Communication failed. Please review payment server return response (put code into debug mode).";
			$is_error = 1;
			$payData['is_error'] = $is_error;
			$payData['errorMessage'] = $errorMessage;
			return $payData;
		}
        
		// Decode Response
		// loop through server response and form an associative array
		// name/value pair format
		if (strlen($response) != 0) {
		  $pairArray = explode("&", $response);
		  foreach ($pairArray as $pair) {
			$param = explode("=", $pair);
			$responseArray[urldecode($param[0])] = urldecode($param[1]);
		  }
		}
		
		// Parse Response
		if (array_key_exists("result", $responseArray)){
		  $result = $responseArray["result"];
		}
		// Form error string if error is triggered
		if ($result == "FAIL" || $result == "FAILURE") {
			  $is_error = 1;
			  if (array_key_exists("failureExplanation", $responseArray)) {
				$errorMessage = rawurldecode($responseArray["failureExplanation"]);
			  }
			  else if (array_key_exists("supportCode", $responseArray)) {
				$errorMessage = rawurldecode($responseArray["supportCode"]);
			  }
			  else {
				$errorMessage = "Reason unspecified.";
			  }

			  if (array_key_exists("failureCode", $responseArray)) {
				$errorCode = "Error (" . $responseArray["failureCode"] . ")";
			  }
			  else {
				$errorCode = "Error (UNSPECIFIED)";
			  }
		}elseif($result == "ERROR"){
			$is_error = 1;
			$errorfield='';
			if (array_key_exists("error.field", $responseArray)) {
				$errorfield = $responseArray["error.field"];
			}
			if($errorfield == 'sourceOfFunds.provided.card.securityCode'){
				$errorMessage = __('message.msg_required', ['attribute' => strtolower(__('label.lbl_security_code'))]);
			}
			else{
				$errorMessage = __('message.msg_something_went_wrong').__('message.msg_please_try_again');
			}
		}else {
		  if (array_key_exists("response.gatewayCode", $responseArray))
			$gatewayCode = rawurldecode($responseArray["response.gatewayCode"]);
		  else
			$gatewayCode = "Response not received.";

		  if($gatewayCode == 'REJECTED' || $gatewayCode == 'BLOCKED'){
				$is_error = 1;	
				$errorMessage = __('message.msg_something_went_wrong').__('message.msg_please_try_again');
		  }
		}

		$payData['is_error'] = $is_error;
		$payData['errorMessage'] = $errorMessage;
		$payData['errorCode'] = $errorCode;
		$payData['responseArray'] = $responseArray;
		$payData['gatewayCode'] = $gatewayCode;
		return $payData;
  
  }

  public function FormRequestUrl($merchantObj) {
    $gatewayUrl = $merchantObj->GetGatewayUrl();
    $gatewayUrl .= "/version/" . $merchantObj->GetVersion();

    $merchantObj->SetGatewayUrl($gatewayUrl);
    return $gatewayUrl;
  }
  // [Snippet] howToConfigureURL - end

  // [Snippet] howToConvertFormData - start
  // Form NVP formatted request and append merchantId, apiPassword & apiUsername
  public function ParseRequest($merchantObj, $formData) {
    $request = "";

    if (count($formData) == 0)
      return "";

    foreach ($formData as $fieldName => $fieldValue) {
      if (strlen($fieldValue) > 0 && $fieldName != "merchant" && $fieldName != "apiPassword" && $fieldName != "apiUsername") {
        // replace underscores in the fieldnames with decimals
        for ($i = 0; $i < strlen($fieldName); $i++) {
          if ($fieldName[$i] == '_')
            $fieldName[$i] = '.';
        }
        $request .= $fieldName . "=" . urlencode($fieldValue) . "&";
      }
    }

    // [Snippet] howToSetCredentials - start
    // For NVP, authentication details are passed in the body as Name-Value-Pairs, just like any other data field
    $request .= "merchant=" . urlencode($merchantObj->GetMerchantId()) . "&";
    $request .= "apiPassword=" . urlencode($merchantObj->GetPassword()) . "&";
    $request .= "apiUsername=" . urlencode($merchantObj->GetApiUsername());
    // [Snippet] howToSetCredentials - end

    return $request;
  }

  // [Snippet] howToConfigureProxy - start
  // Check if proxy config is defined, if so configure cURL object to tunnel through
  protected function ConfigureCurlProxy($merchantObj) {
    // If proxy server is defined, set cURL option
    if ($merchantObj->GetProxyServer() != "") {
      curl_setopt($this->curlObj, CURLOPT_PROXY, $merchantObj->GetProxyServer());
      curl_setopt($this->curlObj, $merchantObj->GetProxyCurlOption(), $merchantObj->GetProxyCurlValue());
    }

    // If proxy authentication is defined, set cURL option
    if ($merchantObj->GetProxyAuth() != "")
      curl_setopt($this->curlObj, CURLOPT_PROXYUSERPWD, $merchantObj->GetProxyAuth());
  }
  // [Snippet] howToConfigureProxy - end

  // [Snippet] howToConfigureSslCert - start
  // configure the certificate verification related settings on the cURL object
  protected function ConfigureCurlCerts($merchantObj) {
    // if user has given a path to a certificate bundle, set cURL object to check against them
    if ($merchantObj->GetCertificatePath() != "")
      curl_setopt($this->curlObj, CURLOPT_CAINFO, $merchantObj->GetCertificatePath());

    curl_setopt($this->curlObj, CURLOPT_SSL_VERIFYPEER, $merchantObj->GetCertificateVerifyPeer());
    curl_setopt($this->curlObj, CURLOPT_SSL_VERIFYHOST, $merchantObj->GetCertificateVerifyHost());
  }
  // [Snippet] howToConfigureSslCert - end
  

}



class Parser extends Connection {
  function __construct($merchantObj) {
    // call parent ctor to init members
    parent::__construct($merchantObj);
  }

  function __destruct() {
    // call parent dtor to free resources
    parent::__destruct();
  }

	// [Snippet] howToConfigureURL - start
  // Modify gateway URL to set the version
  // Assign it to the gatewayUrl member in the merchantObj object
  
  // [Snippet] howToConvertFormData - end
}

?>