<?php

namespace ElementPack\Modules\LearnpressGrid;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {


	public function get_name() {
		return 'learnpress-grid';
	}

	public function get_widgets() {
		return ['Learnpress_Grid'];
	}

	public function add_product_post_class($classes) {
		$classes[] = 'course';

		return $classes;
	}
}
