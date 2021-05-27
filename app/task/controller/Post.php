<?php
namespace app\task\controller;
use app\BaseController;
use think\facade\Db;

class Post extends BaseController{
    public function index(){
        $post=Db::name("article")->orderRaw("rand()")->limit(1)->select()->toArray();

        if(!count($post)){
            echo '数据不存在';
            exit;
        }

        echo $post[0]['article_title'];
        echo '<br><br>';
        echo $post[0]['article_body'];
    }
}