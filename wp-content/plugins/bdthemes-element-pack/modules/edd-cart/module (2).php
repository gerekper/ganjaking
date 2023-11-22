<?php

namespace ElementPack\Modules\EddCart;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bdt-edd-cart';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Cart',
		];

		return $widgets;
	}
}
