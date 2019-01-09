<?php
    namespace App\Http\Controllers\Cart;

    use App\Model\GoodsModel;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    class CartController extends Controller{
        public function index(Request $request){
            $goods = session()->get('cart_goods');
            if(empty($goods)){
                echo "购物车为空";
            }else{
                foreach($goods as $k=>$v){
                    echo "Goods Id".$v;echo "</br>";
                    $detail = GoodsModel::where(['goods_id'=>$v])->first()->toArray();
                    echo '<pre>';print_r($detail);echo '</pre>';
                }
            }
        }
        public function add($goods_id){
            $cart_goods = session()->get('cart_goods');
            //print_r($cart_goods);
            if(!empty($cart_goods)){
                if(in_array($goods_id,$cart_goods)){
                    header('refresh:2;url=/cart');
                    echo '已存在购物车中';exit;
                }
            }
            session()->push('cart_goods',$goods_id);
            $where=[
                'goods_id'=>$goods_id
            ];
            $store=GoodsModel::where($where)->value('store');
            if($store<=0){
                header('refresh:2;url=/cart');
                echo "库存不足";exit;
            }
            $res=GoodsModel::where(['goods_id'=>$goods_id])->decrement('store');
            if($res){
                header('refresh:2;url=/cart');
                echo "添加成功";
            }else{
                echo "添加失败";
            }
        }
        //ajax提交过来的
        public function add2(Request $request)
        {
            $goods_id = $request->input('goods_id');
            $num = $request->input('num');
            $store_num = GoodsModel::where(['goods_id' => $goods_id])->value('store');
            if ($store_num <= 0) {
                $response = [
                    'errno' => 5001,
                    'msg' => '库存不足'
                ];
                return $response;
            }
        }
        public function del($goods_id){
            $goods=session()->get('cart_goods');
            if(in_array($goods_id,$goods)){
                header('refresh:2;url=/cart');
                foreach($goods as $k=>$v){
                    if($goods_id == $v){
                        session()->pull('cart_goods.'.$k);
                    }
                }
            }else{
                header('refresh:2;url=/cart');
                echo "商品不在购物车中";exit;
            }
            //print_r($goods);
        }
    }