<?php
namespace ElementPack\Modules\BookedCalendar;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'booked-calendar';
	}

	public function get_widgets() {

		$widgets = ['BookedCalendar'];

		return $widgets;
	}
}
