@extends("layout.bst")
@section("content")
    <table border="1" class="table table-bordered">
        <thead>
        <tr>
            <td>订单号</td>
            <td>订单价格</td>
            <td>订单状态</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $k=>$v)
            <tr>
                <td><a href="/order/list2/{{$v['order_id']}}">{{$v['order_number']}}</a></td>
                <td><font color="red">￥{{$v['order_amount']/100}}</font></td>
                <td>
                    @if($v['is_pay']==1 && $v['is_delete']==1)
                        订单未支付
                    @elseif($v['is_pay']!=1)
                        订单已支付
                    @elseif($v['is_delete']!=1)
                        订单已取消
                    @endif
                </td>
                <td style="width:200px">
                    <a href="/order/list2/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 订单详情 </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section("footer")
    @parent
@endsection