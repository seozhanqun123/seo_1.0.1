<?php
namespace app\common\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule =   [
        'tags'=>'require|chsDash',
    ];
    
    protected $message  =   [
        'group_name.require' => '游戏名称不能为空',
        'group_lottery_type.require'     => '彩票种类不正确',
        'group_lottery_code.require'     => '彩票代码不正确',
        'group_magnification.require'     => '基础倍数不正确',
        'group_magnification.number'     => '基础倍数不正确',
        'group_bei_list.regex'     => '请认真检查倍投方案',
        'group_bei_list.require'     => '倍投方案必须填写',
        'group_status.require'     => '分组状态不正确',
        'group_status.number'     => '基础倍数不正确',

        'tags.chsDash'     => '标签不存在',
    ];

    protected $scene = [
        'tags'  =>  ['tags'],
    ]; 

}

?>