<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Breadcrumbs extends Component
{
	
	public $items      = [
		['type' => 'a', 'text'=>'Home', 'href'=>'#'],
		['type' => 'a', 'text'=>'Library', 'href'=>'#'],
		['type' => 'a', 'text'=>'Data', 'href'=>'#'],
	];

	public $navbar     = false;
	public $separator  = '/';
	public $background = false;

	public function getItemsLoopProperty()
	{
		if($this->separator){
			$length = count($this->items);
			if($length > 1){
				$ret = [];
				for ($i=0; $i < $length; $i++) { 
					$ret[] = $this->items[$i];
					if($i < $length - 1){
						$ret[] = [
							'type' => 'span',
							'text' => $this->separator,
						];
					}
				}
				return $ret;
			}else{
				return $this->items;
			}
		}else{
			return $this->items;
		}
	}

	public function render()
	{
		return (String) $this->view('breadcrumbs', [
			'id'         => $this->getComponentId(),
			'items_loop' => $this->items_loop,
		]);
	}
}