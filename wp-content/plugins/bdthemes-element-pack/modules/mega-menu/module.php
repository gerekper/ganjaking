<?php

namespace ElementPack\Modules\MegaMenu;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'mega-menu';
	}

	public function get_widgets() {
		$widgets = [
			'Mega_Menu',
		];

		return $widgets;
	}
}
