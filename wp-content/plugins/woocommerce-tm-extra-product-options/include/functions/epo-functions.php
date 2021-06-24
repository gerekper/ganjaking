<?php
/**
 * Extra Product Options functions
 *
 * @package Extra Product Options/Functions
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

// Compatibility
function THEMECOMPLETE_EPO_COMPATIBILITY() {
	return THEMECOMPLETE_EPO_COMPATIBILITY_base::instance();
}

// HTML functions
function THEMECOMPLETE_EPO_HTML() {
	return THEMECOMPLETE_EPO_HTML_base::instance();
}

// HELPER functions 
function THEMECOMPLETE_EPO_HELPER() {
	return THEMECOMPLETE_EPO_HELPER_base::instance();
}

// WPML functions 
function THEMECOMPLETE_EPO_WPML() {
	return THEMECOMPLETE_EPO_WPML_base::instance();
}

// LICENSE functions 
function THEMECOMPLETE_EPO_LICENSE() {
	return THEMECOMPLETE_EPO_UPDATE_Licenser::instance();
}

// UPDATE functions 
function THEMECOMPLETE_EPO_UPDATER() {
	return THEMECOMPLETE_EPO_UPDATE_Updater::instance();
}

// Plugin health check 
function THEMECOMPLETE_EPO_CHECK() {
	return THEMECOMPLETE_EPO_CHECK_base::instance();
}

// Field builder 
function THEMECOMPLETE_EPO_BUILDER() {
	return THEMECOMPLETE_EPO_BUILDER_base::instance();
}

// Main plugin interface 
function THEMECOMPLETE_EPO() {
	return THEMECOMPLETE_Extra_Product_Options::instance();
}

// Main Display 
function THEMECOMPLETE_EPO_DISPLAY() {
	return THEMECOMPLETE_EPO_Display::instance();
}

// Cart Functionality 
function THEMECOMPLETE_EPO_CART() {
	return THEMECOMPLETE_EPO_Cart::instance();
}

// Order Functionality 
function THEMECOMPLETE_EPO_ORDER() {
	return THEMECOMPLETE_EPO_Order::instance();
}

// Associated products Functionality
function THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS() {
	return THEMECOMPLETE_EPO_Associated_Products::instance();
}

// Main Scripts 
function THEMECOMPLETE_EPO_SCRIPTS() {
	return THEMECOMPLETE_EPO_Scripts::instance();
}

// Globals Admin Interface 
function THEMECOMPLETE_EPO_ADMIN_GLOBAL() {
	return THEMECOMPLETE_EPO_ADMIN_Global_base::instance();
}

// Admin Interface 
function THEMECOMPLETE_EPO_ADMIN() {
	return THEMECOMPLETE_EPO_Admin_base::instance();
}

// Settings Interface 
function THEMECOMPLETE_EPO_SETTINGS() {
	return THEMECOMPLETE_EPO_SETTINGS_base::instance();
}

// API helper Interface 
function THEMECOMPLETE_EPO_API() {
	return THEMECOMPLETE_EPO_API_base::instance();
}

if ( ! function_exists( 'themecomplete_convert_local_numbers' ) ) {
	/**
	 * Convert local decimal separator to PHP dot
	 *
	 * @param string $input
	 *
	 * @return mixed|string
	 */
	function themecomplete_convert_local_numbers( $input = "" ) {
		$locale   = localeconv();
		$decimals = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

		// Remove whitespace from string
		$input = preg_replace( '/\s+/', '', $input );

		// Remove locale from string
		$input = str_replace( $decimals, '.', $input );

		// Trim invalid start/end characters
		$input = rtrim( ltrim( $input, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		return $input;
	}
}

if ( ! function_exists( 'themecomplete_woocommerce_check' ) ) {
	/**
	 * Check if WooCommerce is active
	 *
	 * @return bool
	 */
	function themecomplete_woocommerce_check() {
		return ! ( THEMECOMPLETE_EPO_CHECK()->tc_needs_wc_db_update() ) && THEMECOMPLETE_EPO_CHECK()->themecomplete_woocommerce_check_only();
	}
}

if ( ! function_exists( 'themecomplete_woocommerce_subscriptions_check' ) ) {
	/**
	 * Check if Subscriptions is active
	 *
	 * @return bool
	 */
	function themecomplete_woocommerce_subscriptions_check() {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return apply_filters( 'wc_epo_is_subscriptions_active', in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins ) || array_key_exists( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins ) );
	}
}

if ( ! function_exists( 'themecomplete_get_roles' ) ) {
	/**
	 * Get available roles
	 *
	 * @return array
	 */
	function themecomplete_get_roles() {
		$result              = array();
		$result["@everyone"] = esc_html__( 'Everyone', 'woocommerce-tm-extra-product-options' );
		$result["@loggedin"] = esc_html__( 'Logged in users', 'woocommerce-tm-extra-product-options' );
		global $wp_roles;
		if ( empty( $wp_roles ) ) {
			$all_roles = new WP_Roles();
		} else {
			$all_roles = $wp_roles;
		}
		$roles = $all_roles->roles;
		if ( $roles ) {
			foreach ( $roles as $role => $details ) {
				$name            = translate_user_role( $details['name'] );
				$result[ $role ] = $name;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'themecomplete_price' ) ) {
	/**
	 * Format the price with a currency symbol
	 *
	 * @param float $price
	 * @param array $args (default: array())
	 *
	 * @return string
	 */
	function themecomplete_price( $price, $args = array() ) {
		$args = apply_filters( 'tc_price_args', wp_parse_args( $args, array(
			'ex_tax_label'       => FALSE,
			'currency'           => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => apply_filters( 'wc_epo_price_decimals', wc_get_price_decimals() ),
			'price_format'       => get_woocommerce_price_format(),
		) ) );

		$original_price = $price;

		// Convert to float to avoid issues on PHP 8.
		$price = (float) $price;

		$unformatted_price = $price;
		$negative = $price < 0;

		/**
		 * Filter raw price.
		 *
		 * @param float        $raw_price      Raw price.
		 * @param float|string $original_price Original price as float, or empty string. Since 5.0.0.
		 */
		$price    = apply_filters( 'tc_raw_woocommerce_price', $negative ? $price * - 1 : $price, $original_price );

		/**
		 * Filter formatted price.
		 *
		 * @param float        $formatted_price    Formatted price.
		 * @param float        $price              Unformatted price.
		 * @param int          $decimals           Number of decimals.
		 * @param string       $decimal_separator  Decimal separator.
		 * @param string       $thousand_separator Thousand separator.
		 * @param float|string $original_price     Original price as float, or empty string. Since 5.0.0.
		 */
		$price    = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'], $original_price );

		if ( apply_filters( 'woocommerce_price_trim_zeros', FALSE ) && $args['decimals'] > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>', $price );
		$return          = '<span class="woocommerce-Price-amount amount"><bdi>' . $formatted_price . '</bdi></span>';

		if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
			$return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		}

		/**
		 * Filters the string of price markup.
		 *
		 * @param string       $return            Price HTML markup.
		 * @param string       $price             Formatted price.
		 * @param array        $args              Pass on the args.
		 * @param float        $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
		 * @param float|string $original_price    Original price as float, or empty string. Since 5.0.0.
		 */
		return apply_filters( 'tc_price', $return, $price, $args, $unformatted_price, $original_price );
	}
}
