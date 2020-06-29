<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Elementor_Helper {

	/**
	 * Create new category
	 */

	public static function categories_registered( $elements_manager ){

	  $elements_manager->add_category(
			'mfn_builder',
			[
				'title' => __( 'Betheme', 'mfn-opts' ),
				'icon' => 'fa fa-plug',
			]
		);

	}

}
