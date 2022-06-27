<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && ! version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), '6.7.0', '>' ) ) {
	return;	
}

/**
 * Class responsible for dealing with everything related to the adoption of
 * Gutenberg Blocks and WooCommerce.
 */
class WC_Box_Office_Blocks {
	
	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_filter( 'woocommerce_store_api_product_quantity_editable', function( $quantity, $product )  {
            return ! wc_box_office_is_product_ticket( $product );
        }, 10, 2 );

		/**
		 * These two functions enable block based product list components to change
		 * the Add to cart button into a link to the product detail page for
		 * ticket info collection. 
		 */
		add_filter( 'woocommerce_product_has_options', function( $has_options, $product )  {
			if ( wc_box_office_is_product_ticket( $product ) ) {
				return true;
			} 
			return $has_options;
		}, 10, 2 );

		/**
		 * This is needed so that we can get to the product detail page first from any
		 * WooCommerce related block that displays products using the add to cart button. 
		 * See: https://github.com/woocommerce/woocommerce-gutenberg-products-block/issues/5895
		 */
		add_filter( 'woocommerce_product_supports', function( $supports, $feature, $product ) {
			if ( wc_box_office_is_product_ticket( $product ) && 'ajax_add_to_cart' === $feature ) {
				return false;
			}
			return $supports;
		},  10, 3 );
	}
}