<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Tooltip extends Component
{
	
	public $show  = true;
	public $title = 'title';
	public $body  = 'body';
	public $type  = 'default';

	public function close(){
		$this->show = false;
	}

	public function show($title, $body, $type = 'default'){
		$this->title = $title;
		$this->body  = $body;
		$this->type  = $type;
		$this->show  = true;
	}

	public function getColorProperty(){
		return [
			'default' => 'bg-white',
			'primary' => 'bg-blue-600',
			'success' => 'bg-green-500',
			'danger'  => 'bg-yellow-500',
			'warning' => 'bg-red-600',
		][$this->type];
	}

	public function render()
	{
		return (String) $this->view('tooltip', [
			'id'    => $this->getComponentId(),
			'color' => $this->color
		]);
	}
}