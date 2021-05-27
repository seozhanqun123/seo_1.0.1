<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use app\task\controller\Keys as cKeys;

class Keys extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\keys')
            ->setDescription('the app\command\keys command');
    }

    protected function execute(Input $input, Output $output)
    {
        \Swoole\Timer::tick(1000, function () {
            $cKeys=new cKeys();
            $cKeys->index();
        });
        
        \Swoole\Event::wait();
    }
}
