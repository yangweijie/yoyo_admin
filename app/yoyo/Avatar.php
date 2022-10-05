<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Avatar extends Component
{

	public $shape   = 'circle';
	public $shadow  = false;
	public $src     = 'https://mdbcdn.b-cdn.net/img/new/avatars/1.webp';
	public $content = '';

	protected $props = ['shape', 'shadow', 'content'];

	public function render()
	{
		return (String) $this->view('avatar', [
			'id'         => $this->getComponentId(),
		]);
	}
}