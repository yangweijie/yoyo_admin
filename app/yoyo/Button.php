<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Button extends Component
{

	private $sizes      = ['small' => 'px-4 py-1.5', 'medium'=>'px-6 py-2.5', 'large'=>'px-7 py-3'];

	public $text        = 'button';
	public $size        = 'medium';
	public $type        = 'button';
	public $icon        = '';
	public $color       = 'primary';
	public $href        = '';
	public $block       = false;
	public $round       = false;
	public $ripple      = false;
	public $outline     = false;
	public $disabled    = false;
	public $clickEvent = null;
	public $extra_attrs = '';

	private $types = [
		'primary'   => ['bg-blue-600', 'text-white', 'border-2 border-blue-600', 'text-blue-600'],
		'secondary' => ['bg-purple-600', 'text-white', 'border-2 border-purple-600', 'text-purple-600'],
		'success'   => ['bg-green-500', 'text-white', 'border-2 border-green-500','text-green-500'],
		'danger'    => ['bg-red-600', 'text-white', 'border-2 border-red-600', 'text-red-600'],
		'warning'   => ['bg-yellow-500', 'text-white', 'border-2 border-yellow-500', 'text-yellow-500'],
		'info'      => ['bg-blue-400', 'text-white', 'border-2 border-blue-400', 'text-blue-400'],
		'light'     => ['bg-gray-200', 'text-gray-700', 'border-2 border-gray-200', 'text-gray-200'],
		'dark'      => ['bg-gray-800', 'text-white', 'border-2 border-gray-800', 'text-gray-800'],
		'link'      => ['bg-transparent', 'text-blue-600'],
	];

	protected $props = ['text', 'color'];

	public function getSizeClassProperty(){
		return $this->sizes[$this->size]?? $this->size;
	}

	public function getBgColorProperty()
	{
		return $this->types[$this->color][$this->outline ? 2:0]?:'';
	}

	public function getTextColorProperty()
	{
		return $this->types[$this->color][$this->outline ? 3:1]?:'text-white';
	}

	public function getHoverColorProperty()
	{
		return stripos($this->bg_color, '0') === false ? 'bg-transparent': substr($this->bg_color, 0, strpos($this->bg_color, '-', 4));
	}

	public function render()
	{
		return (String) $this->view('button', [
			'id'          => $this->getComponentId(),
			'size_class'  => $this->size_class,
			'bg_color'    => $this->bg_color,
			'text_color'  => $this->text_color,
			'hover_color' => $this->hover_color,
		]);
	}

	public function click(){
		trace('clicked');
		trace($this->clickEvent);
		trace(gettype($this->clickEvent));
		if(isset($this->clickEvent) && gettype($this->clickEvent) == 'Closure'){
			$this->clickEvent();
		}
	}
}