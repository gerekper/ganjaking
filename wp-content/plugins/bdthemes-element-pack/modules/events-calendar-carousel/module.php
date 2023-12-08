<?php

namespace ElementPack\Modules\EventsCalendarCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'event-carousel';
	}

	public function get_widgets() {

		$widgets = ['Events_Calendar_Carousel'];

		return $widgets;
	}
}
