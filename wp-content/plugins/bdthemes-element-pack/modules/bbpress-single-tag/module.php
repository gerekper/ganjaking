<?php

namespace ElementPack\Modules\BbpressSingleTag;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-single-tag';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Single_Tag',
		];

		return $widgets;
	}
}
