<?php
namespace ElementPack\Modules\BuddypressMember;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'buddypress-member';
	}

	public function get_widgets() {

		$widgets = ['Buddypress_Member'];

		return $widgets;
	}
}
