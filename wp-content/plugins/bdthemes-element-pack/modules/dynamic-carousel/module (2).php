<?php

namespace ElementPack\Modules\DynamicCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bdt-dynamic-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Dynamic_Carousel',
		];

		return $widgets;
	}
}
