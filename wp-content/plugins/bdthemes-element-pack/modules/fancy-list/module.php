<?php
namespace ElementPack\Modules\FancyList;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'fancy-list';
	}

	public function get_widgets() {

		$widgets = ['Fancy_List'];

		return $widgets;
	}

}
