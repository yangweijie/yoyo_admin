<?php
declare (strict_types = 1);

namespace app\command;

use app\admin\model\Musics;
use app\admin\model\Playlist;
use FFMpeg\FFMpeg;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Exception;
use think\facade\Db;
use think\Image;

class MvBuild extends \think\admin\Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('mv:build')
            ->setDescription('生成歌单最新歌曲mv')
            ->addOption('id', 'i', Option::VALUE_REQUIRED, '歌单id');
    }

    protected function execute(Input $input, Output $output)
    {
        $id = $input->getOption('id');
        debug('begin');
        $new = Musics::where('playlist_id', $id)->field(['id', 'lrc', 'name', 'artist', 'pic', 'path', 'RANK() OVER (ORDER BY id ASC)'=>'rank', 'mp4'])->select();
        $todos = $new->filter(function($item){
            return $item['mp4'] == '';
        });
        if(!defined('DS')){
            define('DS', DIRECTORY_SEPARATOR);
        }
        list($count, $total) = [0, count($todos)];
        $dir = public_path().'uploads'.DS.'music'.DS.$id;
        if($todos){
            $output->info("歌单 {$id} 有 {$total} 个 MV需要转换");
            $path = $this->getFFMpegPath();
            foreach ($todos as $item){
                $item['path'] = str_ireplace('\\', '/', $item['path']);
                try {
                    if(is_file($item['path'])){
                        debug('item_begin');
                        $mp4 = "{$dir}".DIRECTORY_SEPARATOR."{$item['rank']}. ".safe_name("{$item['name']}-{$item['artist']}.mp4");
                        $mp4 = str_ireplace('\\', '/', $mp4);
                        $cover = str_ireplace('.mp4', '-max.jpg', $mp4);
                        $srt = str_ireplace('.mp4', '.srt', $mp4);
                        $target_name = str_ireplace('.mp4', '', $mp4);
                        if(!is_file($cover)){
                            $this->generateCover(['cover'=>$cover, 'mp3'=>$item['path'], 'name'=>"{$item['rank']}. {$item['name']}-{$item['artist']}"]);
                            if(!is_file($cover)){
                                throw new Exception("歌曲封面生成失败");
                            }
                        }
                        $item->cover = $cover;
                        if(!is_file($srt)){
                            $this->lrc2srt($item['lrc'], $srt);
                            if(!is_file($srt)){
                                throw new Exception("歌曲字幕生成失败");
                            }
                        }
                        $item->srt = $srt;
                        $this->generateMv($path, [
                            'cover'=>$cover,
                            'path'=>$item['path'],
                            'mp4'=>$mp4, 
                            'srt'=>$srt,
                            'rank'=>$item['rank'],
                            'name'=>". {$item['name']}-{$item['artist']}",
                        ]);
                        if(!is_file($mp4)){
                            throw new Exception("歌曲mv生成失败");
                        }
                        debug('item_end');
                        $item->mp4 = $mp4;
                        $item->cost_time = debug('item_begin', 'item_end', 6);
                        $item->save();
                        $this->setQueueProgress("转换歌单 {$id} {$item['name']} {$item['artist']} 歌曲成功", ''. $count / $total);
                    }else{
                        throw new Exception("歌曲文件不存在");
                    }
                    $count++;
                }catch (\Exception $e){
                    debug('item_end');
                    $item->cost_time = debug('item_begin', 'item_end', 6);
                    $item->save();
                    $output->error("转换歌单 {$id} {$item['name']} {$item['artist']} 歌曲失败:".$e->getMessage().PHP_EOL.$e->getTraceAsString());
                    $this->setQueueProgress("转换歌单 {$id} {$item['name']} {$item['artist']} 歌曲失败:". $e->getMessage(), ''. $count / $total);
                }
            }
        }
        $this->setQueueSuccess("转换 {$count} 个 {$id} 歌单的MV！");
        debug('end');
        Playlist::where('id', $id)->update(['last_id'=>Musics::where('playlist_id', $id)->max('id')]);

        // 指令输出
        $output->writeln('转换完成，共耗时'. debug('begin', 'end', 6). '秒');
    }

    private function getFFMpegPath(){
        $dir = env('ffmpeg.dir', 'D:/GreenSoft/ffmpeg-6.0-full_build');
        if(IS_WIN){
            return [
                'ffmpeg.binaries' => "{$dir}/bin/ffmpeg.exe",
                'ffprobe.binaries' => "{$dir}/bin/ffprobe.exe",
            ];
        }else{
            return [
                'ffmpeg.binaries' => "{$dir}/bin/ffmpeg",
                'ffprobe.binaries' => "{$dir}/bin/ffprobe",
            ];
        }
    }

    protected function generateCover($todo){
        $file           = public_path().'black.jpg';
        $cover_path     = str_ireplace('.mp3', '.jpg', $todo['mp3']);
        $cover_tmp_path = str_ireplace('-max.jpg', '-tmp.jpg', $todo['cover']);
        $cover_name     = $todo['name'];
        $image          = Image::open($file);
        Image::open($cover_path)->thumb(200, 200, 6)->save($cover_tmp_path);
        $image->thumb(1276, 720, 6);
        $image->text($cover_name, base_path().'../data/STHeiti Medium.ttc', 24, '#ffffff', 2, 50, 0);
        $image->water($cover_tmp_path, 5)->save($todo['cover']);
    }

    protected function lrc2srt($lrc_path, $target){
        $path = $this->getFFMpegPath();
        $ffmpeg = FFMpeg::create($path);
        $advancedMedia = $ffmpeg->openAdvanced([]);
        $advancedMedia->setInitialParameters([
            '-i', str_ireplace('\\', '/', $lrc_path),
            str_ireplace('\\', '/', $target),
        ]);
        $advancedMedia->save();
    }

    protected function generateMv($path, $todo){
        $ffmpeg = FFMpeg::create($path);
        // 获取音频长度
        $cover_path = str_ireplace('\\', '/', $todo['cover']);
        $mp3_path   = str_ireplace('\\', '/', $todo['path']);
        $mp4_path   = str_ireplace('\\', '/', $todo['mp4']);
        $srt_path   = $todo['srt'];
        if(stripos($srt_path, "'") !== false){
            $tmp_srt = str_ireplace($todo['name'], '', $srt_path);
            copy($srt_path, $tmp_srt);
            $srt_path = $tmp_srt;
        }
        $srt_path = str_ireplace([':'], ['\:'], $srt_path);
        $advancedMedia = $ffmpeg->openAdvanced([]);
        $advancedMedia->setInitialParameters(['-loop', '1', '-i', $cover_path, '-i', $mp3_path,
            // '-vcodec', 'h264_nvenc',
            '-c:v', 'libx264',
            // '-c:v', 'mjpeg',
            '-c:a', 'copy',
            '-filter_complex', "subtitles='{$srt_path}'",
            // '-vf', 'subtitles='.$srt_path,
            '-shortest', $mp4_path]);
        $advancedMedia->save();
    }

}
