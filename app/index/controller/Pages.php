<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use PullWord\PullWord;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use think\facade\Db;
use QL\QueryList;
use QL\Ext\AbsoluteUrl;
use QL\Ext\CurlMulti;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

class Pages extends BaseController{
    public function index(){
        $file=$_SERVER['DOCUMENT_ROOT'].'/sitemap_site_'.$GLOBALS['site']['site_id'].'.xml';
        //判断文件是否存在
        if(!is_file($file)){
            
        }
        
        $sitemap = new Sitemap($file);
        
        $sitemap->setMaxUrls(25000);
        
        $article_list=Db::name("article")
            ->where(['article_site_id'=>$GLOBALS['site']['site_id'],'article_status'=>1])
            ->limit(10)
            ->field(['article_id','article_times'])
            ->order("article_times desc")->select()->toArray();
        
        foreach ($article_list as $value) {
            $url="http://".$GLOBALS['site']['site_domain'].'/article/'.$value['article_id'].'.html';
            $sitemap->addItem($url,$value['article_times']);
        }

        $sitemap->write();

        exit;
        
        $keys=Db::name("keys_list")->where('keys_article_count=-1')->limit(300)->select()->toArray();
        
        if(!count($keys)){
            echo '没有数据了';
            exit;
        }

        foreach ($keys as $value) {
            $article_count=Db::name("article")->where(['article_keys_id'=>$value['keys_id']])->count();
            echo "当前关键词：{$value['keys_name']}  统计数据：{$article_count}<br>";
            Db::name("keys_list")->where(['keys_id'=>$value['keys_id']])->save([
                'keys_article_count'=>$article_count
            ]);
        }
        
        echo '<script>location.href=location.href</script>';

        return '';
        
        $post=Db::name("article")->where(['article_id'=>22737])->limit(1)->select()->toArray();
        
        if(!count($post)){
            echo '没有数据了';
            exit;
        }
        
        $count=Db::name("article")->where(['article_status'=>0])->count();
        
        echo "还有数据：{$count}<br>";

        foreach ($post as $value) {
            if(strlen($value['article_body'])>=100000){
                Db::name("article")->where(['article_id'=>$value['article_id']])->delete();
                echo "内容太长 直接删除<br>";
            }else{
                //判断标题是否带*号
                $er=strstr($value['article_title'], '*');
                if(!!$er){
                    Db::name("article")->where(['article_id'=>$value['article_id']])->delete();
                    echo "标题带*号 直接删除<br>";
                }else{
                    //如果P标签少于10 则删除
                    preg_match_all('/<p[^\>]*|<br>/ism',$value['article_body'],$match);
                    $t=count($match[0]);
                    $rt=mb_strlen(strip_tags($value['article_body']));

                    if($t<=5 || ($rt/$t)<25){
                        Db::name("article")->where(['article_id'=>$value['article_id']])->delete();
                        echo "【".(int)($t==0 ? '0' : $rt/$t)."】<a href='/article/{$value['article_id']}.html' target='_blank'>文章内容太混乱 直接删除</a><br>";
                    }else{
                        echo "【".(int)($t==0 ? '0' : $rt/$t).'】通过：'.$value['article_title'];
                    
                        Db::name("article")->where(['article_id'=>$value['article_id']])->save(['article_status'=>1]);
                        
                        echo '<br>';
                    }
                }
            }

        }
        
        //内容太长 直接删除

        // echo '<script>location.href=location.href</script>';
        
        exit;

        // $post=Db::name("article")->where(['article_id'=>24076])->limit(1)->select()->toArray();
        
        $post=Db::name("article")->where(['article_status'=>1,'article_site_id'=>1])->limit(rand(1,9999),1)->select()->toArray();
        
        // var_dump($post);
        
        echo "<a href='/article/{$post[0]['article_id']}' target='_blank'>{$post[0]['article_title']}</a>";
        
        var_dump($post[0]['article_title']);
        
        // var_dump($post);
  
        preg_match_all('/<p[^\>]*|<br>/ism',$post[0]['article_body'],$match);
        
        $t=count($match[0]);
        
        echo "总计标签：".$t."\n";
        
        // var_dump(count($match[0]));
        
        $rt=mb_strlen(strip_tags($post[0]['article_body']));
        
        echo "文字总数：".$rt."\n";
        
        echo "比例：".($rt/$t)<35;
        
        exit;

        //开始提交外链
        $link_submit_site_cache=(int)cache("link_submit_site");
        
        //查询站点是否存在
        $site=Db::name("site")->where(['site_id'=>$link_submit_site_cache,'site_status'=>1])->limit(1)->select()->toArray();
        
        if(!count($site)){
            $site=Db::name("site")->where(['site_status'=>1])->order("site_id asc")->limit(1)->select()->toArray();
        }
        $site=$site[0];
        
        cache("link_submit_site",$site['site_id']);
        
        $link_submit_link_id=(int)cache("link_submit_link_id");
        $link_submit_link_id_res=Db::name("link_submit")->where([['ls_id','>',$link_submit_link_id]])->order("ls_id asc")->limit(1)->select()->toArray();
        
        //切换网站
        if(!count($link_submit_link_id_res)){
            cache("link_submit_site",$link_submit_site_cache+1);
            cache("link_submit_link_id",0);
            return "切换网站\n";
        }
        $link_submit_link_id_res=$link_submit_link_id_res[0];
        
        //替换网址
        $url=str_replace('{domain}',$site['site_domain'],$link_submit_link_id_res['ls_link']);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$url,[
            'verify'=>false,
            'stream' => true,
            'read_timeout' => 3,
            'http_errors'=>false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
            ],
        ]);
        
        echo "提交链接：{$url}\n";
        
        try {
            if($response->getStatusCode()==200){
                // return '提交成功：200';
                Db::name("link_submit")->where(['ls_id'=>$link_submit_link_id_res['ls_id']])->inc('ls_success')->update();
            }
        } catch (Exception $e) {
            Db::name("link_submit")->where(['ls_id'=>$link_submit_link_id_res['ls_id']])->inc('ls_error')->update();
        }
        
        cache("link_submit_link_id",$link_submit_link_id+1);
        
        return '提交成功';
        
        // return '提交成功：400';

        exit;
        $link_submit_count_cache=(int)cache("link_submit")+1;
        

        //外链批量提交
        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('POST','http://www.cjzzc.com/wailian.html?page=1',[
        //     'verify'=>false,
        //     'stream' => true,
        //     'read_timeout' => 3,
        //     'http_errors'=>false,
        //     'headers' => [
        //         'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
        //     ],
        //     'form_params'=>[
        //         'url'=>'aidafo.com',
        //         'token_cjzzc'=>'ea2789b0375fda72a33d036ddc968b81',
        //         'page'=>100,
        //     ]
        // ]);
        
        // $html=trim($response->getBody());
        
        // var_dump($html);
        
        // exit;
        
        $ql = QueryList::post('http://www.cjzzc.com/wailian.html?page='.$link_submit_count_cache, [
                    'url'=>'aidafo.com',
                    'token_cjzzc'=>'ea2789b0375fda72a33d036ddc968b81',
                    'page'=>$link_submit_count_cache*10,
                ], [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
                ],
                'timeout'=>5,
                
            ])->rules(['title'=>array('#list-table a','texts'),]);

        $data=$ql->queryData();

        $n=0;
        foreach ($data['title'] as $value) {
            $value=htmlspecialchars_decode(str_replace('aidafo.com','{domain}',$value));
            $host=parse_url($value)['host'];
            $ls_count=Db::name("link_submit")->where(['ls_link'=>$value])->count();

            var_dump($ls_count);

            if(!($ls_count)){
                $res=Db::name("link_submit")->save([
                    'ls_domain'=>$host,
                    'ls_link'=>$value
                ]);
                if($res){
                    $n+=1;
                }
            }
        }

        cache("link_submit",$link_submit_count_cache);
        
        echo "当前第{$link_submit_count_cache}页\n";
        
        $count=Db::name("link_submit")->count();
        
        echo "当前总计有数据：{$count}\n";
        
        echo "成功添加：{$n}";
        
        echo "<script>location.href=location.href</script>";

        exit;
        
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