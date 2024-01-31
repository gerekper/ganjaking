<?php
namespace ElementPack\Modules\AcfGallery;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'acf-gallery';
	}

	public function get_widgets() {

		$widgets = [
			'Acf_Gallery',
		];

		return $widgets;
	}
}
