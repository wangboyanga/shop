@extends("layout.bst")
@section("content")
    <table border="1" class="table table-bordered" style="width:500px">
        @foreach($list as $v)
            <tr>
                <td>商品名称</td>
                <td>{{$v['goods_name']}}</td>
            </tr>
            <tr>
                <td>购买数量</td>
                <td>{{$v['num']}}</td>
            </tr>
        @endforeach
        @foreach($data as $k=>$v)
        <tr>
            <td>订单号</td>
            <td>{{$v['order_number']}}</td>
        </tr>
        <tr>
            <td>订单价格</td>
            <td><font color="red">￥{{$v['order_amount']/100}}</font></td>
        </tr>
        {{--<tr>--}}
            {{--<td>支付价格</td>--}}
            {{--<td>--}}
                {{--@if($v['pay_amount']=='')--}}
                {{--@else--}}
                {{--<font color="red">￥{{$v['pay_amount']/100}}</font>--}}
                {{--@endif--}}
            {{--</td>--}}
        {{--</tr>--}}
        <tr>
            <td>支付时间</td>
            <td>
                @if($v['pay_time']=='')
                @else
                    {{date('Y-m-d H-i-s',$v['pay_time'])}}</font>
                @endif
            </td>
        </tr>
        <tr>
            <td>下单时间</td>
            <td>{{date('Y-m-d H-i-s',$v['add_time'])}}</td>
        </tr>
        <tr>
            <td>订单状态</td>
            <td>
                @if($v['is_pay']==1 && $v['is_delete']==1)
                    订单未支付
                @elseif($v['is_pay']==2)
                    订单已支付
                @elseif($v['is_pay']==3)
                    已退款 订单已取消
                @elseif($v['is_delete']==1)
                    订单已生成
                @elseif($v['is_delete']==2)
                    订单已取消
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2">
                @if($v['is_pay']==1 && $v['is_delete']==1)
                    <a href="/order/pay/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 去支付 </a>
                    <a href="/order/off/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 取消订单 </a>
                @elseif($v['is_pay']==2)
                    <a href="/order/refund/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 去退款 </a>
                @elseif($v['is_delete']==1 && $v['is_pay']==1)
                    <a href="/order/off/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 取消订单 </a>
                @endif
                    <a href="/order/list" id="submit_order" class="btn btn-info ">返回订单页</a>
            </td>
        </tr>
        @endforeach

    </table>
@endsection

@section("footer")
    @parent
@endsection