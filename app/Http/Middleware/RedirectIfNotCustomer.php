<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotCustomer
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!isCustomerAuthenticated()) {
            ## Remove all variables from session
            if(session()->has(config('constants.SESSION_PREFIX'))){
                session()->forget(config('constants.SESSION_PREFIX'));
            }
            ## Redirect to Login Page
            if($request->ajax()){
                return response()->json(['is_error'=> '1', 'message' => '', 'error_type' => 'unauthorized', 'data' => array()]);
            }else{
                return redirect()->route('user.login');
            }
        }
        return $next($request);
    }
}
