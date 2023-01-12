<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Route;
use Config;

class AuthenticateAccessRights
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
		   $route_name = Route::currentRouteName();
			
		   $mode = Route::input('mode');
		   $access_module = Config::get('constants.access_module');
		   $access_route_name = array_keys($access_module);
		   $check=0;
		   if (in_array($route_name,$access_route_name) && empty($mode)) {
		   		 
				$check = per_hasModuleAccess($access_module[$route_name],'List');
		   } elseif (in_array($route_name,$access_route_name) && !empty($access_module[$route_name]) && $mode == 'add') {
				$check = per_hasModuleAccess($access_module[$route_name],'Add');
		   } elseif (in_array($route_name,$access_route_name) && !empty($access_module[$route_name]) && $mode == 'edit') {
				$check = per_hasModuleAccess($access_module[$route_name],'Edit');
		   } elseif (in_array($route_name,$access_route_name) && !empty($access_module[$route_name]) && $mode == 'view') {
				$check = per_hasModuleAccess($access_module[$route_name],'View');
		   } elseif (in_array($route_name,$access_route_name) && !empty($access_module[$route_name]) && $mode == 'print') {
		   		$check = per_hasModuleAccess($access_module[$route_name],'View');
		   }elseif (!in_array($route_name,$access_route_name)) {
				$check=0;
		   }
		   if($check != 1) {
				return redirect()->route('unauthorised-access');
		   } else {
				return $next($request);
		   }
	 }
}
?>