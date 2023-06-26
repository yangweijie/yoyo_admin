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
}
