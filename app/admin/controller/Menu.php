<?php
namespace app\admin\controller;

use app\admin\model\Module as ModuleModel;
use app\admin\model\Menu as MenuModel;
use app\admin\model\Role as RoleModel;
use think\facade\Cache;
use think\facade\View;

/**
 * 节点管理
 * @package app\admin\controller
 */
class Menu extends Admin
{
    /**
     * 节点首页
     * @param string $group 分组
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     * @throws \Exception
     */
    public function index($group = 'admin')
    {
        // 保存模块排序
        if ($this->request->isPost()) {
            $modules = $this->request->post('sort/a');
            if ($modules) {
                $data = [];
                foreach ($modules as $key => $module) {
                    $data[] = [
                        'id'   => $module,
                        'sort' => $key + 1
                    ];
                }
                $MenuModel = new MenuModel();
                if (false !== $MenuModel->saveAll($data)) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            }
        }

        cookie('__forward__', $_SERVER['REQUEST_URI']);
        // 配置分组信息
        $list_group = MenuModel::getGroup();
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url']  = url('index', ['group' => $key]);
        }

        // 模块排序
        if ($group == 'module-sort') {
            $map['status'] = 1;
            $map['pid']    = 0;
            $modules = MenuModel::where($map)->order('sort,id')->column('icon,title', 'id');
            View::assign('modules', $modules);
        } else {
            // 获取节点数据
            $data_list = MenuModel::getMenusByGroup($group);

            $max_level = $this->request->get('max', 0);

            View::assign('menus', $this->getNestMenu($data_list, $max_level));
        }

