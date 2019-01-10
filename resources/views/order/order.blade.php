@extends("layout.bst")
@section("content")
    <table border="1" class="table table-bordered">
        <thead>
        <tr>
            <td>订单号</td>
            <td>订单价格</td>
            <td>下单时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $k=>$v)
            <tr>
                <td>{{$v['order_number']}}</td>
                <td><font color="red">￥{{$v['order_amount']/100}}</font></td>
                <td>{{date('Y-m-d H-i-s',$v['add_time'])}}</td>
                <td style="width:200px">
                    <a href="/order/pay/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 支付 </a>
                    <a href="/order/off/{{$v['order_id']}}" id="submit_order" class="btn btn-info "> 取消订单 </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section("footer")
    @parent
@endsection