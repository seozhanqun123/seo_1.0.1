<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\task\controller\Search as cSearch;

class Search extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\search')
            ->setDescription('the app\command\search command');
    }

    protected function execute(Input $input, Output $output)
    {

        \Swoole\Timer::tick(500, function () {
            $cSearch=new cSearch();
            $cSearch->index();
        });
        
        \Swoole\Event::wait();
    }
}
