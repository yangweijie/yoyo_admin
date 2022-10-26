<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class WangEditor extends Component
{
	
	public $name  = 'wang_editor';
	public $label = '富文b本';
	public $rows  = 15;
	public $value = '';

	public function render()
	{
		return (String) $this->view('wang_editor', [
			'id' => $this->getComponentId(),
		]);
	}
}