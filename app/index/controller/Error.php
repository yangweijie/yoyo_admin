<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Config;
use think\facade\View;

class Error extends BaseController
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
    }


    /* 空操作，用于输出404页面 */
    public function __call($method, $args)
    {
        $app = app();
        switch (strtolower($this->request->controller())) {
            case 'public':
                dump($this->request);
                die;
                break;
            case 'archive':
                $Index = new Index($app);
                return $Index->detail($this->request->action());
                break;
            case 'category':
                $Index = new Index($app);
                return $Index->category($this->request->action());
                break;
            case 'search':
                $_GET['kw'] = $this->request->action();
                $Index      = new Index($app);
                return $Index->search($this->request->action());
                break;
            case 'feed':
                $type = input('get.type');
                return $this->feed($type);
                break;
            case 'submit':
            	if(isset($_REQUEST['package_control_version'])){
            		return json(['result'=>'success']);
            	}else{
            		return json(['result'=>'error']);
            	}
            	break;
            default:
                $Index = new Index($app);
                if (is_numeric(input('year', '')) && is_numeric(input('month', ''))) {
                    return $Index->archive(input('year', ''), input('month', ''));
                }
                $action = strtolower($this->request->controller());
                return $Index->$action();
                break;
        }
    }
}
