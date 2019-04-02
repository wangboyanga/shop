<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    //echo 1;
    //phpinfo();
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
//Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>403]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');
Route::get('/test','User\UserController@test');

//用户注册
//Route::get('/user/reg','User\UserController@reg');
//Route::post('/user/reg','User\UserController@doReg');
//Route::get('/user/login','User\UserController@login');
//Route::post('/user/login','User\UserController@doLogin');
//Route::get('/user/center','User\UserController@center');
//Route::get('/logou','User\UserController@logou');

//模板引入静态文件
Route::get('/mvc/test1','Mvc\MvcController@test1');
Route::get('/mvc/bst','Mvc\MvcController@bst');


Route::get('/check_cookie','Mvc\MvcController@checkCookie')->middleware('check.cookie');
Route::get('/cart','Cart\CartController@index')->middleware('check.login.token');
Route::get('/cart/add/{goods_id}','Cart\CartController@add');
Route::post('/cart/add2','Cart\CartController@add2');
Route::get('/cart/del/{goods_id}','Cart\CartController@del');
Route::get('/cart/del2/{id}','Cart\CartController@del2');

//商品详情
Route::get('/goods/list/{goods_id}','Goods\GoodsController@index')->middleware('check.login.token');
Route::get('/goods/list','Goods\GoodsController@list')->middleware('check.login.token');

Route::get('/order/add','Order\OrderController@add')->middleware('check.login.token');
Route::get('/order/list','Order\OrderController@list')->middleware('check.login.token');
Route::get('/order/refund/{order_id}','Order\OrderController@refund')->middleware('check.login.token');
Route::get('/order/list2/{order_id}','Order\OrderController@list2')->middleware('check.login.token');
Route::get('/order/pay/{order_id}','Order\OrderController@pay')->middleware('check.login.token');
Route::get('/order/off/{order_id}','Order\OrderController@off')->middleware('check.login.token');
Route::get('/order/wby','Order\OrderController@wby')->middleware('check.login.token');
Route::get('/order/alipay/test','Order\AlipayController@test')->middleware('check.login.token');


Route::get('/pay/o/{order_id}','Order\AlipayController@pay')->middleware('check.login.token');//订单支付
Route::post('/pay/alipay/notify','Order\AlipayController@aliNotify')->middleware('check.login.token');        //支付宝支付 异步通知回调
Route::get('/pay/alipay/return','Order\AlipayController@aliReturn')->middleware('check.login.token');        //支付宝支付 同步通知回调
Route::get('/pay/delete','Order\AlipayController@deleteOrder')->middleware('check.login.token');        //支付宝支付 同步通知回调



Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

//购票
Route::get('/movie/seat','Movie\IndexController@index');



//微信
Route::get('/weixin/refresh_token','Weixin\WeixinController@refreshToken');     //刷新token

Route::get('/weixin/test','Weixin\WeixinController@test');
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/group','Weixin\WeixinController@textGroup');

Route::get('/form/show','Weixin\WeixinController@formShow');     //表单测试
Route::post('/form/test','Weixin\WeixinController@formTest');     //表单测试

Route::get('/weixin/material/list','Weixin\WeixinController@materialList');     //获取永久素材列表
Route::get('/weixin/material/upload','Weixin\WeixinController@upMaterial');     //上传永久素材
Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu');     //创建菜单





//微信聊天
Route::get('/weixin/private','Weixin\WeixinController@formPrivate');     //私聊
Route::get('/weixin/get_msg','Weixin\WeixinController@privMsg');     //获取用户聊天信息
Route::post('/weixin/send','Weixin\WeixinController@send');     //客服发给用户


//微信支付
Route::get('/weixin/pay/test/{order_id}','Weixin\PayController@test');     //微信支付测试
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调
Route::get('/weixin/pay/wxsuccess','Weixin\PayController@WxSuccess');     //微信支付通知回调

//微信登陆
Route::get('/weixin/login','Weixin\WeixinController@login');   //微信登陆
Route::get('/weixin/getcode','Weixin\WeixinController@getCode');   //微信登陆



//微信jssdk
Route::get('/weixin/jssdk','Weixin\WeixinController@jssdkTest');   //jssdk
Route::get('/weixin/token','Weixin\WeixinController@token');   //jssdk


//api测试
Route::get('/api/test1','Api\ApiController@test1');   //jssdk
Route::post('/api/post','Api\ApiController@post');   //jssdk
//app
Route::post('/api/applogin','Api\ApiController@appLogin');   //jssdk
Route::post('/api/appregister','Api\ApiController@appRegister');   //jssdk
Route::post('/api/center','Api\ApiController@center');   //jssdk



Route::get('/pc/login','User\LoginController@login');   //
Route::post('/pc/logins','User\LoginController@logins');   //
Route::get('/pc/admin','User\LoginController@adminList');   //
Route::post('/api/applogin','Api\AppController@login');   //




