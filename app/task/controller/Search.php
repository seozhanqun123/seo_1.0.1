<?php
namespace app\task\controller;
use app\BaseController;
use QL\QueryList;
use QL\Ext\AbsoluteUrl;
use QL\Ext\CurlMulti;
use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
use think\facade\Db;
use app\task\controller\Keys as cKeys;

class Search{

    public function index(){
        
        $search_array=['baidu','sm'];
        $search_str=$search_array[rand(0,count($search_array)-1)];
        
        // $cache_name='kesy_aedfsafsf1s_'.$search_str;
        // $search_kesy_aedferdf=cache($cache_name);
        // if($search_kesy_aedferdf){
        //     echo "{$search_str}搜索需要等待".($search_kesy_aedferdf-time())."秒才能进行下一次。\n";
        //     return '';
        // }
        
        // cache($cache_name,time()+18,18);

        $keys_list=Db::name("keys_list")
            ->alias('kl')
            ->join(['keys_type'=>'kt'],'kt.keyst_id=kl.keys_type_id')
            ->join(['site'=>'s'],'s.site_id=kl.keys_site_id')
            ->where('keys_length>0 and keys_length>=6 and keys_search_times<='.(time()-86400))
            // ->where('keys_length>0 and keys_length>=6')
            ->order("site_task_search_times,keys_search_times asc")
            // ->orderRaw('rand()')
            ->limit(1,rand(1,100))->select()->toArray();

        $cKeys=new cKeys();

        if(!count($keys_list)){
            echo "无数据了，等待下一轮\n";
            // $cKeys->index();
            return '';
        }
        $keys_list=$keys_list[0];
        
        Db::name("keys_list")->where(['keys_id'=>$keys_list['keys_id']])->save(['keys_search_times'=>time()]);
        
        Db::name("site")->where(['site_id'=>$keys_list['site_id']])->save(['site_task_search_times'=>time()]);

        echo "网站名称：{$keys_list['site_name']} 采集关键词：{$keys_list['keys_name']}\n";

        $list=$this->$search_str($keys_list['keys_name']);
        // $list=$this->baidu($keys_list['keys_name']);

        if(count($list)){
            $dataList=[];

            $keys_res_count=$cKeys->save_all([
                'list'=>$list['keys'],
                'type'=>2,
                'site_id'=>$keys_list['site_id'],
                'filter'=>$keys_list['keyst_list_filter'],
                'required'=>$keys_list['keyst_list_required'],
                'object'=>$keys_list,
            ]);
    
            foreach ($list['link'] as $key => $value) {
                if(strlen($value)<500){
                    //判断网址是否存在
                    $url_count=Db::name("url_search")->where(['surl_url'=>$value])->count();
                    if(!$url_count){
                        $data=[];
                        $data['surl_site_id']=$keys_list['site_id'];
                        $data['surl_keys_id']=$keys_list['keys_id'];
                        $data['surl_url']=$value;
                        $data['surl_create_times']=time();
                        $dataList[]=$data;
                    }
                }
            }
            $res=Db::name("url_search")->insertAll($dataList);
            echo "【{$search_str}】搜索引擎已经成功收集{$res}个网址\n";
            echo "成功采集关键词：{$keys_res_count}\n";
            
        }else{
            echo "【{$search_str}】搜索引擎已经成功收集0个网址\n";
            echo "成功采集关键词：0\n";
            // Db::name("keys_list")->where(['keys_id'=>$keys_list['keys_id']])->save(['keys_search_times'=>time()]);
        }

        return '==============';
    }

	//输入一个关键字传回所有平台数据
    public function inits($keys){
        return 'task';
    }
    
