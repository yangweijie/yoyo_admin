<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;
use Clickfwd\Yoyo\Exceptions\MissingComponentTemplate;

class Input extends Component
{
	
	public $name     = 'input';
	public $type     = 'text';  // email |  password | number | tel | url
	public $disabled = false;
	public $value    = '';
	public $inline   = false;
	public $size     = '1.6'; // lg 2.15 default 1.6
	public $label    = 'input';
	public $required = false;
	public $readonly = false;

	/**
	 * @throws MissingComponentTemplate
	 */
	public function render()
	{
		return (String) $this->view('input', [
			'id' => $this->getComponentId(),
		]);
	}
}