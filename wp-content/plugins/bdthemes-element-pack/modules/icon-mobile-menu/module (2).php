<?php
namespace ElementPack\Modules\IconMobileMenu;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'icon-mobile-menu';
	}

	public function get_widgets() {
		$widgets = [
			'Icon_Mobile_Menu',
		];

		return $widgets;
	}
}
