<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Search extends Component
{
	public $name     = 'keyword';
	public $checked  = false;
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $label    = 'search';

	public function render()
	{
		return (String) $this->view('search', [
			'id' => $this->getComponentId(),
		]);
	}
}