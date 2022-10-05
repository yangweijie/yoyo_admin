<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class header extends Component
{
	
	public $heading    = 'Heading';
	public $subHeading = 'Subeading';
	public $button     = <<<HTML
<a class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out" data-mdb-ripple="true" data-mdb-ripple-color="light" href="#!" role="button">Get started</a>
HTML;

	public $bg_img    = '';
	public $bg_height = '';


	public function render()
	{
		return (String) $this->view('header');
	}
}