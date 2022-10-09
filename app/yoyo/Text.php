<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Text extends Component
{
	
	public $label;
	public $value;

	public function render()
	{
		return (String) $this->view('text', [
			'id' => $this->getComponentId(),
		]);
	}
}