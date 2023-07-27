<?php

namespace app\admin\model;

use think\Model;

class Playlist extends Model
{
    protected $connection = 'music';

    /**
     * 获取所有数据类型
     * @param boolean $simple 加载默认值
     * @return array
     */
    public static function types(bool $simple = false): array
    {
        return ['网易云'];
    }

	public function renderColumns(){
		return [
			['name' => 'id', 'title'=>'ID', 'type'=>'text'],
			['name' => 'type', 'title'=>'来源', 'type'=>'text'],
			['name' => 'title', 'title'=>'名称', 'type'=>'text'],
			['name' => 'url', 'title'=>'链接','type'=>'text'],
			['name' => 'create_time', 'title'=>'创建时间', 'type'=>'text'],
			['name' => 'update_time', 'title'=>'更新时间', 'type'=>'text'],
		];
	}
}
