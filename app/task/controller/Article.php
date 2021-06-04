<?php
namespace app\task\controller;
use QL\QueryList;
use GuzzleHttp\Exception\ClientException;
use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
use GuzzleHttp\Exception\RequestException;
use think\facade\Db;
use GuzzleHttp\Psr7;
use app\task\controller\Search as cSearch;

class Article{

    public function index(){

        // $title='避孕套品牌十大排名_亲子百科';

        // var_dump($this->getTitle($title));

        // exit;

        //从网址库中获取一个网址进行获取内容
        $surl=Db::name("url_search")->limit(1)->select()->toArray();

        if(!count($surl)){
            // echo "没有要采集的文章了\n";
            // $cSearch=new cSearch();
            // $cSearch->index();
            return '';
        }
        $surl=$surl[0];
        Db::name("url_search")->where(['surl_id'=>$surl['surl_id']])->delete(true);

        //获取到当前关键词
        $keys=Db::name("keys_list")
            ->alias('kl')
            ->join(['keys_type'=>'kt'],'kt.keyst_id=kl.keys_type_id')
            ->where(['keys_id'=>$surl['surl_keys_id']])
            ->limit(1)->select()->toArray();
        if(!count($keys)){
            // echo "关键词不存在，准备调用搜索引擎采集\n";
            $cSearch=new cSearch();
            $cSearch->index();
            return '';
        }
        $keys=$keys[0];

        // $surl['surl_url']='https://www.baidu.com/link?url=m9HGTegnOhs35wnDPbNxnItvUhYPujwbAvepOLyiNGntAPkxBXdh3xK0iflF47Z8kugRcq0-USuGrEVDXm14mq&wd=&eqid=fa61b0280001d86600000006609373d2';

        // echo "当前采集网址：{$surl['surl_url']}\n";
        

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('GET',$surl['surl_url'],[
        //     'verify'=>false,
        //     'stream' => true,
        //     'timeout' => 8,
        //     'http_errors'=>false,
        //     'headers' => [
        //         'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36',
        //     ],
        //     'allow_redirects' => [
        //         'max'             => 10,        // allow at most 10 redirects.
        //         'strict'          => true,      // use "strict" RFC compliant redirects.
        //         'referer'         => true,      // add a Referer header
        //         'protocols'       => ['http', 'https'], // only allow https URLs
        //         'track_redirects' => true
        //     ]
        // ]);

        //判断当前网站是否已经被禁用
        $url_info=parse_url($surl['surl_url']);
        if(!empty($url_info['host']) && $url_info['host']!='www.baidu.com'){
            if(empty($url_info['host'])){
                // echo "网站错误\n";
                return '';
            }
            $domain_status_count=Db::name("dlog")->where(['dlog_domain'=>$url_info['host'],'dlog_status'=>4])->count();
            if($domain_status_count){
                // echo "此网站禁止采集\n";
                return '';
            }
        }

        $response=$this->curl(['url'=>$surl['surl_url']]);

        if(!$response){
            // echo "网站无法访问\n";
            return '';
        }

        $data['url']='';

        $readability=null;

        try {
            if($response->getStatusCode()==200){
                $html=trim($response->getBody());
                $data['html']=$html;
                $url_list=$response->getHeader("X-Guzzle-Redirect-History");
                if(count($url_list)){
                    $url=$url_list[count($url_list)-1];
                }else{
                    $url=$surl['surl_url'];
                }
                $data['article']['url']=$url;

                //判断当前网站是否已经被禁用
                $url_info=parse_url($url);
                if(empty($url_info['host'])){
                    // echo "网站错误\n";
                    return '';
                }
                $domain_status_count=Db::name("dlog")->where(['dlog_domain'=>$url_info['host'],'dlog_status'=>4])->count();

                if($domain_status_count){
                    // echo "此网站禁止采集\n";
                    return '';
                }

                $readability=$this->getArticle($html);

                // echo "最终采集网址：{$url}\n";

                $dlog_res=Db::name("dlog")->where(['dlog_domain'=>$url_info['host']])->limit(1)->select()->toArray();

                if($readability['status']==200){
                    //判断是否已经采集过了
                    $article_count=Db::name('article')->whereOr(['article_url'=>$url,'article_title'=>$readability['title']])->count();
                    if(!$article_count){
                        //这里需要一个伪原创
                        $article_data['article_site_id']=$keys['keys_site_id'];
                        $article_data['article_keys_id']=$keys['keys_id'];
                        $article_data['article_url']=$url;
                        $article_data['article_type_id']=$keys['keyst_id'];
                        $article_data['article_title']=$readability['title'];
                        $article_data['article_des']=trim(mb_substr($readability['text'],0,255));
                        $article_data['article_body']=$readability['body'];
                        $article_data['article_img']=$readability['img'];
                        $article_data['article_times']=time();
                        $res=Db::name("article")->save($article_data);
                        if($res){
                            echo "成功采集文章：{$readability['title']}\n";
                        }
                    }else{
                        // echo "文章已经存在了\n";
                    }
                    // echo $readability['msg']."\n";

                    if(count($dlog_res)){
                        Db::name("dlog")->where(['dlog_id'=>$dlog_res[0]['dlog_id']])->inc('dlog_success')->update();
                    }else{
                        Db::name("dlog")->save(['dlog_domain'=>$url_info['host'],'dlog_error'=>1]);
                    }
                    
                }else{
                    //在域名信息库中记录数据
                    if(!empty($url_info['host'])){
                        //存在则自增
                        if(count($dlog_res)){
                            Db::name("dlog")->where(['dlog_id'=>$dlog_res[0]['dlog_id']])->inc('dlog_error')->update();
                            if($dlog_res[0]['dlog_error']>=50 && $dlog_res[0]['dlog_status']!=4 && $dlog_res[0]['dlog_success']<=10){
                                Db::name("dlog")->where(['dlog_id'=>$dlog_res[0]['dlog_id']])->save(['dlog_status'=>4]);
                            }
                        }
                        //不存在则添加数据
                        else{
                            Db::name("dlog")->save(['dlog_domain'=>$url_info['host'],'dlog_error'=>1]);
                        }
                    }
                    // echo "数据已经删除\n";
                    // echo $readability['msg']."\n";
                }
            }
        } catch (Exception $e) {
            // echo "数据采集失败\n";
        }

    }

    public function curl($obj){
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET',$obj['url'],[
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
                ]
            ]);
            // if($response->getStatusCode()==200){
            //     $html=$response->getBody();
            //     return trim($html);
            // }
            return $response;
        }catch (\Exception $e) {
            return false;
        }catch (\Throwable $e) {
            return false;
        }catch (RequestException $e) {
            return false;
        }
    }

    public function getArticle($html){

        $article_array=[];
        $article_array['title']='';
        $article_array['body']='';
        $article_array['img']='';
        $article_array['status']=0;
        $article_array['msg']='';

        try {
            $readability = new Readability(new Configuration([
                'SummonCthulhu'=>true,//通过正则表达式删除所有节点
                // 'FixRelativeURLs'=>true,//将相对URL转换为绝对URL。
                'MaxTopCandidates'=>1,//默认值5，顶级候选的最大数量
                'CharThreshold'=>500,//认为文章已成功解析的最少字符数。
                'SubstituteEntities'=>true,
                'NormalizeEntities'=>true,
                'ArticleByLine'=>true
            ]));

            try{
                $current_encode = mb_detect_encoding($html, array("ASCII","GB2312","GBK",'BIG5','UTF-8')); 
                $html = mb_convert_encoding($html,"UTF-8",$current_encode);
            }catch(Exception $e){
                // $article_array['msg']='没有采集到文章内容';
                return $article_array;
            }
            $readability->parse($html);

            $title=$this->getTitle(trim($readability->getTitle()));

            if(!$title || mb_strlen($title)<=7){
                $article_array['status']=400;
                // $article_array['msg']='标题不存在，或字数不足=>'.$title;
                return $article_array;
            }

            $body=$readability->getContent();
            if(!$body){
                $article_array['status']=400;
                // $article_array['msg']='没有采集到文章内容';
                return $article_array;
            }

            $ql = QueryList::html($body);

            //处理图片
            // $ql->find("img")->map(function($item){
            //     $src = $item->attrs('src');
            //     if(!empty($src[0]) && strlen($src[0])>=10){
            //         //判断是否绝对地址 如果不是则添加绝对网址
            //         $item->replaceWith('<img src="'.$src[0].'">');
            //     }else{
            //         $item->replaceWith('');
            //     }
            // });

            $rt = $ql->getHtml();
            $rt=preg_replace("/[\t\n\r]+/i","",$rt);
            
            //存在域名库中 为了以后查找未注册域名
            preg_match_all("/[\w]+\.com/i",$rt,$domain_list);

        	$trn=0;
        	if(count($domain_list[0])){
        		$er=array_unique($domain_list[0]);
        		foreach ($er as $key => $value) {
        		    $erc=strtolower($value);
        			$ccc=Db::name("domain_not")->where(['dont_host'=>$erc])->count();
        			if(!$ccc){
        				$trn_res=Db::name("domain_not")->save(['dont_host'=>$erc]);
        				if($trn_res){
        					$trn+=1;
        				}
        			}
        		}
        	}

            $rt=strip_tags($rt,"<br><p><h1><h2><h3><h4><h5><h6><table><tbody><theader><td><tr><th><strong><hr><pre><code>");
            $rt=preg_replace("/([\s]{2,})+/i","",$rt);

            //替换网址 问题在于图片网址也去掉了
            $rt=preg_replace("/(http|https)\:\/\/[\w\.\-\/\?\#\=]+/i","",$rt);
            $rt=preg_replace("/(www\.[\w\.\-\/\?\#\=]+)/i","",$rt);
            $rt=preg_replace("/([a-z]+\.[a-z]+([\w\/]+)*)/i","",$rt);
            
            //有时候标题也有网址
            $title=preg_replace("/(http|https)\:\/\/[\w\.\-\/\?\#\=]+/i","",$title);
            $title=preg_replace("/(www\.[\w\.\-\/\?\#\=]+)/i","",$title);
            $title=preg_replace("/([a-z]+\.[a-z]+([\w\/]+)*)/i","",$title);

            //判断文字是否足够
            $ql2 = QueryList::html($rt);
            $text=$ql2->find("")->text();

            if(mb_strlen($text)<=500){
                $article_array['status']=400;
                // $article_array['msg']="文章字数不足500字\n";
                $article_array['msg']="";
                return $article_array;
            }

            $img_list=$readability->getImages();

            if(count($img_list)){
                $img=$img_list[0];
            }else{
                $img='';
            }

            $article_array['title']=($title);
            $article_array['body']=$rt;
            $article_array['img']=strlen($img)>=255 ? '' : $img;
            $article_array['img']='';
            $article_array['text']=$text;

            $article_array['status']=200;
            $article_array['msg']="采集成功\n";
            $article_array['msg']="";

            return $article_array;
        } catch (ParseException $e) {
            $article_array['status']=400;
            $article_array['msg']="没有采集到文章内容\n";
            $article_array['msg']="";
            return $article_array;
        }
    }

    public function getTitle($title){
        $er=explode("_",$title);
        //判断是否足够长度 判断是否有这个分割符
        if(count($er)>=2){
            if(mb_strlen($er[0])>=10){
                return $er[0];
            }elseif (count($er)>=3 && mb_strlen($er[0].$er[1])>=10) {
                return $er[0].'_'.$er[1];
            }else{
                if(mb_strlen($er[count($er)-1])<10){
                    unset($er[count($er)-1]);
                    return implode(" ",$er);
                }
            }
        }

        $er=explode("-",$title);
        //判断是否足够长度 判断是否有这个分割符
        if(count($er)>=2){
            if(mb_strlen($er[0])>=10){
                return $er[0];
            }elseif (count($er)>=3 && mb_strlen($er[0].$er[1])>=10) {
                return $er[0].'-'.$er[1];
            }else{
                if(mb_strlen($er[count($er)-1])<10){
                    unset($er[count($er)-1]);
                    return implode(" ",$er);
                }
            }
        }
        return $title;
    }
}