<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use App\Traits\ApiFunction;
use App\Models\Member;
use App\Models\UserDeviceToken; 
use App\Models\UserLoginLog; 
use App\Models\Settings;
use Config;

class VerifyUserAuthToken
{
    use ApiFunction;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  \Closure  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        ## Variable Declaration
        $message = '';
        $USER_AUTH_TOKEN_EXPIRATION_HOURS = config('api_constants.USER_AUTH_TOKEN_EXPIRATION_HOURS');
        $current_time = date_getSystemDateTime();

        ## Fetch User Auth Token from request
        $user_auth_token = $this->getRequestHeader($request, 'username');
        // echo '<pre>'; print_r($user_auth_token); echo '</pre>'; exit();

        $request_param = $request->all();
        if($user_auth_token == ""){
            ## Invalid access. Please login again.
            $message = "Enter user authentication token.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }else{

            /*$rsSettings = Settings::getSettingsData(array('ENABLE_MULTIPLE_DEVICE_LOGIN'));
            $enable_multiple_device_login = 'Yes';
            if(!empty($rsSettings) && isset($rsSettings[0]['value'])){
                $enable_multiple_device_login = $rsSettings[0]['value'];
            }*/
            $enable_multiple_device_login = config('settings.ENABLE_MULTIPLE_DEVICE_LOGIN');

            // if($enable_multiple_device_login == "Yes"){
                
                ## Get entity type based on Guard
                $req_user_id      = (isset($request_param['user_id']) && $request_param['user_id'] != "" ? $request_param['user_id'] : 0);
                
                $where_array = array(
                    'token' => $user_auth_token,
                    'device_type' => $request->input('device_type')
                );
                
                ## Object of MemberDeviceToken Model Class
                $userDeviceTokenObj   = new UserDeviceToken();
                $deviceData             = $userDeviceTokenObj->getUserDeviceToken($where_array);
                
                if(!empty($deviceData)){
                    $id             = isset($deviceData[0]['id'])?$deviceData[0]['id']:"";
                    $token          = isset($deviceData[0]['token'])?$deviceData[0]['token']:"";
                    $user_id      = isset($deviceData[0]['user_id'])?$deviceData[0]['user_id']:0;
                    $expire_at      = @strtotime($deviceData[0]['expire_at']);

                    if($req_user_id > 0 && $user_id != $req_user_id){
                            ## Invalid access. Please login again.
                        $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                        $message = "Invalid user authentication.";

                    }else{

                        if(strtotime($current_time) <= $expire_at){

                            $userLoginLogObj   = new UserLoginLog();
                            $token_check = $userLoginLogObj->getUserLogin(array('user_id' => $user_id, 'token' => $token));

                            if (isset($token_check[0]['logout_date'])) {
                                $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                                $message = "User has been logged out.";
                            } else {

                                ## Update User Device Token Expiration Time (Based on hours set in global constants)
                                $new_expire_at = date_addDateTime($current_time, $da = 0, $ma = 0, $ya = 0, $ha = $USER_AUTH_TOKEN_EXPIRATION_HOURS, $mi = 0, $ss = 0);
                                $update_array = array(
                                    'expire_at' => $new_expire_at
                                );
                                $update = $userDeviceTokenObj->updateUserDeviceToken($id, $update_array);
                            }
                        }else{
                            ## Your login has expired. Please login for continue.
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message = "User authentication token is expired.";
                        }
                    }
                }else{
                    ## Invalid access. Please login again.
                    $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    $message = "Your session has been expired. Please login again to continue.";
                }
            // }else{
            //     // For Single Device Login Authentication
            //     ## Get entity type based on Guard
            //     //$req_member_id = isset($request->input('member_id'))?$request->input('member_id'):0;

            //     $req_member_id      = (isset($request_param['member_id']) && $request_param['member_id'] != "" ? $request_param['member_id'] : 0);
                                
            //     $where_array = array(
            //         'token' => $user_auth_token,
            //         'device_type' => $request->input('device_type')
            //     );
                
            //     ## Object of MemberDeviceToken Model Class
            //     $memberTokenObj   = new Member();
            //     $deviceData             = $memberTokenObj->getMemberDeviceToken($where_array);
            //     if(!empty($deviceData)){
            //         $member_id             = isset($deviceData[0]['id'])?$deviceData[0]['id']:"";
            //         $token                 = isset($deviceData[0]['token'])?$deviceData[0]['token']:"";
            //         $expire_at             = @strtotime($deviceData[0]['token_expire_at']);

            //         if($req_member_id > 0 && $member_id != $req_member_id){
            //                 ## Invalid access. Please login again.
            //             $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            //             $message = "Invalid user authentication.";
            //         }else{
            //             if(strtotime($current_time) <= $expire_at){
            //                 ## Update User Device Token Expiration Time (Based on hours set in global constants)
            //                 $new_expire_at = date_addDateTime($current_time, $da=0, $ma=0, $ya=0, $ha=$USER_AUTH_TOKEN_EXPIRATION_HOURS, $mi=0, $ss=0);
            //                 $update_array = array(
            //                     'token_expire_at' => $new_expire_at
            //                 );
            //                 $update = $memberTokenObj->updateMember($member_id, $update_array);                   
            //             }else{
            //                 ## Your login has expired. Please login for continue.
            //                 $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            //                 $message = "User authentication token is expired.";
            //             }
            //         }
            //     }else{
            //         ## Invalid access. Please login again.
            //         $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            //         $message = "Your session has been expired. Please login again to continue.";
            //     }
            // }
            ## Return Failure Response
            if($this->getStatusCode() != Response::HTTP_OK){
                return $this->prepareResult($message, $data = []);
            }
        }

        return $next($request);
    }
}
