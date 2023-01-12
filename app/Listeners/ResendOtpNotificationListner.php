<?php

namespace App\Listeners;

use App\Events\ResendOtpNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SMSTemplate;

class ResendOtpNotificationListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ResendOtpNotification  $event
     * @return void
     */
    public function handle(ResendOtpNotification $event) {
        ## Variable Declaration
        $result = array();
       
        ## Fetch variables from ResendOtpNotification Event class
        if(!empty($event->type))
        {
            ## DB Query: Get Notification Template in user's preferred language
            $notification_template = SMSTemplate::getSmsTemplateDataFormID($event->type, $event->section_type);
            if(isset($notification_template[0]['content']) && $notification_template[0]['content'] != "" ){
                
                ## Notification Data
                $body_msg = $notification_template[0]['content'];
                $title = isset($event->title)?$event->title:'';
                $otp = isset($event->otp)?$event->otp:'';

                ## Replace Meassage data
                $array_search_msg  = Array("{#var#}");
                $array_replace_msg = array($otp);
                $notification_data_meassage = addslashes(str_replace($array_search_msg, $array_replace_msg, $body_msg));
                $result['meassge'] = $notification_data_meassage;
            }else{
                ## Template not found any notification
                $result['meassge'] = "We are unable to send notification right now, please try after some time.";
            }
            ## Template not found any notification
        } 
        else
        {
            $result['meassge'] = "We are unable to send notification right now, please try after some time.";  
        }   
        ## Return Response
        return $result;
 
    }
}
