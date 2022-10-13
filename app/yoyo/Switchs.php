<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Switchs extends Component
{
	
	public $name       = 'status';
	public $disabled   = false;
	public $value      = 0;
	public $checkValue = 1;
	public $inline     = false;
	public $label      = '开关';

	public $props = ['label'];

	public function toggle($value){
		$this->value = $value == 0?1:0;
	}

	public function render()
	{
		return (String) $this->view('switch', [
			'id' => $this->getComponentId(),
		]);
	}
}