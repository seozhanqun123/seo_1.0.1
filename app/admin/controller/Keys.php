<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;

class Keys extends BaseController{
	use \liliuwei\think\Jump;
    public function index(){
    	if(Request::isPost(true)){
    	    $pages=input("post.page/d",1);
    		$keys=Db::name("keys_list")->alias('kl')->where(['keys_site_id'=>session("site_id")])->join(['keys_type'=>'kt'],'kt.keyst_id=kl.keys_type_id');

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
                })->order("keys_id desc")->select()->toArray(),
            ];
    	}else{
    		return View::fetch();
    	}
    }
    
    public function add(){
        if(Request::isPost(true)){
            View::layout(false);
            
    	    $keys=input("post.keys_name");
    	    $type_id=input("get.type_id");
    	    
    	    if(!$type_id){
    	        echo '上级分类错误';
    	        exit;
    	    }
    	    
    	    $res=Db::name("keys_list")->save([
                'keys_name'=>$keys,
                'keys_site_id'=>session("site_id"),
                'keys_top_id'=>0,
                'keys_type_id'=>$type_id,
                'keys_last_times'=>0,
                'keys_type'=>1,
                'keys_length'=>mb_strlen($keys),
            ]);
            
            if($res){
    			return $this->success('修改成功','/site/');
    		}else{
    			return $this->error('修改失败');
    		}
            
    	}else{
    		return View::fetch();
    	}
    }
}