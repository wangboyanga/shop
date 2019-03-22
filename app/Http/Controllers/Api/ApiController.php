<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
class ApiController extends Controller
{
    //
    public function test1(){
        $url='http://test.web.com/api.php?type=2';
        $client=new Client();
        $r=$client->request('GET',$url);
        $resquest=$r->getBody();

        $resquest_arr=json_decode($resquest,true);
        print_r($resquest_arr);

    }
    //测试
    public function post(){
        $data=$_POST;
        $date=json_encode($data);
        //echo "<pre>";print_r($_POST);echo "</pre>";
        if(!empty($data)){
            $res=[
                'error'=>0,
                'msg'=>"数据已接收 数据为" .$date
            ];
        }else{
            $res=[
                'error'=>1,
                'msg'=>'错误'
            ];
        }

        //echo json_encode($data);
        return $res;

    }
    //注册
    public function register(Request $request){
        $user_name=$request->input('username');
        $pwd=$request->input('password');
        $email=$request->input('email');
        $data=[
            'name'=>$user_name,
            'password'=>$pwd,
            'email'=>$email,
            'reg_time'=>time()
        ];
        //print_r($data);
        $uid=UserModel::insertGetId($data);
        if($uid){

            $token = substr(md5(time().mt_rand(1,99999)),10,10);
            //setcookie('uid',$res->uid,time()+86400,'/','lening.com',false,true);
            setcookie('uid',$uid,'','/','',false,false);
            setcookie('token',$token,'','/user','',false,true);

            $request->session()->put('u_token',$token);
            $request->session()->put('uid',$uid);
            echo "注册成功";
            //header('Refresh:2;url=/user/center');
        }else{
            echo "注册失败";
        }
    }
    //登陆
    public function appLogin(Request $request){
        $username=$request->input('username');
        $password=$request->input('password');
        $data=[
            'username'=>$username,
            'password'=>$password
        ];
        $url='http://passport.wangby.cn/user/applogin';
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER,0);
        $rs=curl_exec($ch);
        //curl_close();
        //var_dump($rs);exit;
        $response=json_decode($rs,true);
        return $response;
    }
    //注册
    public function appRegister(Request $request){
        $user_name=$request->input('username');
        $pwd=$request->input('password');
        $age=$request->input('age');
        $email=$request->input('email');
        $data=[
            'name'=>$user_name,
            'password'=>$pwd,
            'age'=>$age,
            'email'=>$email,
            'reg_time'=>time()
        ];
        $url='http://passport.wangby.cn/user/appregister';
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER,0);
        $rs=curl_exec($ch);
        //curl_close();
        //var_dump($rs);exit;
        $response=json_decode($rs,true);
        return $response;
    }

}
