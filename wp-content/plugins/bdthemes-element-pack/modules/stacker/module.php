<?php

namespace ElementPack\Modules\Stacker;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'stacker';
	}

	public function get_widgets() {
		$widgets = [
			'Stacker',
		];

		return $widgets;
	}
}
