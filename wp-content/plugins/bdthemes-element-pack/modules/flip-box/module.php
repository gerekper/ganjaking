<?php
namespace ElementPack\Modules\FlipBox;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'flip-box';
	}

	public function get_widgets() {

		$widgets = [
			'Flip_Box',
		];
		
		return $widgets;
	}
}
