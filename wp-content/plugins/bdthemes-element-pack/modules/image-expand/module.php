<?php
namespace ElementPack\Modules\ImageExpand;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'image-expand';
	}

	public function get_widgets() {
		$widgets = [
			'Image_Expand',
		];

		return $widgets;
	}
}
