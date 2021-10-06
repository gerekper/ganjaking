<?php
/**
 * WC_CP_QI_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Quantity Increment compatibility.
 *
 * @version 3.3.0
 */
class WC_CP_QI_Compatibility {

	public static function init() {

		// WooCommerce Quantity Increment plugin support.
		add_filter( 'woocommerce_composite_front_end_params', array( __CLASS__, 'quantity_increment_support' ) );
	}

	/**
	 * Render WC 2.2 quantity buttons markup when the WC Quantity Increment plugin is active.
	 *
	 * @param  array  $params
	 * @return array
	 */
	public static function quantity_increment_support( $params ) {

		$params[ 'show_quantity_buttons' ] = 'yes';

		return $params;
	}
}

WC_CP_QI_Compatibility::init();
