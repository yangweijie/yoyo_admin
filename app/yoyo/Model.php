<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;


// 未完成
class Model extends Component
{
	
	public $title      = 'title';
	public $body       = 'body';
	public $scrollable = false;
	public $centered   = false;
	public $size       = 'lg';
	public $fullscreen = false; // modal-fullscreen

	public function render()
	{
		return (String) $this->view('model', [
			'id' => $this->getComponentId(),
		]);
	}
}