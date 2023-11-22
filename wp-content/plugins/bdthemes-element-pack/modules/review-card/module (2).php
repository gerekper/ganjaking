<?php
namespace ElementPack\Modules\ReviewCard;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'review-card';
	}

	public function get_widgets() {

		$widgets = [
			'Review_Card',
		];

		return $widgets;
	}
}
