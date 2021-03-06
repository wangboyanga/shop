var openid = $("#openid").val();

setInterval(function(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/weixin/get_msg?openid=' + openid + '&pos=' + $("#msg_pos").val(),
        type    :   'get',
        dataType:   'json',
        success :   function(d){
            if(d.errno==0){     //服务器响应正常
                //数据填充
                if(d.data.msg_type==2){
                    var msg_str = '<blockquote>'+ '客服'+
                        '<p>' + d.data.msg + '</p>' +
                        '</blockquote>';
                    $("#chat_div").append(msg_str);
                    $("#msg_pos").val(d.data.id)
                }else{
                    var msg_str = '<blockquote>用户： ' + d.data.openid +
                        '<p>' + d.data.msg + '</p>' +
                        '</blockquote>';
                    $("#chat_div").append(msg_str);
                    $("#msg_pos").val(d.data.id)
                }

            }else{

            }
        }
    });
},5000);

// 客服发送消息 begin
$("#send_msg_btn").click(function(e){
    e.preventDefault();
    var openid = $("#openid").val();
    var send_msg = $("#send_msg").val().trim();
    var msg_str = '<p style="color: mediumorchid"> >>>>> '+send_msg+'</p>';
    $("#chat_div").append(msg_str);
    $("#send_msg").val("");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/weixin/send',
        type    :   'post',
        data    :   {send_msg:send_msg,openid:openid},
        dataType:   'json',
        success :   function(d){
            console.log(d);
        }
    });
});
// 客服发送消息 end