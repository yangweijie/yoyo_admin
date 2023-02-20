<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [
            'app\\listener\\Init',
            'app\\listener\\Config',
            'app\\listener\\YoyoInit',
        ],
        // 'HttpRun'  => [
        //     'app\\common\\listener\\Config',
        //     'app\\common\\listener\\Hook',
        // ],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'LogRecord'=>[
            // 'app\\listener\\SqlWatch'
        ],
    ],

    'subscribe' => [
    ],
];
