<?php
namespace ElementPack\Modules\BbpressSingleTopic;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-single-topic';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Single_Topic',
		];

		return $widgets;
	}
}
