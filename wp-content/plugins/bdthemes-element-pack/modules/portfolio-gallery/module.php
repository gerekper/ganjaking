<?php
namespace ElementPack\Modules\PortfolioGallery;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'portfolio-gallery';
	}

	public function get_widgets() {

		// $portfolio_gallery = element_pack_option('portfolio-gallery', 'element_pack_third_party_widget', 'off' );

		$widgets = ['Portfolio_Gallery'];

		// if ( 'on' === $portfolio_gallery ) {
		// 	$widgets[] = 'Portfolio_Gallery';
		// }

		return $widgets;
	}
}
