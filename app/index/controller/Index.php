<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\View;
use Clickfwd\Yoyo\Yoyo as yoyoClass;

class Index extends BaseController
{
    public function index()
    {
        return View::fetch();
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }

    public function yoyo(){
        return (yoyoClass::getInstance())->update();
    }
}
