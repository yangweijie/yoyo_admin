<?php
namespace app\index\controller;

use app\BaseController;
use Clickfwd\Yoyo\Yoyo as yoyoClass;
use think\facade\View;
use SMEncryptorClass;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use Intervention\Image\Image;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;

class Test extends BaseController
{
	public function phpinfo(){
		phpinfo();
		return '';
	}

	public function cover(){
        $file = public_path().'storage/black.jpg';
        $cover_path = public_path().'storage/121.終生老友-张诗莉-朱彦安.jpg';
        $cover_max_path = public_path().'storage/121.終生老友-张诗莉-朱彦安-max.jpg';
        $cover_name = pathinfo($cover_path, PATHINFO_FILENAME);
        $image = \think\Image::open($file);
        $water = \think\Image::open($cover_path)->thumb(200, 200, 6)->save($cover_max_path);
        $image->thumb(1276, 720, 6);
        $image->text($cover_name, '../data/STHeiti Medium.ttc', 24, '#ffffff', 2, 50, 0);
        $image->water($cover_max_path, 5)->save(public_path().'storage/cover.jpg');
    }

    public function mv(){
    	set_time_limit(0);
    	ds('begin');
    	debug('begin');
    	$path = [
		    'ffmpeg.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffmpeg',
		    'ffprobe.binaries' => '/usr/local/Cellar/ffmpeg/5.1.2/bin/ffprobe',
    	];
    	$ffmpeg = FFMpeg::create($path);
    	// 获取音频长度
    	$cover_path = public_path().'storage/cover.jpg';
    	$mp3_path   = public_path().'storage/121.終生老友-张诗莉-朱彦安.mp3';
    	$mp4_path   = public_path().'storage/121.終生老友-张诗莉-朱彦安.mp4';
    	$srt_path   = public_path().'storage/121.終生老友-张诗莉-朱彦安.srt';
    	$inputs     = [
    		$cover_path,
    		$mp3_path,

    	];
    	$advancedMedia = $ffmpeg->openAdvanced([]);
    	$advancedMedia->setInitialParameters(['-loop', '1', '-i', $cover_path, '-i', $mp3_path,
    		'-c:v', 'libx264',
    		'-c:a', 'copy',
    		'-vf', 'subtitles='.$srt_path,
    		'-shortest', $mp4_path]);
    	$advancedMedia->save();
    	debug('begin', 'end');
    	ds('end');
    	return debug('begin', 'end');
    	// return $advancedMedia->getFinalCommand(new X264(), public_path().'storage/121.終生老友-张诗莉-朱彦安.mp4');

    	// $advancedMedia->save(new X264(), public_path().'storage/121.終生老友-张诗莉-朱彦安.mp4');
    }
}