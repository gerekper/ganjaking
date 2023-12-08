<?php
namespace ElementPack\Modules\CharitableStat;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'charitable-stat';
	}

	public function get_widgets() {

		// $charitable_stat = element_pack_option('charitable-stat', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Charitable_Stat'];

		// if ( 'on' === $charitable_stat ) {
		// 	$widgets[] = 'Charitable_Stat';
		// }

		return $widgets;
	}
}