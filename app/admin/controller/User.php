<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\model\User as UserModel;
use app\admin\model\Role;
use app\admin\model\Menu;
use think\facade\View;

class User extends Admin
{
    public function index(){
        cookie('__forward__', $_SERVER['REQUEST_URI']);
        return View::fetch('', ['title'=>'用户列表']);
    }

    public function edit($id = ''){
        if($this->request->isPost()){
            $this->success('123', cookie('__forward__'));
        }else{
            return View::fetch('', ['title'=>'编辑用户', 'info'=>UserModel::find($id)]);
        }
    }

    public function login()
    {
         if ($this->request->isPost()) {
            // 获取post数据
            $data = $this->request->post();
            $rememberme = isset($data['remember-me']) ? true : false;

            // 验证数据
            $result = $this->validate($data, 'User.signin');
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->error($result);
            }

            // 登录
            $UserModel = new UserModel;
            $uid = $UserModel->login($data['username'], $data['password'], $rememberme);
            if ($uid) {
                // 记录行为
                $this->jumpUrl();
            } else {
                $this->error($UserModel->getError());
            }
        } else {
            if (is_signin()) {
                $this->jumpUrl();
            } else {
                return View::fetch('', ['title'=>'登录']);
            }
        }
    }

    /**
     * 跳转到第一个有权限访问的url
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed|string
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
        $url   = 'index/index';
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
