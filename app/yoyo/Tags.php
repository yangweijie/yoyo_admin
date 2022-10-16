<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Tags extends Component
{
	
	public $name     = 'tags';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $size     = 'text-base';
	public $label    = '标签';
	public $required = false;
	public $readonly = false;

	public function render()
	{
		return (String) $this->view('tags', [
			'id' => $this->getComponentId(),
		]);
	}
}