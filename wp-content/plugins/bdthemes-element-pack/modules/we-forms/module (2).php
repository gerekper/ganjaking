<?php
namespace ElementPack\Modules\WeForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'we-forms';
	}

	public function get_widgets() {

		$widgets = ['We_Forms'];

		return $widgets;
	}
}
