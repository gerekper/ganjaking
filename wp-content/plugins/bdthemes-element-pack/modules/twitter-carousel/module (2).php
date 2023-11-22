<?php
namespace ElementPack\Modules\TwitterCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'twitter-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Twitter_Carousel',
		];

		return $widgets;
	}
}
