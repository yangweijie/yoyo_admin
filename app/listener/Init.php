<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\listener;

use think\helper\Str;
use think\facade\Cache;
use think\facade\Event;
use think\facade\Request;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;

/**
 * 注册钩子
 * @package app\listener
 * @author 蔡伟明 <314013107@qq.com>
 */
class Init
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function handle($event)
    {
        $request = request();
        if ($request->isCli()) {
            return;
        }

        $baseUrl = $request->baseUrl();
        $module  = '';
        if(stripos($baseUrl, '.php') === false){
            if($baseUrl[0] == '/'){
                $baseUrl = substr($baseUrl, 1);
                $module  = explode('/', str_ireplace($request->baseFile().'/', '', $baseUrl))[0];
            }
        }else{
            if(defined('ENTRANCE') && ENTRANCE == 'admin'){
                $module = 'admin';
            }else{
                if(stripos($baseUrl, $request->baseFile().'/') === false){
                    $module = explode('/', str_ireplace($request->baseFile(), '', $baseUrl))[0];
                }else{
                    $module = explode('/', str_ireplace($request->baseFile().'/', '', $baseUrl))[0];
                }
            }
        }
        if(!defined('MODULE')){
            define('MODULE', $module?: trim($request->baseFile(), '.php/'));
        }

        $app_config = config('app');
        // 读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            $system_config = ConfigModel::getConfig();
            // 所有模型配置
            $module_config = ModuleModel::where('config', '<>', '')->column('config', 'name');
            foreach ($module_config as $module_name => $config) {
                $system_config[strtolower($module_name).'_config'] = json_decode($config, true);
            }
            // 非开发模式，缓存系统配置
            if ($system_config['develop_mode'] == 0) {
                cache($system_config, 'system_config');
            }
        }
        // 设置配置信息
        config($system_config, 'app');


        // 获取入口目录
        $base_file = Request::baseFile();
        $base_dir  = substr($base_file, 0, strripos($base_file, '/') + 1);
        if(!defined('PUBLIC_PATH'))
            define('PUBLIC_PATH', $base_dir);

    	 // 如果定义了入口为admin，则修改默认的访问控制器层
        if(defined('ENTRANCE') && ENTRANCE == 'admin') {
            define('ADMIN_FILE', substr($base_file, strripos($base_file, '/') + 1));
            if ($module == '') {
                header("Location: ".$base_file.'/admin', true, 302);exit();
            }
        } else {
            if (MODULE == 'admin') {
                header("Location: ".$base_dir.ADMIN_FILE.'/admin', true, 302);exit();
            }
        }
    }
}
