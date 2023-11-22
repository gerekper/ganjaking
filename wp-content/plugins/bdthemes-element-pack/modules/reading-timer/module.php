<?php

namespace ElementPack\Modules\ReadingTimer;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'reading-timer';
	}

	public function get_widgets() {
		$widgets = [
			'Reading_Timer',
		];

		return $widgets;
	}
}

