<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\common\model\Article as mArticle;
use app\common\validate\Article as vArticle;

class Tags extends BaseController{
    use \liliuwei\think\Jump;
    public function index($name){
        $name=trim($name);
        if(!$name){
            return $this->error('内容不存在1');
        }
        $pages=(int)input("get.page/d");
        if($pages<1){
            $pages=1;
        }
        
        $vArticle = new vArticle();
        $validateResult = $vArticle->scene("tags")->check(['tags'  => $name]);

        if(!$validateResult){
            return $this->error('内容不存在2');
        }
        
        // if($name=='废铁'){
        //     $page_limit=($page-1)*10;
        //     $sql="select article_id,article_title,article_des from article inner join (select article_id from article where (article_site_id = {$GLOBALS['site']['site_id']} and article_status=1 and article_title like '{$name}%') limit {$page_limit},10) b using (article_id)";
            
        //     // $sql="select article_id,article_title,article_des from article where article_title like '%{$name}%' limit {$page_limit},10";
            
        //     $list=Db::query($sql);
            
        //     var_dump($list);
            
        //     // $type_list = Bootstrap::make($sql,10,10,$counts,false,['path'=>Bootstrap::getCurrentPath(),'query'=>request()->param()]);
        //     // $page = $type_list->render();
            
        //     return '';
        // }

        $article_list=Db::name("article")
            ->where([
                'article_site_id'=>$GLOBALS['site']['site_id'],
                'article_status'=>1,
            ])
            ->where([
                ['article_title','like',"".$name."%"],
            ])
            ->field(['article_id','article_title','article_des'])
            ->paginate(10);

        $page = $article_list->render();

        if($pages==1){
            $keys_rand_list=Db::query("SELECT t1.`keys_id`,t1.`keys_name` FROM `keys_list` AS t1 JOIN (SELECT ROUND( RAND () * ((SELECT MAX(keys_id) FROM `keys_list` where keys_site_id=".$GLOBALS['site']['site_id']." and keys_article_count>=10)-(SELECT MIN(keys_id) FROM `keys_list` where keys_site_id=".$GLOBALS['site']['site_id']." and keys_article_count>=10))+(SELECT MIN(keys_id) FROM `keys_list` where keys_site_id=".$GLOBALS['site']['site_id']." and keys_article_count>=10)) AS keys_id) AS t2 WHERE t1.keys_id >= t2.keys_id and keys_site_id=".$GLOBALS['site']['site_id']." and keys_article_count>=10 LIMIT 10");
        }else{
            $keys_rand_list=[];
        }
        
        View::assign('keys_rand_list',$keys_rand_list);
        
        
        $mArticle=new mArticle();
        $article_rand_1=$mArticle->article_rand();
        $article_rand_2=$mArticle->article_rand();
        $article_rand_3=$mArticle->article_rand();
        
        View::assign('article_list',$article_list);
        View::assign('tag_name',$name);
        View::assign('article_rand_1',$article_rand_1);
        View::assign('article_rand_2',$article_rand_2);
        View::assign('article_rand_3',$article_rand_3);
        
        View::assign('page',$page);

        return View::fetch('index@'.$GLOBALS['temp'].'/tags/index');
    }
    
    
}