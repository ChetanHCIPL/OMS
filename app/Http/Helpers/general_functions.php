<?php
/**
* Convert PHP Standard Object to an array
* @return array|object
*/
function stdToArray($stdObject)
{
    if($stdObject instanceof stdClass)
        return json_decode(json_encode($stdObject), true);
    return $stdObject;
}

######## Function When Intitalized Project ##################
## Set validation error message when we need to display messages in popup
function setValidationErrorMessageForPopup($validator) {
    $msg = NULL;
    $errors = $validator->errors();
    if (count($errors) > 0) {
        $i = 0;
        foreach ($errors->all() as $error) {
            if($i > 0){
                $msg .= '</br>';
            }
            $msg .= $error;
            $i++;
        }
    }
    return $msg;
}


## Store Document in folder
function storeDocumentInFolder($fileObj, $destinationPath, $vDoc) {
    $fileObj->move($destinationPath, $vDoc);
}

## Store Document in folder based on different size
function storeFileInFolder($fileTmpName, $destinationPath, $vDoc) {
    $img = Image::make($fileTmpName); 
    $img->save($destinationPath.$vDoc); 
}

## Get Image URL
function getOriginalFileUrl($assetFolderName,$image){

	$img_url = asset('/images/'.$assetFolderName).'/' .$image;
    return  $img_url;
}

function saveFileFromURL($url, $path, $file)
{
    file_put_contents($path.$file, file_get_contents($url));
}
function saveFileFromBase64($decoded_file, $file_dir){
	file_put_contents($file_dir, $decoded_file);
}

## Store Image in folder based on different size
function storeImageinFolder($fileTmpName, $destinationPath, $vImage, $size_array) {

    if (!empty($size_array)) {
        if (!file_exists($destinationPath)) {
            @mkdir($destinationPath, 0777, true);
        }
        $i = 1;
        foreach ($size_array as $rowSizeArray) {
            $img = Image::make($fileTmpName)->resize($rowSizeArray["W"], $rowSizeArray["H"]);
            
            $img->save($destinationPath . $i . '_' . $vImage);
            $i++;
        }
    }
}
function deleteDocFromFolder($doc, $destinationPath){
     
        $del_file_path = $destinationPath.  $doc;

        if ($del_file_path != '' && file_exists($del_file_path)) {

            @unlink($del_file_path); 
        } 
}

function deleteImageFromFolder($image, $destinationPath, $size_array){
    if(!empty($size_array)){
        $cnt = count($size_array);
        for ($i = 1; $i <= $cnt; $i++) {
            $del_file_path = $destinationPath . $i . '_' . $image;
            if ($del_file_path != '' && file_exists($del_file_path)) {
                @unlink($del_file_path);
            }
        }
    }
}

function deletemediaFromFolder($media, $destinationPath){
    if(!empty($media)){
        $del_file_path = $destinationPath.$media;
        if ($del_file_path != '' && file_exists($del_file_path)) {
            @unlink($del_file_path);
        }
    }
}

/**
* Change array format  
* Input : array(
	[0] => stdClass Object (
		'name1'=>'Radhika','email1'=>'radhika@gmail.com'),
	[1]=> stdClass Object (
		'name2'=>'Anu','email2'=>'anu@gmail.com')
	)
 * output : array(
 	'name1'=>'radhika','email1'=>'radhika@gmail.com',
 	'name2'=>'Anu','email2'=>'anu@gmail.com'
 )
*/
function formatInAssociativeArray($array = array()){
    $result = array();
    if(!empty($array)){
        foreach($array as $rowArray){
            foreach($rowArray as $key => $value){
                $result[$key] = $value;
            }
        }
    }
    return $result;
}

