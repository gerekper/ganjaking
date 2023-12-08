<?php
namespace ElementPack\Modules\RemoteFraction;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'remote-fraction';
	}

	public function get_widgets() {
		$widgets = [
			'Remote_Fraction',
		];

		return $widgets;
	}
}
