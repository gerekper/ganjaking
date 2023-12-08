<?php
namespace ElementPack\Modules\PortfolioCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'portfolio-carousel';
	}

	public function get_widgets() {

		// $portfolio_carousel = element_pack_option('portfolio-carousel', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Portfolio_Carousel'];

		// if ( 'on' === $portfolio_carousel ) {
		// 	$widgets[] = 'Portfolio_Carousel';
		// }

		return $widgets;
	}
}
