<?php
namespace ElementPack\Modules\CharitableDonations;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-donations';
	}

	public function get_widgets() {

		// $charitable_donations = element_pack_option('charitable-donations', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Donations'];

		// if ( 'on' === $charitable_donations ) {
		// 	$widgets[] = 'Charitable_Donations';
		// }

		return $widgets;
	}
}