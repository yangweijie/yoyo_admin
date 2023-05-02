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

    // https://www.musicenc.com/
    // https://www.gequbao.com/
    // www.9ku.com
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
            $path = $this->getFFMpegPath();
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
                    $this->lrc2srt($lrc_path, $srt_path);
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

    private function getFFMpegPath(){
        if(IS_WIN){
            return [
                'ffmpeg.binaries' => 'D:/GreenSoft/ffmpeg-6.0-full_build/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'D:/GreenSoft/ffmpeg-6.0-full_build/bin/ffprobe.exe',
            ];
        }else{
            return [
                'ffmpeg.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffprobe',
            ];
        }
    }

    protected function generateCover($todo){
        $file           = public_path().'storage/black.jpg';
        $cover_path     = str_ireplace('.mp3', '.jpg', $todo['mp3']);
        $cover_tmp_path = str_ireplace('-max.jpg', '-tmp.jpg', $todo['cover']);
        $cover_name     = $todo['name'];
        $image          = \think\Image::open($file);
        $water          = \think\Image::open($cover_path)->thumb(200, 200, 6)->save($cover_tmp_path);
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
        // $srt = file_get_contents($lrc_path);
        // $content = lrc2srt($str);
        // if($content){
        //     file_put_contents($target, $content);
        // }
    }

    protected function generateMv($path, $todo){
        $ffmpeg = FFMpeg::create($path);
        // 获取音频长度
        $cover_path = str_ireplace('\\', '/', $todo['cover']);
        $mp3_path   = str_ireplace('\\', '/', $todo['mp3']);
        $mp4_path   = str_ireplace('\\', '/', $todo['mp4']);
        $srt_path   = str_ireplace('.mp3', '.srt', $mp3_path);
        $tmp_srt = '';
        if(stripos($srt_path, "'") !== false){
            $tmp_srt = str_ireplace($todo['name'], (string) time(), $srt_path);
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
        // try{
            $advancedMedia->save();
            // if(!empty($tmp_srt)){
            //     unlink($tmp_srt);
            // }
        // }catch(\Exception $e){
        //     unlink($mp4_path);
            // throw new \Exception($e->getMessage(), $e->getCode());
        // }
    }
}
