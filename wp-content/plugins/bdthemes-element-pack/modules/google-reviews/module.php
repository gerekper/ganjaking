<?php
namespace ElementPack\Modules\GoogleReviews;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'google-reviews';
	}

	public function get_widgets() {
		$widgets = [
			'Google_Reviews',
		];

		return $widgets;
	}
}