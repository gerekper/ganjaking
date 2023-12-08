<?php
namespace ElementPack\Modules\BbpressForumIndex;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-forum-index';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Forum_Index',
		];

		return $widgets;
	}
}
