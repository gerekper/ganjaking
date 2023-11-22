<?php
namespace ElementPack\Modules\ReviewCardCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'review-card-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'Review_Card_Carousel',
		];

		return $widgets;
	}
}
