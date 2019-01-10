<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Ramsey\Uuid\Codec\OrderedTimeCodec;

class OrderController extends Controller
{
    public function index(){
        echo __METHOD__;
    }
    //下单
    public function add(Request $request){
        //查询购物车中的商品
        $goods=CartModel::where(['uid'=>session()->get('uid')])->orderBy('id','desc')->get()->toArray();
        if(empty($goods)){
            die("购物车中无商品");
        }
        $order_amount=0;
        foreach($goods as $k=>$v){
            $goodsInfo=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goodsInfo['num']=$v['num'];
            $list[]=$goodsInfo;
            //计算订单价格 = 商品数量*单价
            $order_amount+=$goodsInfo['price']*$v['num'];
        }
        //生成订单号
        $order_number=OrderModel::generateOrderSN();
        $data=[
            'order_number'=>$order_number,
            'uid'=>session()->get('uid'),
            'add_time'=>time(),
            'order_amount'=>$order_amount
        ];
        $oid=OrderModel::insertGetId($data);
        if(!$oid){
            header('Refresh:2;url=/cart');
            echo "生成订单失败";exit;
        }
        echo "下单成功,您的订单号为：".$order_number;
        header('Refresh:2;url=/order/list');
        //清空购物车
        CartModel::where(['uid'=>session()->get('uid')])->delete();

    }
    public function list(Request $request){
        $data=OrderModel::where(['uid'=>session()->get('uid')])->get();
        $info=[
            'data'=>$data
        ];
        return view('order.order',$info);
        //print_r($res);
    }
    public function pay($order_id){
        if(empty($order_id)){
            die('订单不存在');
        }
        $res=OrderModel::where(['order_id'=>$order_id])->delete();
        if($res){
            header('Refresh:2;url=/order/list');
            echo "支付成功";
        }else{
            header('Refresh:2;url=/order/list');
            echo "支付失败";
        }
    }
    public function off($order_id){
        if(empty($order_id)){
            die('订单不存在');
        }
        $res=OrderModel::where(['order_id'=>$order_id])->delete();
        if($res){
            header('Refresh:2;url=/order/list');
            echo "取消订单成功";
        }else{
            header('Refresh:2;url=/order/list');
            echo "取消订单失败";
        }
    }
}
