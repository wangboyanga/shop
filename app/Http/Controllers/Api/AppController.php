<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\Model\AppUserModel;
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
            Redis::expire($redis_key,10);
            $res['token']=$token;
            $response=[
                'error'=>0,
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
    public function center(Request $request){
        $userid=$request->input('userid');
        $token=$request->input('token');
        //print_r($userid);exit;
        $key='api:app:user:'.$userid;
        $data=Redis::hget($key,'app');
        //echo $redis;
        if($token==$data){
            $response=[
                'error'=>0,
                'msg'=>''
            ];
        }else{
            if($data){
                $response=[
                    'error'=>4002,
                    'msg'=>'已在别处登陆'
                ];
            }else{
                $response=[
                    'error'=>4001,
                    'msg'=>'已过期'
                ];
            }
        }
        return $response;
    }
}