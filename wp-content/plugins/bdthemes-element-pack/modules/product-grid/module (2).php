<?php
namespace ElementPack\Modules\ProductGrid;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'product-grid';
	}

	public function get_widgets() {
		$widgets = [
			'Product_Grid',
		];

		return $widgets;
	}
}
