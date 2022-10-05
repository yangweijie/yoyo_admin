<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Accordion extends Component
{

	public $backgroud  = false;
	public $alwaysOpen = false;
	public $list = [
		[
			'title'   => '标题1',
			'content' => <<<HTML
内容1
HTML,
		],
		[
			'title'   => '标题2',
			'content' => <<<HTML
内容2
HTML,
		],
		[
			'title'   => '标题3',
			'content' => <<<HTML
内容3			
HTML,
		]
	];

	protected $props = ['list', 'backgroud', 'alwaysOpen'];

	public function render()
	{
		return (String) $this->view('accordion', [
			'id'         => $this->getComponentId(),
			'list'       => $this->list, 
			'backgroud'  => $this->backgroud,
			'alwaysOpen' => $this->alwaysOpen,
		]);
	}
}