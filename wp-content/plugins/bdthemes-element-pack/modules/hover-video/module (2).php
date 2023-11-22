<?php
namespace ElementPack\Modules\HoverVideo;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'hover-video';
	}

	public function get_widgets() {

		$widgets = ['Hover_Video'];

		return $widgets;
	}

}
