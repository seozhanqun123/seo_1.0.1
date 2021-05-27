<?php
namespace app\index\middleware;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Config;
use think\Response;

class Inits
{
    //用于后台管理
    //只有logo控制器才可以跳转 其它都不可以跳过
    //验证登录 并且验证 是否为管理员
    public function handle($request, \Closure $next)
    {
        //获取当前网站ID
        $domain=$_SERVER['HTTP_HOST'];
        $domain_array=explode(".",$domain);
        $domain=$domain_array[count($domain_array)-2].'.'.$domain_array[count($domain_array)-1];
        
        $site=Db::name("site")->where(['site_domain'=>$domain])->limit(1)->select()->toArray();

        if(!count($site)){
            $site=Db::name("site")->where(['site_domain'=>'aidafo.com'])->limit(1)->select()->toArray();
        }
        $site=$site[0];

        //站点
        $GLOBALS['site']=$site;
        //模板
        $GLOBALS['temp']=$site['site_template'] ? $site['site_template'] : 'default';
        //网站根网址
        $GLOBALS['host_url']=$_SERVER['REQUEST_SCHEME'].'://'.$domain;

        //配置缓存标签前辍
        // prefix
        $config_cache = config('cache');
        $config_cache['stores'][$config_cache['default']]['prefix'] = 'seo_site_'.$site['site_id'].'_';
        Config::set($config_cache,'cache');

        //头部导航链接
        $GLOBALS['header_link']=Db::name("keys_type")->cache("header_link_".$site['site_id'],86400)->where(['keyst_site_id'=>$site['site_id']])->limit(7)->select()->toArray();
        
        //搜索引擎蜘蛛
        $useragent = addslashes(strtolower($_SERVER['HTTP_USER_AGENT']));

        if (strpos($useragent, 'googlebot')!== false){
            $bot = 'Google';
        }elseif (strpos($useragent,'baiduspider') !== false){
            $bot = 'Baidu';
        }elseif (strpos($useragent,'sogou spider') !== false){
            $bot = 'Sogou';
        }elseif (strpos($useragent,'sosospider') !== false){
            $bot = 'SOSO';
        }elseif (strpos($useragent,'360spider') !== false){
            $bot = '360';
        }elseif (strpos($useragent,'yahoo') !== false){
            $bot = 'Yahoo';
        }elseif (strpos($useragent,'msn') !== false){
            $bot = 'MSN';
        }elseif (strpos($useragent,'sohu') !== false){
            $bot = 'Sohu';
        }elseif (strpos($useragent,'yodaoBot') !== false){
            $bot = 'Yodao';
        }else{
            $bot = '';
        }

        if($bot){
            $spider_data=[
                'spider_site_id'=>$site['site_id'],
                'spider_name'=>strtolower($bot),
                'spider_url'=>$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                'spider_times'=>time(),
                'spider_ip'=>request()->ip(),
            ];
            Db::name("spider")->save($spider_data);
        }

        $response = $next($request);
        return $response;
    }
}