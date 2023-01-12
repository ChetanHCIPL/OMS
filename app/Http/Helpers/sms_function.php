<?php
/**
* Get Values of message string and mobile number
*/
function sendSMS($message_str,$mobile_no){
    try{
        $isError = 0;
         $url = config('settings.SMS_SERVICE_PROVIDER_URL');
         $sms_sender_id = config('settings.SMS_SENDER_ID');
         $sms_sender_mobile = config('settings.SMS_SENDER_MOBILE');
         $sms_sender_password = config('settings.SMS_SENDER_PASSWORD');
         $fields = array(
            'mobile' => $sms_sender_mobile,
            'pass' => $sms_sender_password,
            'senderid' => $sms_sender_id,
            'to' => $mobile_no,
            'msg' => urlencode($message_str),
            'msgtype' =>'uc'
        );

        //http Url to send sms.
        //$url="http://voice.smsbomb.online/smsstatuswithid.aspx";
        /*$fields = array(
            'mobile' => '7600139911',
            'pass' => 'Abdc@1243',
            'senderid' => 'LEVNXT',
            'to' => $mobile_no,
            'msg' => urlencode($message_str),
            'msgtype' =>'uc'
        );*/
        //url-ify the data for the POST
        $fields_string = '';
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
        }
        rtrim($fields_string, '&');
        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //execute post
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            $isError = 1;
        }
        if (empty($ret)) {
            $isError = 1;
            // some kind of an error happened
            die(curl_error($ch));
            curl_close($ch); // close cURL handler
        } else {
            $isError = 0;
            $info = curl_getinfo($ch);
            curl_close($ch); // close cURL handler
        }
        return array('isError'=>$isError);
    }
    catch(Exception $e){
        return $e->getMessage();
    }
}