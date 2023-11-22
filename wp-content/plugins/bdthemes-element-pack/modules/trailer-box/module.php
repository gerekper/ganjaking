<?php
namespace ElementPack\Modules\TrailerBox;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'trailer-box';
	}

	public function get_widgets() {

		$widgets = ['Trailer_Box'];

		return $widgets;
	}
}
