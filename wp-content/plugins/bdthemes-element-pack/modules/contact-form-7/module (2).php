<?php
namespace ElementPack\Modules\ContactForm7;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'contact-form-7';
	}

	public function get_widgets() {

		$widgets = ['Contact_Form_7'];

		return $widgets;
	}
}
