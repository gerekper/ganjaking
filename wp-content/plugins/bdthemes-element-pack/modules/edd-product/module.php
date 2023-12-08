<?php

namespace ElementPack\Modules\EddProduct;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	public function get_name() {
		return 'bdt-edd-product';
	}

	public function get_widgets() {
		return ['EDD_Product'];
	}

	public function add_product_post_class($classes) {
		$classes[] = 'product';

		return $classes;
	}
}
