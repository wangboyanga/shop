<?php

namespace App\Admin\Controllers;

use App\Model\WeixinPmedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Http\Request;

class WeixinPmediaController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinPmedia);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->add_time('Add time');
        $grid->msg_type('Msg type');
        $grid->media_id('Media id');
        $grid->format('Format');
        $grid->msg_id('Msg id');
        $grid->local_file_name('Local file name');
        $grid->local_file_path('Local file path');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinPmedia::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->msg_type('Msg type');
        $show->media_id('Media id');
        $show->format('Format');
        $show->msg_id('Msg id');
        $show->local_file_name('Local file name');
        $show->local_file_path('Local file path');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinPmedia);

        $form->file('media');

        return $form;
    }

    public function formTest(Request $request){
        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';
        //echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';
        //接收文件
        $img_file=$request->file('media');
        //print_r($img_file);exit;
        //文件名
        $img_origin_name=$img_file->getClientOriginalName();
        //print_r($img_origin_name);echo "</pre>";
        //文件类型
        $file_ext=$img_file->getClientOriginalExtension();
        //print_r($file_ext);echo "</pre>";
        //重命名
        $new_file_name=str_random(15).'.'.$file_ext;
        //echo $new_file_name;
        //保存文件

        //保存的路径
        $save_file_path=$request->media->storeAs('form_test',$new_file_name);//值为保存的路径
        //echo $save_file_path;
        //上传至微信永久素材
        $this->upMaterialTest($save_file_path);

    }
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    public function upMaterialTest($file_path){
        $url='https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client=new GuzzleHttp\Client();
        $response=$client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);
        $body=$response->getBody();
        echo $body;echo "</br>";
        $d=json_decode($body,true);
        echo '<pre>';print_r($d);echo "</pre>";
        $data=[
            'media_id'=>$d['media_id'],
            'add_time'=>time(),
            'local_file_path'=>$d['url']
        ];
        $re=WeixinPmedia::insertGetId($data);
        //echo $re;
        if($re){
            echo '添加成功';
        }else{
            echo "添加失败";
        }
    }

    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }
}
