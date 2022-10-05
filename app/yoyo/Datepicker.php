<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Datepicker extends Component
{
	
	public $name     = 'date1';
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $label    = '选择一个日期';


	public function render()
	{
		return (String) $this->view('datepicker');
	}
}