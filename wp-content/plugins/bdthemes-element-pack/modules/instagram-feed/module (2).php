<?php
namespace ElementPack\Modules\InstagramFeed;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'instagram-feed';
	}

	public function get_widgets() {

		$widgets = ['Instagram_Feed'];

		return $widgets;
	}
}
