<?php
use app\ExceptionHandle;
use app\Request;
use app\common\paginator\driver\Yoyo;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
    'think\Paginator'        => Yoyo::class,
];
