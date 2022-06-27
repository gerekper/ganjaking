<?php
/**
 * Extra Product Options functions
 *
 * @package Extra Product Options/Functions
 * @version 6.0
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility.
 *
 * @return THEMECOMPLETE_EPO_COMPATIBILITY_Base
 */
function THEMECOMPLETE_EPO_COMPATIBILITY() {
	return THEMECOMPLETE_EPO_COMPATIBILITY_Base::instance();
}

/**
 * HTML functions.
 *
 * @return THEMECOMPLETE_EPO_HTML_Base
 */
function THEMECOMPLETE_EPO_HTML() {
	return THEMECOMPLETE_EPO_HTML_Base::instance();
}

/**
 * HELPER functions.
 *
 * @return THEMECOMPLETE_EPO_HELPER_Base
 */
function THEMECOMPLETE_EPO_HELPER() {
	return THEMECOMPLETE_EPO_HELPER_Base::instance();
}

/**
 * WPML functions.
 *
 * @return THEMECOMPLETE_EPO_WPML_Base
 */
function THEMECOMPLETE_EPO_WPML() {
	return THEMECOMPLETE_EPO_WPML_Base::instance();
}

/**
 * LICENSE functions.
 *
 * @return THEMECOMPLETE_EPO_UPDATE_Licenser
 */
function THEMECOMPLETE_EPO_LICENSE() {
	return THEMECOMPLETE_EPO_UPDATE_Licenser::instance();
}

/**
 * UPDATE functions.
 *
 * @return THEMECOMPLETE_EPO_UPDATE_Updater
 */
function THEMECOMPLETE_EPO_UPDATER() {
	return THEMECOMPLETE_EPO_UPDATE_Updater::instance();
}

/**
 * Plugin health check.
 *
 * @return THEMECOMPLETE_EPO_CHECK_Base
 */
function THEMECOMPLETE_EPO_CHECK() {
	return THEMECOMPLETE_EPO_CHECK_Base::instance();
}

/**
 * Field builder.
 *
 * @return THEMECOMPLETE_EPO_BUILDER_Base
 */
function THEMECOMPLETE_EPO_BUILDER() {
	return THEMECOMPLETE_EPO_BUILDER_Base::instance();
}

/**
 * Main plugin interface.
 *
 * @return THEMECOMPLETE_Extra_Product_Options
 */
function THEMECOMPLETE_EPO() {
	return THEMECOMPLETE_Extra_Product_Options::instance();
}

/**
 * Main Display.
 *
 * @return THEMECOMPLETE_EPO_Display
 */
function THEMECOMPLETE_EPO_DISPLAY() {
	return THEMECOMPLETE_EPO_Display::instance();
}

/**
 * Cart Functionality.
 *
 * @return THEMECOMPLETE_EPO_Cart
 */
function THEMECOMPLETE_EPO_CART() {
	return THEMECOMPLETE_EPO_Cart::instance();
}

/**
 * Order Functionality.
 *
 * @return THEMECOMPLETE_EPO_Order
 */
function THEMECOMPLETE_EPO_ORDER() {
	return THEMECOMPLETE_EPO_Order::instance();
}

/**
 * Associated products Functionality.
 *
 * @return THEMECOMPLETE_EPO_Associated_Products
 */
function THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS() {
	return THEMECOMPLETE_EPO_Associated_Products::instance();
}

/**
 * Main Scripts.
 *
 * @return THEMECOMPLETE_EPO_Scripts
 */
function THEMECOMPLETE_EPO_SCRIPTS() {
	return THEMECOMPLETE_EPO_Scripts::instance();
}

/**
 * Globals Admin Interface.
 *
 * @return THEMECOMPLETE_EPO_ADMIN_Global_Base
 */
function THEMECOMPLETE_EPO_ADMIN_GLOBAL() {
	return THEMECOMPLETE_EPO_ADMIN_Global_Base::instance();
}

/**
 * Admin Interface.
 *
 * @return THEMECOMPLETE_EPO_Admin_Base
 */
function THEMECOMPLETE_EPO_ADMIN() {
	return THEMECOMPLETE_EPO_Admin_Base::instance();
}

/**
 * Settings Interface.
 *
 * @return THEMECOMPLETE_EPO_SETTINGS_Base
 */
function THEMECOMPLETE_EPO_SETTINGS() {
	return THEMECOMPLETE_EPO_SETTINGS_Base::instance();
}

/**
 * API helper Interface.
 *
 * @return THEMECOMPLETE_EPO_API_Base
 */
function THEMECOMPLETE_EPO_API() {
	return THEMECOMPLETE_EPO_API_Base::instance();
}

if ( ! function_exists( 'themecomplete_convert_local_numbers' ) ) {
	/**
	 * Convert local decimal separator to PHP dot
	 *
	 * @param string $input Number.
	 *
	 * @return mixed|string
	 */
	function themecomplete_convert_local_numbers( $input = '' ) {
		$locale   = localeconv();
		$decimals = [ wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] ];

		// Remove whitespace from string.
		$input = preg_replace( '/\s+/', '', $input );

		// Remove locale from string.
		$input = str_replace( $decimals, '.', $input );

		// Trim invalid start/end characters.
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
		$active_plugins = (array) get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return apply_filters( 'wc_epo_is_subscriptions_active', in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins, true ) || array_key_exists( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins ) );
	}
}

if ( ! function_exists( 'themecomplete_get_roles' ) ) {
	/**
	 * Get available roles
	 *
	 * @return array
	 */
	function themecomplete_get_roles() {
		$result              = [];
		$result['@everyone'] = esc_html__( 'Everyone', 'woocommerce-tm-extra-product-options' );
		$result['@loggedin'] = esc_html__( 'Logged in users', 'woocommerce-tm-extra-product-options' );
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
	 * @param float $price the price.
	 * @param array $args (default: []).
	 *
	 * @return string
	 */
	function themecomplete_price( $price, $args = [] ) {
		$args = apply_filters(
			'tc_price_args',
			wp_parse_args(
				$args,
				[
					'ex_tax_label'       => false,
					'currency'           => '',
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => apply_filters( 'wc_epo_price_decimals', wc_get_price_decimals() ),
					'price_format'       => get_woocommerce_price_format(),
				]
			)
		);

		$original_price = $price;

		// Convert to float to avoid issues on PHP 8.
		$price = (float) $price;

		$unformatted_price = $price;
		$negative          = $price < 0;

		/**
		 * Filter raw price.
		 *
		 * @param float        $raw_price      Raw price.
		 * @param float|string $original_price Original price as float, or empty string. Since 5.0.0.
		 */
		$price = apply_filters( 'tc_raw_woocommerce_price', $negative ? $price * - 1 : $price, $original_price );

		/**
		 * Filter formatted price.
		 *
		 * @param float        $formatted_price    Formatted price.
		 * @param float        $price              Unformatted price.
		 * @param integer      $decimals           Number of decimals.
		 * @param string       $decimal_separator  Decimal separator.
		 * @param string       $thousand_separator Thousand separator.
		 * @param float|string $original_price     Original price as float, or empty string. Since 5.0.0.
		 */
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'], $original_price );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
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
