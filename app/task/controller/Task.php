<?php
namespace app\task\controller;
use app\BaseController;
use app\task\controller\Article as cArticle;
use app\task\controller\Search as cSearch;
use app\task\controller\Keys as cKeys;

class Task  {
    public function index(){
        //随机生成数字以调用权重
        $rand=rand(1,99);

        //采集关键词
        if($rand<5){
            echo "采集关键词\n";
            $cKeys=new cKeys();
            $cKeys->index();
        }
        //执行搜索引擎
        elseif ($rand<12) {
            echo "采集搜索引擎\n";
            $cSearch=new cSearch();
            $cSearch->index();
        }
        //采集文章
        else{
            echo "采集文章\n";
        	$cArticle=new cArticle();
        	$cArticle->index();
        }

        echo "==============================\n";
    }
}