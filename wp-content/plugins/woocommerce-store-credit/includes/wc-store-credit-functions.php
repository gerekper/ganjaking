<?php
/**
 * Useful functions for the plugin
 *
 * @package WC_Store_Credit/Functions
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

// Include core functions.
require 'wc-store-credit-customer-functions.php';
require 'wc-store-credit-product-functions.php';
require 'wc-store-credit-product-category-functions.php';
require 'wc-store-credit-coupon-functions.php';
require 'wc-store-credit-order-functions.php';

/**
 * Gets if it's allowed to create coupons with tax included.
 *
 * Enables or disable the setting `Include tax`.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function wc_store_credit_coupons_can_inc_tax() {
	$allowed = wc_prices_include_tax() && 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' );

	/**
	 * Filters if it's allowed to create coupons with tax included.
	 *
	 * @since 3.1.2
	 *
	 * @param bool $allowed Whether it's allowed to create coupons with tax included.
	 */
	return apply_filters( 'wc_store_credit_coupons_can_inc_tax', $allowed );
}

/**
 * Gets if the 'store credit' coupons should be applied before taxes or not.
 *
 * This function is for backward compatibility.
 * Since version 3.0, all coupons are applied before taxes and their configuration is handled individually.
 *
 * @since 2.3.0
 * @since 2.4.0 Added `$the_order` parameter.
 * @since 3.0.0 The parameter `$the_order` is required.
 *
 * @param mixed $the_order Order object or ID.
 * @return bool
 */
function wc_store_credit_apply_before_tax( $the_order ) {
	$order = wc_store_credit_get_order( $the_order );

	if ( ! $order ) {
		return true;
	}

	$cache_key = 'wc_store_credit_apply_before_tax_order_' . $order->get_id();
	$found     = false;

	// Get the cached result. The $found parameter disambiguates a return of false.
	$before_tax = wp_cache_get( $cache_key, 'store_credit', false, $found );

	if ( ! $found ) {
		$version    = wc_get_store_credit_version_for_order( $order );
		$before_tax = ( version_compare( $version, '2.2', '!=' ) || wc_string_to_bool( $order->get_meta( '_store_credit_before_tax' ) ) );

		// Cache the result.
		wp_cache_set( $cache_key, $before_tax, 'store_credit' );
	}

	/**
	 * Filters if the 'store credit' coupons should be applied before taxes or not.
	 *
	 * @since 2.4.0
	 * @since 3.0.0 The second parameter is always a `WC_Order` object.
	 *
	 * @param bool     $before_tax True to apply the coupons before taxes. False otherwise.
	 * @param WC_Order $order      Order object.
	 */
	return apply_filters( 'wc_store_credit_apply_before_tax', $before_tax, $order );
}

/**
 * Check if currency has decimals.
 *
 * @since 2.4.4
 *
 * @param string $currency Currency to check.
 * @return bool
 */
function wc_store_credit_currency_has_decimals( $currency ) {
	return ( ! in_array( $currency, array( 'HUF', 'JPY', 'TWD' ), true ) );
}

/**
 * Gets the suffix for the script filenames.
 *
 * @since 3.0.0
 *
 * @return string The scripts suffix.
 */
function wc_store_credit_get_scripts_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
}

/**
 * Gets templates passing attributes and including the file.
 *
 * @since 3.0.0
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 */
function wc_store_credit_get_template( $template_name, $args = array() ) {
	wc_get_template( $template_name, $args, '', WC_STORE_CREDIT_PATH . 'templates/' );
}

/**
 * What type of request is this?
 *
 * @since 3.1.3
 *
 * @param string $type admin, ajax, cron, rest_api or frontend.
 * @return bool
 */
function wc_store_credit_is_request( $type ) {
	$is_request = false;

	switch ( $type ) {
		case 'admin':
			$is_request = is_admin();
			break;
		case 'ajax':
			$is_request = wp_doing_ajax();
			break;
		case 'cron':
			$is_request = wp_doing_cron();
			break;
		case 'frontend':
			$is_request = ( ! is_admin() || wp_doing_ajax() ) && ! wp_doing_cron() && ! wc_store_credit_is_request( 'rest_api' );
			break;
		case 'rest_api':
			$is_request = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
			break;
	}

	/**
	 * Filters if the request is of the specified type.
	 *
	 * @since 3.1.3
	 *
	 * @param bool   $is_request Whether the request is of the specified type.
	 * @param string $type       The request type.
	 */
	return apply_filters( 'wc_store_credit_is_request', $is_request, $type );
}

