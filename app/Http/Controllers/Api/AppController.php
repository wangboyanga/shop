<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class AppController extends Controller
{
    public function login(Request $request){
        $name=$request->input('user_name');
        $password=$request->input('user_pwd');
        $where=[
            'name'=>$name,
            'password'=>$password
        ];
        $res=AppUserModel::where($where)->first();
        if($res){
            $token=substr(md5(time()+$res->id.mt_rand(1,99999)),10,20);
            $redis_key='api:app:user:'.$res->id;
            setcookie('app_token','$token','','/','',false,true);
            setcookie('app_id',$res->id,'','/','',false,true);
            Redis::del($redis_key);
            Redis::hset($redis_key,'app',$token);
            $response=[
                'error'=>1,
                'msg'=>$res
            ];
        }else{
            $response=[
                'error'=>4001,
                'msg'=>'账号或密码错误'
            ];
        }
        return $response;
    }
}