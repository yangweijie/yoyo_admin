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
	public function fg(){

		$client = new FileGetContents(new Psr17Factory());
		$browser = new Browser($client, new Psr17Factory(),['allow_redirects' => true, 'verify'=>false]);
		$post = json_decode('{"timeStamp":"2022-11-11 17:28:16","charset":"utf-8","reqId":"uo20221111172816","certId":"826440345119153","version":"1.0","method":"aggregation.scan.mccQuery","check":"eFWSPXCOggiXpPUUYVekc1l188+ch1P7E55UyFRV1f6ayk3GtIt5kBzlunLsysCJXn7DsQRttHAIu3ZIfL3HwhSyULlLWePVA0NK1hWillCHJee+i/aIh3NbtqSW7to0+iG46cKL6Odc4Ima8fn3GtR/MSBzFXt7IQAN+zFldqA=","bizContent":"p98LxEI6hkImYbIlRd3Z/0uaLVIETk2/XBFbj7WuVO4=","sign":"fuq6dgOcYit5jXmRtG+HrJlqqKE49A2ubGC+vrXC4WtSFL19k5HB8BbTe2jf6EW5K45AEwsFNAd1zPHG0NtWuBK8DiCwAaHx0FmEvz6ykT3D9MdglEETe469lYK5aQz/2FQp/spURqqvrLguLhG1cg37JL5KK7zfO/WPuqYVPk4="}', true);
		$response = $browser->submitForm('https://appdev.ysepay.com/openapi/aggregation/scan/mccQuery', $post);
		// dump($response);
		// dump($response->getBody());
		return (string)$response->getBody();
	}

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