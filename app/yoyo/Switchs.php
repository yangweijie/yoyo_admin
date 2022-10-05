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

	public function render()
	{
		return (String) $this->view('switch', [
			'id' => $this->getComponentId(),
		]);
	}
}