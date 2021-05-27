<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;

class Types extends BaseController{
	use \liliuwei\think\Jump;
    public function index(){
        // View::layout(false);
    	if(Request::isPost(true)){
    		$type=Db::name("keys_type")->where(['keyst_site_id'=>session("site_id")]);
            
            return [
                "code"=>0,
                "msg"=>"获取成功",
                "count"=>$type->count(),
                "data"=>$type->select()->toArray(),
            ];
    	}else{
    		return View::fetch();
    	}
    }
    
    public function add(){
        if(Request::isPost(true)){
    		$data['keyst_title']=input("post.keyst_title");
            $data['keyst_list_required']=input("post.keyst_list_required");
            $data['keyst_list_filter']=input("post.keyst_list_filter");
            $data['keyst_status']=(int)input("post.keyst_status");
            $data['keyst_site_id']=session("site_id");
            
            $res=Db::name("keys_type")->save($data);
            
            if($res){
    			return $this->success('添加成功','/types/');
    		}else{
    			return $this->error('添加失败');
    		}
            
    	}else{
    		return View::fetch();
    	}
    }
    
    //修改
    public function edit(){
        $type_id=input("get.id/d");
        $type=Db::name("keys_type")->where(['keyst_id'=>$type_id])->select()->toArray();
        
        if(!count($type)){
	        return $this->error('分类不存在');
	    }
	    $type=$type[0];
	 
	    
	    if(Request::isPost(true)){
            
            View::layout(false);

    		$list=Db::name("site")->select()->toArray();
            $data['keyst_title']=input("post.keyst_title");
            $data['keyst_list_required']=input("post.keyst_list_required");
            $data['keyst_list_filter']=input("post.keyst_list_filter");
            $data['keyst_status']=(int)input("post.keyst_status");

            $res=Db::name("keys_type")->where(['keyst_id'=>$type['keyst_id']])->update($data);
            
            if($res){
    			return $this->success('修改成功','/types/');
    		}else{
    			return $this->error('修改失败');
    		}

    	}else{
    	    
    	    View::assign('type',$type);

    		return View::fetch();
    	}
    }
}