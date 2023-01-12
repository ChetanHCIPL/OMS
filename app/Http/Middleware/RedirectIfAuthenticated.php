<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = "web")
    {
        switch ($guard) {
            case 'admin':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard');
                }
                break;
            case 'web':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('user.dashboard');
                }
                
                # throw error in forgot password page

                /*elseif (isCustomerAuthenticated()) {
                    return redirect()->route('user.dashboard');
                }*/
                break;
            default:
                return redirect()->route('welcome');
                break;
        }

        return $next($request);
    }
}
