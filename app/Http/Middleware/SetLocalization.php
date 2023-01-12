<?php

namespace App\Http\Middleware;

use Closure;
use LaravelLocalization;
use App\Models\Language;

class SetLocalization
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

        if ($request->has('lang_code') && $request->input('lang_code') != "") {
            
            ## Check language exist or not
            $is_lang_exist = Language::checkLanguageExistByCode($request->input('lang_code'));

            if($is_lang_exist != ""){
                LaravelLocalization::setLocale($request->input('lang_code'));
            }else{
                LaravelLocalization::setLocale(config('app.fallback_locale'));
            }
        }else{
            LaravelLocalization::setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
}
