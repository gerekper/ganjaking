<?php
namespace ElementPack\Modules\CustomCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'custom-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Custom_Carousel'
		];

		return $widgets;
	}
}
