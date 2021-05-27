<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\task\controller\Seo as cSeo;

class seo extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\seo')
            ->setDescription('the app\command\seo command');
    }

    protected function execute(Input $input, Output $output)
    {
        
        \Swoole\Timer::tick(1000, function () {
            $cSeo=new cSeo();
            $res=$cSeo->index();
            echo $res;
        });
        
        \Swoole\Event::wait();
    }
}
