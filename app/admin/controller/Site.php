<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;

class Site extends BaseController{
	use \liliuwei\think\Jump;
	
	protected $template_list=[
        [
            'name'=>'默认',
            'value'=>'default',
        ]
    ];

    public function index(){
    	if(Request::isPost(true)){
    		$list=Db::name("site")->select()->toArray();
            
            return [
                "code"=>0,
                "msg"=>"获取成功",
                "count"=>Db::name("site")->count(),
                "data"=>$list,
            ];
            
    	}else{
    		return View::fetch();
    	}
    }

    public function add(){
    	if(Request::isPost(true)){
    		$list=Db::name("site")->select()->toArray();
            $data['site_name']=input("post.site_name");
            $data['site_title']=input("post.site_title");
            $data['site_des']=input("post.site_des");
            $data['site_keys']=input("post.site_keys");
            $data['site_template']=input("post.site_template");
            $data['site_domain']=input("post.site_domain");

            $res=Db::name("site")->save($data);
            View::layout(false);
            if($res){
    			return $this->success('添加成功','/site/');
    		}else{
    			return $this->error('添加失败');
    		}

    	}else{
    		return View::fetch();
    	}
    }
    
    public function edit(){
        $site_id=input("get.id/d");
        
        $site=Db::name("site")->where(['site_id'=>$site_id])->select()->toArray();
        
        if(!count($site)){
	        return $this->error('站点不存在');
	    }

        if(Request::isPost(true)){
            
            View::layout(false);
            
    		$list=Db::name("site")->select()->toArray();
            $data['site_name']=input("post.site_name");
            $data['site_title']=input("post.site_title");
            $data['site_des']=input("post.site_des");
            $data['site_keys']=input("post.site_keys");
            $data['site_template']=input("post.site_template");
            $data['site_domain']=input("post.site_domain");

            $res=Db::name("site")->where(['site_id'=>$site[0]['site_id']])->update($data);
            
            if($res){
    			return $this->success('修改成功','/site/');
    		}else{
    			return $this->error('修改失败');
    		}

    	}else{
    	    
    	    View::assign('template_list',$this->template_list);
    	    
    	    View::assign('site',$site[0]);
    		return View::fetch();
    	}
    }
    
    public function deletes(){
        // $site_id=input("post.site_id/d");
        // var_dump($site_id);
    }
    
    //
    public function selete_site(){
        $site_id=input("get.id/d");
        session("site_id",(int)$site_id);
        header("location:/");
    }
}