        return View::fetch('', [
            'title'   => '节点管理',
            'tab_nav' => ['tab_list' => $tab_list, 'curr_tab' => $group],
        ]);
    }

    /**
     * 新增节点
     * @param string $module 所属模块
     * @param string $pid 所属节点id
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     * @throws \Exception
     */
    public function add($module = 'admin', $pid = '')
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');

            // 验证
            $result = $this->validate($data, 'Menu');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);

            // 顶部节点url检查
            if ($data['pid'] == 0 && $data['url_value'] == '' && ($data['url_type'] == 'module_admin' || $data['url_type'] == 'module_home')) {
                $this->error('顶级节点的节点链接不能为空');
            }

            if ($menu = MenuModel::create($data)) {
                // 自动创建子节点
                if ($data['auto_create'] == 1 && !empty($data['child_node'])) {
                    unset($data['icon']);
                    unset($data['params']);
                    $this->createChildNode($data, $menu['id']);
                }
                // 添加角色权限
                if (isset($data['role'])) {
                    $this->setRoleMenu($menu['id'], $data['role']);
                }
                Cache::clear();
                // 记录行为
                $details = '所属模块('.$data['module'].'),所属节点ID('.$data['pid'].'),节点标题('.$data['title'].'),节点链接('.$data['url_value'].')';
                $this->success('新增成功', cookie('__forward__'));
            } else {
                $this->error('新增失败');
            }
        }

        return View::fetch('', [
            'title'   => '新增节点',
            'group'   => input('group', $module),
            'parents' => MenuModel::getMenuTree(0, '', $module),
        ]);
    }

    /**
     * 编辑节点
     * @param int $id 节点ID
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id = 0)
    {
        if ($id === 0) $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post('', null, 'trim');

            try {
                $error = '';
                // 验证
                $result = $this->validate($data, 'Menu');
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            // 验证失败 输出错误信息
            if($error) $this->error($error);

            // 顶部节点url检查
            if ($data['pid'] == 0 && $data['url_value'] == '' && ($data['url_type'] == 'module_admin' || $data['url_type'] == 'module_home')) {
                $this->error('顶级节点的节点链接不能为空');
            }

            // 设置角色权限
            $this->setRoleMenu($data['id'], isset($data['role']) ? $data['role'] : []);

            // 验证是否更改所属模块，如果是，则该节点的所有子孙节点的模块都要修改
            $map['id']     = $data['id'];
            $map['module'] = $data['module'];
            if (!MenuModel::where($map)->find()) {
                MenuModel::changeModule($data['id'], $data['module']);
            }

            if (MenuModel::update($data)) {
                Cache::clear();
                $this->success('编辑成功', cookie('__forward__'));
            } else {
                $this->error('编辑失败');
            }
        }

        // 获取数据
        $info = MenuModel::find($id);
        // 拥有该节点权限的角色
        $info['role'] = RoleModel::getRoleWithMenu($id);

        return View::fetch('', [
            'title'     => '编辑节点',
            'parents'   => MenuModel::getMenuTree(0, '', $info['module']),
            'form_data' => $info,
        ]);
    }

    /**
     * 设置角色权限
     * @param string $role_id 角色id
     * @param array $roles 角色id
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \Exception
     */
    private function setRoleMenu($role_id = '', $roles = [])
    {
        $RoleModel = new RoleModel();

        // 该节点的所有子节点，包括本身节点
        $menu_child   = MenuModel::getChildsId($role_id);
        $menu_child[] = (int)$role_id;
        // 该节点的所有上下级节点
        $menu_all = MenuModel::getLinkIds($role_id);
        $menu_all = array_map('strval', $menu_all);

        if (!empty($roles)) {
            // 拥有该节点的所有角色id及节点权限
            $role_menu_auth = RoleModel::getRoleWithMenu($role_id, true);
            // 已有该节点权限的角色id
            $role_exists = array_keys($role_menu_auth);
            // 新节点权限的角色
            $role_new = $roles;
            // 原有权限角色差集
            $role_diff = array_diff($role_exists, $role_new);
            // 新权限角色差集
            $role_diff_new = array_diff($role_new, $role_exists);
            // 新节点角色权限
            $role_new_auth = RoleModel::getAuthWithRole($roles);

            // 删除原先角色的该节点权限
            if ($role_diff) {
                $role_del_auth = [];
                foreach ($role_diff as $role) {
                    $auth     = json_decode($role_menu_auth[$role], true);
                    $auth_new = array_diff($auth, $menu_child);
                    $role_del_auth[] = [
                        'id'        => $role,
                        'menu_auth' => array_values($auth_new)
                    ];
                }
                if ($role_del_auth) {
                    $RoleModel->saveAll($role_del_auth);
                }
            }

            // 新增权限角色
            if ($role_diff_new) {
                $role_update_auth = [];
                foreach ($role_new_auth as $role => $auth) {
                    $auth = json_decode($auth, true);
                    if (in_array($role, $role_diff_new)) {
                        $auth = array_unique(array_merge($auth, $menu_all));
                    }
                    $role_update_auth[] = [
                        'id'        => $role,
                        'menu_auth' => array_values($auth)
                    ];
                }
                if ($role_update_auth) {
                    $RoleModel->saveAll($role_update_auth);
                }
            }
        } else {
            $role_menu_auth = RoleModel::getRoleWithMenu($role_id, true);
            $role_del_auth  = [];
            foreach ($role_menu_auth as $role => $auth) {
                $auth     = json_decode($auth, true);
                $auth_new = array_diff($auth, $menu_child);
                $role_del_auth[] = [
                    'id'        => $role,
                    'menu_auth' => array_values($auth_new)
                ];
            }
            if ($role_del_auth) {
                $RoleModel->saveAll($role_del_auth);
            }
        }
    }

    /**
     * 删除节点
     * @param array $record 行为日志内容
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($record = [])
    {
        $id = $this->request->param('id');
        $menu = MenuModel::where('id', $id)->find();

        // if ($menu['system_menu'] == '1') $this->error('系统节点，禁止删除');

        // 获取该节点的所有后辈节点id
        $menu_childs = MenuModel::getChildsId($id);

        // 要删除的所有节点id
        $all_ids = array_merge([(int)$id], $menu_childs);

        // 删除节点
        if (MenuModel::destroy($all_ids)) {
            Cache::clear();
            // 记录行为
            $details = '节点ID('.$id.'),节点标题('.$menu['title'].'),节点链接('.$menu['url_value'].')';
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 保存节点排序
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (!empty($data)) {
                $menus = $this->parseMenu($data['menus']);
                foreach ($menus as $menu) {
                    if ($menu['pid'] == 0) {
                        continue;
                    }
                    MenuModel::update($menu);
                }
                Cache::clear();
                $this->success('保存成功');
            } else {
                $this->error('没有需要保存的节点');
            }
        }
        $this->error('非法请求');
    }

    /**
     * 添加子节点
     * @param array $data 节点数据
     * @param string $pid 父节点id
     * @author 蔡伟明 <314013107@qq.com>
     */
    private function createChildNode($data = [], $pid = '')
    {
        $url_value  = substr($data['url_value'], 0, strrpos($data['url_value'], '/')).'/';
        $child_node = [];
        $data['pid'] = $pid;

        foreach ($data['child_node'] as $item) {
            switch ($item) {
                case 'add':
                    $data['title'] = '新增';
                    break;
                case 'edit':
                    $data['title'] = '编辑';
                    break;
                case 'delete':
                    $data['title'] = '删除';
                    break;
                case 'enable':
                    $data['title'] = '启用';
                    break;
                case 'disable':
                    $data['title'] = '禁用';
                    break;
                case 'quickedit':
                    $data['title'] = '快速编辑';
                    break;
            }
            $data['url_value']   = $url_value.$item;
            $data['create_time'] = $this->request->time();
            $data['update_time'] = $this->request->time();
            $child_node[] = $data;
        }

        if ($child_node) {
            $MenuModel = new MenuModel();
            $MenuModel->insertAll($child_node);
        }
    }

    /**
     * 递归解析节点
     * @param array $menus 节点数据
     * @param int $pid 上级节点id
     * @author 蔡伟明 <314013107@qq.com>
     * @return array 解析成可以写入数据库的格式
     */
    private function parseMenu($menus = [], $pid = 0)
    {
        $sort   = 1;
        $result = [];
        foreach ($menus as $menu) {
            $result[] = [
                'id'   => (int)$menu['id'],
                'pid'  => (int)$pid,
                'sort' => $sort,
            ];
            if (isset($menu['children'])) {
                $result = array_merge($result, $this->parseMenu($menu['children'], $menu['id']));
            }
            $sort ++;
        }
        return $result;
    }

    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $pid 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    private function getNestMenu($lists = [], $max_level = 0, $pid = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['pid'] == $pid) {
                $disable  = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item '.$disable.'" data-id="'.$value['id'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content"><i class="'.$value['icon'].'"></i> '.$value['title'];
                if ($value['url_value'] != '') {
                    $result .= '<span class="link"><i class="fa fa-link"></i> '.$value['url_value'].'</span>';
                }
                $result .= '<div class="action">';
                $result .= '<a href="'.strtolower(admin_url('add', ['module' => $value['module'], 'pid' => $value['id']])).'" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.strtolower(admin_url('edit', ['id' => $value['id']])).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                if ($value['status'] == 0) {
                    // 启用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                } else {
                    // 禁用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                }
                $result .= '<a href="'.strtolower(admin_url('delete', ['id' => $value['id'], 'table' => 'admin_menu'])).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }

    /**
     * 启用节点
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function enable($record = [])
    {
        $id      = input('param.ids');
        $menu    = MenuModel::where('id', $id)->find();
        $details = '节点ID('.$id.'),节点标题('.$menu['title'].'),节点链接('.$menu['url_value'].')';
        $this->setStatus('enable', ['menu_enable', 'admin_menu', $id, UID, $details]);
    }

    /**
     * 禁用节点
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function disable($record = [])
    {
        $id      = input('param.ids');
        $menu    = MenuModel::where('id', $id)->find();
        $details = '节点ID('.$id.'),节点标题('.$menu['title'].'),节点链接('.$menu['url_value'].')';
        $this->setStatus('disable', ['menu_disable', 'admin_menu', $id, UID, $details]);
    }

    /**
     * 设置状态
     * @param string $type 类型
     * @param array $record 行为日志
     * @author 小乌 <82950492@qq.com>
     */
    public function setStatus($type = '', $record = [])
    {
        $id = input('param.ids');

        $status = $type == 'enable' ? 1 : 0;

        if (false !== MenuModel::where('id', $id)->update(['status'=>$status])) {
            Cache::clear();
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
