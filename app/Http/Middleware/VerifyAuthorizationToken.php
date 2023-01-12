<?php

namespace App\Http\Middleware;

use Closure;
use App\GlobalClass\ApiAuthorizationToken;
use Illuminate\Http\Response;
use App\Traits\ApiFunction;

class VerifyAuthorizationToken
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
        ## Fetch Authorization Token from request
        $authorization_token = $request->bearerToken();
        ## Blank Authorization Token
        if ($authorization_token == "") {
            $message = "Add authorization token.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }

        ## Blank Device Type
        if (!$request->has('device_type') || !$request->filled('device_type')){
            $message = "Add device type.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }

        ## Invalid Device Type
        if ($request->filled('device_type') && !preg_match('/^[0-9]+$/', $request->input('device_type'))) {
            $message = "Enter valid device type.";
            $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->prepareResult($message, $data = []);
        }

        ## Check Authorization Token is same as the token allocated to this guard (stored in Database) or not
        $db_token = ApiAuthorizationToken::checkAuthorizationToken($request->input('device_type'));

        ## Invalid Authorization Token
        if($authorization_token != $db_token){
            $message = "Add valid authorization token.";
            $this->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this->prepareResult($message, $data = []);
        }

        return $next($request);
    }
}