/**
* Generate random string
*/
function random_string($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
* Generate random string for password
*/
function random_string_for_password($length = 20){
	$char = [range('A','Z'),range('a','z'),range(0,9),['$','@','$','!','%','*','?','&']];
	$pw = '';
	for($a = 0; $a < count($char); $a++)
	{
		$randomkeys = array_rand($char[$a], 2);
		$pw .= $char[$a][$randomkeys[0]].$char[$a][$randomkeys[1]];
	}
	$userPassword = str_shuffle($pw);
	return $userPassword;
}

/**
* Generate random numbers
*/
function random_number($length){
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++){
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
* Create Files and folder
* @param absolute path
*/
function createFileFolder($iId, $image_path)
{
	if(isset($ext_para) && $ext_para != "")
		$get_path = $iId.'/'.$ext_para.'/';
	else
		$get_path = $iId.'/';
	$path = $image_path.$get_path;
	
	if(!is_dir($path))
	{
		@mkdir($path, 0777);
		@chmod($path, 0777);
	}
	return $path;
}

/**
* Remove Files Recursively
* @param absolute path
*/
function removeFileFolder($path){
    array_map('unlink', glob($path.'/*'));
    @rmdir($path);
}

/**
 * Function: Remove File
 *
 * @param   string $file (absolute path)
 * @return  void
 */ 
function removeFile($file){
    if ($file != '' && file_exists($file)) {
        @unlink($file); 
    } 
}

/**
* Format price
*
* @param mixed $price
* @return double
*/
function formatPrice($price)
{
	$symbol = config('settings.CURRENCIES_SYMBOL');
    $currency_symbol = ($symbol == "")?'':$symbol;
	if($price != '' && $price != '0'){
	    return $currency_symbol.' '.number_format((float)$price,2);
	}else{
		return $currency_symbol.' '.$price;
	}
}

/**
* Option Label based on value
* @param array,value need to match
* @return string
* [['value' => 1,'label' => 'Yes'],['value' => 2,'label' => 'No']]
*/
function getOptionLabel($array,$value){
    if(!empty($array)){
        foreach($array as $key => $label){
            if(isset($label['value']) && isset($label['label']) && $label['value'] == $value){
                return $label['label'];
            }
        }
    }
    return '';
}

/**
* truncate a string to a specified length.
* @return string
*/
function truncateString($string, $length = 70){
  if (strlen($string) > $length) {
	$split = preg_split("/\n/", wordwrap($string, $length));
	return ($split[0]);
  }
  return ($string);
}

/**
* To Checks Image Global Setting and also checks Image is Exist or not(with Local and AWS)
* @return array
*/
function checkImageExistWithSetting($AWSdestinationPath,$localDestinationPath,$assetFolderName,$imgPrefix=NULL,$imgSufix,$image,$sizeCnt=NULL,$size_image=0){
	$large_img_url='';
    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
        $img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.'1'.$imgSufix,$image);
        $fancy_img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$sizeCnt.$imgSufix,$image);
    }else{
        $file_path = public_path('/images/'.$assetFolderName.'/1'.$imgSufix.$image);
        
        if(file_exists($file_path)){
            $imgPrefix = ($imgPrefix != '')?$imgPrefix.'/' : '';
            $img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.'1'.$imgSufix.$image;
            $fancy_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;
    		if($size_image > 0){
    			$large_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$size_image.$imgSufix.$image;
    		}
        }else{
            $img_url = $large_image_url = asset('/images/'.'no_image/80x80.jpg'); 
            $fancy_img_url = $large_image_url = asset('/images/'.'no_image/500x500.jpg'); 
            $large_img_url = $large_image_url = asset('/images/'.'no_image/500x500.jpg'); 
        }


    }
    return  array('img_url' => $img_url, 'fancy_box_url' => $fancy_img_url, 'large_img_url' => $large_img_url);
}

/**
* To Checks Image Global Setting and also checks Image is Exist or not out side images folder (with Local and AWS)
* @return array
*/
function checkImageExistForOutsideImagefolderWithSetting($AWSdestinationPath,$localDestinationPath,$assetFolderName,$imgPrefix=NULL,$imgSufix,$image,$sizeCnt=NULL,$size_image=0){
    $large_img_url='';
    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
        $img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.'1'.$imgSufix,$image);
        $fancy_img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$sizeCnt.$imgSufix,$image);
    }else{
      
        $file_path = public_path($assetFolderName.'/1'.$imgSufix.$image);
        //echo $file_path; 
        if(file_exists($file_path)){
            $imgPrefix = ($imgPrefix != '')?$imgPrefix.'/' : '';
            $img_url = asset($assetFolderName).'/' .$imgPrefix.'1'.$imgSufix.$image;
            $fancy_img_url = asset($assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;
            if($size_image > 0){
                $large_img_url = asset($assetFolderName).'/' .$imgPrefix.$size_image.$imgSufix.$image;
            }
        }else{
            $img_url = $large_image_url = asset('/images/'.'no_image/80x80.jpg'); 
            $fancy_img_url = $large_image_url = asset('/images/'.'no_image/500x500.jpg'); 
            $large_img_url = $large_image_url = asset('/images/'.'no_image/500x500.jpg'); 
        }


    }
    return  array('img_url' => $img_url, 'fancy_box_url' => $fancy_img_url, 'large_img_url' => $large_img_url);
}

