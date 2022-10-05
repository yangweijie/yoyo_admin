<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Select extends Component
{
	
	public $name     = 'select';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $size     = 'text-base';
	public $label    = 'select';
	public $required = false;
	public $readonly = false;
	public $options = [
		''  => '请选择',
		'1' => 'One',
		'2' => 'Two',
		'3' => 'Three',
	];


	public function render()
	{
		return (String) $this->view('select', [
			'id' => $this->getComponentId(),
		]);
	}
}