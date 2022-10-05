<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Textarea extends Component
{
	
	public $name     = 'textarea';
	public $disabled = false;
	public $value    = '';
	public $rows     = 3;
	public $label    = '文本框';
	public $readonly = false;

	public function render()
	{
		return (String) $this->view('textarea', [
			'id' => $this->getComponentId(),
		]);
	}
}