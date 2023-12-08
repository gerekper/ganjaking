<?php

namespace ElementPack\Modules\LearnpressCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
	public function get_name() {
		return 'learnpress-carousel';
	}

	public function get_widgets() {
		return ['Learnpress_Carousel'];
	}
}
