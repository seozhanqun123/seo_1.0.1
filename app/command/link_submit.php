<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\task\controller\Seo as cSeo;
use think\facade\Db;

class link_submit extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\link_submit')
            ->setDescription('the app\command\link_submit command');
    }

    protected function execute(Input $input, Output $output)
    {

        \Swoole\Timer::tick(3000, function () {
            $this->inits();
        });
        
        \Swoole\Event::wait();
    }
    
    public function inits(){
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
        $link_submit_link_id_res=Db::name("link_submit")->where([['ls_id','>',$link_submit_link_id]])->where(['ls_status'=>1])->order("ls_id asc")->limit(1)->select()->toArray();
        
        //切换网站
        if(!count($link_submit_link_id_res)){
            cache("link_submit_site",$link_submit_site_cache+1);
            cache("link_submit_link_id",0);
            echo "切换网站\n";
            return '';
        }
        $link_submit_link_id_res=$link_submit_link_id_res[0];
        
        //替换网址
        $url=str_replace('{domain}',$site['site_domain'],$link_submit_link_id_res['ls_link']);
        
        echo "发送链接：{$url}\n";

        cache("link_submit_link_id",$link_submit_link_id+1);
        
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET',$url,[
                'verify'=>false,
                'stream' => true,
                'read_timeout' => 3,
                'http_errors'=>false,
                'timeout'=>3,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
                ],
            ]);
            if($response->getStatusCode()==200){
                Db::name("link_submit")->where(['ls_id'=>$link_submit_link_id_res['ls_id']])->inc('ls_success')->update();
                echo "提交成功：200 【{$site['site_name']}】\n";
                return "";
            }
        } catch (\Exception $e) {
            Db::name("link_submit")->where(['ls_id'=>$link_submit_link_id_res['ls_id']])->inc('ls_error')->update();
        }
        echo "提交成功：400 【{$site['site_name']}\n";
        return '';
    }
}
