<?php
namespace ElementPack\Modules\Calendly;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'calendly';
	}

	public function get_widgets() {
		$widgets = [
			'Calendly',
		];

		return $widgets;
	}
}
