<?php
/**
 * WC_CP_Zapier_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    8.1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zapier compatibility.
 *
 * @version  8.1.1
 */
class WC_CP_Zapier_Compatibility {

	public static function init() {
		// Remove 'composite_scenarios' field for requests coming from Zapier.
		add_filter( 'woocommerce_composite_products_rest_api_product_schema', array( __CLASS__, 'remove_composite_scenarios_field' ), 10, 1 );
	}

	/**
	 * Removes Scenario fields from REST API schema.
	 *
	 * @param  array $schema
	 * @return array
	 */
	public static function remove_composite_scenarios_field( $schema ) {
		if ( ! empty( $GLOBALS[ 'wp' ]->query_vars[ 'rest_route' ] ) && strpos( $GLOBALS[ 'wp' ]->query_vars[ 'rest_route' ], 'wc-zapier' ) !== false ) {
			unset( $schema[ 'composite_scenarios' ] );
		}
		return $schema;
	}

}

WC_CP_Zapier_Compatibility::init();
