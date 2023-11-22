<?php
namespace ElementPack\Modules\PanelSlider;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'panel-slider';
	}

	public function get_widgets() {
		$widgets = [
			'Panel_Slider',
		];

		return $widgets;
	}
}
