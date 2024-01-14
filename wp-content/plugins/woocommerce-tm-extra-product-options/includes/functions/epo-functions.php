<?php
/**
 * Extra Product Options functions
 *
 * @package Extra Product Options/Functions
 * @version 6.4
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
 * Admin Field builder.
 *
 * @return THEMECOMPLETE_EPO_ADMIN_BUILDER_Base
 */
function THEMECOMPLETE_EPO_ADMIN_BUILDER() {
	return THEMECOMPLETE_EPO_ADMIN_BUILDER_Base::instance();
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
 * Admin Lookup Table Interface.
 *
 * @return THEMECOMPLETE_EPO_Admin_LookupTable_Base
 */
function THEMECOMPLETE_EPO_ADMIN_LOOKUPTABLE() {
	return THEMECOMPLETE_EPO_Admin_LookupTable_Base::instance();
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

/**
 * Conditional Logic Interface.
 *
 * @return THEMECOMPLETE_EPO_Conditional_Logic_Base
 */
function THEMECOMPLETE_EPO_CONDITIONAL_LOGIC() {
	return THEMECOMPLETE_EPO_Conditional_Logic_Base::instance();
}

/**
 * Actions Interface.
 *
 * @return THEMECOMPLETE_EPO_Actions_Base
 */
function THEMECOMPLETE_EPO_ACTIONS() {
	return THEMECOMPLETE_EPO_Actions_Base::instance();
}

/**
 * Data Store Interface.
 *
 * @return THEMECOMPLETE_EPO_Data_Store_Base
 */
function THEMECOMPLETE_EPO_DATA_STORE() {
	return THEMECOMPLETE_EPO_Data_Store_Base::instance();
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

		// Trim string.
		$input = trim( $input );

		// Remove locale from string.
		foreach ( $decimals as $decimal ) {
			if ( '.' !== $decimal && '' !== $decimal ) {
				$input = preg_replace_callback(
					'~if\(.*?\)(*SKIP)(*F)|lookuptable\(.*?\)(*SKIP)(*F)|min\(.*?\)(*SKIP)(*F)|max\(.*?\)(*SKIP)(*F)|{[^{}]*}(*SKIP)(*F)|(?<!\bif\s\()\d+\\' . $decimal . '\d+(?![^\(]*?\))~',
					function ( $m ) use ( $decimal ) {
						return str_replace( $decimal, '.', $m[0] );
					},
					$input
				);
			}
		}

		// Trim invalid start/end characters.
		$input = rtrim( ltrim( $input, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		return $input;
	}
}

if ( ! function_exists( 'themecomplete_woocommerce_check' ) ) {
	/**
	 * Check if WooCommerce is active
	 *
	 * @return boolean
	 */
	function themecomplete_woocommerce_check() {
		return ! ( THEMECOMPLETE_EPO_CHECK()->tc_needs_wc_db_update() ) && THEMECOMPLETE_EPO_CHECK()->themecomplete_woocommerce_check_only();
	}
}

if ( ! function_exists( 'themecomplete_woocommerce_subscriptions_check' ) ) {
	/**
	 * Check if Subscriptions is active
	 *
	 * @return boolean
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
	 * @param array<string> $disable Disable roles from showing.
	 * @return array<string>
	 */
	function themecomplete_get_roles( $disable = [] ) {
		$result = [];
		if ( ! in_array( '@everyone', $disable, true ) ) {
			$result['@everyone'] = esc_html__( 'Everyone', 'woocommerce-tm-extra-product-options' );
		}
		if ( ! in_array( '@loggedin', $disable, true ) ) {
			$result['@loggedin'] = esc_html__( 'Logged in users', 'woocommerce-tm-extra-product-options' );
		}
		global $wp_roles;
		if ( empty( $wp_roles ) ) {
			$all_roles = new WP_Roles();
		} else {
			$all_roles = $wp_roles;
		}
		$roles = $all_roles->roles;
		if ( $roles ) {
			foreach ( $roles as $role => $details ) {
				if ( ! in_array( $role, $disable, true ) ) {
					$name            = translate_user_role( $details['name'] );
					$result[ $role ] = $name;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'themecomplete_price' ) ) {
	/**
	 * Format the price with a currency symbol
	 *
	 * @param mixed        $price the price.
	 * @param array<mixed> $args (default: []).
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
		 * @param float|string $formatted_price    Formatted price.
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

if ( ! function_exists( 'themecomplete_get_tax_rate' ) ) {
	/**
	 * Get the tax rate of the given tax classes
	 *
	 * @param string $classes Tax class.
	 *
	 * @return integer
	 */
	function themecomplete_get_tax_rate( $classes ) {
		$tax_rate = 0;

		if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
			$tax_rates = WC_Tax::get_rates( $classes );
			foreach ( $tax_rates as $key => $value ) {
				$tax_rate = $tax_rate + floatval( $value['rate'] );
			}
		}

		return $tax_rate;
	}
}

if ( ! function_exists( 'themecomplete_do_shortcode' ) ) {
	/**
	 * Search content for shortcodes and filter shortcodes through their hooks.
	 *
	 * If there are no shortcode tags defined, then the content will be returned
	 * without any filtering. This might cause issues when plugins are disabled but
	 * the shortcode will still show up in the post or content.
	 *
	 * @since 6.2
	 *
	 * @global array $shortcode_tags List of shortcode tags and their callback hooks.
	 *
	 * @param string  $content     Content to search for shortcodes.
	 * @param boolean $ignore_html When true, shortcodes inside HTML elements will be skipped.
	 *                            Default false.
	 * @return string Content with shortcodes filtered out.
	 */
	function themecomplete_do_shortcode( $content, $ignore_html = false ) {
		global $wp_embed;

		// The < character when used in math formulas conflicts with the
		// shortcode functionality and several cases so we use the following
		// workaround to fix it.
		$content = str_replace( '<', '<>THEMECOMPLETEDIDREPLACE', $content );

		// Disable acf ajax filter in order to get correct prices.
		add_filter( 'acf/ajax/shortcode_capability', '__return_false' );
		if ( $wp_embed ) {
			$content = do_shortcode( $wp_embed->run_shortcode( $content ), $ignore_html );
		} else {
			$content = do_shortcode( $content, $ignore_html );
		}
		remove_filter( 'acf/ajax/shortcode_capability', '__return_false' );

		$content = str_replace( '<>THEMECOMPLETEDIDREPLACE', '<', $content );

		return $content;
	}
}

/**
 * Convert a number to locale independent string
 * without E notation and without loosing precision.
 *
 * @param integer|float|double $number The number to convert.
 * @param integer              $scale This optional parameter is used to set the number of digits after the decimal place in the result.
 * @return string The locale independent converted number.
 */
function themecomplete_bcconv( $number, $scale = -1 ) {
	$append   = '';
	$decimals = $scale;
	if ( -1 === $decimals ) {
		$decimals = ini_get( 'precision' ) - floor( log10( abs( $number ) ) );
	}
	if ( 0 > $decimals ) {
		$number  *= pow( 10, $decimals );
		$append   = str_repeat( '0', -$decimals );
		$decimals = 0;
	}
	return number_format( $number, $decimals, '.', '' ) . $append;
}

/**
 * Computes the factoral (x!).
 *
 * @param mixed   $fact The number to convert.
 * @param integer $scale This optional parameter is used to set the number of digits after the decimal place in the result.
 * @return string The factoral (x!).
 */
function themecomplete_bcfact( $fact, $scale = 100 ) {
	return '1' === (string) $fact ? 1 : bcmul( (string) $fact, themecomplete_bcfact( bcsub( (string) $fact, '1' ), $scale ), $scale );
}

/**
 * Computes e^x, where e is Euler's constant.
 *
 * @param mixed   $x The number to e is raised to.
 * @param integer $iters The number of itterations to use.
 * @param integer $scale This optional parameter is used to set the number of digits after the decimal place in the result.
 * @return string The e^x.
 */
function themecomplete_bcexp( $x, $iters = 50, $scale = 100 ) {
	$res = bcadd( '1.0', (string) $x, $scale );
	for ( $i = 0; $i < $iters; $i++ ) {
		$res += bcdiv( bcpow( (string) $x, strval( $i + 2 ), $scale ), themecomplete_bcfact( ( $i + 2 ), $scale ), $scale );
	}
	return $res;
}

/**
 * Computes ln(x).
 *
 * @param mixed   $x The number to get the ln(x).
 * @param integer $iters The number of itterations to use.
 * @param integer $scale This optional parameter is used to set the number of digits after the decimal place in the result.
 * @return string The ln(x).
 */
function themecomplete_bcln( $x, $iters = 50, $scale = 100 ) {
	$result = '0.0';
	for ( $i = 0; $i < $iters; $i++ ) {
		$pow      = bcadd( '1.0', bcmul( (string) $i, '2.0', $scale ), $scale );
		$mul      = bcdiv( '1.0', (string) $pow, $scale );
		$fraction = bcmul( $mul, bcpow( bcdiv( bcsub( (string) $x, '1.0', $scale ), bcadd( (string) $x, '1.0', $scale ), $scale ), $pow, $scale ), $scale );
		$result   = bcadd( $fraction, $result, $scale );
	}
	$res = bcmul( '2.0', $result, $scale );
	return $res;
}

/**
 * Computes a^b, where a and b can have decimal digits, be negative and/or very large.
 * Also works for 0^0. Only able to calculate up to 10 digits. Quite slow.
 *
 * @param string  $a The base, as a string.
 * @param string  $b The exponent, as a string.
 * @param integer $iters The number of itterations to use.
 * @param integer $scale This optional parameter is used to set the number of digits after the decimal place in the result.
 * @return string The ln(x).
 */
function themecomplete_bcpow( $a, $b, $iters = 50, $scale = 100 ) {
	if ( ctype_digit( $b ) ) { // Check if $b is integer.
		return bcpow( (string) $a, (string) $b );
	}
	$ln = themecomplete_bcln( $a, $iters, $scale );
	return themecomplete_bcexp( bcmul( $ln, $b, $scale ), $iters, $scale );
}

/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 *
 * @since 6.4
 *
 * @param string $color The hex color.
 * @return string
 */
function themecomplete_sanitize_hex_color( $color ) {
	$color = sanitize_hex_color( $color );
	if ( ! is_string( $color ) ) {
		$color = '';
	}
	return $color;
}

/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * Saving hex colors without a hash puts the burden of adding the hash on the
 * UI, which makes it difficult to use or upgrade to other color types such as
 * rgba, hsl, rgb, and HTML color names.
 *
 * Returns either '', a 3 or 6 digit hex color (without a #), or null.
 *
 * @since 6.4
 *
 * @param string $color The hex color without a hash.
 * @return string
 */
function themecomplete_sanitize_hex_color_no_hash( $color ) {
	$color = sanitize_hex_color_no_hash( $color );
	if ( ! is_string( $color ) ) {
		$color = '';
	}
	return $color;
}
