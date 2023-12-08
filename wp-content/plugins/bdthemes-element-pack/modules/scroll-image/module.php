<?php
namespace ElementPack\Modules\ScrollImage;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'scroll-image';
	}

	public function get_widgets() {

		$widgets = [
			'Scroll_Image',
		];

		return $widgets;
	}
}
