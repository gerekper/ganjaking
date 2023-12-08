<?php
namespace ElementPack\Modules\ForminatorForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'forminator-forms';
	}

	public function get_widgets() {

		$widgets = ['Forminator_Forms'];

		return $widgets;
	}
}
