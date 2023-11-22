<?php
namespace ElementPack\Modules\ImageCompare;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'image-compare';
	}

	public function get_widgets() {

		$widgets = [
			'Image_Compare',
		];

		return $widgets;
	}
}
