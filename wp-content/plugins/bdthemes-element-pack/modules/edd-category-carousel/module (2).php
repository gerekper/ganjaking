<?php

namespace ElementPack\Modules\EddCategoryCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bdt-edd-category-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Category_Carousel',
		];

		return $widgets;
	}
}
