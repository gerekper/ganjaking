<?php

namespace ElementPack\Modules\FacebookFeedCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'facebook-feed-carousel';
	}

	public function get_widgets() {
		$widgets = [
			'Facebook_Feed_Carousel',
		];

		return $widgets;
	}
}
