<?php
namespace ElementPack\Modules\Weather;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'weather';
	}

	public function get_widgets() {
		$widgets = [
			'Weather',
		];

		return $widgets;
	}
}
