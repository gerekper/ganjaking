<?php
namespace ElementPack\Modules\CharitableCampaigns;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-campaigns';
	}

	public function get_widgets() {

		// $charitable_campaigns = element_pack_option('charitable-campaigns', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Campaigns'];

		// if ( 'on' === $charitable_campaigns ) {
		// 	$widgets[] = 'Charitable_Campaigns';
		// }

		return $widgets;
	}
}