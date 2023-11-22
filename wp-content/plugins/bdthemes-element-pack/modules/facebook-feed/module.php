<?php

namespace ElementPack\Modules\FacebookFeed;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'facebook-feed';
	}

	public function get_widgets() {
		$widgets = [
			'Facebook_Feed',
		];

		return $widgets;
	}
}
