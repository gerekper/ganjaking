<?php
namespace ElementPack\Modules\TheNewsletter;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'the-newsletter';
	}

	public function get_widgets() {

		$widgets = ['The_Newsletter'];

		return $widgets;
	}
}
