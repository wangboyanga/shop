<?php
    namespace App\Http\Controllers\Cart;

    use App\Model\GoodsModel;
    use App\Model\CartModel;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    class CartController extends Controller{
        public $uid;
        public function __construct()
        {
            $this->middleware(function($request,$next){
                $this->uid= session()->get('uid');
                return $next($request);
            });
        }

        public function index(Request $request){
//            $goods = session()->get('cart_goods');
//            if(empty($goods)){
//                echo "购物车为空";
//            }else{
//                foreach($goods as $k=>$v){
//                    echo "Goods Id".$v;echo "</br>";
//                    $detail = GoodsModel::where(['goods_id'=>$v])->first()->toArray();
//                    echo '<pre>';print_r($detail);echo '</pre>';
//                }
//            }
            $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();
            //print_r($cart_goods);
            if(empty($cart_goods)){
                header('refresh:2;url=/goods/list/1');
                die("购物车是空的");
            }
            if($cart_goods){
                //获取商品最新信息
                foreach($cart_goods as $k=>$v){
                    $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                    $goods_info['num']  = $v['num'];
                    $goods_info['id']=$v['id'];
                    $goods_info['add_time']=$v['add_time'];
                    //echo '<pre>';print_r($goods_info);echo '</pre>';
                    $list[] = $goods_info;
                }
            }
            //print_r($list);
            $data = [
                'list'  => $list
            ];
            //print_r($data);
            return view('cart.cart',$data);
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
            if ($store_num <= $num || $store_num<=0) {
                $response = [
                    'errno' => 5001,
                    'msg' => '库存不足'
                ];
                return $response;
            }
            //var_dump($store_num);
            $data =[
                'goods_id'=>$goods_id,
                'num'=>$num,
                'add_time'=>time(),
                'uid'=>$this->uid,
                'session_token'=>session()->get('u_token')
            ];
            //var_dump($data);exit;
            $where=[
                'goods_id'=>$goods_id,
                'uid'=>$this->uid
            ];
            $res=CartModel::where($where)->first();
            if(!empty($res)){
                if($res['num']+$num>=$store_num){
                    $response = [
                        'errno' => 5001,
                        'msg' => '库存不足'
                    ];
                    return $response;
                }else{
                    $updateWhere=[
                        'num'=>$res['num']+$num,
                        'add_time'=>time()
                    ];
                    $res2=CartModel::where(['goods_id'=>$goods_id])->update($updateWhere);
                }

            }else{
                $res2 =CartModel::insertGetId($data);
            }
            //var_dump($cid);
            if(!$res2){
                $response = [
                    'errno' => 5002,
                    'msg'   => '添加购物车失败，请重试'
                ];
                return $response;
            }
            //echo $num;
            $response = [
                'error' => 0,
                'msg'   => '添加成功'
            ];
            //echo 1;
            return $response;
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
        public function del2($id){
            $res=CartModel::where(['id'=>$id])->delete();
            if($res){
                header('refresh:2;url=/cart');
                echo "删除成功";
            }else{
                header('refresh:2;url=/cart');
                echo "删除失败";
            }
        }
    }