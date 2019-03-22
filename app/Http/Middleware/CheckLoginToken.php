<?php

namespace App\Http\Middleware;

use Closure;
use http\Env\Request;
use Illuminate\Support\Facades\Redis;

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
        if(isset($_COOKIE['uid']) && isset($_COOKIE['token'])){
            //验证token
            $key='str:u:token:'.$_COOKIE['uid'];
            $token=Redis::hget($key,'web');
            if($_COOKIE['token']==$token){
                //token有效
                $request->attributes->add(['is_login'=>1]);
            }else{
                //token无效
                $request->attributes->add(['is_login'=>0]);
            }
        }else{
            //未登录
            $request->attributes->add(['is_login'=>0]);
        }
        //echo $request->session()->get('u_token');echo '</br>';
        return $next($request);
    }
}
