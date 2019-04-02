<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\AppUserModel;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function login(){
        return view('user.pc');
    }
    public function logins(Request $request){
        $uname=$request->input('uname');
        $pwd=$request->input('password');
        $where=[
            'name'=>$uname,
            'password'=>$pwd
        ];
        $res=AppUserModel::where($where)->first();
        if($res){
            $token=substr(md5(time()+$res->id.mt_rand(1,99999)),10,20);
            $redis_key='api:app:user:'.$res->id;
            setcookie('app_token',$token,'','/','',false,true);
            setcookie('app_id',$res->id,'','/','',false,true);
            Redis::del($redis_key);
            Redis::hset($redis_key,'app',$token);
            Redis::expire($redis_key,10);
            echo "登陆成功";
            header('Refresh:1;url=/pc/admin');
        }else{
            exit('请输入正确的账号密码');
        }
    }
    public function adminList(Request $request){
        if(isset($_COOKIE['app_id']) && isset($_COOKIE['app_token'])){
            //验证token
            $key='api:app:user:'.$_COOKIE['app_id'];
            $token=Redis::hget($key,'app');
            if($_COOKIE['app_token']==$token){
                //token有效
               $redis=Redis::TTL($key);
               //echo $redis;
               if($redis<=0){
                   header('Refresh:1;url=/pc/login');
                   echo '长时间未操作请重启登陆';exit;
               }
               echo "hello";
            }else{
                if($token){
                    header('Refresh:1;url=/pc/login');
                    echo '已在别处登陆';exit;
                }else{
                    header('Refresh:1;url=/pc/login');
                    echo '请先登录';exit;
                }
            }
        }else{
            //未登录
            header('Refresh:1;url=/pc/login');
            echo '请先登录';exit;
        }
    }
}
