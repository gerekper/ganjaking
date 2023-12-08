<?php

namespace ElementPack\Modules\EddMiniCart;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'edd-mini-cart';
	}

	public function get_widgets() {

		$widgets = [
			'Edd_Mini_Cart',
		];

		return $widgets;
	}
}
