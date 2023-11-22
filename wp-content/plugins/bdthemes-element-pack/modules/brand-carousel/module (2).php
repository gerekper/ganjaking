<?php
namespace ElementPack\Modules\BrandCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'brand-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Brand_Carousel',
		];

		return $widgets;
	}
}
