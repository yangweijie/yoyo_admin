<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Card extends Component
{
	
	public $header     = '';
	public $footer     = '';
	public $img        = '';
	public $title      = 'title';
	public $content    = 'content';
	public $button     = '';
	public $horizontal = false;


	public function render()
	{
		return (String) $this->view('card', [
			'id' => $this->getComponentId(),
		]);
	}
}