<?php
namespace ElementPack\Modules\CreativeButton;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'creative-button';
	}

	public function get_widgets() {

		$widgets = ['Creative_Button'];

		return $widgets;
	}
}
