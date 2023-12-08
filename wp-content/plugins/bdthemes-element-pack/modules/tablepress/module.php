<?php
namespace ElementPack\Modules\Tablepress;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'tablepress';
	}

	public function get_widgets() {

		$widgets = ['Tablepress'];

		return $widgets;
	}
}
