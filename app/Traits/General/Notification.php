<?php 

namespace App\Traits\General;

use Validator;
use Config;
use GuzzleHttp;
use Illuminate\Http\Request;

trait Notification
{
    
    /**
     * Function: Validation messages - Topic View
     *
     * @return   array of messages
     */
    private function sendPushNotification($registrationIDs_GET,$title,$message,$androidaction,$android_firebase_apiKey){
        //echo "hello";exit;
        //global $android_firebase_apiKey;
        $msg = array
              (
            'body'  => $message,
            'title' => $title,
            'icon'  => 'myicon',/*Default Icon*/
            'sound' => 'mySound'/*Default sound*/
            /*'click_action'=>$redirect*/
              );
        $fields = array
                (
                    'to'  => $registrationIDs_GET,
                    'notification'  => $msg
                );
        
        $headers = array
                (
                    'Authorization: key=' . $android_firebase_apiKey,
                    'Content-Type: application/json'
                );
        // Open connection
        $ch = curl_init();
         
        // Set the url, number of POST vars, POST data
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        $res_msg  = $response;
        $res_type = $response["success"];

        $res_arr = array();
        
        if ($result === FALSE) {
            $res_msg = "".curl_error($ch);
            //echo ($registrationIDs_GET.'Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        //Response From Curl request
        //{"multicast_id":8104949437611558201,"success":1,"failure":0,"canonical_ids":0,"results":[{"message_id":"0:1523626948936788%c6fbd322c6fbd322"}]}
        //echo "<pre>";print_r($result);exit;
        
        
        $res_arr['response'] = $res_type;
        $res_arr['message'] = $res_msg;

        return $res_arr;
        
    }

    private function sendPushNotificationCron($registrationIDs_GET, $notification_data, $redirect, $android_firebase_apiKey){
        
        $endpoint = "https://fcm.googleapis.com/fcm/send";
        $client = new \GuzzleHttp\Client();
        //$redirect = 'TOPIC_VIDEO_SCREEN'; //Redirect Key based on notification
        $msg = array
              (
                // 'icon'  => 'myicon',/*Default Icon*/
                'icon'  => 'https://www.levelnext.in/img/gallery/1.jpeg',/*Default Icon*/
                'sound' => 'mySound',/*Default sound*/
                'click_action'=>$redirect
              );

        $click_action_data = array();
        if(!empty($notification_data)){
            foreach ($notification_data as $nd_key => $nd_value) {
                $msg[$nd_key] = $nd_value;
            }
            if(array_key_exists('click_action_data', $notification_data)){
                $click_action_data = $notification_data['click_action_data'];
            }
        }

        $postInput = [
            'to'  => $registrationIDs_GET,
            'notification'  => $msg,
            'data' => json_decode($click_action_data)
        ];

        $header = [
            'Authorization'  => " key=".$android_firebase_apiKey,
            'Content-Type' => 'application/json',
            'Accept'  => "application/json"
        ];


        $response = $client->post($endpoint, [
            'headers' => $header,
            'body'    => json_encode($postInput)
        ]); 

        //\Log::info("Send Push Notifications Traits response: ".print_r($response, true));

        $response = json_decode($response->getBody(), true);
        //\Log::info("Send Push Notifications Traits json_decode response: ".print_r($response, true));

        $res_msg  = $response;
        // echo '<pre>';print_r($res_msg);exit;
        $res_type = $response["success"];

        $res_arr = array();
        
        
        $res_arr['response'] = $res_type;
        $res_arr['message'] = $res_msg;

        return $res_arr;
        
    }


    
    
}