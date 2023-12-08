<?php
namespace ElementPack\Modules\Dropbar;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'dropbar';
	}

	public function get_widgets() {

		$widgets = [
			'Dropbar',
		];

		return $widgets;
	}
}
