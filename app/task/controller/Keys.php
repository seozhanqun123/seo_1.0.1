<?php
namespace app\task\controller;
use app\BaseController;
use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
use think\facade\Db;

//采集关键词

class Keys{

    public function index(){

        set_time_limit(20);
        
        // $cache_name='kesy_aedfsafsfs';
        // $search_kesy_aedferdf=cache($cache_name);
        
        // if($search_kesy_aedferdf){
        //     echo "采集关键词需要等待".($search_kesy_aedferdf-time())."秒才能进行下一次。\n";
        //     return '';
        // }
        
        // $tt=time()+10;
        
        // cache($cache_name,$tt,10);

        $list=Db::name("keys_list")
            ->alias('kl')
            ->join(['keys_type'=>'kt'],'kt.keyst_id=kl.keys_type_id')
            ->join(['site'=>'s'],'s.site_id=kt.keyst_site_id')
            // ->where('keys_length>0 and keys_length<=8 and keys_last_times<='.(time()-3600))
            ->where('keys_length>0 and keys_length<=8')
            ->order("keyst_collection_times asc,keys_last_times asc")
            ->limit(1)->select()->toArray();
        
        Db::name("keys_type")->where(['keyst_id'=>$list[0]['keyst_id']])->update(['keyst_collection_times'=>time()]);
   
        //更改当前关键词最后一次采集时间
        Db::name("keys_list")->where(['keys_id'=>$list[0]['keys_id']])->save([
            'keys_last_times'=>time(),
        ]);

        if(!count($list)){
            echo "没有关键词了\n";
            return "\n";
        }
        
        echo "采集大类：{$list[0]['keyst_title']}\n";
        
        $keys_list=$this->inits($list[0]['keys_name']);

        if(is_array($keys_list) && count($keys_list)){
            //关键词过滤
            $keyst_list_required=$list[0]['keyst_list_required'];
            $keyst_list_required_array=array_filter(explode("\n",$keyst_list_required));
            $keys_list['list']=$this->key_required($keys_list['list'],$keyst_list_required_array);
    
            $keyst_list_filter=$list[0]['keyst_list_filter'];
            $keyst_list_filter_array=array_filter(explode("\n",$keyst_list_filter));
            $keys_list['list']=$this->key_filter($keys_list['list'],$keyst_list_filter_array);
    
            $n=0;
            foreach ($keys_list['list'] as $key => $value) {
                //判断是否必须包含关键词
                $keys_count=Db::name("keys_list")->where(['keys_name'=>$value])->count();
                if(!$keys_count){
                    $res=Db::name("keys_list")->save([
                        'keys_name'=>$value,
                        'keys_site_id'=>$list[0]['site_id'],
                        'keys_top_id'=>$list[0]['keys_id'],
                        'keys_type_id'=>$list[0]['keys_type_id'],
                        'keys_last_times'=>0,
                        'keys_length'=>mb_strlen($value),
                    ]);
                    $n+=$res ? 1 : 0;
                }
            }
        }else{
            $n=0;
        }

        echo "当前采集关键词：{$list[0]['keys_name']}\n";
        echo "成功采集关键词：{$n}\n";

        // echo "采集统计：\n";
        // foreach ($keys_list['code'] as $key => $value) {
        //     echo "【{$value['name']}】：{$value['count']}\n";
        // }

        // echo '<script>setTimeout(function(){location.href=location.href;},1000)</script>';
        
    }

    //一批关键词进行判断是否需要过滤 再写入数据库 返回成功数量
    public function save_all($arr){
        //关键词过滤
        $keyst_list_required=$arr['required'];
        $keyst_list_required_array=array_filter(explode("\n",$keyst_list_required));
        $arr['list']=$this->key_required($arr['list'],$keyst_list_required_array);

        $keyst_list_filter=$arr['filter'];
        $keyst_list_filter_array=array_filter(explode("\n",$keyst_list_filter));
        $arr['list']=$this->key_filter($arr['list'],$keyst_list_filter_array);

        $n=0;
        foreach ($arr['list'] as $key => $value) {
            //判断是否必须包含关键词
            $keys_count=Db::name("keys_list")->where(['keys_name'=>$value])->count();
            if(!$keys_count){
                $res=Db::name("keys_list")->save([
                    'keys_name'=>$value,
                    'keys_site_id'=>$arr['object']['site_id'],
                    'keys_top_id'=>$arr['object']['keys_id'],
                    'keys_type_id'=>$arr['object']['keys_type_id'],
                    'keys_last_times'=>0,
                    'keys_type'=>empty($arr['type']) ? 1 : $arr['type'],
                    'keys_length'=>mb_strlen($value),
                ]);
                $n+=$res ? 1 : 0;
            }
        }



        return $n;
    }

