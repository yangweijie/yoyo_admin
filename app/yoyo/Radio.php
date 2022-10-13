<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class radioRadio extends Component
{
	
	public $name     = 'radio';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $label    = '单选';
	public $options = [
		0 => '男',
		1 => '女',
	];


	public function render()
	{
		return (String) $this->view('radio', [
			'id' => $this->getComponentId(),
		]);
	}
}