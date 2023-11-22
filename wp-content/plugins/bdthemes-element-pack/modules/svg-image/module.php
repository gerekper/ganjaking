<?php
namespace ElementPack\Modules\SvgImage;

use ElementPack\Base\Element_Pack_Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'svg-image';
	}

	public function get_widgets() {
		$widgets = [
			'Svg_Image',
		];

		return $widgets;
	}
}
