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
            $app_token=Redis::hget($key,'app');
            if($_COOKIE['token']==$token || $_COOKIE['token']==$app_token){
                //token有效
                $request->attributes->add(['is_login'=>1]);
            }else{
                //token无效
                $request->attributes->add(['is_login'=>0]);

                header('Refresh:1;url=http://passport.wangby.cn/user/login');
                echo '请先登录';exit;
            }
        }else{
            //未登录
            $request->attributes->add(['is_login'=>0]);
            header('Refresh:1;url=http://passport.wangby.cn/user/login');
            echo '请先登录';exit;
        }
        //echo $request->session()->get('u_token');echo '</br>';
        return $next($request);
    }
}
