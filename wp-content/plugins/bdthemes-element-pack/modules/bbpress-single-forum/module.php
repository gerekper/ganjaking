<?php
namespace ElementPack\Modules\BbpressSingleForum;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-single-forum';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Single_Forum',
		];

		return $widgets;
	}
}
