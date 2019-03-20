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
        echo json_encode($data);
    }
}
