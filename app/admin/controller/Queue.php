<?php

namespace app\admin\controller;

use think\facade\View;

class Queue extends Admin
{
	public function index(){
		cookie('__forward__', $_SERVER['REQUEST_URI']);
		return View::fetch('', ['title' => '系统任务']);
	}
}