<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Input extends Component
{
	
	public $name     = 'input';
	public $type     = 'text';  // email |  password | number | tel | url
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $size     = 'text-base';
	public $label    = 'input';
	public $required = false;
	public $readonly = false;

	public function render()
	{
		return (String) $this->view('input', [
			'id' => $this->getComponentId(),
		]);
	}
}