/**
* To Checks Image Global Setting and also checks Image is Exist or not(with Local and AWS)
* @return array
*/
function checkImageExistInFolder($AWSdestinationPath,$localDestinationPath,$assetFolderName,$imgPrefix=NULL,$imgSufix,$image,$sizeCnt=NULL){

    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
        $img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.'1'.$imgSufix,$image);
        $fancy_img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$sizeCnt.$imgSufix,$image);
    }else{
        $imgPrefix = ($imgPrefix != '')?$imgPrefix.'/' : '';
        if($image != '' )
        {
            $img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.'1'.$imgSufix.$image;
            $fancy_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;
        }else{
            $image = 'no-image';
            $img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.'1'.$imgSufix.$image;
            $fancy_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;    
        }
        
    }
    return  array('img_url' => $img_url, 'fancy_box_url' => $fancy_img_url);
}

/**
* Random Alphanumeric String Generator using HC
*/
function assign_rand_value($num){
    switch($num){
        case "1":
            $rand_value = "a";
        break;
        case "2":
            $rand_value = "b";
        break;
        case "3":
            $rand_value = "c";
        break;
        case "4":
            $rand_value = "d";
        break;
        case "5":
            $rand_value = "e";
        break;
        case "6":
            $rand_value = "f";
        break;
        case "7":
            $rand_value = "g";
        break;
        case "8":
            $rand_value = "h";
        break;
        case "9":
            $rand_value = "i";
        break;
        case "10":
            $rand_value = "j";
        break;
        case "11":
            $rand_value = "k";
        break;
        case "12":
            $rand_value = "l";
        break;
        case "13":
            $rand_value = "m";
        break;
        case "14":
            $rand_value = "n";
        break;
        case "15":
            $rand_value = "o";
        break;
        case "16":
            $rand_value = "p";
        break;
        case "17":
            $rand_value = "q";
        break;
        case "18":
            $rand_value = "r";
        break;
        case "19":
            $rand_value = "s";
        break;
        case "20":
            $rand_value = "t";
        break;
        case "21":
            $rand_value = "u";
        break;
        case "22":
            $rand_value = "v";
        break;
        case "23":
            $rand_value = "w";
        break;
        case "24":
            $rand_value = "x";
        break;
        case "25":
            $rand_value = "y";
        break;
        case "26":
            $rand_value = "z";
        break;
        case "27":
            $rand_value = "0";
        break;
        case "28":
            $rand_value = "1";
        break;
        case "29":
            $rand_value = "2";
        break;
        case "30":
            $rand_value = "3";
        break;
        case "31":
            $rand_value = "4";
        break;
        case "32":
            $rand_value = "5";
        break;
        case "33":
            $rand_value = "6";
        break;
        case "34":
            $rand_value = "7";
        break;
        case "35":
            $rand_value = "8";
        break;
        case "36":
            $rand_value = "9";
        break;
    }
    return $rand_value;
}
/**
* Generate Random Password of only numbers using HC
*/
function getRandPasswordByHC($length){
    if($length>0)
    {
        $rand_id="";
        for($i=1; $i<=$length; $i++)
        {
           mt_srand((double)microtime() * 1000000);
           $num = mt_rand(1,36);
           $rand_id .= assign_rand_value($num);
        }
    }
    return $rand_id;
}

/**
* Password Encrypt using HC
*/
function encryptByHC($data){
    for($i = 0, $key = 27, $c = 48; $i <= 255; $i++)
    {
        $c = 255 & ($key ^ ($c << 1));
        $table[$key] = $c;
        $key = 255 & ($key + 1);
    }
    $len = strlen($data);
    for($i = 0; $i < $len; $i++)
    {
        $data[$i] = chr($table[ord($data[$i])]);
    }
    return base64_encode($data);
}

/**
* Password Decrypt using HC
*/
function decryptByHC($data){
    $data = base64_decode($data);
    for($i = 0, $key = 27, $c = 48; $i <= 255; $i++)
    {
        $c = 255 & ($key ^ ($c << 1));
        $table[$c] = $key;
        $key = 255 & ($key + 1);
    }
    $len = strlen($data);
    for($i = 0; $i < $len; $i++)
    {
        $data[$i] = chr($table[ord($data[$i])]);
    }
    return $data;
}

/**
* Store Language in session
*/
function storeLanguageToSession($lang){
    // Remove Language Data from Cookie if any
    if(isset($_COOKIE['applocale'])){ 
        unset($_COOKIE['applocale']);
        setcookie('applocale', null, -1, '/');
    }
    // Store Language to Session
    \Session::put('applocale', $lang);
    // Store Language to Cookies
    setcookie('applocale', $lang, time() + (86400 * 90)); // 86400 = 1 day
    // Flush all cache files
    \Cache::flush();
}

