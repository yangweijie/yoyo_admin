<?php
declare (strict_types = 1);

namespace app\command;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;
use Symfony\Component\Console\Helper\ProgressBar;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Symfony\Component\Console\Output\OutputInterface;


class Mv extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('mv')
            ->addOption('dir', null, Option::VALUE_REQUIRED, '要生成的mp3目录')
            ->setDescription('批量转换mp3为mv');
    }

    protected function execute(Input $input, Output $output)
    {
        set_time_limit(0);
        $dir = $input->getOption('dir');
        if(empty($dir)){
            throw new \Exception("dir 为空");
        }
        $dir = realpath($dir);
        if(!is_dir($dir)){
            $output->error("`{$dir}` 目录不存在");
        }else{
            $old_pwd = getcwd();
            chdir($dir);
            // 扫描可转换的歌曲数量
            $mp3Files = glob('*.mp3');
            $output->writeln(sprintf('共%d首歌曲', count($mp3Files)));
            $todos = [];
            foreach($mp3Files as $mp3){
                $mp3 = $dir.DIRECTORY_SEPARATOR.$mp3;
                $name       = pathinfo($mp3, PATHINFO_FILENAME);
                $mp4_path   = str_ireplace('.mp3', '.mp4', $mp3);
                $cover_path = str_ireplace('.mp3', '-max.jpg', $mp3);
                if(!is_file($mp4_path)  && is_file(str_ireplace('.mp3', '.jpg', $mp3)) && is_file(str_ireplace('.mp3', '.lrc', $mp3))){
                    $todos[]     = [
                        'mp3'   => $mp3,
                        'name'  => $name,
                        'cover' => $cover_path,
                        'mp4'   => $mp4_path,
                    ];
                }
            }
            $output->writeln(sprintf("其中，%d 首需要生成 MV", count($todos)));
        }
        if($todos){
            $path = [
                'ffmpeg.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffprobe',
            ];
            foreach($todos as $todo){
                $output->writeln("开始 {$todo['name']} 的转换");
                debug('begin');
                if(!is_file($todo['cover'])){
                    $output->writeln('生成封面');
                    $this->generateCover($todo);
                }
                $lrc_path = str_ireplace('.mp3', '.lrc', $todo['mp3']);
                $srt_path = str_ireplace('.mp3', '.srt', $todo['mp3']);
                // $output->writeln( var_export([$todo['mp3'], $lrc_path, $srt_path], true));
                if(!is_file($srt_path)){
                    $output->writeln('转换歌词为字幕');
                    $this->lrc2srt(file_get_contents($lrc_path), $srt_path);
                    if(!is_file($srt_path)){
                        $output->error("字幕转换失败");
                        return;
                    }
                }
                $this->generateMv($path, $todo);
                debug('end');
                $output->writeln(sprintf("完成 %s 的转换耗时 %d 秒", $todo['name'], debug('begin', 'end')));
            }
        }
    }

    protected function generateCover($todo){
        $file           = public_path().'storage/black.jpg';
        $cover_path     = public_path()."storage/{$todo['name']}.jpg";
        $cover_tmp_path = str_ireplace('-max.jpg', '-tmp.jpg', $todo['cover']);
        $cover_name     = $todo['name'];
        $image          = \think\Image::open($file);
        $water          = \think\Image::open($cover_path)->thumb(200, 200, 6)->save($cover_tmp_path);
        $image->thumb(1276, 720, 6);
        $image->text($cover_name, base_path().'../data/STHeiti Medium.ttc', 24, '#ffffff', 2, 50, 0);
        $image->water($cover_tmp_path, 5)->save($todo['cover']);
    }

    protected function lrc2srt($str, $target){
        $content = lrc2srt($str);
        file_put_contents($target, $content);
    }

    protected function generateMv($path, $todo){
        $ffmpeg = FFMpeg::create($path);
        // 获取音频长度
        $cover_path = $todo['cover'];
        $mp3_path   = $todo['mp3'];
        $mp4_path   = $todo['mp4'];
        $srt_path   = str_ireplace('.mp3', '.srt', $todo['mp3']);
        $advancedMedia = $ffmpeg->openAdvanced([]);
        $advancedMedia->setInitialParameters(['-loop', '1', '-i', $cover_path, '-i', $mp3_path,
            '-c:v', 'libx264',
            '-c:a', 'copy',
            '-vf', 'subtitles='.$srt_path,
            '-shortest', $mp4_path]);
        $advancedMedia->save();
    }
}
