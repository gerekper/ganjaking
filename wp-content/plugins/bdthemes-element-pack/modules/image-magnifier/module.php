<?php
namespace ElementPack\Modules\ImageMagnifier;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'image-magnifier';
	}

	public function get_widgets() {

		$widgets = [
			'Image_Magnifier',
		];

		return $widgets;
	}
}
