<?php
namespace ElementPack\Modules\ImageAccordion;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'image-accordion';
	}

	public function get_widgets() {
		$widgets = [
			'Image_Accordion',
		];

		return $widgets;
	}
}
