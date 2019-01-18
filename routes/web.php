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
    //echo 1;
    return view('welcome');
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
Route::get('/logou','User\UserController@quit');

//模板引入静态文件
Route::get('/mvc/test1','Mvc\MvcController@test1');
Route::get('/mvc/bst','Mvc\MvcController@bst');


Route::get('/check_cookie','Mvc\MvcController@checkCookie')->middleware('check.cookie');
Route::get('/cart','Cart\CartController@index');
Route::get('/cart/add/{goods_id}','Cart\CartController@add');
Route::post('/cart/add2','Cart\CartController@add2');
Route::get('/cart/del/{goods_id}','Cart\CartController@del');
Route::get('/cart/del2/{id}','Cart\CartController@del2');

//商品详情
Route::get('/goods/list/{goods_id}','Goods\GoodsController@index');
Route::get('/goods/list','Goods\GoodsController@list');

Route::get('/order/add','Order\OrderController@add');
Route::get('/order/list','Order\OrderController@list');
Route::get('/order/refund/{order_id}','Order\OrderController@refund');
Route::get('/order/list2/{order_id}','Order\OrderController@list2');
Route::get('/order/pay/{order_id}','Order\OrderController@pay');
Route::get('/order/off/{order_id}','Order\OrderController@off');
Route::get('/order/wby','Order\OrderController@wby');
Route::get('/order/alipay/test','Order\AlipayController@test');


Route::get('/pay/o/{order_id}','Order\AlipayController@pay');//订单支付
Route::post('/pay/alipay/notify','Order\AlipayController@aliNotify');        //支付宝支付 异步通知回调
Route::get('/pay/alipay/return','Order\AlipayController@aliReturn');        //支付宝支付 同步通知回调
Route::get('/pay/delete','Order\AlipayController@deleteOrder');        //支付宝支付 同步通知回调



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
