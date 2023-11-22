<?php

namespace ElementPack\Modules\TutorLmsCourseGrid;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'tutor-lms-grid';
	}

	public function get_widgets() {

		$widgets = ['TutorLms_Course_Grid'];

		return $widgets;
	}
}
