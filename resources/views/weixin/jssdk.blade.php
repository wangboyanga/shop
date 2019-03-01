@extends("layout.bst")
@section("content")
    <div class="container">
        <h2>JS-SDK</h2>
        <button id="btn1">选择照片</button>
        <button id="btn2">地理位置</button>
    </div>
@endsection
@section("footer")
    @parent
    <script src="{{URL::asset('/js/weixin/chat.js')}}"></script>
    <script src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js "></script>
    <script>
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: "{{$appid}}", // 必填，公众号的唯一标识
            timestamp:"{{$time}}" , // 必填，生成签名的时间戳
            nonceStr: "{{$noncestr}}", // 必填，生成签名的随机串
            signature: "{{$sign}}",// 必填，签名
            jsApiList: ['checkJsApi','uploadImage','chooseImage','getLocalImgData','startRecord','getLocation'] // 必填，需要使用的JS接口列表
        });
        wx.ready(function(){


            $("#btn1").click(function () {
                wx.chooseImage({
                    count: 9, // 默认9
                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    }
                });
            });
            $('.btn2').click(function () {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                    }
                });
            })

        });
    </script>
@endsection