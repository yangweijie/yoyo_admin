<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Spinner extends Component
{
	
	public $text     = 'Loading...';
	public $color    = '';  // text-blue-600 text-purple-500 text-green-500 text-red-500 text-yellow-500 text-blue-300 text-gray-300
	public $type     = 'border'; // grow
	public $w        = 8;
	public $h        = 8;
	public $showText = false;

	public function render()
	{
		return (String) $this->view('spinner');
	}
}