<?php
namespace app\admin\middleware;
use think\facade\Cache;
use think\facade\Db;
use think\Response;

class Login
{
    //用于后台管理
    //只有logo控制器才可以跳转 其它都不可以跳过
    //验证登录 并且验证 是否为管理员
    public function handle($request, \Closure $next)
    {
        $json=[
            'msg'=>'登录超时，请重新登录',
            'status'=>700,
        ];
        
        $s=strpos($_SERVER['REQUEST_URI'],'/login');

        if($s===false){
            if(session("user")){
                $response = $next($request);
                
                if(!session("site_id")){
                    $site=Db::name("site")->limit(1)->select()->toArray();
                    if(count($site)){
                        session("site_id",$site[0]['site_id']);
                    }
                }
            }else{
                header("location:/login");
                exit;
            }
        }else{
            $response = $next($request);
        }
        return $response;
    }
}