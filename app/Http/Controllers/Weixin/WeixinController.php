<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use App\Model\WeixinMedia;
use App\Model\WeixinChatModel;
use App\Model\WxUser;
use App\Model\UserModel;
class WeixinController extends Controller
{
     /**
      * 首次接入
      */
    public function validToken1(){
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    protected $redis_weixin_jsapi_ticket = 'str:weixin_jsapi_ticket';     //微信 jsapi_ticket
    public function test()
    {
        //echo __METHOD__;
        //$this->getWXAccessToken();
        echo 'Token: '. $this->getWXAccessToken();
    }

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");
        //解析XML
        $xml = simplexml_load_string($data);
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
        $event = $xml->Event;                    //事件类型
        $openid = $xml->FromUserName;
        //处理用户发送消息
        if(isset($xml->MsgType)){    //用户发送文本
            if($xml->MsgType=='text'){
                $msg=$xml->Content;
                //记录聊天消息

                $data = [
                    'msg'       => $xml->Content,
                    'msgid'     => $xml->MsgId,
                    'openid'    => $openid,
                    'msg_type'  => 1,        // 1用户发送消息 2客服发送消息
                    'add_time'  =>time()
                ];

                $id = WeixinChatModel::insertGetId($data);
                //var_dump($id);
                $xml_response= '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$msg. date('Y-m-d H:i:s') .']]></Content></xml>';
                //echo $xml_response;

            }elseif($xml->MsgType=='image'){     //用户发送图片
                if(1){     //下载图片
                    $file_name=$this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;
                    //写入数据库
                    $data = [
                        'openid'    => $openid,
                        'add_time'  => time(),
                        'msg_type'  => 'image',
                        'media_id'  => $xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   => $file_name
                    ];

                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=='voice'){
                $file_name=$this->dlVoice($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
                //写入数据库
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'voice',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                var_dump($m_id);
            }elseif($xml->MsgType=='video'){
                $file_name=$this->dlVideo($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
                //写入数据库
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'video',
                    'media_id'  => $xml->MediaId,
                    'format'    => $xml->Format,
                    'msg_id'    => $xml->MsgId,
                    'local_file_name'   => $file_name
                ];
                $m_id = WeixinMedia::insertGetId($data);
                var_dump($m_id);
            }elseif($xml->MsgType=='event'){
                //var_dump($xml);echo '<hr>';
                if($event=='subscribe'){
                    //用户openid
                    $sub_time = $xml->CreateTime;               //扫码关注时间

                    echo 'openid: '.$openid;echo '</br>';
                    echo '$sub_time: ' . $sub_time;

                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
                    echo '<pre>';print_r($user_info);echo '</pre>';

                    //保存用户信息
                    $u = WeixinUser::where(['openid'=>$openid])->first();
                    //var_dump($u);die;
                    if($u){       //用户不存在
                        echo '用户已存在';
                    }else{
                        $user_data = [
                            'openid'            => $openid,
                            'add_time'          => time(),
                            'nickname'          => $user_info['nickname'],
                            'sex'               => $user_info['sex'],
                            'headimgurl'        => $user_info['headimgurl'],
                            'subscribe_time'    => $sub_time,
                        ];

                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                        var_dump($id);
                    }
                }elseif($event=='CLICK'){
                    if($xml->EventKey=='kefu01'){
                        $this->kefu01($openid,$xml->ToUserName);
                    }
                }
            }
            //exit();
        }
    }
    //群发消息
    public function textGroup(){
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getWXAccessToken();
        //请求微信接口
        $client=new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'filter'=>[
                'is_to_all'=>true,
                'tag_id'=>2  //is_to_all为true可不填写
            ],
            'text'=>[
                'content'=>'撒由那拉  欢迎大家'
            ],
            'msgtype'=>'text'
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "群发成功";
        }else{
            echo "群发失败，请重试";
            echo "<br/>";
        }
    }
    //客服处理
    public function kefu01($openid,$from){
        //文本信息
        $xml_response='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'Hello World,现在时间'.date('Y-m-d H:i:s').']]></Content></xml>';
        echo $xml_response;
    }
    //下载图片
    public function dlWxImg($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //获取图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            //echo "ok";
        }else{      //保存失败
            //echo "no";
        }
        return $file_name;
    }
    //下载语音文件
    public function dlVoice($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //获取图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path = 'wx/voicd/'.$file_name;
        //保存语音
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            //echo "ok";
        }else{      //保存失败
            //echo "no";
        }
        return $file_name;
    }
    //下载视频
    public function dlVideo($media_id){
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //获取图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path = 'wx/video/'.$file_name;
        //保存视频
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
            //echo "ok";
        }else{      //保存失败
            //echo "no";
        }
        return $file_name;
    }

    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }

    public function createMenu(){
        // 1 获取access_token 拼接请求接口
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getWXAccessToken();
        //echo $url;echo '</br>';exit;
        //2请求微信接口
        $client=new GuzzleHttp\Client(['base_uri'=>$url]);
        $data=[
            "button"=>[
                [
                    //"type"=>"click",//view类型  跳转指定地址url
                    "name"=>"study",
                    //"url"=>"https://www.baidu.com",
                    "sub_button"=>[
                      [
                          "type"=>"scancode_push",
                          "name"=>"Mathematics",
                          "key"=>"rselfmenu_0_1",
                          "sub_button"=> [ ]
                      ]
                    ]
                ],
                [
                    //"type"=>"click",//view类型  跳转指定地址url
                    "name"=>"百度",
                    //"url"=>"https://www.baidu.com",
                    "sub_button"=>[
                        [
                            "type"=>"view",
                            "name"=>"网址",
                            "url"=>"https://www.baidu.com",
                        ]
                    ]
                ],
                [
                    "type"=>"click",//view类型  跳转指定地址url
                    "name"=>"客服01",
                    "key"=>"kefu01"
                    //"url"=>"https://www.baidu.com",
                ],
            ]
        ];

        $r = $client->request('POST',$url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        //var_dump($r);exit;
        //接收微信接口返回信息
        $request_arr=json_decode($r->getBody(),true);

        if($request_arr['errcode']==0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo "</br>";
            echo $request_arr['errmsg'];
        }
    }
    /**
     * 刷新access_token
     */
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }


    public function formShow(){
        return view('test.form');
    }

    public function formTest(Request $request){
        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';
        //echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';
        //接收文件
        $img_file=$request->file('media');
        //print_r($img_file);
        //文件名
        $img_origin_name=$img_file->getClientOriginalName();
        //print_r($img_origin_name);echo "</pre>";
        //文件类型
        $file_ext=$img_file->getClientOriginalExtension();
        //print_r($file_ext);echo "</pre>";
        //重命名
        $new_file_name=str_random(15).'.'.$file_ext;
        //echo $new_file_name;
        //保存文件

        //保存的路径
        $save_file_path=$request->media->storeAs('form_test',$new_file_name);//值为保存的路径
        //echo $save_file_path;
        //上传至微信永久素材
        $this->upMaterialTest($save_file_path);

    }
    public function upMaterialTest($file_path){
        $url='https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client=new GuzzleHttp\Client();
        $response=$client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);
        $body=$response->getBody();
        echo $body;echo "</br>";
        $d=json_decode($body,true);
        echo '<pre>';print_r($d);echo "</pre>";
    }

    //微信客服聊天
    public function formPrivate(){
        $data=[
            'openid'=>'o3hRJ6Co-bPbeUp9mnbJ6ESLIByk'
        ];
        return view('test.private',$data);
    }
    public function privMsg(){
        $openid = $_GET['openid'];  //用户openid
        //var_dump($openid);
        $pos = $_GET['pos'];        //上次聊天位置

        $msg = WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->first();
        //$msg = WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->get();
        //var_dump($msg);exit;
        if($msg){
            $response = [
                'errno' => 0,
                'data'  => $msg->toArray()
            ];

        }else{
            $response = [
                'errno' => 50001,
                'msg'   => '服务器异常，请联系管理员'
            ];
        }
        die( json_encode($response));
    }
    public function send(Request $request){
        $send_msg=$request->input('send_msg');
        $openid=$request->input('openid');
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();
        //请求微信接口
        $client=new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>
            [
                "content"=>$send_msg
            ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        $data = [
            'msg'       => $send_msg,
            'msgid'     => '',
            'openid'    => $openid,
            'msg_type'  => 2,        // 1用户发送消息 2客服发送消息
            'add_time'  =>time()
        ];

        $id = WeixinChatModel::insertGetId($data);
        var_dump($id);
        if($response_arr['errcode']==0){
            echo "发送成功";
        }else{
            echo "发送失败，请重试";
            echo "<br/>";
        }
    }

    //微信登陆
    public function login(){
        return view('weixin.login');
    }
    //接收
    public function getCode(Request $request)
    {
        //echo '<pre>';print_r($_GET);echo '</pre>';
        $code = $_GET['code'];
        //echo 'code: '.$code;
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($user_arr);echo '</pre>';
        //查询数据库中是否存在该账号
        $unionid = $user_arr['unionid'];
        $where = [
            'unionid'   =>  $unionid
        ];
        $wx_user_info = WxUser::where($where)->first();
        if($wx_user_info){
            $user_info = UserModel::where(['wechat_id'=>$wx_user_info->id])->first();
        }
        if(empty($wx_user_info)){
            //第一次登录
            $data = [
                'openid'        =>  $user_arr['openid'],
                'nickname'      =>  $user_arr['nickname'],
                'sex'           =>  $user_arr['sex'],
                'headimgurl'    =>  $user_arr['headimgurl'],
                'unionid'      =>  $unionid,
                'add_time'      =>  time()
            ];
            $wechat_id = WxUser::insertGetId($data);
            $rs = UserModel::insertGetId(['wechat_id'=>$wechat_id]);
            if($rs){
                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$rs,time()+86400,'/','shop.com',false,true);
                setcookie('token',$token,time()+86400,'/user','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$rs);
                echo '注册成功';
                header("refresh:2,url='/goods/list'");
            }else{
                echo '注册失败';
            }
            exit;
        }
        $token=substr(md5(time().mt_rand(1,99999)),10,10);
        setcookie('uid',$user_info->uid,time()+86400,'/','shop.com',false,true);
        setcookie('token',$token,time()+86400,'/user','',false,true);
        $request->session()->put('u_token',$token);
        $request->session()->put('uid',$user_info->uid);
        echo "登录成功";
        header("refresh:2,url='/goods/list'");
    }


    public function jssdkTest(){
        //计算机签名
        $info=[
            'appid'=>env('WEIXIN_APPID'),
            'time'=>time(),
            'noncestr'=>str_random(15),
            //'sign'=>$this->jssign()
        ];
        $sign=$this->jssign($info);
        $info['sign']=$sign;

        return view('weixin.jssdk',$info);
    }

    //计算jssdk sign
    public function jssign($param){
        $current_url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $ticket=$this->getJsapiToken();
        $str='jsapi_ticket='.$ticket.'&noncestr='.$param['noncestr'].'&timestamp='.$param['time'].'&url='.$current_url;
        $signature=sha1($str);
        return $signature;
    }

    //获取ticket_token
    public function getJsapiToken(){
        //是否有缓存
        $ticket=Redis::get($this->redis_weixin_jsapi_ticket);
        if(!$ticket){           //没有缓存
            //$access_token=$this->getWXAccessToken();
            $access_token='19_QejIlWJ3yMYq_KmDpDHdXmSZZYHW_wl3alBTexqbXMmNFhRZfe2CQ3-ujmfXi7-NjSU7KRIyinoGSq6ESkIErKtWfTPln_RHG9wW3XvqGaCOPl4luZrSHgp_ylDbZZYAtLEyxT5VsXkS7W35GVRcAJACRN';
            //var_dump($access_token);exit;
            $ticket_url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $ticket_info=file_get_contents($ticket_url);
            $ticket_arr=json_decode($ticket_info,true);
            //print_r($ticket_arr);exit;
            if(isset($ticket_arr['ticket'])){
                $ticket=$ticket_arr['ticket'];
                Redis::set($this->redis_weixin_jsapi_ticket,$ticket);
                Redis::setTimeout($this->redis_weixin_jsapi_ticket,3600);
            }

        }
        return $ticket;
    }
    public function token(){
        $access_token=$this->getWXAccessToken();
        var_dump($access_token);
    }
}

