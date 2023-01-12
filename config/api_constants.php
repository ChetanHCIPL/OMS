<?php  

return [
    'API_LATEST_VERSION_PREFIX' => 'V1',
    'API_LATEST_VERSION_PREFIX_V2' => 'V2',
    'USER_AUTH_TOKEN_EXPIRATION_HOURS' => 2160, // 2160 Hours = 3 Month(90 Days)
    'CLIENT_AUTH_TOKEN_EXPIRATION_HOURS' => 2160, // 2160 Hours = 3 Month(90 Days)
    //'AUTHORIZATION_TOKEN' => 'Bearer 0ea4e3e69cb3bae2e3a7838db6ac66849a3908b8833c2d0e8d0504cd8bc8d31322812505356b4f8g',
    //'WEB_DEVICE_TYPE' => '1',
    'USER_API_PREFIX' => 'user',
    'CLIENT_API_PREFIX' => 'client',
	'HTTP_STATUS_MSG' => array(
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
     ),
	'ANDROID_CURRENT_APP'=> '1.0.0',
	'ALLOW_LOGIN_ANDROID_APP_VER_ARR' => array("1.0.0", "1.0.1"),
	'IOS_CURRENT_APP'=> '1.0.0',
	'ALLOW_LOGIN_IOS_APP_VER_ARR' => array("1.0.0", "1.0.1"),

];
