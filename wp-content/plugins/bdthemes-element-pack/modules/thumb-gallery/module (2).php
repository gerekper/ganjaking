<?php
namespace ElementPack\Modules\ThumbGallery;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'thumb-gallery';
	}

	public function get_widgets() {

		$widgets = [
			'Thumb_Gallery',
		];

		return $widgets;
	}
}
