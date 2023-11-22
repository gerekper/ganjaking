<?php
namespace ElementPack\Modules\ScrollButton;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'scroll-button';
	}

	public function get_widgets() {

		$widgets = [
			'Scroll_Button',
		];

		return $widgets;
	}
}
