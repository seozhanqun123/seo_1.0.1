<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\common\model\Article as mArticle;

class Index extends BaseController{
    public function index(){
        
        //首页列表
        $index_list=Db::name("article")
            ->cache('index_list_'.$GLOBALS['site']['site_id'],1800,'index')
            ->where([
                'article_site_id'=>$GLOBALS['site']['site_id'],
                'article_status'=>1,
            ])
            ->order("article_times desc")
            ->field(['article_id','article_title','article_des'])
            ->limit(15)->select()->toArray();
        
        $mArticle=new mArticle();
        $article_rand_1=$mArticle->article_rand();
        $article_rand_2=$mArticle->article_rand();
        $article_rand_3=$mArticle->article_rand();
        
        View::assign('index_list',$index_list);
        View::assign('article_rand_1',$article_rand_1);
        View::assign('article_rand_2',$article_rand_2);
        View::assign('article_rand_3',$article_rand_3);
        
        return View::fetch('index@'.$GLOBALS['temp'].'/index/index');
    }
}