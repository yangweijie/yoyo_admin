<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Link extends Component
{
	
	public $type      = 'primary';
	public $href      = '#!';
	public $underline = false;
	public $default   = 'transition duration-300 ease-in-out mb-4';
	public $text      = 'link';

	public function getColorClassProperty(){
		$colors  = [
			'primary'   => 'text-blue-600 hover:text-blue-700',
			'secondary' => 'text-purple-600 hover:text-purple-700',
			'success'   => 'text-green-500 hover:text-green-600',
			'danger'    => 'text-red-600 hover:text-red-700"',
			'warning'   => 'text-yellow-500 hover:text-yellow-600',
			'info'      => 'text-blue-400 hover:text-blue-500',
			'light'     => 'text-gray-200 hover:text-gray-300',
			'dark'      => 'text-gray-800 hover:text-gray-900',
			'white'     => 'class="text-white hover:text-gray-100"',
		];
		$color     = $colors[$this->type];
		return $color;
	}

	public function render()
	{
		return (String) $this->view('link', [
			'color' => $this->color_class,
		]);
	}
}