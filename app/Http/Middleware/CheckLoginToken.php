<?php

namespace App\Http\Middleware;

use Closure;
use http\Env\Request;

class CheckLoginToken
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
        if(!$request->session()->get('u_token')){
            header('Refresh:2;url=/user/login');
            echo "请先登录";exit;
        }
        //echo $request->session()->get('u_token');echo '</br>';
        return $next($request);
    }
}
