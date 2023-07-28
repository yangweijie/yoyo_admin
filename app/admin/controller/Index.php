<?php
namespace app\admin\controller;

use Clickfwd\Yoyo\Yoyo as yoyoClass;
use think\facade\Cache;
use think\facade\Env;
use think\facade\View;
/**
 * 首页
 * @package app\admin\controller
 */
class Index extends Admin
{
    public function index(){
        return View::fetch('', ['title'=>'首页']);
    }

     /**
     * 清空系统缓存
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function wipeCache()
    {
        $wipe_cache_type = config('app.wipe_cache_type');
        if (!empty($wipe_cache_type)) {
            foreach ($wipe_cache_type as $item) {
                switch ($item) {
                    case 'TEMP_PATH':
                        array_map('unlink', glob(Env::get('runtime_path'). 'temp/*.*'));
                        break;
                    case 'LOG_PATH':
                        $dirs = (array) glob(Env::get('runtime_path') . 'log/*');
                        foreach ($dirs as $dir) {
                            array_map('unlink', glob($dir . '/*.log'));
                        }
                        array_map('rmdir', $dirs);
                        break;
                    case 'CACHE_PATH':
                        array_map('unlink', glob(Env::get('runtime_path'). 'cache/*.*'));
                        break;
                }
            }
            Cache::clear();
            $this->success('清空成功');
        } else {
            $this->error('请在系统设置中选择需要清除的缓存类型');
        }
    }

	public function yoyo(){
		return (yoyoClass::getInstance())->update();
	}
}