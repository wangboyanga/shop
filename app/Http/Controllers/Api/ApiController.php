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
}
