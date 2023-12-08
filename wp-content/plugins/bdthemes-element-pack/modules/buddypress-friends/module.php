<?php
namespace ElementPack\Modules\BuddypressFriends;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'buddypress-friends';
	}

	public function get_widgets() {

		$widgets = ['Buddypress_Friends'];

		return $widgets;
	}
}
