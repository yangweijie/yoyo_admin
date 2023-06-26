<?php
declare (strict_types = 1);

namespace app\command;

use app\admin\model\Musics;
use app\admin\model\Playlist;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Command;
use think\console\Output;

class DownloadPlayList extends \think\admin\Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('playlist:down')
            ->setDescription('下载最新歌单歌曲')
            ->addOption('id', 'i', Option::VALUE_REQUIRED, '歌单id');
    }

    protected function execute(Input $input, Output $output)
    {
        if(!defined('DS')){
            define('DS', DIRECTORY_SEPARATOR);
        }
        debug('begin');
        $id = $input->getOption('id')?:0;
        $playlist = Playlist::find($id);
        $musicAll = json_decode(file_get_contents($playlist['url']), true);
        $new = Musics::new($musicAll, $id);
        dump($new);
        die;
        list($count, $total) = [0, count($new)];
        $dir = public_path().'/uploads/music/'.$id;
        if(!is_dir($dir)){
            mkdir($dir);
        }
        foreach($new as $item){
            $count++;
            $name = "{$item['name']}-{$item['artist']}";
            $name = str_replace('/', '-', $name);
            $name = str_replace('?', '', $name);
            $name = str_replace('\\', '', $name);
            $file = "{$name}.mp3";
            $cover = "{$name}.jpg";
            $lrc = "{$name}.lrc";
            file_put_contents($dir.DS.$file, file_get_contents($item['url']));
            file_put_contents($dir.DS.$cover, file_get_contents($item['pic']));
            file_put_contents($dir.DS.$lrc, file_get_contents($item['lrc']));
            $insert = Musics::create([
                'playlist_id'=>$id,
                'type'=>$playlist['type'],
                'name'=>$item['name'],
                'artist'=>$item['artist'],
                'url'=>$item['url'],
                'path'=>$file,
                'lrc'=>$lrc,
                'pic'=>$cover,
                'mp4'=>'',
            ]);
            $state = is_file($file)?'成功':'失败';
            $this->setQueueProgress("下载歌单 {$id} {$item['name']} {$item['artist']} 歌曲{$state}", ''. $count / $total);
        }
        debug('end');
        $this->setQueueSuccess("下载 {$count} 个 {$id} 歌单的新歌！");
        // 指令输出
        $output->writeln('下载完成，共耗时'. debug('begin', 'end', 6). '秒');
    }
}
