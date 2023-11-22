<?php
namespace ElementPack\Modules\FeaturedBox;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'featured-box';
	}

	public function get_widgets() {

		$widgets = [
			'Featured_Box',
		];

		return $widgets;
	}
}
