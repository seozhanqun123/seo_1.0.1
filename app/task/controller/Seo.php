<?php
namespace app\task\controller;
use app\BaseController;
use think\facade\Db;
use PullWord\PullWord;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class Seo{
    public function index(){
        $post=Db::name("article")->where(['article_status'=>0])
        ->orderRaw("rand()")
        ->limit(1)->select()->toArray();

        if(!count($post)){
            echo '没有数据了';
            sleep(15);
            return "\n";
        }
        
        $article=$post[0];
        
        //如果标题为空则删除
        if(strlen($article['article_title'])<2){
            Db::name("article")->where(['article_id'=>$article['article_id']])->delete();
            return "标题为空 直接删除\n";
        }
        //标题是否有中文 如果没有则删除
        if(!preg_match('/[\x7f-\xff]/',$article['article_title'])){
            Db::name("article")->where(['article_id'=>$article['article_id']])->delete();
            return "标题没有中文 直接删除\n";
        }
        //标题太长 直接删除
        if(mb_strlen($article['article_title'])>60){
            Db::name("article")->where(['article_id'=>$article['article_id']])->delete();
            return "标题太长 直接删除\n";
        }
        //内容太长 直接删除
        if(strlen($article['article_body'])>=100000){
            Db::name("article")->where(['article_id'=>$article['article_id']])->delete();
            return "内容太长 直接删除\n";
        }
        //如果P标签少于10 则删除
        preg_match_all('/<p[^\>]*|<br>/ism',$post[0]['article_body'],$match);
        $t=count($match[0]);
        $rt=mb_strlen(strip_tags($post[0]['article_body']));
        if($t<=5 || ($rt/$t)<40){
            Db::name("article")->where(['article_id'=>$article['article_id']])->delete();
            return "文章内容太混乱 直接删除\n";
        }

        //判断文章是否有违禁词
        
        //去除电话号码
        
        //开始分词
        $pullWord = new PullWord($article['article_title']);
        $result = $pullWord->pull()->debug()->threshold(0.8)->toJson()->get();

        $tags_array=json_decode(trim($result),true);
        
        $tags_array_new=[];
        
        //先获取到等于为1的关键词
        foreach ($tags_array as $key => $value) {
            if($value['p']>=1){
                $tags_array_new[]=$value['t'];
            }
        }
        //判断关键词是否大于五个
        if(count($tags_array_new)<5){
            foreach ($tags_array as $key => $value) {
                if($value['p']>=0.9 && $value['p']<1){
                    $tags_array_new[]=$value['t'];
                }
            }
        }
        if(count($tags_array_new)<5){
            foreach ($tags_array as $key => $value) {
                if($value['p']>=0.8 && $value['p']<1){
                    $tags_array_new[]=$value['t'];
                }
            }
        }
        $tags_array_new=array_unique($tags_array_new);
        $tags_array_new=array_slice($tags_array_new,0,5);
        $tags_keys=implode(",",$tags_array_new);

        //开始进行伪装原创
        $naipan=$this->naipan($article['article_body']);
        if($naipan===false){
            return "伪原创失败\n";
        }
        
        $article_des=trim(mb_substr(strip_tags($naipan),0,85));
        $article_des=str_replace("　",'',$article_des);
        $article_des=str_replace(" ",'',$article_des);
        $article_des=str_replace("\"",'',$article_des);
        
        $article_res=[];
        $article_res['article_tags']=$tags_keys;
        $article_res['article_des']=$article_des;
        $article_res['article_body']=$naipan;
        $article_res['article_status']=1;
        
        Db::name("article")->where(['article_id'=>$article['article_id']])->save($article_res);
        
        //统计关键词下面有多少个文章
        Db::name("keys_list")->where(['keys_id'=>$article['article_keys_id']])->inc('keys_article_count')->update();
        
        return "伪原创成功：".$article['article_title']."\n";
        
    }
    
    public function naipan($string){
        //奶盘：P6ENBDUUDAK6M82G37XBPW6K5TJTZAE7
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST','http://www.naipan.com/open/weiyuanchuang/towei.html',[
                'verify'=>false,
                'stream' => true,
                'timeout' => 8,
                'http_errors'=>true,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
                ],
                'allow_redirects' => [
                    'max'             => 10,        // allow at most 10 redirects.
                    'strict'          => true,      // use "strict" RFC compliant redirects.
                    'referer'         => true,      // add a Referer header
                    'protocols'       => ['http', 'https'], // only allow https URLs
                    'track_redirects' => true
                ],
                'form_params'=>[
                    'regname'=>'seozhanqun123@pm.me',
                    'regsn'=>'P6ENBDUUDAK6M82G37XBPW6K5TJTZAE7',
                    'content'=>$string,
                ],
            ]);
            if($response->getStatusCode()==200){
                $html=$response->getBody();
                $json=json_decode($html,true);
                if($json['result']==1){
                    return trim($json['content']);
                }
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }catch (\Throwable $e) {
            return false;
        }catch (RequestException $e) {
            return false;
        }
    }
}