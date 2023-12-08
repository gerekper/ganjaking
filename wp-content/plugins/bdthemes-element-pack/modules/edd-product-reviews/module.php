<?php

namespace ElementPack\Modules\EddProductReviews;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'edd-product-reviews';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Product_Reviews',
		];

		return $widgets;
	}
}
