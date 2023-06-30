<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\model\Menu as MenuModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Icon as IconModel;
use app\admin\model\Role as RoleModel;
use Exception;
use think\admin\service\QueueService;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Db;
use think\facade\App;
use think\facade\View;

/**
 * 后台公共控制器
 * @package app\admin\controller
 */
class Admin extends BaseController
{
    /**
     * 初始化
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     */
    protected function initialize()
    {
        // 是否拒绝ie浏览器访问
        if (config('system.deny_ie') && get_browser_type() == 'ie') {
            $this->redirect('admin/ie/index');
        }
        if(!in_array($this->request->controller().'/'.$this->request->action(), ['User/login', 'User/logout'])){
	        // 判断是否登录，并定义用户ID常量
	        defined('UID') or define('UID', $this->isLogin());

	        // 设置当前角色菜单节点权限
	        role_auth();

	        // 检查权限
	        if (!RoleModel::checkAuth()) $this->error('权限不足！');

	        // 设置分页参数
	        $this->setPageParam();

	        // 如果不是ajax请求，则读取菜单
	        if (!$this->request->isAjax()) {
                // 读取顶部菜单
                View::assign('_top_menus', MenuModel::getTopMenu(config('app.top_menu_max'), '_top_menus'));
                // 读取全部顶级菜单
                View::assign('_top_menus_all', MenuModel::getTopMenu('', '_top_menus_all'));
	            // 获取侧边栏菜单
	            View::assign('_sidebar_menus', MenuModel::getSidebarMenu());
	            // 获取面包屑导航
	            View::assign('_location', MenuModel::getLocation('', true));
	            // 构建侧栏
	            $data = [
	                'table'      => 'admin_config', // 表名或模型名
	                'prefix'     => 1,
	                'module'     => 'admin',
	                'controller' => 'system',
	                'action'     => 'quickedit',
	            ];
	            $table_token = substr(sha1('_aside'), 0, 8);
	            session($table_token, $data);
	            $settings = [
	                [
	                    'title'   => '站点开关',
	                    'tips'    => '站点关闭后将不能访问',
	                    'checked' => Db::name('admin_config')->where('id', 1)->value('value'),
	                    'table'   => $table_token,
	                    'id'      => 1,
	                    'field'   => 'value'
	                ]
	            ];

	        }
        }

    }

    /**
     * 获取当前操作模型
     * @author 蔡伟明 <314013107@qq.com>
     * @return object|\think\db\Query
     */
    final protected function getCurrModel()
    {
        $table_token = input('param._t', '');
        $module      = MODULE;
        $controller  = parse_name($this->request->controller());

        $table_token == '' && $this->error('缺少参数');
        !session('?'.$table_token) && $this->error('参数错误');

        $table_data = session($table_token);
        $table      = $table_data['table'];

        $Model = null;
        if ($table_data['prefix'] == 2) {
            // 使用模型
            try {
                $Model = App::model($table);
            } catch (Exception $e) {
                $this->error('找不到模型：'.$table);
            }
        } else {
            // 使用DB类
            $table == '' && $this->error('缺少表名');
            if ($table_data['module'] != $module || $table_data['controller'] != $controller) {
                $this->error('非法操作');
            }

            $Model = $table_data['prefix'] == 0 ? Db::table($table) : Db::name($table);
        }

        return $Model;
    }

    /**
     * 设置分页参数
     * @author 蔡伟明 <314013107@qq.com>
     */
    final protected function setPageParam()
    {
        _system_check();
        $list_rows             = input('?param.list_rows') ? input('param.list_rows') : config('paginate.list_rows');
        $paginate              = config('app.paginate');
        $paginate['list_rows'] = $list_rows;
        $paginate['query']     = input('get.');
        config($paginate, 'paginate');
    }

    /**
     * 检查是否登录，没有登录则跳转到登录页面
     * @author 蔡伟明 <314013107@qq.com>
     * @return int
     */
    final protected function isLogin()
    {
        // 判断是否登录
        if ($uid = is_signin()) {
            // 已登录
            return $uid;
        } else {
            // 未登录
            $this->redirect('/admin.php/user/login');
        }
    }

