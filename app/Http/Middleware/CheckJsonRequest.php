<?php ## Created by Radhika Mangrola as on 3rd Aug 2019

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use App\Traits\General\ApiFunction;

class CheckJsonRequest
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
        $post =  $request->all();

        // if(isset($post['lang_code']) && !empty($post['lang_code']) && $post['lang_code'] == 'gu')
        // {
        //     $post['lang_code'] = 'gu';
        // }
        $locale = isset($post['lang_code']) ? $post['lang_code'] : "en";
        app()->setLocale($locale);
        generateMemberAPIDebugLog();

        ## Invalid request content type 
        if (!$request->isJson()) {
            $message[] = "Bad Request.";
            $this->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->prepareResult($message, $data = []);
        }
        return $next($request);
    }
}
