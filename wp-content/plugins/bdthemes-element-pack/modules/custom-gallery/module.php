<?php
namespace ElementPack\Modules\CustomGallery;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'custom-gallery';
	}

	public function get_widgets() {

		$widgets = [
			'Custom_Gallery',
		];

		return $widgets;
	}
}
