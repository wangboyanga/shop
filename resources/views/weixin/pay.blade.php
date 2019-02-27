<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="{{URL::asset('/js/goods/qrcode.js')}}"></script>
    <title>Document</title>
</head>
<body>
    <div id="qrcode" align="center"></div>
</body>
</html>
<script>
    (function() {
        // 设置参数方式
        var qrcode = new QRCode('qrcode', {
            text: 'your content',
            width: 256,
            height: 256,
            colorDark : '#000000',
            colorLight : '#ffffff',
            correctLevel : QRCode.CorrectLevel.H
        });

        // 使用 API
        qrcode.clear();
        qrcode.makeCode("{{$code_url}}");
    })()
    setInterval(function () {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:     '/weixin/pay/wxsuccess?order_id='+"{{$order_id}}",
            type:    'get',
            dataType: 'json',
            success:   function (d) {
                if(d.error == 0){
                    alert(d.msg);
                    location.href = '/order/list'
                }
            }
        });
    },5000)

</script>