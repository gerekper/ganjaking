<?php
namespace ElementPack\Modules\CallOut;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'call-out';
	}

	public function get_widgets() {

		$widgets = [
			'Call_Out',
		];

		return $widgets;
	}
}
