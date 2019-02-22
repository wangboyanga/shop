<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/goods',GoodsController::class);
    $router->resource('/goods2',Goods2Controller::class);
    $router->resource('/wx/wxuser',WeixinController::class);
    $router->resource('/wx/wxmedia',WeixinMediaController::class);
    $router->resource('/wx/group',WeixinGroup::class);
    $router->post('/wx/group', 'WeixinGroup@textGroup');

    //上传永久素材
    $router->resource('/wx/pmedia',WeixinPmediaController::class);
    $router->post('/wx/pmedia', 'WeixinPmediaController@formTest');
});
