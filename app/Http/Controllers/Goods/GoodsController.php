<?php

namespace App\Http\Controllers\Goods;

use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    //
    public function index($goods_id){
        if($goods_id<=0){
            echo '请选择正确商品';
        }
        $goods=GoodsModel::where(['goods_id'=>$goods_id])->first();
        //var_dump($goods);
        if(!$goods){
            header('Refresh:2;url=/user/center');
            echo '商品不存在,正在跳转至首页';
            exit;
        }
        $data=[
            'goods'=>$goods
        ];
        return view('goods.index',$data);
    }
    public function list(){
        $goods=GoodsModel::where(['uid'=>session()->get('uid')])->get();
        $data=[
            'goods'=>$goods
        ];
        return view('goods.list',$data);
        //var_dump($goods);
    }
}
