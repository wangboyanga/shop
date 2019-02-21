<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->post('/wx/wxuser', 'WeixinGroup@textGroup');
    $router->resource('/goods',GoodsController::class);
    $router->resource('/goods2',Goods2Controller::class);
    $router->resource('/wx/wxuser',WeixinController::class);
    $router->resource('/wx/wxmedia',WeixinMediaController::class);
});
