<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Tag extends Component
{
	
	public $text    = 'text';
	public $data    = '';
	public $icon    = '';
	public $outline = false;

	public function render()
	{
		return (String) $this->view('tag');
	}
}