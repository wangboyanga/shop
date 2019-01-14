@extends("layout.bst")
@section("content")
    <table border="1" class="table table-bordered">
        <thead>
        <tr>
            <td>商品id</td>
            <td>商品名称</td>
            <td>商品库存</td>
            <td>时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        @foreach($goods as $k=>$v)
            <tr>
                <td>{{$v['goods_id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['store']}}</td>
                <td>{{date('Y-m-d H-i-s',$v['add_time'])}}</td>
                <td style="width:200px">
                    <a href="/goods/list/{{$v['goods_id']}}" id="submit_order" class="btn btn-info "> 商品详情 </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section("footer")
    @parent
@endsection