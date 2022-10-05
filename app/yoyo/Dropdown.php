<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Dropdown extends Component
{
	
	public $btn  = [
		'text'        => 'dropdown',
		'extra_attrs' => [
			'data-bs-toggle' => 'dropdown',
			'aria-expanded'  => 'false',
			'one-link-mark'  => 'yes',
		],
	];

	public $pos = 'dropdown'; // dropdown（下）| dropup （上）|dropend（右）|dropstart(左)

	public $dark = false;

	public $header = '';

	public $items = [
		[
			'type' => 'a',
			'href' => '#',
			'text' => '项目',
		],
		[
			'type' => 'disable',
			'href' => '#',
			'text' => '项目',
		],
		['type' => 'divider'],
		['type' => 'text', 'text'=>'项目文本'],
	];

	public function mount(){
		$id = $this->getComponentId();
		$this->btn['extra_attrs']['id'] = $id.'Button';
		$extra_attrs = [];
		foreach ($this->btn['extra_attrs'] as $key => $value) {
			$extra_attrs[] = "{$key}={$value}";
		}
		$this->btn['extra_attrs'] = implode(' ', $extra_attrs);
	}


	public function render()
	{
		return (String) $this->view('dropdown', ['id'=>$this->getComponentId()]);
	}
}