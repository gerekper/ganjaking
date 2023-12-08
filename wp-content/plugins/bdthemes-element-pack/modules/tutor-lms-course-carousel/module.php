<?php

namespace ElementPack\Modules\TutorLmsCourseCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'tutor-lms-carousel';
	}

	public function get_widgets() {

		$widgets = ['TutorLms_Course_Carousel'];

		return $widgets;
	}
}
