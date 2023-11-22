<?php

namespace ElementPack\Modules\EddProductCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	public function get_name() {
		return 'bdt-edd-product-carousel';
	}

	public function get_widgets() {
		return ['EDD_Product_Carousel'];
	}

	public function add_product_post_class($classes) {
		$classes[] = 'product';

		return $classes;
	}
}
