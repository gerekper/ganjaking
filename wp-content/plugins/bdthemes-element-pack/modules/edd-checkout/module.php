<?php

namespace ElementPack\Modules\EddCheckout;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bdt-edd-checkout';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Checkout',
		];

		return $widgets;
	}
}