    protected $user_agert=[
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; AcooBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Acoo Browser; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)",
        "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.5; AOLBuild 4337.35; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
        "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN) AppleWebKit/523.15 (KHTML, like Gecko, Safari/419.3) Arora/0.3 (Change: 287 c9dfb30)",
        "Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.6",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2pre) Gecko/20070215 K-Ninja/2.1.1",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/20080705 Firefox/3.0 Kapiko/3.0",
        "Mozilla/5.0 (X11; Linux i686; U;) Gecko/20070322 Kazehakase/0.4.5",
        "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.8) Gecko Fedora/1.9.0.8-1.fc10 Kazehakase/0.5.6",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20",
        "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.11 TaoBrowser/2.0 Safari/536.11",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.71 Safari/537.1 LBBROWSER",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; LBBROWSER)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E; LBBROWSER)",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 LBBROWSER",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; QQBrowser/7.0.3698.400)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SV1; QQDownload 732; .NET4.0C; .NET4.0E; 360SE)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)",
        "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1",
        "Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
        "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b13pre) Gecko/20110307 Firefox/4.0b13pre",
        "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11",
        "Mozilla/5.0 (X11; U; Linux x86_64; zh-CN; rv:1.9.2.10) Gecko/20100922 Ubuntu/10.10 (maverick) Firefox/3.6.10",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36",
    ];

    //百度
    public function baidu($keys){
        $ql = QueryList::get('http://www.baidu.com/s?rn=50&wd='.urlencode($keys), null, [
                'headers' => [
                    'User-Agent' => $this->user_agert[rand(1,count($this->user_agert)-1)],
                    'Cookie'=>"PSTM={time()}; BAIDUID={md5(time().rand(1,100000))}:FG=1; BIDUPSID={time()}; BDORZ={md5(time().rand(1,100000))}; __yjs_duid=1_{md5(time().rand(1,100000))}; BCLID_BFESS={time()}{time()}; BDSFRCVID_BFESS=H_{md5(time().rand(1,100000))}{md5(time().rand(1,100000))}_{md5(time().rand(1,100000))}; H_BDCLCKID_SF_BFESS=tJue_D_5fII3fP36q6_aDjvH-UnLq-3n3eOZ0l8Ktfc1Oh-zXPoA56LlyPr8K5JML6cU-bOmWIQHDPJY0t5djb-ub4jh2-bKBIr4KKJx-RLWeIJo5t5ByP0yhUJiB5QLBan7_qvIXKohJh7FM4tW3J0ZyxomtfQxtNRJ0DnjtpChbC_4DTDKjTv-epJf-KCttRCOsJOOaCvFVpROy4oTj6Dly-7RexR-yjFt3hvYMR05eUjC3pJj3MvB-fnjaloaBnnfbRQI5J7JHqRPQft20b_EeMtjBbQaMm3bQb7jWhkhDq72y5jvQlRX5q79atTMfNTJ-qcH0KQpsIJM5-DWbT8IjHCHJ5KqtnuDoKv5b-0_jb7G2trj-tFqMeT22-usWNOA2hcH0KLKMUbd5t5xbqIB-tjiBRcMtKk83xbYJfb1MRjvXUOlybDLLHoO3bOzyNcNXq5TtUJ18DnTDMRh-6L_2JJyKMniJCj9-pPKWhQrh459XP68bTkA5bjZKxtq3mkjbPbDfn028DKu-n5jHjjQDa-f3q; plus_cv=1::m:7.94e+147; MSA_WH=400_850; H_WISE_SIDS=110085_154213_171565_171711_173125_173369_173601_174445_174638_174806_174909_174975_175100_8000122_8000137; BD_UPN=12314753; H_PS_PSSID=; kleck=3f3478aef688bcac80969586980055a7; delPer=0; BD_CK_SAM=1; PSINO=7; BD_HOME=1; BAIDUID_BFESS=F8FC62F2A90796B7AEE2E88C9CE2F63E:FG=1; BDRCVFR[S4-dAuiWMmn]=I67x6TjHwwYf0; H_PS_645EC=e761O3X7%2BXqU%2BSzS%2FmfSCTiKvnjCHpEwBDklHSbwBUjiZ733r5iUjKxxpXkBKzdzDw; BA_HECTOR=248lag84a40la4a0e01g97l8o0r; WWW_ST={time()}",
                ],
                'timeout'=>10,
                'proxy'  => 'http://t12185869913167:defac8gd@tps174.kdlapi.com:15818',
            ])->rules([
            'title'=>array('h3','texts','',function($arr){
                $array=[];
                foreach ($arr as $item) {
                    $array[] = trim($item);
                }
                return $array;
            }),
            'link'=>array('h3>a','htmlOuters','',function($arr){
                $hrefs = [];
                foreach ($arr as $item) {
                    $hrefs[] = pq($item)->attr('href');
                }
                return $hrefs;
            }),
            'keys'=>array('.new-inc-rs-table a','texts','',function($arr){
                $array=[];
                foreach ($arr as $item) {
                    $array[] = trim($item);
                }
                return $array;
            })
        ]);

        $data=$ql->queryData();

        if(!empty($data['link']) && count($data['link'])){
            return $data;
        }else{
            return [];
        }
    }
    //360 垃圾 不做
    // public function so($keys){
    	
    // }
    //google
    public function google($keys){
    	
    }

    //搜狗 内容杂乱得狠 不容入目 哎
    // public function sogou($keys){

    // }

    //神马
    public function sm($keys){

        try {
            $url='https://m.sm.cn/s?q='.urlencode($keys).'&tomode=center';
            $ql = QueryList::get($url, null, [
            'headers' => [
                'User-Agent' => $this->user_agert[rand(1,count($this->user_agert)-1)],
            ],
            'timeout'=>10,
            'http_errors'=>true,
            'verify'=>false,
            'stream' => true,
            'proxy'  => [
                'http'=>'http://t12185869913167:defac8gd@tps174.kdlapi.com:15818',
            ],
        ])->rules([
                'title'=>array('.article.ali_row h2 a','texts','',function($arr){
                    $array=[];
                    foreach ($arr as $item) {
                        $array[] = trim($item);
                    }
                    return $array;
                }),
                'link'=>array('.article.ali_row h2 a','htmlOuters','',function($arr){
                    $hrefs = [];
                    foreach ($arr as $item) {
                        $hrefs[] = pq($item)->attr('href');
                    }
                    return $hrefs;
                }),
                'keys'=>array('.ali_rel a','texts','',function($arr){
                    $array=[];
                    foreach ($arr as $item) {
                        $array[] = trim($item);
                    }
                    return $array;
                })
            ]);
        
            $data=$ql->queryData();   

            return $data;
            
        } catch (\Exception $e) {
            $data=[];
        }

        if(!empty($data['link']) && count($data['link'])){
            return $data;
        }else{
            return [];
        }

    }
    //必应
    public function bind($keys){
    	
    }

    public function curl($obj){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$obj['url'],[
            'verify'=>false,
            'stream' => true,
            'read_timeout' => 3,
            'http_errors'=>false,
            'headers' => [
                'User-Agent' => $this->user_agert[rand(1,count($this->user_agert)-1)],
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
    }
}