/**
 * Like wc_store_credit_get_template, but returns the HTML instead of outputting.
 *
 * @since 3.0.0
 *
 * @see wc_store_credit_get_template
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 * @return string
 */
function wc_store_credit_get_template_html( $template_name, $args = array() ) {
	ob_start();
	wc_store_credit_get_template( $template_name, $args );
	return ob_get_clean();
}

/**
 * Logs a message.
 *
 * @since 2.4.0
 *
 * @param string         $message The message to log.
 * @param string         $level   The level.
 * @param string         $handle  Optional. The log handlers.
 * @param WC_Logger|null $logger  Optional. The logger instance.
 */
function wc_store_credit_log( $message, $level = 'notice', $handle = 'wc_store_credit', $logger = null ) {
	if ( ! $logger ) {
		$logger = wc_get_logger();
	}

	if ( method_exists( $logger, $level ) ) {
		call_user_func( array( $logger, $level ), $message, array( 'source' => $handle ) );
	} else {
		$logger->add( $handle, $message );
	}
}

/**
 * Gets if the specified plugin is active.
 *
 * @since 4.1.0
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool
 */
function wc_store_credit_is_plugin_active( $plugin ) {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return is_plugin_active( $plugin );
}

/**
 * Gets the proportional discounts for the specified amounts.
 *
 * The discounts are calculated as a percentage of the total discounting amount.
 *
 * @since 3.0.0
 *
 * @param array $discounting_amounts An array with the discounting amounts.
 * @param float $total_discount      The total discount.
 * @param int   $precision           Optional. The rounding precision.
 * @return array
 */
function wc_store_credit_get_proportional_discounts( $discounting_amounts, $total_discount, $precision = 0 ) {
	$discounts = array();

	$total_discounting_amount = array_sum( $discounting_amounts );
	$total_discount           = (float) min( $total_discount, $total_discounting_amount );
	$cent_precision           = pow( 10, $precision );
	$trimmed_discounts        = array();

	foreach ( $discounting_amounts as $key => $discounting_amount ) {
		if ( $total_discounting_amount <= 0 ) {
			$discounts[ $key ] = 0;
		} else {
			// Trim the discounts with high precision instead of rounding them to avoid discrepancies with the total discount.
			$discount_percent = ( $discounting_amount / $total_discounting_amount );
			$raw_discount     = ( $total_discount * $discount_percent * $cent_precision );
			$trimmed_discount = floor( $raw_discount );

			$trimmed_discounts[ $key ] = ( $raw_discount - $trimmed_discount );
			$discounts[ $key ]         = ( $trimmed_discount / $cent_precision );
		}
	}

	// Round the result to avoid weird floating numbers.
	$remaining = round( $total_discount - array_sum( $discounts ), $precision );

	// Apply the remaining cents.
	if ( $remaining > 0 ) {
		arsort( $trimmed_discounts );

		// The remaining cents are always lower or equals to the number of items.
		$increment = ( 1 / $cent_precision );
		$keys      = array_keys( $trimmed_discounts );
		$keys      = array_slice( $keys, 0, ( $remaining / $increment ) );

		foreach ( $keys as $key ) {
			$discounts[ $key ] += $increment;
		}
	}

	return $discounts;
}

/**
 * Combine multiple amounts into a single array, preserving keys.
 *
 * @since 3.0.0
 *
 * @param array $combine_amounts Amounts to combine.
 * @return array
 */
function wc_store_credit_combine_amounts( $combine_amounts ) {
	$merged_amounts = array();

	foreach ( $combine_amounts as $amounts ) {
		foreach ( $amounts as $key => $amount ) {
			$recursive = is_array( $amount );

			if ( ! isset( $merged_amounts[ $key ] ) ) {
				$merged_amounts[ $key ] = ( $recursive ? array() : 0 );
			}

			if ( $recursive ) {
				$merged_amounts[ $key ] = wc_store_credit_combine_amounts( array( $merged_amounts[ $key ], $amount ) );
			} else {
				$merged_amounts[ $key ] += $amount;
			}
		}
	}

	return $merged_amounts;
}

