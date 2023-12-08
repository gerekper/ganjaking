<?php
namespace ElementPack\Modules\CharitableRegistration;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-registration';
	}

	public function get_widgets() {

		// $charitable_registration = element_pack_option('charitable-registration', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Registration'];

		// if ( 'on' === $charitable_registration ) {
		// 	$widgets[] = 'Charitable_Registration';
		// }

		return $widgets;
	}
}