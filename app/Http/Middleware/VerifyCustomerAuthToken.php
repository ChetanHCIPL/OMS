<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction;
use App\Models\CustomerDeviceToken; 

class VerifyCustomerAuthToken
{

    use ApiFunction;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ## Variable Declaration
        $message = array();
        $CUSTOMER_AUTH_TOKEN_EXPIRATION_HOURS = config('api_constants.CUSTOMER_AUTH_TOKEN_EXPIRATION_HOURS');
        $current_time = date_getSystemDateTime();

        ## Fetch Customer  Auth Token from request
        $user_auth_token = $this->getRequestHeader($request, 'username');
         
        if($user_auth_token == ""){
            ## Please login to continue.
            $message[] = __('message.msg_login_to_continue');
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }else{
            
            ## Object of CustomerDeviceToken Model Class
            $customerDeviceTokenObj = new CustomerDeviceToken();

            ## Blank customer id
            if ($request->input('buyer_id') == "") {
                $message[] = __('message.msg_login_to_continue');
                $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                return $this->prepareResult($message, $data = []);

            }else{ 

                $where_array = array( 
                    'device_type' => $request->input('device_type'),
                    'customer_id' => $request->input('buyer_id')
                );
                $deviceData = $customerDeviceTokenObj->getCustomerDeviceToken($where_array);
               
                if(!empty($deviceData)){            
                    $id = isset($deviceData[0]['id'])?$deviceData[0]['id']:"";
                    $token = isset($deviceData[0]['token'])?$deviceData[0]['token']:"";
                    $expire_at = @strtotime($deviceData[0]['expire_at']);
                    
                    if($user_auth_token == $token){
                        if(strtotime($current_time) <= $expire_at){

                            ## Update Customer  Device Token Expiration Time (Based on hours set in global constants)
                            $new_expire_at = date_addDateTime($current_time, $da=0, $ma=0, $ya=0, $ha=$CUSTOMER_AUTH_TOKEN_EXPIRATION_HOURS, $mi=0, $ss=0);

                            $update_array = array(
                                'created_at' => $current_time,
                                'expire_at' => $new_expire_at,
                            );
                            $update = $customerDeviceTokenObj->updateCustomerDeviceToken($id, $update_array);

                        }else{
                            ## Your login has expired. Please login for continue.
                            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                            $message[] = __('message.msg_login_session_expired');
                        }
                    }else{
                        ## Invalid access. Please login again.
                        $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
                        $message[] = __('message.msg_invalid_login_access');
                    }       
                }else{
                    ## No records found with request parameters in User device toke table - device type and customer id
                    $this->setStatusCode(Response::HTTP_NOT_FOUND);
                    $message[] = __('message.msg_invalid_login_access');
                }
            }

            ## Return Failure Response 
            if($this->getStatusCode() != Response::HTTP_OK){
                return $this->prepareResult($message, $data = []);
            }
        } 
        return $next($request); 
    }
}
