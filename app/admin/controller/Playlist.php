<?php

namespace app\admin\controller;

use think\facade\View;

class Playlist extends Admin
{
	public function index(){
		cookie('__forward__', $_SERVER['REQUEST_URI']);
		return View::fetch('', ['title' => '歌单列表']);
	}
}