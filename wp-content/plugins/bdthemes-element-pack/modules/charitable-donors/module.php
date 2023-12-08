<?php
namespace ElementPack\Modules\CharitableDonors;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-donors';
	}

	public function get_widgets() {

		// $charitable_donors = element_pack_option('charitable-donors', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Donors'];

		// if ( 'on' === $charitable_donors ) {
		// 	$widgets[] = 'Charitable_Donors';
		// }

		return $widgets;
	}
}