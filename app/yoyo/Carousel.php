<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;

class Carousel extends Component
{
	
	public $imgs = [
		[
			'src'         => 'https://mdbootstrap.com/img/Photos/Slides/img%20(15).jpg',
			'alt'         => '...',
			'href'        => '',
			'label'       => 'First slide label',
			'placeholder' => 'Some representative placeholder content for the first slide.',
		],
		[
			'src'  => 'https://mdbootstrap.com/img/Photos/Slides/img%20(22).jpg',
			'alt'  => '...',
			'href' => '',
			'label'       => 'Second slide label',
			'placeholder' => 'Some representative placeholder content for the second slide.',
		],
		[
			'src'  => 'https://mdbootstrap.com/img/Photos/Slides/img%20(23).jpg',
			'alt'  => '...',
			'href' => '',
			'label'       => 'Third slide label',
			'placeholder' => 'Some representative placeholder content for the third slide.',
		]
	];

	public $control    = true;
	public $indicators = true;
	public $fade       = false;
	public $dark       = false;

	public function render()
	{
		return (String) $this->view('carousel',[
			'id' => $this->getComponentId(),
		]);
	}
}