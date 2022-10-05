<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Alert extends Component
{

	private $types = [
		'primary'   => 'blue',
		'secondary' => 'purple',
		'success'   => 'green',
		'danger'    => 'red',
		'warning'   => 'yellow',
		'indigo'    => 'indigo',
		'light'     => 'gray',
		'dark'      => 'gray',
	];

	public $backgroud  = false;
	public $alwaysOpen = false;
	public $content    = '内容';
	public $link       = [];
	public $icon       = [];

	public $type = 'primary';

	public function getBgColorProperty()
	{
		$color = $this->types[$this->type];
		if($color == 'gray'){
			return $this->type == 'light'? 'bg-gray-50' : 'bg-gray-300';
		}else{
			return "bg-{$color}-100";
		}
	}

	public function getTextColorProperty()
	{
		$color = $this->types[$this->type];
		if($color == 'gray'){
			return $this->type == 'light'? 'text-gray-500' : 'text-gray-800';
		}else{
			return "text-{$color}-700";
		}
	}

	public function getLinkColorProperty()
	{
		$color = $this->types[$this->type];
		if($color == 'gray'){
			return $this->type == 'light'? 'text-gray-600' : 'text-gray-900';
		}else{
			return "text-{$color}-800";
		}
	}

	public function getLinkContentProperty(){
		return str_ireplace('text-blue-800', $this->link_color, $this->content);
	}

	protected $props = ['type', 'content', 'link', 'icon'];

	public function render()
	{
		return (String) $this->view('alert', [
			'id'           => $this->getComponentId(),
			'bg_color'     => $this->bg_color,
			'text_color'   => $this->text_color,
			'link_content' => $this->link_content,
		]);
	}
}