<?php ## Created by Radhika Mangrola as on 3rd Aug 2019

namespace App\Http\Middleware;

use Closure;
use App\GlobalClass\ApiAuthorizationToken;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction;

class VerifyGeneralAuthToken
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

        ## Fetch Authorization Token from request
        $authorization_token = $request->bearerToken();

        ## Blank Authorization Token
        if ($authorization_token == "") {
            $message[] = "Add authorization token.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }

        ## Check Authorization Token is valid or not
        $token_data = ApiAuthorizationToken::checkAuthorizationTokenForGeneralAPI($authorization_token);
        
        ## Invalid Authorization Token
        if(empty($token_data)){
            $message = "Add valid authorization token.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }

        return $next($request);
    }
}
