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
        //print_r($token);exit;
        $key='api:app:user:'.$userid;
        echo $key;
        $data=Redis::hget($key,'app');
        var_dump($data);exit;
        if($data){
            if($token==$data){
                $response=[
                    'error'=>0,
                    'msg'=>''
                ];
                return $response;
            }
            $response=[
                'error'=>4002,
                'msg'=>'已在别处登陆'
            ];
            return $response;
        }else{
            $response=[
                'error'=>4001,
                'msg'=>'请先登录'
            ];
            return $response;
        }
    }
}