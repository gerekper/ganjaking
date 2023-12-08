<?php
namespace ElementPack\Modules\LayerSlider;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'layer-slider';
	}

	public function get_widgets() {

		$widgets = ['Layer_Slider'];

		return $widgets;
	}
}
