<?php
namespace app\index\controller;

use app\BaseController;
use app\admin\model\Document;
use Clickfwd\Yoyo\Yoyo as yoyoClass;
use think\db\Where;
use think\facade\Config;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    /**
     * @var mixed
     */
    private $cache;

    /**
     * 初始化方法
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function initialize()
    {
    	$this->cache = get_front_cache();
        // 系统开关
        if (!config('app.web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
        if ($this->request->isPjax()) {
            Config::set('default_ajax_return', 'html');
            View::config(['layout_on' =>true, 'layout_name'=>'home/layout_pjax']);
        } else {
        	View::config(['layout_on' =>true, 'layout_name'=>'home/layout']);
        }
        View::assign('new_article', $this->cache['new_article']);
        View::assign('category', $this->cache['cate']);
        View::assign('tags', ['测试']);
        View::assign('single', $this->cache['single_list']);
        View::assign('archive', $this->cache['date']);
        View::assign('tab_list', [
                [
                    'title' => '首页',
                    'url'   => '/',
                ],
                [
                    'title'=>'电脑爱好者',
                    'url'=>'http://new-qk.lifves.com/list.php?url=NDFEMyVldXNzaTYyJTAyMDJEMyVyYWV5NjIlZGE4NDAzNDFjMzZkLTNmZDktMTA2NC1kNzJhLTlmMmQ1NDA3RDMlZGlGMyVsaWF0ZWRGMiVhZ2FtRjIlY3BGMiVtb2MubmFraXEuc3BkLmdzdHhkbmpGMiVGMiVBMyVwdHRo',
                ],
                [
                    'title'=>'电脑报',
                    'url'=>'http://new-qk.lifves.com/list.php?url=MDNEMyVldXNzaTYyJTAyMDJEMyVyYWV5NjIlOGQ3ZGJhYjFlZmUzLWQxMGItY2I2NC05YTYyLWY5MDVhMzg2RDMlZGlGMyVsaWF0ZWRGMiVhZ2FtRjIlY3BGMiVtb2MubmFraXEuc3BkLmdzdHhkbmpGMiVGMiVBMyVwdHRo',
                ],
            ]
        );
    }

    public function index($page = 1)
    {
        $this->lists($page);
        return View::fetch('', []);
    }

    /* 文档模型列表页 */
    public function lists($page = 1, $cid = 0)
    {
        $tag = input('get.tag', '');
        $map = [];
        if ($cid) {
            $map['cms_document.cid'] = $cid;
        }
        if ($kw = input('get.kw', '', 'trim')) {
            $map['cms_document.title'] = ['like', "%{$kw}%"];
            $like_id                   = Db::name('cms_document_article')->where("content like '%{$kw}%'")->column('aid');
            if ($like_id) {
                $map['cms_document.id'] = $like_id;
            }
        }
        $year  = input('get.year', 0);
        $month = input('get.month', 0);
        if ($year && $month) {
            $begin_time                      = strtotime($year . $month . "01");
            $end_time                        = strtotime("+1 month", $begin_time);
            $map['cms_document.create_time'] = array('between time', [$begin_time, $end_time]);
        }
        if ($tag) {
            $ids = Db::name('cms_document_article')->where("FIND_IN_SET('{$tag}', tags)")->column('aid');
            if ($ids) {
                $map['cms_document.id'] = $ids;
            } else {
                $map['cms_document.id'] = 0;
            }
        }
        $map['cms_document.trash'] = 0;
        // 排序
        $order = 'update_time desc';
        // 数据列表
        $data_list = Document::getList($map, $order);
      
        View::assign('list', $data_list);
        return $data_list;
    }

    //详情
    public function detail($id)
    {
        $map       = [['id','in','1,2']];
        $list      = Document::where($map)->select();
        // halt($list);
        /* 标识正确性检测 */
        if (!($id && is_numeric($id))) {
            return $this->error('文档ID错误！');
        }

        /* 获取详细信息 */
        $info = Document::getOne($id);
        if (!$info) {
            return $this->error('未获取到详情信息');
        }
        $info['cate_title'] = get_cate_name($info['cid']);

        $tmpl = 'index/detail';

        /* 模板赋值并渲染模板 */
        View::assign('info', $info);
        return View::fetch($tmpl);
    }

    //分类
    public function category($name)
    {
        $cate = Db::name('cms_column')->getByName($name);
        if (!$cate) {
            $this->error('错误的分类');
        }
        View::assign('cate', $cate);
        $this->lists(input('get.page', 1), $cate['id']);
        return View::fetch('index/cate');
    }

    //归档
    public function archive($year, $month)
    {
        $_GET['month'] = $month;
        $_GET['year']  = $year;
        View::assign('year', $year);
        View::assign('month', $month);
        $this->lists(input('get.page', 1));
        return View::fetch('index/archive');
    }

    //搜索
    public function search($kw ='')
    {
        if (!$kw) {
            return $this->error('请输入关键字');
        }

        View::assign('kw', $kw);
        $this->lists(input('get.page', 1));
        return View::fetch('index/search');
    }

    public function phpinfo()
    {
        phpinfo();
        return '';
    }

    public function yoyo(){
        return (yoyoClass::getInstance())->update();
    }
}
