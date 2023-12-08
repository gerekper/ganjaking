<?php
namespace ElementPack\Modules\TagsCloud;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'tags-cloud';
	}

	public function get_widgets() {

		$widgets = ['Tags_Cloud'];

		return $widgets;
	}

}
