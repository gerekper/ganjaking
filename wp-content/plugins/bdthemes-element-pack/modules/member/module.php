<?php
namespace ElementPack\Modules\Member;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'member';
	}

	public function get_widgets() {

		$widgets = [
			'Member',
		];
		
		return $widgets;
	}
}
