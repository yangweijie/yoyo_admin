<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class check extends Component
{
	
	public $name     = 'check';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $label    = 'check';
	public $options  = [
		'0'=>'男',
		'1'=>'女',
	];

	public function render()
	{
		return (String) $this->view('check', [
			'id' => $this->getComponentId(),
		]);
	}
}