<?php
namespace ElementPack\Modules\ProfileCard;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'profile-card';
	}

	public function get_widgets() {

		$widgets = [
			'Profile_Card',
		];

		return $widgets;
	}
}
