<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use GuzzleHttp\Client;
class OrderController extends Controller
{
    public function index(){
        echo __METHOD__;
    }

    public function wby(){
        $url='http://wby.wangby.cn';
        $client = new Client([
            'base_uri' => $url,
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET', '/order.php');
        echo $response->getBody();
        //$url="http://www.order.com";
        //$client=new Client(['base_uri'=>$url,'timeout'=>2.0,]);
        //$response=$client->request('GET','/order.php');
        //echo $response->getBody();
    }
    //下单
    public function add(Request $request){
        //查询购物车中的商品
        $goods=CartModel::where(['uid'=>session()->get('uid')])->orderBy('id','desc')->get()->toArray();
        if(empty($goods)){
            die("购物车中无商品");
        }
        $goods_id=[];
        $num=[];
        $order_amount=0;
        foreach($goods as $k=>$v){
            $goods_id[]=$v['goods_id'];
            $num[]=$v['num'];
            $goodsInfo=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goodsInfo['num']=$v['num'];
            $list[]=$goodsInfo;
            //计算订单价格 = 商品数量*单价
            $order_amount+=$goodsInfo['price']*$v['num'];
        }

        $goods_id=implode(',',$goods_id);
        $num=implode(',',$num);
        //print_r($num);
        //print_r($num);
        //exit;
        //生成订单号
        $order_number=OrderModel::generateOrderSN();
        $data=[
            'order_number'=>$order_number,
            'uid'=>session()->get('uid'),
            'add_time'=>time(),
            'order_amount'=>$order_amount,
            'goods_id'=>$goods_id,
            'goods_num'=>$num
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
    public function list2($order_id){
        $data=OrderModel::where(['order_id'=>$order_id])->get()->toArray();

        $res1=OrderModel::where(['order_id'=>$order_id])->first();
        $goods_id=$res1['goods_id'];
        $num=$res1['goods_num'];
        $goods_id=explode(',',$goods_id);
        $num=explode(',',$num);
        //print_r($goods_id);exit;
        foreach($goods_id as $k=>$v){
            //echo $v;
            $res2=GoodsModel::where(['goods_id'=>$v])->first()->toArray();
            foreach ($num as $val){

            }
            $res2['num']=$val;
            $list[]=$res2;
            //print_r($data);
        }
        $info=[
            'data'=>$data,
            'list'=>$list
        ];
        //print_r($list);exit;
        //return view('order.orders',$list);
        return view('order.orders',$info);
        //print_r($res);
    }
    public function pay($order_id){
        if(empty($order_id)){
            die('订单不存在');
        }
        $data=[
            'is_pay'=>2,
            'pay_time'=>time()
        ];
        $where=[
            'order_id'=>$order_id
        ];
        $res=OrderModel::where($where)->update($data);
        //修改库存
        $res1=OrderModel::where($where)->first();
        $goods_id=$res1['goods_id'];
        $num=$res1['goods_num'];
        $goods_id=explode(',',$goods_id);
        $num=explode(',',$num);
        //print_r($goods_id);exit;
        foreach($goods_id as $k=>$v){
            //echo $v;
            $res2=GoodsModel::where(['goods_id'=>$v])->first()->toArray();
            foreach ($num as $val){
                //echo $val;
            }
            $store=$res2['store']-$val;
            if($store<=0){
                exit('库存不足');
            }
            $data=[
                'store'=>$store
            ];
            $res3=GoodsModel::where(['goods_id'=>$v])->update($data);
            //print_r($data);
        }
        if($res && $res3){
            header("Refresh:2;url=/order/list2/$order_id");
            echo "支付成功";
        }else{
            header("Refresh:2;url=/order/list2/$order_id");
            echo "支付失败";
        }

    }
    public function off($order_id){
        if(empty($order_id)){
            die('订单不存在');
        }
        $data=[
            'pay_time'=>time(),
            'is_delete'=>2,
        ];
        $res=OrderModel::where(['order_id'=>$order_id])->update($data);
        if($res){
            header("Refresh:2;url=/order/list2/$order_id");
            echo "取消订单成功";
        }else{
            header("Refresh:2;url=/order/list2/$order_id");
            echo "取消订单失败";
        }
    }
    public function refund($order_id){
        if(empty($order_id)){
            die('订单不存在');
        }
        $data=[
            'is_pay'=>3,
            'pay_time'=>time(),
            'is_delete'=>2
        ];
        $where=[
            'order_id'=>$order_id
        ];
        $res=OrderModel::where($where)->update($data);
        //修改库存
        $res1=OrderModel::where($where)->first();
        $goods_id=$res1['goods_id'];
        $num=$res1['goods_num'];
        $goods_id=explode(',',$goods_id);
        $num=explode(',',$num);
        //print_r($goods_id);exit;
        foreach($goods_id as $k=>$v){
            //echo $v;
            $res2=GoodsModel::where(['goods_id'=>$v])->first()->toArray();
            foreach ($num as $val){
                //echo $val;
            }
            $store=$res2['store']+$val;
            $data=[
                'store'=>$store
            ];
            $res3=GoodsModel::where(['goods_id'=>$v])->update($data);
            //print_r($data);
        }
        if($res && $res3){
            header("Refresh:2;url=/order/list2/$order_id");
            echo "退款成功";
        }else{
            header("Refresh:2;url=/order/list2/$order_id");
            echo "退款失败";
        }
    }
}
