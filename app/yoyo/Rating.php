<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Rating extends Component
{
	public $score = 0;
	public $icon  = 'star'; // or  heart
	public $total = 5;

	public function render()
	{
		return (String) $this->view('rating');
	}
}