<?php
namespace ElementPack\Modules\PriceList;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'price-list';
	}

	public function get_widgets() {
		return [
			'Price_List',
		];
	}
}
