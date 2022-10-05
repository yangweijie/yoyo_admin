<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class File extends Component
{
	
	public $label = '文件';
	public $value = 0;

	public function render()
	{
		return (String) $this->view('file', [
			'id' => $this->getComponentId(),
		]);
	}
}