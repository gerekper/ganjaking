<?php
namespace ElementPack\Modules\GravityForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'gravity-forms';
	}

	public function get_widgets() {

		$widgets = ['Gravity_Forms'];

		return $widgets;
	}
}
