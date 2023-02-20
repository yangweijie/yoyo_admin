<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\listener;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;
use think\facade\Env;
use think\facade\App;

/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\listener
 * @author CaiWeiMing <314013107@qq.com>
 */
class Config
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @return void
     */
    public function handle($event)
    {
        $module = MODULE;


        // 视图输出字符串内容替换
        $view_replace_str = [
            // 静态资源目录
            '__STATIC__'    => PUBLIC_PATH. 'static',
            // 文件上传目录
            '__UPLOADS__'   => PUBLIC_PATH. 'uploads',
            // JS插件目录
            '__LIBS__'      => PUBLIC_PATH. 'static/libs',
            // 后台CSS目录
            '__ADMIN_CSS__' => PUBLIC_PATH. 'static/admin/css',
            // 后台JS目录
            '__ADMIN_JS__'  => PUBLIC_PATH. 'static/admin/js',
            // 后台IMG目录
            '__ADMIN_IMG__' => PUBLIC_PATH. 'static/admin/img',
            // 前台CSS目录
            '__HOME_CSS__'  => PUBLIC_PATH. 'static/home/css',
            // 前台JS目录
            '__HOME_JS__'   => PUBLIC_PATH. 'static/home/js',
            // 前台IMG目录
            '__HOME_IMG__'  => PUBLIC_PATH. 'static/home/img',
            // 表单项扩展目录
            '__EXTEND_FORM__' => PUBLIC_PATH.'extend/form'
        ];
        $view = config('view');
        // 插件静态资源目录
        $view_replace_str['__PLUGINS__'] = '/plugins';
        // 定义模块资源目录
        $view_replace_str['__MODULE_CSS__'] = PUBLIC_PATH. 'static/'. $module .'/css';
        $view_replace_str['__MODULE_JS__'] = PUBLIC_PATH. 'static/'. $module .'/js';
        $view_replace_str['__MODULE_IMG__'] = PUBLIC_PATH. 'static/'. $module .'img';
        $view_replace_str['__MODULE_LIBS__'] = PUBLIC_PATH. 'static/'. $module .'/libs';
        $view['tpl_replace_string'] = $view_replace_str;

        config($view, 'view');
        // 静态文件目录

        $app_config = config('app');
        $app_config['public_static_path'] =   PUBLIC_PATH. 'static/';
        config($app_config, 'app');

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
        request()->baseFile();
        request()->baseUrl();
    }
}
