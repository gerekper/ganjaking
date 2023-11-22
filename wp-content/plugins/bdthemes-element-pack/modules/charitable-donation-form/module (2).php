<?php
namespace ElementPack\Modules\CharitableDonationForm;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-donation-form';
	}

	public function get_widgets() {

		// $charitable_donation_form = element_pack_option('charitable-donation-form', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Donation_Form'];

		// if ( 'on' === $charitable_donation_form ) {
		// 	$widgets[] = 'Charitable_Donation_Form';
		// }

		return $widgets;
	}
}