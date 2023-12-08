<?php
namespace ElementPack\Modules\Accordion;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'accordion';
	}

	public function get_widgets() {
		$widgets = [
			'Accordion',
		];

		return $widgets;
	}
}
