<?php
namespace ElementPack\Modules\AcfAccordion;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'acf-accordion';
	}

	public function get_widgets() {
		$widgets = ['Acf_Accordion'];

		return $widgets;
	}
}