    /**
     * 禁用
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function disable($record = [])
    {
        return $this->setStatus('disable', $record);
    }

    /**
     * 启用
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function enable($record = [])
    {
        return $this->setStatus('enable', $record);
    }

    /**
     * 启用
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($record = [])
    {
        return $this->setStatus('delete', $record);
    }

    /**
     * 快速编辑
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function quickEdit($record = [])
    {
        $field           = input('post.name', '');
        $value           = input('post.value', '');
        $type            = input('post.type', '');
        $id              = input('post.pk', '');
        $validate        = input('post.validate', '');
        $validate_fields = input('post.validate_fields', '');

        $field == '' && $this->error('缺少字段名');
        $id    == '' && $this->error('缺少主键值');

        $Model = $this->getCurrModel();
        $protect_table = [
            '__ADMIN_USER__',
            '__ADMIN_ROLE__',
            database_config('database.prefix').'admin_user',
            database_config('database.prefix').'admin_role',
        ];

        // 验证是否操作管理员
        if (in_array($Model->getTable(), $protect_table) && $id == 1) {
            $this->error('禁止操作超级管理员');
        }

        // 验证器
        if ($validate != '') {
            $validate_fields = array_flip(explode(',', $validate_fields));
            if (isset($validate_fields[$field])) {
                $result = $this->validate([$field => $value], $validate.'.'.$field);
                if (true !== $result) $this->error($result);
            }
        }

        switch ($type) {
            // 日期时间需要转为时间戳
            case 'combodate':
                $value = strtotime($value);
                break;
            // 开关
            case 'switch':
                $value = $value == 'true' ? 1 : 0;
                break;
            // 开关
            case 'password':
                $value = \Hash::make((string)$value);
                break;
        }

        // 主键名
        $pk     = $Model->getPk();
        $result = $Model->where($pk, $id)->update([$field=>$value]);

        cache('hook_plugins', null);
        cache('system_config', null);
        cache('access_menus', null);
        if (false !== $result) {
            // 记录行为日志
            if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 自动创建添加页面
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     * @throws \think\Exception
     */
    public function add()
    {
        // 获取表单项
        $cache_name = MODULE.'/'.parse_name($this->request->controller()).'/add';
        $cache_name = strtolower($cache_name);
        $form       = Cache::get($cache_name, []);
        if (!$form) {
            $this->error('自动新增数据不存在，请重新打开此页面');
        }

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            $_pop = $this->request->get('_pop');

            // 验证
            if ($form['validate'] != '') {
                $result = $this->validate($data, $form['validate']);
                if(true !== $result) $this->error($result);
            }

            // 是否需要自动插入时间
            if ($form['auto_time'] != '') {
                $now_time = $this->request->time();
                foreach ($form['auto_time'] as $item) {
                    if (strpos($item, '|')) {
                        list($item, $format) = explode('|', $item);
                        $data[$item] = date($format, $now_time);
                    } else {
                        $data[$item] = $form['format'] != '' ? date($form['format'], $now_time) : $now_time;
                    }
                }
            }

            // 插入数据
            if (Db::name($form['table'])->insert($data)) {
                if ($_pop == 1) {
                    $this->success('新增成功', null, '_parent_reload');
                } else {
                    $this->success('新增成功', $form['go_back']);
                }
            } else {
                $this->error('新增失败');
            }
        }

        // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems($form['items'])
            ->fetch();
    }

    /**
     * 设置状态
     * 禁用、启用、删除都是调用这个内部方法
     * @param string $type 操作类型：enable,disable,delete
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($type = '', $record = [])
    {
        $ids   = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
        $ids   = (array)$ids;
        $field = input('param.field', 'status');

        empty($ids) && $this->error('缺少主键');

        $Model = $this->getCurrModel();
        $protect_table = [
            '__ADMIN_USER__',
            '__ADMIN_ROLE__',
            '__ADMIN_MODULE__',
            database_config('database.prefix').'admin_user',
            database_config('database.prefix').'admin_role',
            database_config('database.prefix').'admin_module',
        ];

        // 禁止操作核心表的主要数据
        if (in_array($Model->getTable(), $protect_table) && in_array('1', $ids)) {
            $this->error('禁止操作');
        }

        // 主键名称
        $pk = $Model->getPk();
        $map = [
            [$pk, 'in', $ids]
        ];

        $result = false;
        switch ($type) {
            case 'disable': // 禁用
                $result = $Model->where($map)->update([$field=> 0]);
                break;
            case 'enable': // 启用
                $result = $Model->where($map)->update([$field=>1]);
                break;
            case 'delete': // 删除
                $result = $Model->where($map)->delete();
                break;
            default:
                $this->error('非法操作');
                break;
        }

        if (false !== $result) {
            Cache::clear();
            // 记录行为日志
            if (!empty($record)) {
                call_user_func_array('action_log', $record);
            }
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

	    /**
     * 创建异步任务并返回任务编号
     * @param string $title 任务名称
     * @param string $command 执行内容
     * @param integer $later 延时执行时间
     * @param array $data 任务附加数据
     * @param integer $rscript 任务类型(0单例,1多例)
     * @param integer $loops 循环等待时间
     */
    protected function _queue(string $title, string $command, int $later = 0, array $data = [], int $rscript = 0, int $loops = 0)
    {
        try {
            $queue = QueueService::register($title, $command, $later, $data, $rscript, $loops);
            $this->success(lang('创建任务成功！'), '', $queue->code);
        } catch (Exception $exception) {
            $code = $exception->getData();
            if (is_string($code) && stripos($code, 'Q') === 0) {
                $this->success(lang('任务已经存在，无需再次创建！'), '', $code);
            } else {
				trace_file($exception);
                $this->error($exception->getMessage());
            }
        }
	}
}
