<?php
namespace ElementPack\Modules\EverestForms;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'everest-forms';
	}

	public function get_widgets() {

		$widgets = ['Everest_Forms'];

		return $widgets;
	}
}
