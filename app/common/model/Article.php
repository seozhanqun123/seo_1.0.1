<?php
namespace app\common\model;
use app\common\model\Model;
use think\facade\Cache;
use think\facade\Db;

class Article extends Model
{
	//表名称
	protected $name = 'article';
	//表的主键
	protected $pk = 'article_id';

	protected $type_list=[];


	//随机获取文章
    public function article_rand($number=12){
        $rand=rand(1,10000);
        $cache_name='article_rand_list_'.$rand.'_'.$GLOBALS['site']['site_id'];
        $cache=cache($cache_name);
        if($cache){
            return $cache;
        }else{
            $res=Db::query("SELECT t1.`article_id`,t1.`article_title` FROM `article` AS t1 JOIN (SELECT ROUND( RAND () * ((SELECT MAX(article_id) FROM `article` where article_site_id=".$GLOBALS['site']['site_id']." and article_status=1)-(SELECT MIN(article_id) FROM `article` where article_site_id=".$GLOBALS['site']['site_id']." and article_status=1))+(SELECT MIN(article_id) FROM `article` where article_site_id=".$GLOBALS['site']['site_id']." and article_status=1)) AS article_id) AS t2 WHERE t1.article_id >= t2.article_id and article_site_id=".$GLOBALS['site']['site_id']." and article_status=1 LIMIT 10");
            cache($cache_name,$res,86400*30);
            return $res;
        }
        
        return Db::query("SELECT t1.`article_id`,t1.`article_title` FROM `article` AS t1 JOIN (SELECT ROUND( RAND () * ((SELECT MAX(article_id) FROM `article`)-(SELECT MIN(article_id) FROM `article`))+(SELECT MIN(article_id) FROM `article`)) AS article_id) AS t2 WHERE t1.article_id >= t2.article_id and article_site_id=".$GLOBALS['site']['site_id']." and article_status=1 LIMIT 10");
        
        //以下内容做废
        return [];
        
        
        $sql=$this->where([
                'article_site_id'=>$GLOBALS['site']['site_id'],
                'article_status'=>1,
            ]);
        //获取本站有多少个文章 并缓存总数量
        $article_count=$sql->cache("article_count_".$GLOBALS['site']['site_id'],1800,'article_count')->count();
        $rand=rand(1,1000);
        return $this->cache('article_rand_list_'.$rand.'_'.$GLOBALS['site']['site_id'],86400*30,'article_rand_list')
            ->where([
                'article_site_id'=>$GLOBALS['site']['site_id'],
                'article_status'=>1,
            ])
            ->field(['article_id','article_title'])
            ->limit(rand(0,$article_count-$number),$number)->select()->toArray();
    }

}