/**
 * Gets the negative value of the specified amount.
 *
 * @since 3.0.0
 *
 * @param mixed $amount The amount.
 * @return mixed
 */
function wc_store_credit_get_negative( $amount ) {
	return -$amount;
}

/**
 * Whether rounding taxes at subtotal or per line.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function wc_store_credit_round_tax_at_subtotal() {
	return ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) );
}

/**
 * Gets the discount keys for the specified type.
 *
 * @since 3.0.0
 *
 * @param string $type The discount type. Accepts 'base', 'tax', 'base_tax', 'taxes', 'cart', 'shipping'.
 * @return array
 */
function wc_store_credit_discount_type_keys( $type ) {
	$types = array(
		'base'     => array( 'cart', 'shipping' ),
		'tax'      => array( 'cart_tax', 'shipping_tax' ),
		'base_tax' => array( 'cart', 'cart_tax', 'shipping', 'shipping_tax' ),
		'taxes'    => array( 'cart_taxes', 'shipping_taxes' ),
		'cart'     => array( 'cart', 'cart_tax', 'cart_taxes' ),
		'shipping' => array( 'shipping', 'shipping_tax', 'shipping_taxes' ),
	);

	$keys = ( isset( $types[ $type ] ) ? $types[ $type ] : array() );

	/**
	 * Filters the discount keys for the specified type.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $keys The discount keys.
	 * @param string $type The discount type.
	 */
	return apply_filters( 'wc_store_credit_discount_type_keys', $keys, $type );
}

/**
 * Checks if the string starts with the specified word.
 *
 * @since 3.0.0
 *
 * @param string $haystack Complete sentence.
 * @param string $needle   Excerpt.
 * @return bool
 */
function wc_store_credit_starts_with( $haystack, $needle ) {
	return ( substr( $haystack, 0, strlen( $needle ) ) === $needle );
}

/**
 * Gets the time period choices to use them in a select field.
 *
 * @since 3.2.0
 *
 * @param string $form Optional. The translation form. Accepts: 'singular', 'plural'. Default: 'singular'.
 * @return array
 */
function wc_store_credit_get_time_period_choices( $form = 'singular' ) {
	$number = ( 'singular' === $form ) ? 1 : 2;

	$periods = array(
		'day'   => _n( 'day', 'days', $number, 'woocommerce-store-credit' ),
		'week'  => _n( 'week', 'weeks', $number, 'woocommerce-store-credit' ),
		'month' => _n( 'month', 'months', $number, 'woocommerce-store-credit' ),
		'year'  => _n( 'year', 'years', $number, 'woocommerce-store-credit' ),
	);

	/**
	 * Filters the time period choices.
	 *
	 * @since 3.2.0
	 *
	 * @param array  $periods The time periods.
	 * @param string $form    The translation form.
	 */
	return apply_filters( 'wc_store_credit_time_period_choices', $periods, $form );
}

/**
 * Converts the value to a WC_DateTime.
 *
 * @since 3.3.0
 *
 * @param mixed $value String, timestamp or a Datetime object.
 * @return WC_DateTime|false Datetime object, false on failure.
 */
function wc_store_credit_get_datetime( $value ) {
	if ( $value instanceof WC_DateTime ) {
		return $value;
	}

	if ( $value instanceof DateTime ) {
		$value = $value->getTimestamp();
	}

	if ( is_numeric( $value ) ) {
		try {
			$datetime = new WC_DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
		} catch ( Exception $e ) {
			$datetime = false;
		}
	} else {
		$datetime = wc_string_to_datetime( $value );
	}

	return $datetime;
}

/**
 * Gets the HTML markup for a datetime.
 *
 * @since 3.3.0
 *
 * @param mixed $value String, timestamp or a Datetime object.
 * @return string
 */
function wc_store_credit_get_datetime_html( $value ) {
	$datetime    = wc_store_credit_get_datetime( $value );
	$date_format = wc_date_format();

	return sprintf(
		'<time datetime="%1$s" title="%2$s">%3$s</time>',
		esc_attr( $datetime->date( 'c' ) ),
		esc_html( $datetime->date_i18n( $date_format . ' ' . wc_time_format() ) ),
		esc_html( $datetime->date_i18n( $date_format ) )
	);
}
