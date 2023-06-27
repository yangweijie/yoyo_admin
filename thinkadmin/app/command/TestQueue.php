<?php

namespace app\command;

use think\console\Input;
use think\console\Output;

class TestQueue extends \think\admin\Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('test_queue')
            ->setDescription('测试队列')
            ;
    }

    protected function execute(Input $input, Output $output)
    {
        for($i = 0; $i< 60;$i++){
            sleep(1);
            $this->setQueueProgress("同步第{$i}步", ''. $i / 60);
        }
        $this->setQueueSuccess("测试完毕");
    }

}