/**
* Append http to URL if not
*/
function addHttpToUrlIfNot($url){
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

/**
* Remove special character from Image Name
*/
function gen_remove_spacial_char($vImage_name) {
    $find = array('-', ' ', '#', '?', '"', "'", '$', '@', '*', '%', '!', '+', '`', '~', '^', '&', '=', '|');
    return str_replace($find, '_', $vImage_name);
}

/**
* Current IP
*/
function getIP(){
    return \Request::ip();
}

/**
* Get Time Ago based on time
*/
function get_time_ago($time){
    $time_difference = time() - $time;
    if($time_difference < 1 ) { return 'less than 1 second ago'; }
    $condition = array( 
        12 * 30 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );
    foreach($condition as $secs => $str){
        $d = $time_difference / $secs;
        if($d >= 1){
            $t = round($d);
            return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}

/**
* Function : Append/Prepand random string
*/
function gen_generate_encoded_str($str = NULL, $front_length = NULL, $back_length = NULL, $sep = NULL) {
    if (isset($str) && $str != "") {
        if (isset($sep)) {
            $sep = $sep;
        } else {
            $sep = "";
        }
        if ((isset($front_length) && $front_length != "" && $front_length != 0) && (isset($back_length) && $back_length != "" && $back_length != 0)) {
            return random_string($front_length) . $sep . $str . $sep . random_string($back_length);
        } elseif ((!isset($front_length) || $front_length == "" || $front_length == 0) && (isset($back_length) && $back_length != "" && $back_length != 0)) {
            return $str . $sep . random_string($back_length);
        } elseif ((isset($front_length) && $front_length != "" && $front_length != 0) && (!isset($back_length) || $back_length == "" || $back_length == 0)) {
            return random_string($front_length) . $sep . $str;
        } else {
            return $str;
        }
    }
}

function gen_getLanguageNameByCode($code){
    $query_data = App\Models\Language::getLanguageNameByCode($code);
    if (isset($query_data) && !empty($query_data)) {
        $lang_name = $query_data[0]['lang_name'];
    } else {    
        $lang_name = '';
    }
    return $lang_name;
}

/**
*To Generate Array for Advance Search Filter
*/
function getAdvanceSearchFilterColsData($filderDataObj){

    $filterData = array();
    $searchFilterData = objToArray(json_decode($filderDataObj));
    if(isset($searchFilterData)){
        foreach($searchFilterData as $searchData)
            $filterData[$searchData['key']] = $searchData['val'];
    }
    return $filterData;
}
/*
 * Convert object to array
 * * from : Array([0]=> stdClass Object ('name'=>'Radhika','email'=>'radhika@gmail.com'),[1]=> stdClass Object'name'=>'Anu','email'=>'anu@gmail.com'))
 * to : Array( [0] => Array ('name'=>'radhika','email1'=>'radhika@gmail.com'),[1] => Array( 'name2'=>'Anu','email2'=>'anu@gmail.com'))
 */

function objToArray($obj, &$arr = array()) {

    if (!is_object($obj) && !is_array($obj)) {
        $arr = $obj;
        return $arr;
    }

    foreach ($obj as $key => $value) {
        if (!empty($value)) {
            $arr[$key] = array();
            objToArray($value, $arr[$key]);
        } else {
            $arr[$key] = $value;
        }
    }
    return $arr;
}

/**
 * Function: Check permission for multiple access name for any module
 * @param string $module
 * @return boolean
 */
function gen_checkAllAccessRights($module) {
    if (per_hasModuleAccess($module, 'List') || per_hasModuleAccess($module, 'Add') || per_hasModuleAccess($module, 'Edit') || per_hasModuleAccess($module, 'Delete') || per_hasModuleAccess($module, 'Status'))
        return 1;
    else
        return 0;
}


/**
 * Function: Document copy to another table 
 * @param string $fromDocPath, $toDocPath
 * @return boolean
 */ 
function copy_to_another_folder($fromDocPath,$toDocPath) {
    if(file_exists($fromDocPath)) 
    {      
        $copied = copy($fromDocPath , $toDocPath);
        if ($copied)
        { 
            if(!unlink($fromDocPath)){
                return 0;
            }else{
                return 1;
            }
        } else
        { 
            return 0;
        }
    }else{
        return 0;
    }
}

## Cretaed By Krupali on 20th August 2019
## Function: get basename from Image URL in API
function getBaseNameFromImageURL($image_url){
    $base_name = '';
    if($image_url != ''){
        $image = basename($image_url);
        $base_name =  substr($image, strpos($image, "_")+1);
    }
    return $base_name;
}


/**
* To Checks Image Global Setting and also checks Image is Exist or not(with Local and AWS)
* @return array
*/
function checkMoveExistWithSetting($AWSdestinationPath,$localDestinationPath,$assetFolderName,$imgPrefix=NULL,$imgSufix,$image,$sizeCnt=NULL){

    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
        $img_url_1 = getImagefromAWS($AWSdestinationPath,$imgPrefix.'1'.$imgSufix,$image);
        $img_url_2 = getImagefromAWS($AWSdestinationPath,$imgPrefix.'2'.$imgSufix,$image);
        $fancy_img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$sizeCnt.$imgSufix,$image);
    }else{
        $imgPrefix = ($imgPrefix != '')?$imgPrefix.'/' : '';
        $img_url_1 = asset('/images/'.$assetFolderName).'/' .$imgPrefix.'1'.$imgSufix.$image;
        $img_url_2 = asset('/images/'.$assetFolderName).'/' .$imgPrefix.'2'.$imgSufix.$image;
        $fancy_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;
    }
    return  array('img_url_1' => $img_url_1,'img_url_2' => $img_url_2, 'fancy_box_url' => $fancy_img_url);
}

/**
 * Function: Check customer is authenticated or not
 *
 * @return   boolean
 */
function isCustomerAuthenticated(){
    $response = \App\GlobalClass\GeneralFunction::getLatestUserAuthToken();
    if((isset($response['status_code']) && $response['status_code'] == config('constants.HTTP_MSG_STATUS.UNAUTHORIZED')) || !session()->has(config('constants.SESSION_PREFIX').'.user_auth_token') || (session()->has(config('constants.SESSION_PREFIX').'.user_auth_token') && session()->get(config('constants.SESSION_PREFIX').'.user_auth_token') == "")){
        return false;
    }
    return true;
}

/**
 * Function: Get Formatted Title
 *
 * @return   string
 */
function getFormatTitle($word){
	$word_cnt = preg_split('/\s+/', $word);
	$cnt =  count($word_cnt);
	$firsthalf = array_slice($word_cnt, 0, $cnt / 2);
	$secondhalf = array_slice($word_cnt, $cnt / 2);
	if($cnt > 1){
		$title_format = " <span>".implode(' ',$firsthalf)." </span>".implode(' ',$secondhalf);
	}else{
		$title_format = " <span>".$word." </span>";
	}
	
	return $title_format;
}
/**
 * Function: Get information about IP
 *
 * @return   string
 */
function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }

    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
		if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

