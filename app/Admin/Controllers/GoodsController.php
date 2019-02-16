<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use App\Model\GoodsModel;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class GoodsController extends Controller
{
    use HasResourceActions;


    public function index(Content $content)
    {
        //echo __METHOD__;
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    protected function grid(){
        $grid=new Grid(new GoodsModel());
        $grid->model()->orderBy('goods_id','desc');
        $grid->goods_id('商品id');
        $grid->goods_name('商品名称');
        $grid->store('库存');
        $grid->price('价格');
        $grid->created_at('添加时间');
        return $grid;
    }
    public function edit($id,Content $content){
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    public function create(Content $content){
        return $content
            ->header('商品管理')
            ->description('添加')
            ->body($this->form());
    }

//    public function update($id)
//    {
//        echo '<pre>';print_r($_POST);echo '</pre>';
//    }
//    public function store()
//    {
//        echo '<pre>';print_r($_POST);echo '</pre>';
//    }
//
//
//
//    public function show($id)
//    {
//        echo __METHOD__;echo '</br>';
//    }

    //删除
//    public function destroy($id)
//    {
//
//        $response = [
//            'status' => true,
//            'message'   => 'ok'
//        ];
//        return $response;
//    } v
    protected function form(){
        $form=new Form(new GoodsModel());
        $form->display('goods_id','商品id');
        $form->text('goods_name', '商品名称');
        $form->number('store', '库存');
        $form->currency('price', '价格')->symbol('¥');
        $form->ckeditor('content');
        return $form;
    }
}
