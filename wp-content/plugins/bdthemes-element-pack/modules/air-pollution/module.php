<?php
namespace ElementPack\Modules\AirPollution;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'air-pollution';
	}

	public function get_widgets() {
		$widgets = [
			'Air_Pollution',
		];

		return $widgets;
	}
}
