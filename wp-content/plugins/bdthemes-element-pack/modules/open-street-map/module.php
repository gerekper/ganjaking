<?php
namespace ElementPack\Modules\OpenStreetMap;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'open-street-map';
	}

	public function get_widgets() {

		$widgets = [
			'Open_Street_Map'
		];

		return $widgets;
	}
}
