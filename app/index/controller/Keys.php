<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\common\model\Article as mArticle;

class Keys extends BaseController{
    use \liliuwei\think\Jump;
    public function index($id){
        $id=(int)$id;
        if(!$id){
            return $this->error('标签不存在');
        }

        $keys=Db::name("keys_list")
            ->alias('a')
            ->where([
                'keys_id'=>$id,
                'keys_site_id'=>$GLOBALS['site']['site_id']
            ])
            ->limit(1)->select()->toArray();
        if(!count($keys)){
            return $this->error('标签不存在');
        }
        $keys=$keys[0];
        
        $article_list=Db::name("article")->where([
            // 'article_site_id'=>$GLOBALS['site']['site_id'],
            'article_keys_id'=>$keys['keys_id'],
        ])->field(['article_id','article_title','article_des'])->limit(10)->select()->toArray();
 
        $mArticle=new mArticle();
        $article_rand_1=$mArticle->article_rand();
        $article_rand_2=$mArticle->article_rand();
        $article_rand_3=$mArticle->article_rand();

        View::assign('keys',$keys);
        View::assign('article_list',$article_list);

        View::assign('article_rand_1',$article_rand_1);
        View::assign('article_rand_2',$article_rand_2);
        View::assign('article_rand_3',$article_rand_3);

        return View::fetch('index@'.$GLOBALS['temp'].'/keys/index');
    }
    
    
}