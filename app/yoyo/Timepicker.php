<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Timepicker extends Component
{
	
	public $name     = 'time';
	public $disabled = false;
	public $value    = '';
	public $label    = '时间';
	public $readonly = false;

	public function render()
	{
		return (String) $this->view('timepicker', [
			'id' => $this->getComponentId(),
		]);
	}
}