## Function : get nearest element from an array
## Created By : Krupali on 6th Sepetember,2019
function getClosestElementFromArray($arr,$var){
    sort($arr);
    usort($arr, function($a,$b) use ($var){
        return  abs($a - $var) - abs($b - $var);
    });
    return array_shift($arr);
}
## Function : get url slug with '-' seperator
## Created By : Iva on 12th Sepetember,2019
/**
 * @param string $string string 
 * @param string $separator 
 */
function make_slug($string = null, $separator = "-") {
    if (is_null($string)) {
        return "";
    }
	$string = strtolower($string);
    // Convert whitespaces and underscore to the given separator
    $string = preg_replace("/[\s_]/", $separator, $string);

    return $string;
}

##Function: Encrypt & Decrypt String
##created by : Ritu on 10th Oct 2019
/**
 * @param string $string string to be encrypted/decrypted
 * @param string $action (e for encrypt, d for decrypt)
 */
function encrypt_decryptData($string, $action = 'e'){
    $secret_key = 'encr_decr_key';
    $secret_iv = 'encr_decr_iv';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output =  openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) ;
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt(  $string , $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}

## Function: Set Page title for LevelNext Panel
function setAdminPanelPageTitle($name=''){
    $result = Config::has('settings.ADMIN_TITLE')?Config::get('settings.ADMIN_TITLE'):"";
    if($name != ''){
        if($result != ''){
            $result .= " | ".$name;
        }else{
            $result = $name;
        }
    }
    return $result;
}

