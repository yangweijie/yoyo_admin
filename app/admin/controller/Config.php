<?php
namespace app\admin\controller;

use app\admin\model\Config as ConfigModel;
use think\facade\View;

/**
 * 系统配置控制器
 * @package app\admin\controller
 */
class Config extends Admin
{
    /**
     * 配置首页
     * @param string $group 分组
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index($group = 'base')
    {
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        // 配置分组信息
        $list_group = config('app.config_group');
        $tab_list   = [];
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url']   = url('index', ['group' => $key]);
        }

        return View::fetch('', ['title'=>'配置管理', 'tab_list'=>$tab_list, 'group'=>$group]);
    }

    public function add($group = '')
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'Config');
            if(true !== $result) $this->error($result);

            // 如果是快速联动
            if ($data['type'] == 'linkages') {
                $data['key']    = $data['key']    == '' ? 'id'   : $data['key'];
                $data['pid']    = $data['pid']    == '' ? 'pid'  : $data['pid'];
                $data['level']  = $data['level']  == '' ? '2'    : $data['level'];
                $data['option'] = $data['option'] == '' ? 'name' : $data['option'];
            }

            if ($config = ConfigModel::create($data)) {
                cache('system_config', null);
                $forward = $this->request->param('_pop') == 1 ? null : cookie('__forward__');
                // 记录行为
                $details = '详情：分组('.$data['group'].')、类型('.$data['type'].')、标题('.$data['title'].')、名称('.$data['name'].')';
                action_log('config_add', 'admin_config', $config['id'], UID, $details);
                $this->success('新增成功', $forward);
            } else {
                $this->error('新增失败');
            }
        }

        return View::fetch('', [
            'title' => '新增',
            'group' => $group,
        ]);
    }

    public function edit($id = 0)
    {
        if ($id === 0) $this->error('参数错误');

        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();

            // 验证
            $result = $this->validate($data, 'Config');
            if(true !== $result) $this->error($result);

            // 如果是快速联动
            if ($data['type'] == 'linkages') {
                $data['key']    = $data['key']    == '' ? 'id'   : $data['key'];
                $data['pid']    = $data['pid']    == '' ? 'pid'  : $data['pid'];
                $data['level']  = $data['level']  == '' ? '2'    : $data['level'];
                $data['option'] = $data['option'] == '' ? 'name' : $data['option'];
            }

            // 原配置内容
            $config  = ConfigModel::where('id', $id)->find();
            $details = '原数据：分组('.$config['group'].')、类型('.$config['type'].')、标题('.$config['title'].')、名称('.$config['name'].')';

            if ($config = ConfigModel::update($data)) {
                cache('system_config', null);
                $forward = $this->request->param('_pop') == 1 ? null : cookie('__forward__');
                // 记录行为
                action_log('config_edit', 'admin_config', $config['id'], UID, $details);
                $this->success('编辑成功', $forward, '_parent_reload');
            } else {
                $this->error('编辑失败');
            }
        }


        // 获取数据
        $info = ConfigModel::find($id);

        return View::fetch('', [
            'title'     => '编辑',
            'form_data' => $info,
        ]);
    }

    /**
     * 删除配置
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($record = [])
    {
        return $this->setStatus('delete');
    }

    /**
     * 启用配置
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function enable($record = [])
    {
        return $this->setStatus('enable');
    }

    /**
     * 禁用配置
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function disable($record = [])
    {
        return $this->setStatus('disable');
    }

    /**
     * 设置配置状态：删除、禁用、启用
     * @param string $type 类型：delete/enable/disable
     * @param array $record
     * @author 蔡伟明 <314013107@qq.com>
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setStatus($type = '', $record = [])
    {
        $ids        = $this->request->isPost() ? input('post.ids/a') : input('param.ids');
        $uid_delete = is_array($ids) ? '' : $ids;
        $ids        = ConfigModel::where('id', 'in', $ids)->column('title');
        return parent::setStatus($type, ['config_'.$type, 'admin_config', $uid_delete, UID, implode('、', $ids)]);
    }

    /**
     * 快速编辑
     * @param array $record 行为日志
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function quickEdit($record = [])
    {
        $id      = input('post.pk', '');
        $field   = input('post.name', '');
        $value   = input('post.value', '');
        $config  = ConfigModel::where('id', $id)->value($field);
        $details = '字段(' . $field . ')，原值(' . $config . ')，新值：(' . $value . ')';
        return parent::quickEdit(['config_edit', 'admin_config', $id, UID, $details]);
    }
}