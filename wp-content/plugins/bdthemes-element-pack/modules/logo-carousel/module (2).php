<?php
namespace ElementPack\Modules\LogoCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'logo-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Logo_Carousel',
		];

		return $widgets;
	}
}