/*
to take mime type as a parameter and return the equivalent extension
*/
function mime2ext($mime){
    $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp",
    "image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp",
    "image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp",
    "application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg",
    "image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],
    "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],
    "ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg",
    "video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],
    "kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],
    "rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application",
    "application\/x-jar"],"zip":["application\/x-zip","application\/zip",
    "application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
    "7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],
    "svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],
    "mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],
    "webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],
    "pdf":["application\/pdf","application\/octet-stream"],
    "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
    "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
    "application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
    "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
    "xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel",
    "application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
    "xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo",
    "video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],
    "log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],
    "wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],
    "tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop",
    "image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],
    "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar",
    "application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40",
    "application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
    "cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary",
    "application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],
    "ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],
    "wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],
    "dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php",
    "application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
    "swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],
    "mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],
    "rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],
    "jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],
    "eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],
    "p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],
    "p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
    $all_mimes = json_decode($all_mimes,true);
    foreach ($all_mimes as $key => $value) {
        if(array_search($mime,$value) !== false) return $key;
    }
    return false;
}

## Function created by Iva on 27th July 2020 
## To get embed youtube url
function getYoutubeEmbedUrl($url)
{
    //$shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
    //$longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    $youtube_id = $match[1];

    if($youtube_id != ''){
        $play_url = 'https://www.youtube.com/embed/'.$youtube_id;
    }else{
        $play_url = $url;
    }   
    
    return $play_url ;
}

## Function Generate Random digit
function gen_generateRandomDigits($digits) {
    return rand(pow(10, $digits - 1) - 1, pow(10, $digits) - 1);
}



/**
* Function to Get Image url based on storage type
*/
function getImageUrl($image, $folder_name, $size="1", $sizecnt="2", $prefix="", $suffix="_",$default_img="80x80.jpg", $return_no_image = 1){
    $small_image_url = $large_image_url = asset('/images/'.'no_image/'.$default_img); 
    if(isset($image) && $image != ""){
        if(config('settings.SITE_IMAGES_STORAGE') == config('constants.SITE_IMAGES_STORAGE_AWS')){
            $destinationPath = config('filesystems.disks.s3.url').config('filesystems.disks.s3.bucket')."/";
            $small_image_url = $destinationPath.$folder_name.$prefix.$size.$suffix.$image;
            $large_image_url = $destinationPath.$folder_name.$prefix.$sizecnt.$suffix.$image;
        }else{
            $file_path = public_path('/images/'.$folder_name.$prefix.$size.$suffix.$image); 
            //echo "<pre>"; print_r($image); echo "</pre>"; exit('here');
            if(file_exists($file_path)){
                $small_image_url = asset('/images/'.$folder_name.$prefix.$size.$suffix.$image); 
                $large_image_url = asset('/images/'.$folder_name.$prefix.$sizecnt.$suffix.$image); 
            }else{
                if($return_no_image == 0 ){
                    $small_image_url =$large_image_url = '';
                }
            }
        }
    }else{
        if($return_no_image == 0 ){
            $small_image_url =$large_image_url = '';
        }
    }
    return array('small_image_url' => $small_image_url, 'large_image_url' => $large_image_url);
}

/**
* Function to Get Image url based on storage type
*/
function getResourceFileUrl($image, $folder_name, $size="1", $sizecnt="2", $prefix="", $suffix="_"){
    $small_image_url = $large_image_url = asset('/images/'.'no_image/80x80.jpg'); 
    if(isset($image) && $image != ""){
        if(config('settings.SITE_IMAGES_STORAGE') == config('constants.SITE_IMAGES_STORAGE_AWS')){
            $destinationPath = config('filesystems.disks.s3.url').config('filesystems.disks.s3.bucket')."/";
            $small_image_url = $destinationPath.$folder_name.$prefix.$size.$suffix.$image;
            $large_image_url = $destinationPath.$folder_name.$prefix.$sizecnt.$suffix.$image;
        }else{
            $file_path = public_path($folder_name.$prefix.$size.$suffix.$image); 
            if(file_exists($file_path)){
                $small_image_url = asset($folder_name.$prefix.$size.$suffix.$image); 
                $large_image_url = asset($folder_name.$sizecnt.$suffix.$image); 
            }
        }
    }
    return array('small_image_url' => $small_image_url, 'large_image_url' => $large_image_url);
}

/**
* Function to Get Certificate PDF url
*/
function getCertificateFileUrlAndPath($file_name, $prefix="", $suffix=""){
    $file_url = $file_path = '';
    $folder_path = config('path.certificatePdfGeneratePath'); 
    $folder_url = config('path.certificatePdfGenerateURL'); 
    if((isset($file_name) && $file_name != "")) {
        $file_path = $folder_path.$prefix.$suffix.$file_name; 
        if(file_exists($file_path)){
            $file_url = $folder_url.'/'.$prefix.$suffix.$file_name; 
        }
    }
    return array('file_url' => $file_url, 'file_path' => $file_path);
}

