<?php

namespace ElementPack\Modules\EddPurchaseHistory;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'easy-digital-purchase-history';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Purchase_History',
		];

		return $widgets;
	}
}
