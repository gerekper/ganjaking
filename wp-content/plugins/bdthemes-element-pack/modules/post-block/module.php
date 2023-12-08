<?php
namespace ElementPack\Modules\PostBlock;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'post-block';
	}

	public function get_widgets() {

		$widgets = [
			'Post_Block',
		];
		
		return $widgets;
	}
}
