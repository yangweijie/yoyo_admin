<?php
namespace app\admin\controller;

use app\admin\model\Role as RoleModel;
use app\admin\model\Menu;
use think\Exception;
use think\facade\View;
use util\Tree;
use think\facade\Db;

/**
 * 角色控制器
 * @package app\admin\controller
 */
class Role extends Admin
{
	public function index(){
		cookie('__forward__', $_SERVER['REQUEST_URI']);
		return View::fetch('', ['title' => '角色管理']);
	}

	/**
	 * 新增
	 * @return mixed
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function add()
	{
		// 保存数据
		if ($this->request->isPost()) {
			$data = $this->request->post();

			if (!isset($data['menu_auth'])) {
				$data['menu_auth'] = [];
			} else {
				$data['menu_auth'] = explode(',', $data['menu_auth']);
			}
			// 验证
			try {
				$error = '';
				$result = $this->validate($data, 'Role');
			}catch (\Exception $e){
				$error = $e->getMessage();
			}

			// 验证失败 输出错误信息
			if ($error) $this->error($error);

			// 非超级管理员检查可添加角色
			if (session('user_auth.role') != 1) {
				$role_list = RoleModel::getChildsId(session('user_auth.role'));
				if ($data['pid'] != session('user_auth.role') && !in_array($data['pid'], $role_list)) {
					$this->error('所属角色设置错误，没有权限添加该角色');
				}
			}

			// 非超级管理员检查可添加的节点权限
			if (session('user_auth.role') != 1) {
				$menu_auth = RoleModel::where('id', session('user_auth.role'))->value('menu_auth');
				$menu_auth = json_decode($menu_auth, true);
				$menu_auth = array_intersect($menu_auth, $data['menu_auth']);
				$data['menu_auth'] = $menu_auth;
			}

			// 添加数据
			if ($role = RoleModel::create($data)) {
				$this->success('新增成功', url('index'));
			} else {
				$this->error('新增失败');
			}
		}

		// 菜单列表
		$menus = cache('access_menus');
		if (!$menus) {
			$modules = Db::name('admin_module')->where('status', 1)->column('title', 'name');
			$map = [];
			// 非超级管理员角色，只能分配当前角色所拥有的权限
			if (session('user_auth.role') != 1) {
				$menu_auth = RoleModel::where('id', session('user_auth.role'))->value('menu_auth');
				$menu_auth = json_decode($menu_auth, true);
				$map[] = ['id', 'in', $menu_auth];
			}

			// 当前用户能分配的所有菜单
			$menus = Menu::where('module', 'in', array_keys($modules))
				->where($map)
				->order('module,sort,id')
				->column('id,pid,sort,url_value,title,icon,module');

			// 按模块分组菜单
			$moduleMenus = [];
			foreach ($menus as $key => $menu) {
				if (!isset($moduleMenus[$menu['module']])) {
					$moduleMenus[$menu['module']] = [
						'title' => isset($modules[$menu['module']]) ? $modules[$menu['module']] : '未知',
						'menus' => [$menu]
					];
				} else {
					$moduleMenus[$menu['module']]['menus'][] = $menu;
				}
			}

			// 层级化每个模块的菜单
			foreach ($moduleMenus as $key => $module) {
				$menu = Tree::toLayer($module['menus']);
				$moduleMenus[$key]['menus'] = $this->buildJsTree($menu);
			}
			$menus = $moduleMenus;

			// 非开发模式，缓存菜单
			if (config('app.develop_mode') == 0) {
				cache('access_menus', $menus);
			}
		}

		if (session('user_auth.role') != 1) {
			$role_list = RoleModel::getTree(null, false, session('user_auth.role'));
		} else {
			$role_list = RoleModel::getTree();
		}

		return View::fetch('', [
			'title'       => '新增',
			'role_list'   => $role_list,
			'module_list' => Menu::where('pid', 0)->column('title', 'id'),
			'curr_tab'    => 0,
			'menus'       => $menus,
		]);
	}

	/**
	 * 编辑
	 * @param null $id 角色id
	 * @return mixed
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function edit($id = null)
	{
		if ($id === null) $this->error('缺少参数');
		if ($id == 1) $this->error('超级管理员不可修改');

		// 非超级管理员检查可编辑角色
		if (session('user_auth.role') != 1) {
			$role_list = RoleModel::getChildsId(session('user_auth.role'));
			if (!in_array($id, $role_list)) {
				$this->error('权限不足，当前没有编辑该角色的权限！');
			}
		}

		// 保存数据
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if (!isset($data['menu_auth'])) {
				$data['menu_auth'] = [];
			} else {
				$data['menu_auth'] = explode(',', $data['menu_auth']);
			}
			// 验证
			$result = $this->validate($data, 'Role');
			// 验证失败 输出错误信息
			if (true !== $result) $this->error($result);

			// 非超级管理员检查可添加角色
			if (session('user_auth.role') != 1) {
				$role_list = RoleModel::getChildsId(session('user_auth.role'));
				if ($data['pid'] != session('user_auth.role') && !in_array($data['pid'], $role_list)) {
					$this->error('所属角色设置错误，没有权限添加该角色');
				}
			}

			// 检查所属角色不能是自己当前角色及其子角色
			$role_list = RoleModel::getChildsId($data['id']);
			if ($data['id'] == $data['pid'] || in_array($data['pid'], $role_list)) {
				$this->error('所属角色设置错误，禁止设置为当前角色及其子角色。');
			}

			// 非超级管理员检查可添加的节点权限
			if (session('user_auth.role') != 1) {
				$menu_auth = RoleModel::where('id', session('user_auth.role'))->value('menu_auth');
				$menu_auth = json_decode($menu_auth, true);
				$menu_auth = array_intersect($menu_auth, $data['menu_auth']);
				$data['menu_auth'] = $menu_auth;
			}

			if (RoleModel::update($data)) {
				// 更新成功，循环处理子角色权限
				RoleModel::resetAuth($id, $data['menu_auth']);
				role_auth();
				// 记录行为
				action_log('role_edit', 'admin_role', $id, UID, $data['name']);
				$this->success('编辑成功', url('index'));
			} else {
				$this->error('编辑失败');
			}
		}

		// 获取数据
		$info = RoleModel::find($id);

		if (session('user_auth.role') != 1) {
			$role_list = RoleModel::getTree($id, false, session('user_auth.role'));
		} else {
			$role_list = RoleModel::getTree($id, '顶级角色');
		}

		$modules = Db::name('admin_module')->where('status', 1)->column('title', 'name');
		$map = [];
		// 非超级管理员角色，只能分配当前角色所拥有的权限
		if (session('user_auth.role') != 1) {
			$menu_auth = RoleModel::where('id', session('user_auth.role'))->value('menu_auth');
			$menu_auth = json_decode($menu_auth, true);
			$map[] = ['id', 'in', $menu_auth];
		}

		// 当前用户能分配的所有菜单
		$menus = Menu::where('module', 'in', array_keys($modules))
			->where($map)
			->order('module,sort,id')
			->column('id,pid,sort,url_value,title,icon,module');

		// 按模块分组菜单
		$moduleMenus = [];
		foreach ($menus as $key => $menu) {
			if (!isset($moduleMenus[$menu['module']])) {
				$moduleMenus[$menu['module']] = [
					'title' => isset($modules[$menu['module']]) ? $modules[$menu['module']] : '未知',
					'menus' => [$menu]
				];
			} else {
				$moduleMenus[$menu['module']]['menus'][] = $menu;
			}
		}

		// 层级化每个模块的菜单
		foreach ($moduleMenus as $key => $module) {
			$menu = Tree::toLayer($module['menus']);
			$moduleMenus[$key]['menus'] = $this->buildJsTree($menu, $info);
		}

		View::assign('page_title', '编辑');
		View::assign('role_list', $role_list);
		View::assign('module_list', Menu::where('pid', 0)->column('title', 'id'));
		View::assign('menus', $moduleMenus);
		View::assign('curr_tab', current(array_keys($moduleMenus)));
		View::assign('info', $info);
		return View::fetch('edit');
	}

	/**
	 * 构建jstree代码
	 * @param array $menus 菜单节点
	 * @param array $user 用户信息
	 * @return string
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	private function buildJsTree($menus = [], $user = [])
	{
		$result = '';
		if (!empty($menus)) {
			$option = [
				'opened' => true,
				'selected' => false,
				'icon' => '',
			];
			foreach ($menus as $menu) {
				$option['icon'] = $menu['icon'];
				if (isset($user['menu_auth'])) {
					$option['selected'] = in_array($menu['id'], $user['menu_auth']) ? true : false;
				}
				if (isset($menu['child'])) {
					$result .= '<li id="' . $menu['id'] . '" data-jstree=\'' . json_encode($option) . '\'>' . $menu['title'] . ($menu['url_value'] == '' ? '' : ' (' . $menu['url_value'] . ')') . $this->buildJsTree($menu['child'], $user) . '</li>';
				} else {
					$result .= '<li id="' . $menu['id'] . '" data-jstree=\'' . json_encode($option) . '\'>' . $menu['title'] . ($menu['url_value'] == '' ? '' : ' (' . $menu['url_value'] . ')') . '</li>';
				}
			}
		}

		return '<ul>' . $result . '</ul>';
	}

	/**
	 * 删除角色
	 * @param array $record 行为日志
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function delete($record = [])
	{
		return $this->setStatus('delete');
	}

	/**
	 * 启用角色
	 * @param array $record 行为日志
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function enable($record = [])
	{
		return $this->setStatus('enable');
	}

	/**
	 * 禁用角色
	 * @param array $record 行为日志
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function disable($record = [])
	{
		return $this->setStatus('disable');
	}

	/**
	 * 设置角色状态：删除、禁用、启用
	 * @param string $type 类型：delete/enable/disable
	 * @param array $record
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function setStatus($type = '', $record = [])
	{
		$ids = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
		$ids = (array)$ids;

		// 当前角色所能操作的子角色
		$role_list = RoleModel::getChildsId(session('user_auth.role'));
		if (session('user_auth.role') != 1 && !$role_list) {
			$this->error('权限不足，没有可操作的角色');
		}

		foreach ($ids as $id) {
			if ($id == 1) {
				// 跳过默认角色
				continue;
			}

			// 非超级管理员检查可管理角色
			if (session('user_auth.role') != 1) {
				if (!in_array($id, $role_list)) {
					$this->error('权限不足，禁止操作角色ID：' . $id);
				}
			}

			switch ($type) {
				case 'enable':
					if (false === RoleModel::where('id', $id)->update(['status' => 1])) {
						$this->error('启用失败，角色ID：' . $id);
					}
					break;
				case 'disable':
					if (false === RoleModel::where('id', $id)->update(['status' => 0])) {
						$this->error('禁用失败，角色ID：' . $id);
					}
					break;
				case 'delete':
					$all_id = array_merge([$id], RoleModel::getChildsId($id));

					if (false === RoleModel::where('id', 'in', $all_id)->delete()) {
						$this->error('删除失败，角色ID：' . $id);
					}
					break;
				default:
					$this->error('非法操作');
			}
		}

		$this->success('操作成功');
	}

	/**
	 * 快速编辑
	 * @param array $record 行为日志
	 * @return mixed
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	public function quickEdit($record = [])
	{
		$id = input('post.pk', '');
		$field = input('post.name', '');
		$value = input('post.value', '');

		// 非超级管理员检查可操作的角色
		if (session('user_auth.role') != 1) {
			$role_list = RoleModel::getChildsId(session('user_auth.role'));
			if (!in_array($id, $role_list)) {
				$this->error('权限不足，没有可操作的角色');
			}
		}

		$config = RoleModel::where('id', $id)->value($field);
		$details = '字段(' . $field . ')，原值(' . $config . ')，新值：(' . $value . ')';
		return parent::quickEdit(['role_edit', 'admin_role', $id, UID, $details]);
	}
}
