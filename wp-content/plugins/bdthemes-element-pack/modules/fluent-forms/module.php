<?php
namespace ElementPack\Modules\FluentForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'fluent-forms';
	}

	public function get_widgets() {

		$widgets = ['Fluent_Forms'];

		return $widgets;
	}
}