/**
* Function to  reduce title name in update mode
*/
function reduceTitleName($title){
   if($title !=''){
        $title_len = strlen($title);
        if($title_len > 50){
             $new_title = substr($title, 0, 50)."...";
        }else{
            $new_title = $title;
        }
   }else{
       $new_title = $title;
   }
   return $new_title;
}

/**
* Function to  reduce title name in update mode
*/
function reduceshortdescription($title){
   if($title !=''){
        $title_len = strlen($title);
        if($title_len > 100){
             $new_title = substr($title, 0, 100)."...";
        }else{
            $new_title = $title;
        }
   }else{
       $new_title = $title;
   }
   return $new_title;
}


/**
* Function to calculate review percentage
*/
function findReviewRatingPercentage($rating_user, $total_reviews){
    $rating_per = 0;
    if($rating_user > 0  && $total_reviews > 0){
        $rating_per = round(($rating_user * 100 ) / $total_reviews);
    }
    return $rating_per;
}

/**
* Function to calculate test result average per
*/
function findAwarenessTestResultPercentage($right_answer_count, $total_questions){
    $rating_per = 0;
    if($right_answer_count > 0  && $total_questions > 0){
        $rating_per = round(($right_answer_count * 100 ) / $total_questions);
    }
    return $rating_per;
}

function getTopicMediaThumbnail($thumbnail_file)
{     
    $Thumbnail_Destination_Path = Config::get('path.Thumbnail_Destination_Path');
    $noimg_path = Config::get('path.noimg_path');
    $thumbnail_default_image = Config::get('constants.topic_vidoe_default_thumbnail_image');

    if($thumbnail_file !=''){
        $img_arr = getImageUrl($thumbnail_file, $Thumbnail_Destination_Path, $size = '','','1','_',$default_img = $thumbnail_default_image);
        $media_thumbnail_url = $img_arr['small_image_url'];
    }else{
        $media_thumbnail_url = asset("images/".$noimg_path.$thumbnail_default_image);
    }
    return $media_thumbnail_url;
}

function generateSecureCDNMediaURLDynamically($media_url){
    $APP_TATA_CDN_SECRET_KEY = Config::get('constants.APP_TATA_CDN_SECRET_KEY');
    $APP_TATA_CDN_URL_EXPIRY_HOURS = config('settings.APP_TATA_CDN_URL_EXPIRY_HOURS');
    
    $current_date_time  = date_getSystemDateTime();
    $url_expiry_time    = strtotime(date_addDateTime($current_date_time, $da=0, $ma=0, $ya=0, $ha=$APP_TATA_CDN_URL_EXPIRY_HOURS,$ia=0,$sa=0));
    $dec_media_file     = base64_decode($media_url);
    $parse_media_url    = parse_url($dec_media_file);
    $media_schema       = $parse_media_url['scheme']; // => https
    $media_host         = $parse_media_url['host']; // => allieddigital.pc.cdn.bitgravity.com
    $media_url_path     = $parse_media_url['path']; // => /hls/SampleVideos/EDII/TOPIC_74 SUB/master.m3u8
    // echo "<prE>"; print_r($parse_media_url);exit;
    $refrence_hash      = md5($APP_TATA_CDN_SECRET_KEY.$media_url_path."?e=".$url_expiry_time);
    $new_media_url      = $media_schema."://".$media_host.$media_url_path."?e=".$url_expiry_time."&h=".$refrence_hash;
    $enc_media_url      = base64_encode($new_media_url);
    return $enc_media_url;
}

function generateSecureCDNMediaURLDynamicallyAdmin($media_url){
    $APP_TATA_CDN_SECRET_KEY = Config::get('constants.APP_TATA_CDN_SECRET_KEY');
    $APP_TATA_CDN_URL_EXPIRY_MINUTES = 5;
    
    $current_date_time  = date_getSystemDateTime();
    $url_expiry_time    = strtotime(date_addDateTime($current_date_time, $da=0, $ma=0, $ya=0, $ha=0,$ia=$APP_TATA_CDN_URL_EXPIRY_MINUTES,$sa=0));
    $dec_media_file     = base64_decode($media_url);
    $parse_media_url    = parse_url($dec_media_file);
    $media_schema       = $parse_media_url['scheme']; // => https
    $media_host         = $parse_media_url['host']; // => allieddigital.pc.cdn.bitgravity.com
    $media_url_path     = $parse_media_url['path']; // => /hls/SampleVideos/EDII/TOPIC_74 SUB/master.m3u8
    // echo "<prE>"; print_r($parse_media_url);exit;
    $refrence_hash      = md5($APP_TATA_CDN_SECRET_KEY.$media_url_path."?e=".$url_expiry_time);
    $new_media_url      = $media_schema."://".$media_host.$media_url_path."?e=".$url_expiry_time."&h=".$refrence_hash;
    $enc_media_url      = base64_encode($new_media_url);
    return $enc_media_url;
}

