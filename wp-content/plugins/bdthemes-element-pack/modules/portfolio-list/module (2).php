<?php
namespace ElementPack\Modules\PortfolioList;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'portfolio-list';
	}

	public function get_widgets() {

		// $portfolio_list = element_pack_option('portfolio-list', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Portfolio_List'];

		// if ( 'on' === $portfolio_list ) {
		// 	$widgets[] = 'Portfolio_List';
		// }

		return $widgets;
	}
}
