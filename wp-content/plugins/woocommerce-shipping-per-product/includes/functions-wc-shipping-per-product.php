<?php
/**
 * Common functions.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get matching rule.
 *
 * @param mixed $product_id Product ID.
 * @param mixed $package    Package to ship.
 * @param bool  $standalone Whether standalone or not. Deprecated in 2.3.0.
 *
 * @return false|null|object
 */
function woocommerce_per_product_shipping_get_matching_rule( $product_id, $package, $standalone = true ) {
	global $wpdb;

	$product_id = apply_filters( 'woocommerce_per_product_shipping_get_matching_rule_product_id', $product_id );

	if ( 'yes' !== get_post_meta( $product_id, '_per_product_shipping', true ) ) {
		return false;
	}

	if ( ! $standalone && get_post_meta( $product_id, '_per_product_shipping_add_to_all', true ) !== 'yes' ) {
		return null; // No rates, don't fallback to parent product if variable.
	}

	$country  = $package['destination']['country'];
	$state    = $package['destination']['state'];
	$postcode = $package['destination']['postcode'];

	// Define valid postcodes.
	$valid_postcodes = array( '', $postcode );

	// Work out possible valid wildcard postcodes.
	$postcode_length   = strlen( $postcode );
	$wildcard_postcode = $postcode;

	for ( $i = 0; $i < $postcode_length; $i ++ ) {
		$wildcard_postcode = substr( $wildcard_postcode, 0, -1 );
		$valid_postcodes[] = $wildcard_postcode . '*';
	}

	// Get rules matching product, country and state.
	$matching_rule = $wpdb->get_row(
		// @codingStandardsIgnoreStart
		$wpdb->prepare(
			"
			SELECT * FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules
			WHERE product_id = %d
			AND rule_country IN ( '', %s )
			AND rule_state IN ( '', %s )
			AND rule_postcode IN ( '" . implode( "','", $valid_postcodes ) . "' )
			ORDER BY rule_order
			LIMIT 1
			" , $product_id, strtoupper( $country ), strtoupper( $state )
		)
		// @codingStandardsIgnoreEnd
	);

	return $matching_rule;
}
