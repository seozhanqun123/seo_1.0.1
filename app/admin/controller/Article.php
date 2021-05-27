<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;

class Article extends BaseController{
    public function index(){
    	if(Request::isPost(true)){
    		$pages=input("post.page/d",1);
    		$keys=Db::name("article")->alias('a')->where(['	article_site_id'=>session("site_id")])
    		    ->join(['keys_type'=>'kt'],'kt.keyst_id=a.article_type_id')
    		    ->join(['keys_list'=>'kl'],'a.article_keys_id=kl.keys_id');

            return [
                "code"=>0,
                "msg"=>"获取成功",
                "count"=>$keys->count(),
                "data"=>$keys->page($pages,20)->hidden(['keyst_list_required','keyst_list_filter'])->withAttr('keys_last_times', function($value, $data) {
                	if($value){
                	    return date("Y-m-d H:i:s",$value);
                	}else{
                	    return '';
                	}
                })->hidden(['article_body'])->select()->toArray(),
            ];
    	}else{
    		return View::fetch();
    	}
    }
}