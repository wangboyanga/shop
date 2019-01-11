<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{
    //

	public function user($uid)
	{
		echo $uid;
	}

	public function add()
	{
		$data = [
			'name'      => str_random(5),
			'age'       => mt_rand(20,99),
			'email'     => str_random(6) . '@gmail.com',
			'reg_time'  => time()
		];

		$id = UserModel::insertGetId($data);
		var_dump($id);
	}
	public function test(){
        $list=UserModel::all()->toArray();
        //var_dump($list);exit;
        $data=[
            'title'=>'bbbb',
            'list'=>$list
        ];
        return view('user.child',$data);
    }
    public function reg(){
	    return view('user.reg');
    }
    public function doReg(Request $request){
        //echo "<pre>";print_r($_POST);
        $name=$request->input('u_name');
        $password=$request->input('u_password');
        $password1=$request->input('u_password1');
        $age=$request->input('u_age');
        $email=$request->input('u_email');
        if(empty($name)){
            echo "用户名必填";exit;
        }
        if(empty($password)){
            echo "密码必填";exit;
        }
        if(empty($password1)){
            echo "确认密码必填";exit;
        }else if($password!==$password1){
            echo "确认密码必须和密码保持一致";
        }
        if(empty($age)){
            echo "年龄必填";exit;
        }
        if(empty($email)){
            echo "邮箱必填";exit;
        }
        $res=UserModel::where(['name'=>$name])->first();
        if($res){
            echo "该账号已存在";exit;
        }
        $password2=password_hash($password,PASSWORD_BCRYPT);
        $data=[
            'name'=>$name,
            'password'=>$password2,
            'age'=>$age,
            'email'=>$email,
            'reg_time'=>time()
        ];
        //print_r($data);
        $uid=UserModel::insertGetId($data);
        if($uid){

            $token = substr(md5(time().mt_rand(1,99999)),10,10);
            //setcookie('uid',$res->uid,time()+86400,'/','lening.com',false,true);
            setcookie('uid',$uid,time()+60*60*24,'/','',false,false);
            setcookie('token',$token,time()+60*60*24,'/user','',false,true);

            $request->session()->put('u_token',$token);
            $request->session()->put('uid',$uid);
            echo "注册成功";
            header('Refresh:2;url=/user/center');
        }else{
            echo "注册失败";
        }
    }
    public function login(){
        if(!empty($_COOKIE['uid'])){
            header('Location:/user/center');exit;
        }
        $data = [];
        //var_dump($_COOKIE);
	    return view('user.login',$data);
    }
    public function doLogin(Request $request){
        $name=$request->input('u_name');
        $password=$request->input('u_password');
        if(empty($name)){
            echo "用户名必填";exit;
        }
        if(empty($password)){
            echo "密码必填";exit;
        }
        $res=UserModel::where(['name'=>$name])->first();
        if($res){
            if(password_verify($password,$res->password)){
                $token = substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$res->uid,time()+60*60*24,'/','',false,true);
                setcookie('token',$token,time()+60*60*24,'/user','',false,true);

                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$res->uid);
                header("Refresh:3;url=/user/center");
                echo "登陆成功";
            }else{
                echo "账号或密码错误";
            }
        }else{
            echo "账号或密码错误";
        }
    }
    public function center(Request $request)
    {
        if(empty($request->session()->get('u_token'))){
            header('Refresh:2;url=/user/login');
            //echo $request->session()->get('u_token');
            die('请先登录');
        }else if($_COOKIE['token'] != $request->session()->get('u_token')){
            die("非法请求");
        }else{
            echo '正常请求';
        }
        if(empty($_COOKIE['uid'])){
            header('Refresh:2;url=/user/login');
            echo '请先登录';
            exit;
        }else{
            echo 'UID: '.$_COOKIE['uid'] . ' 欢迎回来';
            header('Refresh:2;url=/goods/list/1');
            echo "</br>";
            echo "正在前往商品页面";
        }
    }
    public function logou(){
        setcookie('uid',null,time()+60*60*24,'/','lening.com',false,true);
        setcookie('token',null,time()+60*60*24,'/user','',false,true);
        echo "退出成功";
        header('Refresh:2;url=/user/login');
    }
}
