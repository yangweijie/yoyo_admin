<?php
namespace app\admin\model;

use think\Model;
use util\Tree;
use app\admin\model\Menu as Menu;

/**
 * 角色模型
 * @package app\admin\model
 */
class Role extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_role';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

	public function renderColumns(){
		return [
			['name' => 'id', 'title'=>'ID', 'type'=>'text'],
			['name' => 'name', 'title'=>'角色名称', 'type'=>'text'],
			['name' => 'parent_name', 'title'=>'上级名称', 'type'=>'text'],
			['name' => 'description', 'title'=>'描述','type'=>'text'],
			['name' => 'create_time', 'title'=>'创建时间', 'type'=>'text'],
			['name'=>  'access','title'=>'是否可登录后台','type'=>'status'],
			['name' => 'status', 'title'=>'状态', 'type'=>'status'],
		];
	}

    // 写入时，将菜单id转成json格式
    public function setMenuAuthAttr($value)
    {
        return json_encode($value);
    }

    // 读取时，将菜单id转为数组
    public function getMenuAuthAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 获取树形角色
     * @param null $id 需要隐藏的角色id
     * @param string $default 默认第一个菜单项，默认为“顶级角色”，如果为false则不显示，也可传入其他名称
     * @param null $filter 角色id，过滤显示指定角色及其子角色
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public static function getTree($id = null, $default = '', $filter = null)
    {
        $result[0]       = '顶级角色';
        $where = [
            ['status', '=', 1]
        ];

        // 排除指定菜单及其子菜单
        $hide_ids = [];
        if ($id !== null) {
            $hide_ids = array_merge([$id], self::getChildsId($id));
        }

        // 过滤显示指定角色及其子角色
        if ($filter !== null) {
            $show_ids = self::getChildsId($filter);

            if (!empty($hide_ids)) {
                $ids = array_diff($show_ids, $hide_ids);
                $where[] = ['id', 'in', $ids];
            } else {
                $where[] = ['id', 'in', $show_ids];
            }
        } else {
            if (!empty($hide_ids)) {
                $where[] = ['id', 'not in', $hide_ids];
            }
        }

        // 获取菜单
        $roles = self::where($where)->column('id,pid,name');
        $pid   = self::where($where)->order('pid')->value('pid');
        $roles = Tree::config(['title' => 'name'])->toList($roles, $pid);
        foreach ($roles as $role) {
            $result[$role['id']] = $role['title_display'];
        }

        // 设置默认菜单项标题
        if ($default != '') {
            $result[0] = $default;
        }

        // 隐藏默认菜单项
        if ($default === false) {
            unset($result[0]);
        }
        return $result;
    }

    /**
     * 获取所有子角色id
     * @param string $pid 父级id
     * @author 蔡伟明 <314013107@qq.com>
     * @return array
     */
    public static function getChildsId($pid = '')
    {
        $ids = self::where('pid', $pid)->column('id');
        foreach ($ids as $value) {
            $ids = array_merge($ids, self::getChildsId($value));
        }
        return $ids;
    }

    /**
     * 检查访问权限
     * @param int $id 需要检查的节点ID，默认检查当前操作节点
     * @param bool $url 是否为节点url，默认为节点id
     * @author 蔡伟明 <314013107@qq.com>
     * @return bool
     * @throws \think\Exception
     */
    public static function checkAuth($id = 0, $url = false)
    {
        // 当前用户的角色
        $role = session('user_auth.role');

        // id为1的是超级管理员，或者角色为1的，拥有最高权限
        if (session('user_auth.uid') == '1' || $role == '1') {
            return true;
        }

        // 获取当前用户的权限
        $menu_auth = session('role_menu_auth');

        // 检查权限
        if ($menu_auth) {
            if ($id !== 0) {
                return $url === false ? isset($menu_auth[$id]) : in_array($id, $menu_auth);
            }
            // 获取当前操作的id
            $location = Menu::getLocation();
            $action   = end($location);

            return $url === false ? isset($menu_auth[$action['id']]) : in_array($action['url_value'], $menu_auth);
        }

        // 其他情况一律没有权限
        return false;
    }

    /**
     * 读取当前角色权限
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function roleAuth()
    {
    	// session('user_auth.role', 1); // MOCK

        $menu_auth = cache('role_menu_auth_'.session('user_auth.role'));
        if (!$menu_auth) {
            $menu_auth = self::where('id', session('user_auth.role'))->value('menu_auth');
            $menu_auth = json_decode($menu_auth, true);
            $menu_auth = Menu::where('id', 'in', $menu_auth)->column('url_value','id');
        }
        // 非开发模式，缓存数据
        if (config('app.develop_mode') == 0) {
            cache('role_menu_auth_'.session('user_auth.role'), $menu_auth);
        }
        return $menu_auth;
    }

    /**
     * 根据节点id获取所有角色id和权限
     * @param string $menu_id 节点id
     * @param bool $menu_auth 是否返回所有节点权限
     * @author 蔡伟明 <314013107@qq.com>
     * @return array
     */
    public static function getRoleWithMenu($menu_id = '', $menu_auth = false)
    {
        if ($menu_auth) {
            return self::where('menu_auth', 'like', '%"'.$menu_id.'"%')->column('menu_auth','id');
        } else {
            return self::where('menu_auth', 'like', '%"'.$menu_id.'"%')->column('id');
        }
    }

    /**
     * 根据角色id获取权限
     * @param array $role 角色id
     * @author 蔡伟明 <314013107@qq.com>
     * @return array
     */
    public static function getAuthWithRole($role = [])
    {
        return self::where('id', 'in', $role)->column('menu_auth','id');
    }

    /**
     * 重设权限
     * @param null $pid 父级id
     * @param array $new_auth 新权限
     * @author 蔡伟明 <314013107@qq.com>
     */
    public static function resetAuth($pid = null, $new_auth = [])
    {
        if ($pid !== null) {
            $data = self::where('pid', $pid)->column('menu_auth','id');
            foreach ($data as $id => $menu_auth) {
                $menu_auth = json_decode($menu_auth, true);
                $menu_auth = json_encode(array_intersect($menu_auth, $new_auth));
                self::where('id', $id)->update(['menu_auth'=>$menu_auth]);
                self::resetAuth($id, $new_auth);
            }
        }
    }

	public function getParentNameAttr($value, $data){
		return $data['pid'] == 0? '顶级角色': $this->getFieldById($data['pid'], 'name');
	}
}
