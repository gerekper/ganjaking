<?php
namespace ElementPack\Modules\LottieImage;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'lottie-image';
	}

	public function get_widgets() {

		$widgets = [
			'Lottie_Image',
		];

		return $widgets;
	}
}
