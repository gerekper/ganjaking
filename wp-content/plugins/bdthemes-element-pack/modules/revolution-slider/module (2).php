<?php
namespace ElementPack\Modules\RevolutionSlider;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'revolution-slider';
	}

	public function get_widgets() {

		$widgets = ['Revolution_Slider'];

		return $widgets;
	}
}
