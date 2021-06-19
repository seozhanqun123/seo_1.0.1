<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;
use think\facade\Db;

class Domain extends BaseController{
    use \liliuwei\think\Jump;
    public function index(){
        
        $domain=Db::name("domain_not")->where(['domain_reg'=>0])->limit(1)->select()->toArray();
        
        if(!count($domain)){
            echo '没有数据了';
            return '';
        }
        $domain=$domain[0];
        
        $doamin_curl=$this->curl(str_replace(".com",'',$domain['dont_host']));
        
        if($doamin_curl===false){
            echo 'URL解析出错';
            return '';
        }
        
        $r='';
        if($doamin_curl['status']==true){
            if($doamin_curl['available']==true){
                Db::name("domain_not")->where(['dnot_id'=>$domain['dnot_id']])->save(['domain_reg'=>1,'dont_length'=>strlen($domain['dont_host'])]);
                $r='未注册';
            }else{
                Db::name("domain_not")->where(['dnot_id'=>$domain['dnot_id']])->save(['domain_reg'=>4,'dont_length'=>strlen($domain['dont_host'])]);
                $r='已经注册';
            }
        }else{
            Db::name("domain_not")->where(['dnot_id'=>$domain['dnot_id']])->save(['domain_reg'=>7,'dont_length'=>strlen($domain['dont_host'])]);
            $r='查询失败 废弃';
        }
        
        echo "域名：{$domain['dont_host']} {$r}";
        
        echo '<script>location.href=location.href</script>';

    }
    
    public function curl($doamin){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST','http://www.qiuyumi.com/query/whois.com.php?t='.(md5(time().rand(1,99999))),[
            'verify'=>false,
            'stream' => true,
            'read_timeout' => 5,
            'http_errors'=>false,
            // 'proxy'  => [
            //     'http'=>'http://t12185869913167:defac8gd@tps174.kdlapi.com:15818',
            // ],
            'form_params'=>[
                'name'=>$doamin,
            ],
        ]);

        try {
            if($response->getStatusCode()==200){
                $html=trim($response->getBody());
                $json=json_decode($html,true);
                return $json;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}