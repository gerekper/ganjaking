<?php
namespace ElementPack\Modules\BrandGrid;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'brand-grid';
	}

	public function get_widgets() {
		$widgets = [
			'Brand_Grid',
		];

		return $widgets;
	}
}