/**
 * @param $n
 * @return string
 * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
 */
function number_format_short( $n ) {
    if ($n >= 0 && $n < 1000) {
        // 1 - 999
        $n_format = floor($n);
        $suffix = '';
    } else if ($n >= 1000 && $n < 1000000) {
        // 1k-999k
        $n_format = floor($n / 1000);
        $suffix = 'K+';
    } else if ($n >= 1000000 && $n < 1000000000) {
        // 1m-999m
        $n_format = floor($n / 1000000);
        $suffix = 'M+';
    } else if ($n >= 1000000000 && $n < 1000000000000) {
        // 1b-999b
        $n_format = floor($n / 1000000000);
        $suffix = 'B+';
    } else if ($n >= 1000000000000) {
        // 1t+
        $n_format = floor($n / 1000000000000);
        $suffix = 'T+';
    }

    return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
}

function genrateRandomStringGenerator($str1,$str2='',$str3='',$id)
{
    $str1 = str_replace(' ', '', $str1);
    $str2 = str_replace(' ', '', $str1);
    $str3 = str_replace(' ', '', $str1);

    $random_str_genrate = substr(str_shuffle(strtoupper($str1).$str2.$str3.$id),
                   0, 12);
    return $random_str_genrate;
}

/**
* To Checks Image Global Setting and also checks Image is Exist or not(with Local and AWS)
* @return array
*/
function checkImageExistWithFolder($AWSdestinationPath,$localDestinationPath,$assetFolderName,$imgPrefix=NULL,$imgSufix,$image,$sizeCnt=NULL,$size_image=0){
    $large_img_url='';
    if(Config::get('settings.SITE_IMAGES_STORAGE') == Config::get('constants.SITE_IMAGES_STORAGE_AWS')){
        $img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$imgSufix,$image);
        $fancy_img_url = getImagefromAWS($AWSdestinationPath,$imgPrefix.$sizeCnt.$imgSufix,$image);
    }else{
      
        $file_path = public_path('/images/'.$assetFolderName.'/'.$imgSufix.$image);
        //echo $file_path; 
        if(file_exists($file_path)){
            $imgPrefix = ($imgPrefix != '')?$imgPrefix.'/' : '';
            $img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$imgSufix.$image;
            $fancy_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$sizeCnt.$imgSufix.$image;
            if($size_image > 0){
                $large_img_url = asset('/images/'.$assetFolderName).'/' .$imgPrefix.$size_image.$imgSufix.$image;
            }
        }else{
            $img_url = ''; 
            $fancy_img_url = '';
            $large_img_url = '';
        }
    } 
    return  array('img_url' => $img_url, 'fancy_box_url' => $fancy_img_url, 'large_img_url' => $large_img_url);
}

/**
* Function to Get Image url based on storage type
*/
function getImageUrlCheckExist($image, $folder_name, $size="1", $sizecnt="2", $prefix="", $suffix="_",$default_img="80x80.jpg"){
    $small_image_url = $large_image_url = asset('/images/'.'no_image/'.$default_img); 
    if(isset($image) && $image != ""){
        if(config('settings.SITE_IMAGES_STORAGE') == config('constants.SITE_IMAGES_STORAGE_AWS')){
            $destinationPath = config('filesystems.disks.s3.url').config('filesystems.disks.s3.bucket')."/";
            $small_image_url = $destinationPath.$folder_name.$prefix.$size.$suffix.$image;
            $large_image_url = $destinationPath.$folder_name.$prefix.$sizecnt.$suffix.$image;
        }else{
            $file_path = public_path('/images/'.$folder_name.$prefix.$size.$suffix.$image); 
            if(file_exists($file_path)){
                $small_image_url = asset('/images/'.$folder_name.$prefix.$size.$suffix.$image); 
                $large_image_url = asset('/images/'.$folder_name.$prefix.$sizecnt.$suffix.$image); 
            }else{
                $small_image_url = '';
                $large_image_url = '';
            }
        }
    }
    return array('small_image_url' => $small_image_url, 'large_image_url' => $large_image_url);
}

function getFileFolderPath($iId, $file_path, $ext_para="")
{
    if($ext_para != "")
        $get_path = $iId.'/'.$ext_para.'/';
    else
        $get_path = $iId.'/';
    $path = $file_path.$get_path;

    return $path;
}
