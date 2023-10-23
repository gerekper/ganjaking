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

	/**
	 * Allow 3rd parties to filter the product ID rules are being fetched for.
	 *
	 * @since 2.0.2
	 * @param int $product_id Product ID to filter.
	 */
	$product_id = apply_filters( 'woocommerce_per_product_shipping_get_matching_rule_product_id', $product_id );

	if ( 'yes' !== get_post_meta( $product_id, '_per_product_shipping', true ) ) {
		return false;
	}

	if ( ! $standalone && get_post_meta( $product_id, '_per_product_shipping_add_to_all', true ) !== 'yes' ) {
		return null; // No rates, don't fallback to parent product if variable.
	}

	$country  = strtoupper( $package['destination']['country'] );
	$state    = strtoupper( $package['destination']['state'] );
	$postcode = $package['destination']['postcode'];

	// Get 2 Characters state code if exists in lookup array or return $state.
	$two_characters_state_code = woocommerce_per_product_state_code_alias( $state, $country );

	// Build array of state codes.
	$valid_state_codes = array( $state );

	// If returned state code alias is different than original $state add it into the valid state codes array.
	if ( $two_characters_state_code !== $state ) {
		$valid_state_codes[] = $two_characters_state_code;
	}
	$valid_state_codes     = esc_sql( $valid_state_codes );
	$valid_state_codes_sql = "'" . implode( "', '", $valid_state_codes ) . "'";

	// Define valid postcodes.
	$valid_postcodes = array( $postcode );

	// Work out possible valid wildcard postcodes.
	$postcode_length   = strlen( $postcode );
	$wildcard_postcode = $postcode;

	for ( $i = 0; $i < $postcode_length; $i ++ ) {
		$wildcard_postcode = substr( $wildcard_postcode, 0, -1 );
		$valid_postcodes[] = $wildcard_postcode . '*';
	}
	$valid_postcodes     = esc_sql( $valid_postcodes );
	$valid_postcodes_sql = "'" . implode( "', '", $valid_postcodes ) . "'";
	// Get rules matching product, country and state.
	$matching_rule = $wpdb->get_row(
		// @codingStandardsIgnoreStart
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}woocommerce_per_product_shipping_rules
			WHERE product_id = %d
			AND rule_country IN ( '', %s )
			AND rule_state IN ( '', {$valid_state_codes_sql} )
			AND rule_postcode IN ( '', {$valid_postcodes_sql} )
			ORDER BY rule_order
			LIMIT 1",
			$product_id,
			$country
		)
		// @codingStandardsIgnoreEnd
	);

	return $matching_rule;
}

/**
 * Get state code alias if exists.
 *
 * @param string $state_code   State code.
 * @param string $country_code Country code.
 *
 * @return string
 */
function woocommerce_per_product_state_code_alias( $state_code, $country_code ) {
	$state_code_alias = array(
		'AU' => array(
			'NSW' => 'NS',
			'ACT' => 'AC',
			'QLD' => 'QL',
			'TAS' => 'TS',
			'VIC' => 'VI',
		),
	);

	return isset( $state_code_alias[ $country_code ][ $state_code ] ) ? $state_code_alias[ $country_code ][ $state_code ] : $state_code;
}
