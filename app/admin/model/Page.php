<?php
namespace app\admin\model;

use think\Model as ThinkModel;

/**
 * 单页模型
 * @package app\admin\model
 */
class Page extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'cms_page';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取单页标题列表
     * @author 蔡伟明 <314013107@qq.com>
     * @return array|mixed
     */
    public static function getTitleList()
    {
        $result = cache('cms_page_title_list');
        if (!$result) {
            $result = self::where('status', 1)->column('title','id');
            // 非开发模式，缓存数据
            if (config('app.develop_mode') == 0) {
                cache('cms_page_title_list', $result);
            }
        }
        return $result;
    }
}
