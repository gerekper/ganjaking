<?php
namespace ElementPack\Modules\CharitableLogin;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-login';
	}

	public function get_widgets() {

		// $charitable_login = element_pack_option('charitable-login', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Login'];

		// if ( 'on' === $charitable_login ) {
		// 	$widgets[] = 'Charitable_Login';
		// }

		return $widgets;
	}
}