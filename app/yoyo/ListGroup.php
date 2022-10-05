<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class ListGroup extends Component
{
	
	public $list = [
		['text' => 'An item', 'active'=>false, 'disabled'=>false, 'href'=>''],
		['text' => 'A second item', 'active'=>true, 'disabled'=>false, 'href'=>''],
		['text' => 'A third item', 'active'=>false, 'disabled'=>true, 'href'=>''],
		['text' => 'A fourth item', 'active'=>false, 'disabled'=>false, 'href'=>'http://www.baidu.com'],
	];

	public $type = 'text'; // link

	public $flush = false;

	public function render()
	{
		return (String) $this->view('list_group');
	}
}