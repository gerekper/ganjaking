<?php
namespace ElementPack\Modules\ContentSwitcher;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'content-switcher';
	}

	public function get_widgets() {
		$widgets = [
			'Content_Switcher',
		];

		return $widgets;
	}
}
