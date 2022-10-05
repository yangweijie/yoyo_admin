<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Tabs extends Component
{
	
	public $items = [
		[
			'title'    => 'HOME',
			'content'  => 'Tab 1 content',
			'disabled' => false,
		],
		[
			'title'    => 'PROFILE',
			'content'  => 'Tab 2 content',
			'disabled' => false,
		],
		[
			'title'    => 'MESSAGE',
			'content'  => 'Tab 3 content',
			'disabled' => false,
		],
		[
			'title'    => 'CONTACT',
			'content'  => 'Tab 4 content',
			'disabled' => true,
		],
	];

	public $fill = false;

	public $justify = false;

	public $vertical = false;

	public $active = 0;

	public function getUlClassProperty(){
		$extra = "md:flex-row ";
		if($this->justify){
			$extra .= "nav-justified ";
		}
		if($this->vertical){
			$extra = str_ireplace('md:flex-row', '', $extra);
		}
		return $extra;
	}

	public function getLiClassProperty(){
		if($this->justify || $this->vertical){
			return "flex-grow text-center"; 
		}
		if($this->fill){
			return "flex-auto text-center"; 
		}
	}

	public function render()
	{
		return (String) $this->view('tabs', [
			'id'       => $this->getComponentId(),
			'ul_class' => $this->ul_class,
			'li_class' => $this->li_class,
		]);
	}
}