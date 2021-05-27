<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\common\model\Article as mArticle;

class Article extends BaseController{
    use \liliuwei\think\Jump;
    public function index($id){
        $id=(int)$id;
        if(!$id){
            return $this->error('文章不存在');
        }
        
        $article=Db::name("article")
            ->alias('a')
            ->where([
                'article_id'=>$id,
                'article_site_id'=>$GLOBALS['site']['site_id']
            ])
            ->join(['keys_type'=>'kt'],'kt.keyst_id=a.article_type_id')
            ->limit(1)->select()->toArray();
        if(!count($article)){
            return $this->error('文章不存在');
        }
        $article=$article[0];
        
        $mArticle=new mArticle();
        $article_rand_1=$mArticle->article_rand();
        $article_rand_2=$mArticle->article_rand();
        $article_rand_3=$mArticle->article_rand();

        View::assign('article_rand_1',$article_rand_1);
        View::assign('article_rand_2',$article_rand_2);
        View::assign('article_rand_3',$article_rand_3);
        
        //处理tags标签
        $article_tags=explode(",",$article['article_tags']);
        
        $article['article_tags_array']=$article_tags;

        View::assign('article',$article);
        
        return View::fetch('index@'.$GLOBALS['temp'].'/article/index');
    }
    
    
}