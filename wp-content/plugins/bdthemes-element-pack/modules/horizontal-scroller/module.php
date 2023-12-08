<?php

namespace ElementPack\Modules\HorizontalScroller;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bdt-horizonal-scroller';
	}

	public function get_widgets() {
		$widgets = [
			'Horizontal_Scroller',
		];

		return $widgets;
	}
}
