<?php
namespace ElementPack\Modules\PostBlockModern;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'post-block-modern';
	}

	public function get_widgets() {

		$widgets = [
			'Post_Block_Modern',
		];
		
		return $widgets;
	}
}
