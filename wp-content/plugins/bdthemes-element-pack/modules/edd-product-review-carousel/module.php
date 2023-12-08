<?php

namespace ElementPack\Modules\EddProductReviewCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'edd-product-review-carousel';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Product_Review_Carousel',
		];

		return $widgets;
	}
}