	//输入一个关键字传回所有平台数据
    public function inits($keys){

        $p_list=[
            'baidu',
            'so',
            'sogou',
            'sm'
        ];

        //随机取出一个
        $rand_array=$p_list[rand(0,count($p_list)-1)];

        //判断时间必须间隔15秒
        $task_keys_jg15m=(int)cache("task_keys_jg15m".$rand_array);

        // if($task_keys_jg15m>time()){
        //     echo '你需要等待15秒 '.$rand_array."\n";
        //     // echo '<script>setTimeout(function(){location.href=location.href;},3000)</script>';
        //     return '';
        // }

        $array=[];
        $code=[];
        foreach ([$rand_array] as $key => $value) {
            $arr=$this->$value($keys);
            $array=array_merge($array,$arr['list']);
            $code[]=['code'=>$arr['code'],'name'=>$arr['name'],'count'=>count($arr['list'])];
        }

        // $list_baidu=$this->baidu($keys);
        // $list_so=$this->so($keys);
        // $list_sogou=$this->sogou($keys);
        // $list_sm=$this->sm($keys);

        // $array=array_merge($list_baidu,$list_so,$list_sogou,$list_sm);
        // cache("task_keys_jg15m".$rand_array,time()+5);
        $new_array=array_unique($array);
        return ['list'=>$new_array,'code'=>$code];
    }

    //百度
    public function baidu($keys){
        $info=['name'=>'百度','code'=>'sm'];
    	$url='https://www.baidu.com/sugrec?prod=pc&wd='.urlencode($keys);
        // $url='https://www.baidu.com/sugrec?prod=wise&wd='.urlencode($keys);

        $json=$this->curl([
            'url'=>$url,
        ]);

        if(empty($json['g'])){
            $info['list']=[];
            return $info;
        }
        $array=[];
        foreach ($json['g'] as $key => $value) {
            $array[]=$value['q'];
        }
        $info['list']=$array;
        return $info;
    }
    //360
    public function so($keys){
        $info=['name'=>'360','code'=>'sm'];
    	$url='https://sug.so.360.cn/suggest?word='.urlencode($keys);
        $json=$this->curl([
            'url'=>$url,
        ]);

        if(empty($json['result'])){
            $info['list']=[];
            return $info;
        }
        $array=[];
        foreach ($json['result'] as $key => $value) {
            $array[]=$value['word'];
        }
        $info['list']=$array;
        return $info;
    }
    //google
    public function google($keys){
    	
    }
    //搜狗
    public function sogou($keys){
        $info=['name'=>'搜狗','code'=>'sm'];
        $url='https://wap.sogou.com/web/sugg/'.urlencode($keys).'?s=1&source=wapsearch';
        $json=$this->curl([
            'url'=>$url,
        ]);

        if(empty($json['s'])){
            $info['list']=[];
            return $info;
        }
        $array=[];
        foreach ($json['s'] as $key => $value) {
            $array[]=$value['q'];
        }
        $info['list']=$array;
        return $info;
    }
    //神马
    public function sm($keys){
        $info=['name'=>'神马','code'=>'sm'];
    	$url='https://sugs.m.sm.cn/web?q='.urlencode($keys);
        $json=$this->curl([
            'url'=>$url,
        ]);

        if(empty($json['r'])){
            $info['list']=[];
            return $info;
        }
        $array=[];
        foreach ($json['r'] as $key => $value) {
            $array[]=$value['w'];
        }
        $info['list']=$array;
        return $info;
    }
    //必应
    public function bind($keys){
    	
    }

    //存在则过滤
    public function key_filter($keys_list,$key_required){
        foreach ($key_required as $key => $value) {
            foreach ($keys_list as $key2 => $value2) {
                if(strpos(trim($value2),trim($value))!==false){
                    unset($keys_list[$key2]);
                }
            }
        }
        return $keys_list;
    }

    //不存在则过滤
    public function key_required($keys_list,$key_required){
        if(!count($key_required)){
            return $keys_list;
        }
        foreach ($keys_list as $key => $value) {
            $n=0;
            foreach ($key_required as $key2 => $value2) {
                if(strlen($value2) && strpos(trim($value),trim($value2))!==false){
                    $n+=1;
                }
            }
            if(!$n){
                unset($keys_list[$key]);
            }
        }
        return $keys_list;
    }

    public function curl($obj){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$obj['url'],[
            'verify'=>false,
            'stream' => true,
            'read_timeout' => 5,
            'http_errors'=>false,
            'proxy'  => [
                'http'=>'http://t12185869913167:defac8gd@tps174.kdlapi.com:15818',
            ],
        ]);

        try {
            if($response->getStatusCode()==200){
                $html=$response->getBody();
                return json_decode($html,true);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}