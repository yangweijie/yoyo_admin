<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Heading extends Component
{
	
	public $type = 'h6';
	public $text = 'Tailwind Elements';


	public function render()
	{
		return (String) $this->view('heading');
	}
}