<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\common\model\Article as mArticle;
use think\paginator\driver\Bootstrap;

class Types extends BaseController{
    use \liliuwei\think\Jump;
    public function index($id){
        $id=(int)$id;
        if(!$id){
            return $this->error('内容不存在');
        }
        $page=(int)input("get.page/d");
        if($page<1){
            $page=1;
        }
        
        $type=Db::name("keys_type")
            ->cache('type_'.$id,86400,'type')
            ->where(['keyst_id'=>$id,'keyst_site_id'=>$GLOBALS['site']['site_id']])
            ->field(['keyst_title','keyst_id'])
            ->limit(1)->select()->toArray();
        if(!count($type)){
            return $this->error('内容不存在');
        }
        $type=$type[0];
        
        $page_limit=($page-1)*10;
        $sql="select article_id,article_title,article_des from article inner join (select article_id from article where (article_site_id = {$GLOBALS['site']['site_id']} and article_type_id={$id} and article_status=1) limit {$page_limit},10) b using (article_id)";
        
        $list = Db::query($sql);

        //总计多少页 分页
        $sql_count="select count(*) as count_num from article where article_site_id = {$GLOBALS['site']['site_id']} and article_type_id={$id} and article_status=1";
        $counts = Db::query($sql_count)[0]['count_num'];
        $type_list = Bootstrap::make($sql,10,10,$counts,false,['path'=>'']);
        $page = $type_list->render();

        $mArticle=new mArticle();
        $article_rand_1=$mArticle->article_rand();
        $article_rand_2=$mArticle->article_rand();
        $article_rand_3=$mArticle->article_rand();
        
        View::assign('type',$type);
        View::assign('type_list',$list);
        View::assign('article_rand_1',$article_rand_1);
        View::assign('article_rand_2',$article_rand_2);
        View::assign('article_rand_3',$article_rand_3);
        
        View::assign('page',$page);

        return View::fetch('index@'.$GLOBALS['temp'].'/types/index');
    }
    
    
}