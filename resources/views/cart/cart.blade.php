@extends("layout.bst")
@section("content")
    <table border="1" class="table table-bordered">
        <thead>
            <tr>
                <td>商品名称</td>
                <td>商品数量</td>
                <td>时间</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
            @foreach($list as $k=>$v)
                <tr>
                    <td>{{$v['goods_name']}}</td>
                    <td>{{$v['num']}}</td>
                    <td>{{date('Y-m-d H-i-s',$v['add_time'])}}</td>
                    <td><a href="/cart/del2/{{$v['id']}}">删除</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-left:800px">
        <a href="/order/add" id="submit_order" class="btn btn-info "> 提交订单 </a>
        <a href="/order/list" id="submit_order" class="btn btn-info "> 前往查看订单 </a>
    </div>
@endsection

@section("footer")
    @parent
@endsection
