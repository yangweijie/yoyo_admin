<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'mv' => 'app\command\Mv',
        'playlist_init'=>'app\command\PlaylistInit',
        'download_playlist'=>'app\command\DownloadPlayList',
    ],
];
