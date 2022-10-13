<?php

namespace app\admin\controller;

use app\BaseController;
use app\admin\model\User as UserModel;
use app\admin\model\Role;
use app\admin\model\Menu;
use think\Exception;
use think\facade\View;

class User extends Admin
{
	public function index()
	{
		cookie('__forward__', $_SERVER['REQUEST_URI']);
		return View::fetch('', ['title' => '用户列表']);
	}


	public function add()
	{
		// 保存数据
		if ($this->request->isPost()) {
			$data = $this->request->post();

			try {
				$error = '';
				// 验证
				$result = $this->validate($data, 'User');
			}catch (\Exception $e){
				$error = $e->getMessage();
			}

			// 验证失败 输出错误信息
			if ($error) $this->error($error);

			// 非超级管理需要验证可选择角色
			if (session('user_auth.role') != 1) {
				if ($data['role'] == session('user_auth.role')) {
					$this->error('禁止创建与当前角色同级的用户');
				}
				$role_list = Role::getChildsId(session('user_auth.role'));
				if (!in_array($data['role'], $role_list)) {
					$this->error('权限不足，禁止创建非法角色的用户');
				}
			}

			if ($user = UserModel::create($data)) {
				$this->success('新增成功', cookie('__forward__'));
			} else {
				$this->error('新增失败');
			}
		}

		// 角色列表
		if (session('user_auth.role') != 1) {
			$role_list = Role::getTree(null, false, session('user_auth.role'));
		} else {
			$role_list = Role::getTree(null, false);
		}
		return View::fetch('', ['title'=>'添加用户', 'info'=>new UserModel()]);
	}

	public function edit($id = '')
	{
		if ($this->request->isPost()) {
			$data = $this->request->post();

			// 禁止修改超级管理员的角色和状态
			if ($data['id'] == 1 && $data['role'] != 1) {
				$this->error('禁止修改超级管理员角色');
			}

			// 禁止修改超级管理员的状态
			$data['status'] = $data['status'] ?? 0; // 修复checkbox 提交不选中不提交的bug
			if ($data['id'] == 1 && $data['status'] != 1) {
				$this->error('禁止修改超级管理员状态');
			}

			try{
				$error = '';
				// 验证
				$result = $this->validate($data, 'User.update');
			}catch (\Exception $e){
				$error = $e->getMessage();
			}

			// 验证失败 输出错误信息
			if ($error) $this->error($error);

			// 如果没有填写密码，则不更新密码
			if ($data['password'] == '') {
				unset($data['password']);
			}

			// 非超级管理需要验证可选择角色
			if (session('user_auth.role') != 1) {
				if ($data['role'] == session('user_auth.role')) {
					$this->error('禁止修改为当前角色同级的用户');
				}
				$role_list = Role::getChildsId(session('user_auth.role'));
				if (!in_array($data['role'], $role_list)) {
					$this->error('权限不足，禁止修改为非法角色的用户');
				}
			}

			if (UserModel::update($data)) {
				$this->success('编辑成功', cookie('__forward__'));
			} else {
				$this->error('编辑失败');
			}
		} else {
			return View::fetch('', ['title' => '编辑用户', 'info' => UserModel::where('id', $id)->withoutField('password')->find()]);
		}
	}

	/**
	* 删除用户
	* @param array $ids 用户id
	* @author 蔡伟明 <314013107@qq.com>
	* @throws \think\Exception
	* @throws \think\exception\PDOException
	*/
	public function delete($ids = [])
	{
		$this->setStatus('delete');
	}

	public function login()
	{
		if ($this->request->isPost()) {
			// 获取post数据
			$data = $this->request->post();
			$rememberme = isset($data['remember-me']) ? true : false;

			// 验证数据
			$result = $this->validate($data, 'User.signin');
			if (true !== $result) {
				// 验证失败 输出错误信息
				$this->error($result);
			}
			// 登录
			$UserModel = new UserModel;
			$uid = $UserModel->login($data['username'], $data['password'], $rememberme);
			if ($uid) {
				$this->jumpUrl();
			} else {
				$this->error($UserModel->getError());
			}
		} else {
			if (is_signin()) {
				$this->jumpUrl();
			} else {
				return View::fetch('', ['title' => '登录']);
			}
		}
	}

	/**
	 * 跳转到第一个有权限访问的url
	 * @return mixed|string
	 * @author 蔡伟明 <314013107@qq.com>
	 */
	private function jumpUrl()
	{
		if (session('user_auth.role') == 1) {
			$this->success('登录成功', url('/index/index'));
		}

		$default_module = Role::where('id', session('user_auth.role'))->value('default_module');
		$menu = Menu::find($default_module);
		if (!$menu) {
			$this->error('当前角色未指定默认跳转模块！');
		}

		if ($menu['url_type'] == 'link') {
			$this->success('登录成功', $menu['url_value']);
		}

		$menu_url = explode('/', $menu['url_value']);
		role_auth();

		$menus = Menu::getSidebarMenu($default_module, $menu['module'], $menu_url[1]);
		$url = 'index/index';
		foreach ($menus as $key => $menu) {
			if (!empty($menu['url_value'])) {
				$url = $menu['url_value'];
				break;
			}
			if (!empty($menu['child'])) {
				$url = $menu['child'][0]['url_value'];
				break;
			}
		}

		if ($url == '') {
			$this->error('权限不足');
		} else {
			ds(6);
			$this->success('登录成功', $url);
		}
	}

	public function logout()
	{
		session(null);
		cookie('uid', null);
		cookie('signin_token', null);

		$this->redirect('login');
	}
}
