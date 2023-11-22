<?php
namespace ElementPack\Modules\TwitterGrid;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'twitter-grid';
	}

	public function get_widgets() {
		$widgets = [
			'Twitter_Grid',
		];

		return $widgets;
	}
}
