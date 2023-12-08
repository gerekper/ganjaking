<?php
namespace ElementPack\Modules\CircleInfo;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'circle-info';
	}

	public function get_widgets() {

		$widgets = ['Circle_Info'];

		return $widgets;
	}

}
