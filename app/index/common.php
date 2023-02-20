<?php

use app\admin\model\Column;
use app\admin\model\Document;
use app\admin\model\Page;
use think\facade\Cache;
use util\Tree;

if (!function_exists('get_front_cache')) {
    function get_front_cache()
    {
        if (!Cache::get('front_cache')) {
	        $singleList = Page::where('status', 1)->order('update_time DESC')->select();
	        $newArticle = Document::view('cms_document', true)
	                ->view("cms_column", ['name' => 'column_name'], 'cms_column.id=cms_document.cid', 'left')
	                ->view("admin_user", 'username', 'admin_user.id=cms_document.uid', 'left')
	                ->where(['cms_document.status' => 1])
	                ->order('update_time DESC')
	                ->limit(3)->select();

	        $category = Column::where('status = 1')->order('sort asc')->select();
	        $count = Document::group('cid')->where('status=1')->column('cid', 'count(*) as num');
	        $count = array_combine(array_values($count), array_keys($count));
	        foreach ($category as $key => $value) {
	            $category[$key]['article_num'] = isset($count[$value['id']]) ? $count[$value['id']] : 0;
	        }

	        $cate = Tree::config(['title' => 'name'])->toList($category);
	        $list = Document::getList([], 'create_time DESC, id DESC');
	        $date = $time = [];
	        foreach ($list as $key => $value) {
	            if ($value['create_time']) {
	                $time[] = date('F Y', strtotime($value['create_time']));
	            }
	        }
	        $time = array_unique($time);
	        foreach ($time as $key => $value) {
                $year = date('Y', strtotime($value));
                $month = date('m', strtotime($value));
	            $date[] = [
	                'text' => $value,
	                'link' => sprintf('/index/index/archive/year/%d/month/%s', $year,$month)
	            ];
	        }

	        $cache = [
	            'single_list' => $singleList,
	            'new_article' => $newArticle,
	            'cate' => $cate,
	            'date' => $date,
	        ];

	        Cache::set('front_cache', $cache);
        }
        return Cache::get('front_cache');
    }
}
