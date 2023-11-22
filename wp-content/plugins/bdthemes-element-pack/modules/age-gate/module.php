<?php
namespace ElementPack\Modules\AgeGate;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'age-gate';
	}

	public function get_widgets() {

		$widgets = [
			'Age_Gate',
		];

		return $widgets;
	}
}
