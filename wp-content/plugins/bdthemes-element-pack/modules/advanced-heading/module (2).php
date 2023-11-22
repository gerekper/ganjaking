<?php
namespace ElementPack\Modules\AdvancedHeading;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'advanced-heading';
	}

	public function get_widgets() {

		$widgets = [
			'AdvancedHeading'
		];

		return $widgets;
	}
}
