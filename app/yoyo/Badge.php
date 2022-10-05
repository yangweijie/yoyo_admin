<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Badge extends Component
{

	public $text   = 'new';
	public $size   = '';
	public $color  = 'primary';
	public $pills  = false;
	private $types = [
		'primary'   => ['bg-blue-600', 'text-white'],
		'secondary' => ['bg-purple-600', 'text-white'],
		'success'   => ['bg-green-500', 'text-white'],
		'danger'    => ['bg-red-600', 'text-white'],
		'warning'   => ['bg-yellow-500', 'text-white'],
		'info'      => ['bg-blue-400', 'text-white'],
		'light'     => ['bg-gray-200', 'text-gray-700'],
		'dark'      => ['bg-gray-800', 'text-white'],
	];

	protected $props = ['text', 'color'];


	public function getBgColorProperty()
	{
		return $this->types[$this->color][0];
	}

	public function getTextColorProperty()
	{
		return $this->types[$this->color][1];
	}


	public function render()
	{
		return (String) $this->view('badge', [
			'id'         => $this->getComponentId(),
			'bg_color'   => $this->bg_color,
			'text_color' => $this->text_color,
		]);
	}
}