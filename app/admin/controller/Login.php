<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;

class Login extends BaseController{
	use \liliuwei\think\Jump;
    public function index(){
         View::layout(false);
    	if(Request::isPost(true)){
    		$user=input("post.user/s");
    		$pass=input("post.pass/s");
    		if($user=='seo' && $pass=="seo@admin"){
    			session("user",1);
    			return $this->success('登录成功','/');
    		}else{
    			return $this->error('帐号密码错误');
    		}
    	}else{
    		return View::fetch();
    	}
    }
}