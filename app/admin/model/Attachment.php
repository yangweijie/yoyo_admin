<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

/**
 * 附件模型
 * @package app\admin\model
 */
class Attachment extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_attachment';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    public function renderColumns(){
        return [
            ['name' => 'id', 'title'=>'ID', 'type'=>'text'],
            ['name' => 'type', 'title'=>'类型', 'type'=>'text'],
            ['name' => 'name', 'title'=>'名称', 'type'=>'text'],
            ['name' => 'size', 'title'=>'大小','type'=>'byte'],
            ['name' => 'driver','title'=>'上传驱动','type'=>'label'],
            ['name' => 'create_time', 'title'=>'创建时间', 'type'=>'text'],
            ['name' => 'status', 'title'=>'状态', 'type'=>'status'],
        ];
    }

    /**
     * 根据附件id获取路径
     * @param string|array $id 附件id
     * @param  int $type 类型：0-补全目录，1-直接返回数据库记录的地址
     * @author 蔡伟明 <314013107@qq.com>
     * @return array|bool|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFilePath($id = '', $type = 0)
    {
        if (is_array($id)) {
            $data_list = $this->where('id', 'in', $id)->select();
            $paths = [];
            foreach ($data_list as $key => $value) {
                if ($value['driver'] == 'local') {
                    $paths[$value['id']] = ($type == 0 ? PUBLIC_PATH : '').$value['path'];
                } else {
                    $paths[$value['id']] = $value['path'];
                }
            }
            return $paths;
        } else {
            $data = $this->where('id', $id)->find();
            if ($data) {
                if ($data['driver'] == 'local') {
                    return ($type == 0 ? PUBLIC_PATH : '').$data['path'];
                } else {
                    return $data['path'];
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 根据图片id获取缩略图路径，如果缩略图不存在，则返回原图路径
     * @param string $id 图片id
     * @author 蔡伟明 <314013107@qq.com>
     * @return array|mixed|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getThumbPath($id = '')
    {
        $result = $this->where('id', $id)->field('path,driver,thumb')->find();
        if ($result) {
            if ($result['driver'] == 'local') {
                return $result['thumb'] != '' ? PUBLIC_PATH.$result['thumb'] : PUBLIC_PATH.$result['path'];
            } else {
                return $result['thumb'] != '' ? $result['thumb'] : $result['path'];
            }
        } else {
            return $result;
        }
    }

    /**
     * 根据附件id获取名称
     * @param  string $id 附件id
     * @return string     名称
     */
    public function getFileName($id = '')
    {
        return $this->where('id', $id)->value('name');
    }

    public function getTypeAttr($value, $data){
        // 图片
        if (in_array(strtolower($data['ext']), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            if ($data['driver'] == 'local') {
                $thumb = $data['thumb'] != '' ? $data['thumb'] : $data['path'];
                return '<div class="js-gallery"><img class="image" title="点击查看大图" data-original="'. PUBLIC_PATH . $data['path'].'" src="'. PUBLIC_PATH . $thumb.'"></div>';
            } else {
                return '<div class="js-gallery"><img class="image" title="点击查看大图" data-original="'. $data['path'].'" src="'. $data['path'].'"></div>';
            }
        }else{
            if ($data['driver'] == 'local') {
                $path = PUBLIC_PATH. $data['path'];
            } else {
                $path = $data['path'];
            }
            if (is_file('.'.config('app.public_static_path').'admin/img/files/'.$data['ext'].'.png')) {
                return '<a href="'. $path.'"
                    data-toggle="tooltip" title="点击下载">
                    <img class="image" src="/static/admin/img/files/'.$data['ext'].'.png"></a>';
            } else {
                return '<a href="'. $path.'"
                    data-toggle="tooltip" title="点击下载">
                    <img class="image" src="/static/admin/img/files/file.png"></a>';
            }
        }
    }
}
