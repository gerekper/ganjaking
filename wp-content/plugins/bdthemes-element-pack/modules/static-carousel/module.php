<?php
namespace ElementPack\Modules\StaticCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'static-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'Static_Carousel',
		];

		return $widgets;
	}
}
