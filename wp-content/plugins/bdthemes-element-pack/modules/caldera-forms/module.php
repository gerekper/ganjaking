<?php
namespace ElementPack\Modules\CalderaForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'caldera-forms';
	}

	public function get_widgets() {

		$widgets = ['Caldera_Forms'];

		return $widgets;
	}
}
