<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'download_playlist'=>'app\command\DownloadPlayList',
        'mv' => 'app\command\Mv',
        'mv:build' => 'app\command\MvBuild',
        'playlist_init'=>'app\command\PlaylistInit',
    ],
];
