<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use PullWord\PullWord;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use think\facade\Db;

class Pages extends BaseController{
    public function index(){
        
        // $a= Db::query("SELECT t1.`article_id`,t1.`article_title` FROM `article` AS t1 JOIN (SELECT ROUND( RAND () * ((SELECT MAX(article_id) FROM `article`)-(SELECT MIN(article_id) FROM `article`))+(SELECT MIN(article_id) FROM `article`)) AS article_id) AS t2 WHERE t1.article_id >= t2.article_id and article_site_id=2 and article_status=1 LIMIT 10");
        
        
        // var_dump($a);
        
        exit;
        
        //要访问的目标页面
        $page_url = "http://pv.sohu.com/cityjson?ie=utf-8";
        
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$page_url,[
            'verify'=>false,
            'stream' => true,
            'read_timeout' => 3,
            'http_errors'=>false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
            ],
            'proxy'  => [
                'http'=>'http://t12185869913167:defac8gd@tps174.kdlapi.com:15818',
            ],
        ]);
        
        try {
            if($response->getStatusCode()==200){
                $html=$response->getBody();
                return trim($html);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
        
        exit;
        
        
        
        $ch = curl_init();
        $tunnelhost = "tps174.kdlapi.com";
        $tunnelport = "15818";
        $proxy = $tunnelhost.":".$tunnelport;
        
        //隧道用户名密码
        $username   = "t12185869913167";
        $password   = "defac8gd";
        
        //$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $page_url);
        
        //发送post请求
        $requestData["post"] = "send post request";
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestData));
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        //设置代理
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        //设置代理用户名密码
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$username}:{$password}");
        
        //自定义header
        $headers = array();
        $headers["user-agent"] = 'User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0);';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        //自定义cookie
        curl_setopt($ch, CURLOPT_COOKIE,''); 
        
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); //使用gzip压缩传输数据让访问更快
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        echo "$result"; // 使用请求页面方式执行时，打印变量需要加引号
        
        var_dump($result);
        
        echo "\n\nfetch ".$info['url']."\ntimeuse: ".$info['total_time']."s\n\n";

        

        // global $g_site;
        // // var_dump($g_site);
        // return View::fetch();
    }
    
    
}