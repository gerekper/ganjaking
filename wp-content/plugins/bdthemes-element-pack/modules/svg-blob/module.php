<?php

namespace ElementPack\Modules\SvgBlob;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'svg-blob';
	}

	public function get_widgets() {

		$widgets = [
			'Svg_Blob',
		];

		return $widgets;
	}
}
