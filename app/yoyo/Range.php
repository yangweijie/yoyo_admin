<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Range extends Component
{
	
	public $name     = 'range';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $min      = 0;
	public $max      = 5;
	public $step     = 0.5;
	public $label    = 'range';

	public function render()
	{
		return (String) $this->view('range', [
			'id' => $this->getComponentId(),
		]);
	}
}