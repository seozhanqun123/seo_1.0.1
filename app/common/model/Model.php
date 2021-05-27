<?php
namespace app\common\model;
use think\model as Models;
// use think\model\concern\SoftDelete;

class Model extends Models
{
    // use SoftDelete;
    //设置软删除的默认值
    public $defaultSoftDelete = 0;
    // 设置JSON数据返回数组
    public $jsonAssoc = true;
    //开启时间辍
    public $autoWriteTimestamp = true;

    public $json=[
    	'user_login_json',
    ];
    
    
}