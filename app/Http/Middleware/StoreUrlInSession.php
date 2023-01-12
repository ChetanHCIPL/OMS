<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class StoreUrlInSession
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
        ## Store URL to session to redirect to previous Page after login
        if(!$request->ajax()){
            Session::put("previousURL", $request->url());
        }
        return $next($request);
    }
}
