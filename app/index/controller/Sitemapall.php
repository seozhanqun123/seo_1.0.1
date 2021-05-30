<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\Db;
use think\facade\Response;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

class Sitemapall extends BaseController{
    public function index(){
        $file=$_SERVER['DOCUMENT_ROOT'].'/sitemap_site_'.$GLOBALS['site']['site_id'].'.xml';
        
        //缓存地图
        $cache_name='sitemap_site_'.$GLOBALS['site']['site_id'];
        if(!cache($cache_name)){
            $this->created_sitemap();
            cache($cache_name,1,3600*4);
        }

        //判断文件是否存在
        if(!is_file($file)){
            $this->created_sitemap();
        }

        $myfile = fopen($file, "r") or die("");
        echo fread($myfile,filesize($file));

        fclose($myfile);
        exit;
    }
    
    //创建地图
    public function created_sitemap(){
        $file=$_SERVER['DOCUMENT_ROOT'].'/sitemap_site_'.$GLOBALS['site']['site_id'].'.xml';
        $sitemap = new Sitemap($file);

        $article_list=Db::name("article")
            ->where(['article_site_id'=>$GLOBALS['site']['site_id'],'article_status'=>1])
            ->limit(10000)
            ->field(['article_id','article_times'])
            ->order("article_times desc")->select()->toArray();
        
        foreach ($article_list as $value) {
            $url="http://".$GLOBALS['site']['site_domain'].'/article/'.$value['article_id'].'.html';
            $sitemap->addItem($url,$value['article_times']);
        }

        $sitemap->write();
    }
}