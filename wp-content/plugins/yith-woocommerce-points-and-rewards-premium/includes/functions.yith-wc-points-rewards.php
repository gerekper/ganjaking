<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Points and Rewards
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */

global $yith_ywpar_db_version;

$yith_ywpar_db_version = '1.0.2';

if ( ! function_exists( 'yith_ywpar_db_install' ) ) {
	/**
	 * Install the table yith_ywpar_points_log
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywpar_db_install() {
		global $wpdb;
		global $yith_ywpar_db_version;

		$installed_ver = get_option( 'yith_ywpar_db_version' );

		$table_name = $wpdb->prefix . 'yith_ywpar_points_log';

		$charset_collate = $wpdb->get_charset_collate();

		if ( ! $installed_ver ) {
			$sql = "CREATE TABLE $table_name (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `action` VARCHAR (255) NOT NULL,
            `order_id` int(11),
            `amount` int(11) NOT NULL,
            `date_earning` datetime NOT NULL,
            `cancelled` datetime,
            `description` TEXT, 
            PRIMARY KEY (id)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'yith_ywpar_db_version', $yith_ywpar_db_version );
		}

		if ( version_compare( $installed_ver, '1.0.2', '<=' ) ) {
			$sql  = "SELECT COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table_name'";
			$cols = $wpdb->get_col( $sql );

			if ( is_array( $cols ) && ! in_array( 'cancelled', $cols ) && version_compare( $installed_ver, '1.0.0', '=' ) ) {
				$sql = "ALTER TABLE $table_name ADD `cancelled` datetime";
				$wpdb->query( $sql );
			}
			if ( is_array( $cols ) && ! in_array( 'description', $cols ) && version_compare( $installed_ver, '1.0.1', '=' ) ) {
				$sql = "ALTER TABLE $table_name ADD `description` TEXT";
				$wpdb->query( $sql );
			}
			update_option( 'yith_ywpar_db_version', $yith_ywpar_db_version );
		}

	}
}

if ( ! function_exists( 'yith_ywpar_update_db_check' ) ) {
	/**
	 * check if the function yith_ywpar_db_install must be installed or updated
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywpar_update_db_check() {
		global $yith_ywpar_db_version;

		if ( get_site_option( 'yith_ywpar_db_version' ) != $yith_ywpar_db_version ) {

			yith_ywpar_db_install();
		}
	}
}

if ( ! function_exists( 'yith_ywpar_locate_template' ) ) {
	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path
	 * @param array  $var
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yith_ywpar_locate_template( $path, $var = null ) {

		global $woocommerce;

		if ( function_exists( 'WC' ) ) {
			$woocommerce_base = WC()->template_path();
		} elseif ( defined( 'WC_TEMPLATE_PATH' ) ) {
			$woocommerce_base = WC_TEMPLATE_PATH;
		} else {
			$woocommerce_base = $woocommerce->plugin_path() . '/templates/';
		}

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_YWPAR_DIR . 'templates/' . $path;

		$located = locate_template(
			array(
				$template_woocommerce_path, // Search in <theme>/woocommerce/
				$template_path,             // Search in <theme>/
				$plugin_path,                // Search in <plugin>/templates/
			)
		);

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_ywpar_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_ywpar_locate_template', $located, $path );
	}
}

if ( ! function_exists( 'yith_ywpar_get_roles' ) ) {
	/**
	 * Return the roles of users
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function yith_ywpar_get_roles() {
		global $wp_roles;
		$roles = array();

		foreach ( $wp_roles->get_names() as $key => $role ) {
			$roles[ $key ] = translate_user_role( $role );
		}
		return $roles;
	}
}

if ( ! function_exists( 'yith_ywpar_calculate_user_total_orders_amount' ) ) {
	/**
	 * Calculate the amount of all order completed and processed of a user
	 *
	 * @param     $user_id
	 * @param int          $order_id
	 *
	 * @param     $starter_date
	 *
	 * @return float
	 * @since 1.1.3
	 */

	function yith_ywpar_calculate_user_total_orders_amount( $user_id, $order_id = 0, $starter_date ) {

		$orders       = wc_get_orders(
			array(
				'customer'   => $user_id,
				'status'     => array( 'wc-completed', 'wc-processing' ),
				'date_after' => $starter_date,
			)
		);
		$o            = wc_get_order( $order_id );
		$total_amount = 0;

		if ( $orders ) {
			foreach ( $orders as $order ) {
				if ( $order_id && $order_id == $order->get_id() ) {
					continue;
				}
				$total_amount += $order->get_subtotal();
			}
		}

		if ( $o ) {
			$total_amount += $o->get_subtotal();
		}

		return $total_amount;

	}
}

if ( ! function_exists( 'ywpar_get_customer_order_count' ) ) {
	/**
	 * Calculate the amount of all order completed and processed of a user
	 *
	 * @param $user_id
	 *
	 * @param $starter_date
	 *
	 * @return float
	 * @internal param int $order_id
	 *
	 * @since    1.1.3
	 */

	function ywpar_get_customer_order_count( $user_id, $starter_date ) {

		$orders = wc_get_orders(
			array(
				'customer'   => $user_id,
				'status'     => array( 'wc-completed', 'wc-processing' ),
				'limit'      => -1,
				'date_after' => $starter_date,
			)
		);

		return count( $orders );

	}
}

/**
 * Provides functionality for array_column() to projects using PHP earlier than
 * version 5.5.
 *
 * @copyright (c) 2015 WinterSilence (http://github.com/WinterSilence)
 * @license MIT
 */
if ( ! function_exists( 'array_column' ) ) {
	/**
	 * Returns an array of values representing a single column from the input
	 * array.
	 *
	 * @param array $array A multi-dimensional array from which to pull a
	 *     column of values.
	 * @param mixed $columnKey The column of values to return. This value may
	 *     be the integer key of the column you wish to retrieve, or it may be
	 *     the string key name for an associative array. It may also be NULL to
	 *     return complete arrays (useful together with index_key to reindex
	 *     the array).
	 * @param mixed $indexKey The column to use as the index/keys for the
	 *     returned array. This value may be the integer key of the column, or
	 *     it may be the string key name.
	 * @return array
	 */
	function array_column( array $array, $columnKey, $indexKey = null ) {
		$result = array();
		foreach ( $array as $subArray ) {
			if ( ! is_array( $subArray ) ) {
				continue;
			} elseif ( is_null( $indexKey ) && array_key_exists( $columnKey, $subArray ) ) {
				$result[] = $subArray[ $columnKey ];
			} elseif ( array_key_exists( $indexKey, $subArray ) ) {
				if ( is_null( $columnKey ) ) {
					$result[ $subArray[ $indexKey ] ] = $subArray;
				} elseif ( array_key_exists( $columnKey, $subArray ) ) {
					$result[ $subArray[ $indexKey ] ] = $subArray[ $columnKey ];
				}
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'ywpar_get_price' ) ) {
	/**
	 * @param        $product
	 * @param int     $qty
	 * @param string  $price
	 *
	 * @return float|string
	 */
	function ywpar_get_price( $product, $qty = 1, $price = '' ) {

		// error_log( print_r( $product, true ) );
		// error_log($product->get_price());
		// error_log( $product instanceof WC_Product );
		if ( $price === '' && $product instanceof WC_Product ) {
			$price = $product->get_price();
		}

		$tax_display_mode = apply_filters( 'ywpar_get_price_tax_on_points', get_option( 'woocommerce_tax_display_shop', 'incl' ) );
		$display_price    = $tax_display_mode == 'incl' ? yit_get_price_including_tax( $product, $qty, $price ) : yit_get_price_excluding_tax( $product, $qty, $price );

		return $display_price;
	}
}

if ( ! function_exists( 'ywpar_get_subtotal_cart' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywpar_get_subtotal_cart() {

		$tax_display_mode = apply_filters( 'ywpar_get_price_tax_on_points', get_option( 'woocommerce_tax_display_shop' ) );
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$subtotal = ( $tax_display_mode == 'incl' ) ? WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() : WC()->cart->get_subtotal();
		} else {
			$subtotal = $tax_display_mode == 'incl' ? WC()->cart->subtotal : WC()->cart->subtotal_ex_tax;
		}

		return apply_filters( 'ywpar_rewards_points_cart_subtotal', $subtotal );
	}
}

if ( function_exists( 'AW_Referrals' ) ) {
	add_filter( 'woocommerce_coupon_is_valid', 'validate_ywpar_coupon', 11, 2 );
	/**
	 * Compatibility with AutomateWoo - Referrals Add-on
	 *
	 * @param $valid
	 * @param $coupon
	 *
	 * @return bool
	 */
	function validate_ywpar_coupon( $valid, $coupon ) {
		if ( 'ywpar_discount' == $coupon->code ) {
			return true;
		}

		return $valid;
	}
}

if ( ! function_exists( 'ywpar_coupon_is_valid' ) ) {

	/**
	 * Check if a coupon is valid
	 *
	 * @param       $coupon
	 * @param array  $object
	 *
	 * @return bool|WP_Error
	 * @throws Exception
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywpar_coupon_is_valid( $coupon, $object = array() ) {
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$wc_discounts = new WC_Discounts( $object );
			$valid        = $wc_discounts->is_coupon_valid( $coupon );
			$valid        = is_wp_error( $valid ) ? false : $valid;
		} else {
			$valid = $coupon->is_valid();
		}

		return $valid;
	}
}

if ( ! function_exists( 'remove_ywpar_coupons' ) ) {
	function remove_ywpar_coupons() {
		if ( WC()->cart ) {
			$coupons = WC()->cart->get_applied_coupons();
			foreach ( $coupons as $coupon ) {
				$current_coupon = new WC_Coupon( $coupon );
				if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $current_coupon ) ) {
					WC()->cart->remove_coupon( $coupon );
				}
			}
		}
	}
}

/**
 * WooCommerce Multilingual - MultiCurrency
 */
if ( function_exists( 'wcml_is_multi_currency_on' ) && wcml_is_multi_currency_on() ) {

	add_filter( 'ywpar_multi_currency_current_currency', 'ywpar_multi_currency_current_currency', 10 );
	if ( ! function_exists( 'ywpar_multi_currency_current_currency' ) ) {
		/**
		 * Get current currency.
		 *
		 * @param $currency
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_multi_currency_current_currency( $currency ) {
			global $woocommerce_wpml;
			$client_currency = $woocommerce_wpml->multi_currency->get_client_currency();

			return ! empty( $client_currency ) ? $client_currency : $currency;
		}
	}

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_get_active_currency_list' );
	if ( ! function_exists( 'ywpar_get_active_currency_list' ) ) {
		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_get_active_currency_list( $currencies ) {
			global $woocommerce_wpml;
			$multi_currencies = $woocommerce_wpml->multi_currency->get_currencies( 'include_default = true' );
			if ( $multi_currencies ) {
				$currencies = array_keys( $multi_currencies );
			}

			return $currencies;
		}
	}

	add_action( 'woocommerce_coupon_loaded', 'remove_wcml_filter', 1 );
	if ( ! function_exists( 'remove_wcml_filter' ) ) {

		/**
		 * Remove wcml filter when a coupon is loaded
		 *
		 * @param $coupon
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function remove_wcml_filter( $coupon ) {
			global $woocommerce_wpml;

			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupon ) ) {
				remove_action(
					'woocommerce_coupon_loaded',
					array(
						$woocommerce_wpml->multi_currency->coupons,
						'filter_coupon_data',
					),
					10
				);
			}
		}
	}


	// add_action('wcml_user_switch_language', 'ywpar_wcml_remove_ywpar_coupons', 10 , 3);
	add_action( 'wcml_switch_currency', 'ywpar_wcml_remove_ywpar_coupons' );
	/**
	 * @param string $code
	 * @param string $cookie_lang
	 * @param string $original
	 */
	function ywpar_wcml_remove_ywpar_coupons( $code = '', $cookie_lang = '', $original = '' ) {
		$action = current_action();
		switch ( $action ) {

			case 'wcml_user_switch_language':
				if ( ! empty( $code ) && $code != $cookie_lang ) {
					remove_ywpar_coupons();
				}

				break;
			case 'wcml_switch_currency':
				remove_ywpar_coupons();
				break;

		}
	}
}

if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {

	add_filter( 'ywpar_multi_currency_current_currency', 'ywpar_aelia_current_currency' );
	if ( ! function_exists( 'ywpar_aelia_current_currency' ) ) {
		function ywpar_aelia_current_currency( $currency ) {

			$instance = isset( $GLOBALS['woocommerce-aelia-currencyswitcher'] ) ? $GLOBALS['woocommerce-aelia-currencyswitcher'] : false;

			if ( $instance ) {
				$currency = $instance->get_selected_currency();
			}

			return $currency;
		}
	}

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_aelia_get_active_currency_list' );
	if ( ! function_exists( 'ywpar_aelia_get_active_currency_list' ) ) {

		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */

		function ywpar_aelia_get_active_currency_list( $currencies ) {
			$settings_controller = WC_Aelia_CurrencySwitcher::settings();
			$enabled_currencies  = $settings_controller->get_enabled_currencies();
			$currencies          = ! empty( $enabled_currencies ) ? $enabled_currencies : $currencies;

			return $currencies;
		}
	}

	add_action( 'woocommerce_coupon_get_amount', 'remove_aelia_filter_woocommerce_coupon_get_amount', 1, 2 );
	/**
	 * @param $amount
	 * @param $coupon
	 *
	 * @return mixed
	 */
	function remove_aelia_filter_woocommerce_coupon_get_amount( $amount, $coupon ) {
		$is_par = YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupon );
		if ( $is_par ) {
			remove_action( 'woocommerce_coupon_get_amount', array( WC_Aelia_CurrencyPrices_Manager::Instance(), 'woocommerce_coupon_get_amount' ), 5, 2 );
		}
		return $amount;
	}
}

if ( class_exists( 'WOOCS_STARTER' ) ) {

	add_filter( 'ywpar_get_active_currency_list', 'ywpar_woocommerce_currency_switcher_currency_list' );
	if ( ! function_exists( 'ywpar_woocommerce_currency_switcher_currency_list' ) ) {

		/**
		 * Return the list of active currencies.
		 *
		 * @param array $currencies
		 *
		 * @return array
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocommerce_currency_switcher_currency_list( $currencies ) {
			global $WOOCS; //phpcs:ignore
			$enabled_currencies = array_keys( $WOOCS->get_currencies() );
			$currencies         = ! empty( $enabled_currencies ) ? $enabled_currencies : $currencies;

			return $currencies;
		}
	}

	add_action( 'ywpar_before_currency_loop', 'ywpar_woocommerce_currency_switcher_before_currency_loop' );
	if ( ! function_exists( 'ywpar_woocommerce_currency_switcher_before_currency_loop' ) ) {
		/**
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocommerce_currency_switcher_before_currency_loop() {
			global $WOOCS; //phpcs:ignore
			remove_filter( 'woocommerce_currency_symbol', array( $WOOCS, 'woocommerce_currency_symbol' ), 9999 );
		}
	}

	add_action( 'before_return_calculate_price_worth', 'ywpar_woocs_before_rewards_message' );
	// add_action( 'ywpar_before_rewards_message', 'ywpar_woocs_before_rewards_message' );
	if ( ! function_exists( 'ywpar_woocs_before_rewards_message' ) ) {
		/**
		 * @since 1.5.2
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_before_rewards_message() {
			global $WOOCS; //phpcs:ignore
			remove_filter( 'wc_price_args', array( $WOOCS, 'wc_price_args' ), 9999 ); //phpcs:ignore
			remove_filter( 'raw_woocommerce_price', array( $WOOCS, 'raw_woocommerce_price' ), 9999 ); //phpcs:ignore
		}
	}

	add_action( 'ywpar_after_rewards_message', 'ywpar_woocs_after_rewards_message' );
	if ( ! function_exists( 'ywpar_woocs_after_rewards_message' ) ) {
		/**
		 * @since 1.5.2
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_after_rewards_message() {
			global $WOOCS; //phpcs:ignore
			add_filter( 'wc_price_args', array( $WOOCS, 'wc_price_args' ), 9999 ); //phpcs:ignore
			if ( ! $WOOCS->is_multiple_allowed ) { //phpcs:ignore
				add_filter( 'raw_woocommerce_price', array( $WOOCS, 'raw_woocommerce_price' ), 9999 ); //phpcs:ignore
			}

		}
	}


	add_filter( 'ywpar_get_point_earned_price', 'ywpar_woocs_convert_price', 10, 2 );
	add_filter( 'ywpar_calculate_rewards_discount_max_discount_fixed', 'ywpar_woocs_convert_price', 10, 1 );
	add_filter( 'ywpar_calculate_rewards_discount_max_discount_percentual', 'ywpar_woocs_convert_price', 10, 1 );
	if ( ! function_exists( 'ywpar_woocs_convert_price' ) ) {
		/**
		 * @param        $price
		 * @param string $currency
		 *
		 * @return float|int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_convert_price( $price, $currency = '' ) {
			global $WOOCS; //phpcs:ignore
			if ( $WOOCS->is_multiple_allowed ) { //phpcs:ignore
				return $price;
			}
			$currencies = $WOOCS->get_currencies(); //phpcs:ignore
			$currency   = empty( $currency ) ? $WOOCS->current_currency : $currency; //phpcs:ignore
			if ( isset( $currencies[ $currency ] ) ) {
				$price = $price * $currencies[ $currency ]['rate'];
			}

			return $price;
		}
	}

	add_filter( 'ywpar_hide_value_for_max_discount', 'ywpar_woocs_hide_value_for_max_discount' );
	if ( ! function_exists( 'ywpar_woocs_hide_value_for_max_discount' ) ) {
		/**
		 * @param $discount
		 * @return int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_hide_value_for_max_discount( $discount ) {
			global $WOOCS; //phpcs:ignore
			if ( $WOOCS->is_multiple_allowed ) { //phpcs:ignore
				$currencies = $WOOCS->get_currencies(); //phpcs:ignore
				return $WOOCS->back_convert( $discount, $currencies[ $WOOCS->current_currency ]['rate'] ); //phpcs:ignore
			}
			remove_all_filters( 'ywpar_calculate_rewards_discount_max_discount_fixed' );
			remove_all_filters( 'ywpar_calculate_rewards_discount_max_discount_percentual' );

			return YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
		}
	}

	add_filter( 'ywpar_adjust_discount_value', 'ywpar_woocs_adjust_discount_value' );
	if ( ! function_exists( 'ywpar_woocs_adjust_discount_value' ) ) {
		/**
		 * @param $discount
		 * @return int
		 * @since  1.5.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function ywpar_woocs_adjust_discount_value( $discount ) {
			global $WOOCS; //phpcs:ignore
			if ( $WOOCS->is_multiple_allowed ) { //phpcs:ignore
				$currencies = $WOOCS->get_currencies(); //phpcs:ignore
				$discount   = $WOOCS->back_convert( $discount, $currencies[ $WOOCS->current_currency ]['rate'] ); //phpcs:ignore
			}
			return $discount;
		}
	}
}

/**
 *
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
if ( ! function_exists( 'ywpar_conversion_points_multilingual' ) ) {
	/**
	 * Conversion point based on
	 */
	function ywpar_conversion_points_multilingual() {

		$old_conversion = get_option( 'yit_ywpar_multicurrency', false );
		if ( ! $old_conversion ) {
			$default_currency = get_woocommerce_currency();
			$roles            = yith_ywpar_get_roles();

			$options = array(
				'earn_points_conversion_rate',
				'rewards_conversion_rate',
				'rewards_percentual_conversion_rate',
			);

			foreach ( $options as $option_name ) {
				$conversion_role = YITH_WC_Points_Rewards()->get_option( $option_name );
				// error_log( print_r( $conversion_role, true ) );
				$new_conversion_role = get_conversion_rate_with_default_currency( $conversion_role, $default_currency );
				// error_log( print_r( $conversion_role, true ) );
				YITH_WC_Points_Rewards()->set_option( $option_name, $new_conversion_role );
			}

			$options_by_roles = array( 'earn_points_role_', 'rewards_points_role_', 'rewards_points_percentual_role_' );
			foreach ( $options_by_roles as $option_name ) {
				foreach ( $roles as $role ) {
					$conversion_role     = YITH_WC_Points_Rewards()->get_option( $option_name . $role );
					$new_conversion_role = get_conversion_rate_with_default_currency( $conversion_role, $default_currency );
					YITH_WC_Points_Rewards()->set_option( $option_name . $role, $new_conversion_role );
				}
			}

			update_option( 'yit_ywpar_multicurrency', true );
		}

	}
}


/**
 * @param $options
 * @param $currency
 *
 * @return array
 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
 */
function get_conversion_rate_with_default_currency( $options, $currency ) {
	$new_option = array();
	if ( isset( $options['points'] ) ) {
		$new_option[ $currency ] = $options;
	} else {
		$new_option = $options;
	}

	return $new_option;
}


/**
 * Remove duplicate roles from the array
 *
 * @param $value
 *
 * @return mixed
 */
function ywpar_option_role_convertion_sanitize( $value ) {
	$sanitized_values = $value;
	if ( $value && isset( $value['role_conversion'] ) ) {
		$sanitized_values = array();
		$roles            = array();
		foreach ( $value['role_conversion'] as $item ) {
			if ( isset( $item['role'] ) && ! in_array( $item['role'], $roles ) ) {
				$sanitized_values['role_conversion'][] = $item;
				$roles[]                               = $item['role'];
			}
		}
	}

	return $sanitized_values;
}

/**
 * Remove empty fields from the array
 *
 * @param $value
 *
 * @return mixed
 */
function ywpar_option_extrapoints_sanitize( $value ) {
	$sanitized_values = $value;
	if ( $value && isset( $value['list'] ) ) {
		$sanitized_values = array();
		foreach ( $value['list'] as $item ) {
			if ( empty( $item['number'] ) || empty( $item['points'] ) ) {
				continue;
			}
			$sanitized_values['list'][] = $item;
		}
	}

	return $sanitized_values;
}

if ( ! function_exists( 'ywpar_get_usable_comments' ) ) {
	/**
	 * @param $user_id
	 * @param $starter_date
	 *
	 * @return array|int
	 */
	function ywpar_get_usable_comments( $user_id, $starter_date ) {

		$args = array(
			'status'    => 1,
			'user_id'   => $user_id,
			'post_type' => 'product',
			'number'    => '',
		);

		if ( $starter_date ) {
			$d                  = explode( '-', $starter_date );
			$args['date_query'] = array(
				array(
					'after'     => array(
						'year'  => $d[0],
						'month' => $d[1],
						'day'   => $d[2],
					),
					'inclusive' => true,
				),
			);
		}

		$usable_comments = get_comments( $args );

		return $usable_comments;

	}
}

if ( ! function_exists( 'ywpar_options_porting' ) ) {
	/**
	 * @param $old_options
	 */
	function ywpar_options_porting( $old_options ) {
		$earn_points_for_role    = array();
		$rewards_points_for_role = array();
		$reward_method           = isset( $old_options['conversion_rate_method'] ) ? $old_options['conversion_rate_method'] : 'fixed';
		foreach ( $old_options as $key => $value ) {
			if ( strpos( $key, 'earn_points_role_' ) !== false ) {
				$new_value                                 = $value;
				$new_value['role']                         = str_replace( 'earn_points_role_', '', $key );
				$earn_points_for_role['role_conversion'][] = $new_value;
				continue;
			}
			if ( strpos( $key, 'rewards_points_role_' ) !== false ) {
				$new_value                                    = $value;
				$new_value['role']                            = str_replace( 'rewards_points_role_', '', $key );
				$rewards_points_for_role['role_conversion'][] = $new_value;
				continue;
			}
			if ( 'extra_points' == $key ) {
				$ywpar_amount_spent_exp       = array();
				$ywpar_review_exp             = array();
				$ywpar_num_order_exp          = array();
				$ywpar_number_of_points_exp   = array();
				$ywpar_points_on_registration = '';
				if ( $value ) {
					foreach ( $value as $extrp ) {
						if ( isset( $extrp['option'] ) ) {
							switch ( $extrp['option'] ) {
								case 'reviews':
									$ywpar_review_exp['list'][] = array(
										'number' => $extrp['value'],
										'points' => $extrp['points'],
										'repeat' => isset( $extrp['repeat'] ) ? $extrp['repeat'] : 0,
									);
									break;
								case 'num_of_orders':
									$ywpar_num_order_exp['list'][] = array(
										'number' => $extrp['value'],
										'points' => $extrp['points'],
										'repeat' => isset( $extrp['repeat'] ) ? $extrp['repeat'] : 0,
									);
									break;
								case 'reviews':
									break;
								case 'amount_spent':
									$ywpar_amount_spent_exp['list'][] = array(
										'number' => $extrp['value'],
										'points' => $extrp['points'],
										'repeat' => isset( $extrp['repeat'] ) ? $extrp['repeat'] : 0,
									);
									break;
								case 'points':
									$ywpar_number_of_points_exp['list'][] = array(
										'number' => $extrp['value'],
										'points' => $extrp['points'],
										'repeat' => isset( $extrp['repeat'] ) ? $extrp['repeat'] : 0,
									);
									break;
								case 'registration':
									$ywpar_points_on_registration = $extrp['points'];
									break;
							}
						}
					}

					if ( $ywpar_amount_spent_exp ) {
						update_option( 'ywpar_enable_amount_spent_exp', 'yes' );
						update_option( 'ywpar_amount_spent_exp', $ywpar_amount_spent_exp );
					}

					if ( $ywpar_review_exp ) {
						update_option( 'ywpar_enable_review_exp', 'yes' );
						update_option( 'ywpar_review_exp', $ywpar_review_exp );
					}

					if ( $ywpar_num_order_exp ) {
						update_option( 'ywpar_enable_num_order_exp', 'yes' );
						update_option( 'ywpar_num_order_exp', $ywpar_num_order_exp );
					}

					if ( $ywpar_number_of_points_exp ) {
						update_option( 'ywpar_enable_number_of_points_exp', 'yes' );
						update_option( 'ywpar_number_of_points_exp', $ywpar_number_of_points_exp );
					}

					if ( ! empty( $ywpar_points_on_registration ) ) {
						update_option( 'ywpar_enable_points_on_registration_exp', 'yes' );
						update_option( 'ywpar_points_on_registration', $ywpar_points_on_registration );
					}
				}

				continue;
			}

			$key   = 'ywpar_' . $key;
			$key   = apply_filters( 'ywpar_porting_options_key', $key, $value );
			$value = apply_filters( 'ywpar_porting_options_value', $value, $key );

			update_option( $key, $value );
		}

		if ( $earn_points_for_role ) {
			update_option( 'ywpar_earn_points_role_conversion_rate', $earn_points_for_role );
		}

		if ( $rewards_points_for_role ) {
			$key = $reward_method == 'fixed' ? 'ywpar_rewards_points_role_rewards_fixed_conversion_rate' : 'ywpar_rewards_points_role_rewards_percentage_conversion_rate';
			update_option( $key, $rewards_points_for_role );
		}

	}
}

if ( ! function_exists( 'ywpar_get_order_status_to_earn_points' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywpar_get_order_status_to_earn_points() {
		$options = array(
			'woocommerce_order_status_completed'  => __( 'Order Completed', 'yith-woocommerce-points-and-rewards' ),
			'woocommerce_payment_complete'        => __( 'Payment Completed', 'yith-woocommerce-points-and-rewards' ),
			'woocommerce_order_status_processing' => __( 'Order Processing', 'yith-woocommerce-points-and-rewards' ),
		);

		return apply_filters( 'ywpar_order_status_to_earn_points', $options );
	}
}

if ( ! function_exists( 'ywpar_add_order_points_summary' ) ) {
	/**
	 *
	 * @param $order
	 */
	function ywpar_add_order_points_summary( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$message        = '';
		$point_earned   = get_post_meta( $order->get_id(), '_ywpar_points_earned', 1 );
		$point_redeemed = get_post_meta( $order->get_id(), '_ywpar_redemped_points', 1 );

		if ( $point_earned ) {
			$message = sprintf( '<strong>%s</strong> <span>%d</span>', esc_html( __( 'Points earned:', 'yith-woocommerce-points-and-rewards' ) ), esc_html( $point_earned ) );
		}

		if ( $point_redeemed ) {
			$message .= $message ? '<br>' : '';
			$message .= sprintf( '<strong>%s</strong> <span>%d</span>', esc_html( __( 'Points used:', 'yith-woocommerce-points-and-rewards' ) ), esc_html( $point_redeemed ) );
		}

		echo apply_filters( 'ywpar_add_order_points_summary', $message ? '<p class="ywpar-order-point-summary">' . $message . '</p>' : '', $order, $point_earned, $point_redeemed ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'ywpar_get_users_for_bulk' ) ) {
	/**
	 * @param $datas
	 * @param $step
	 *
	 * @return array|bool
	 */
	function ywpar_get_users_for_bulk( $datas, $step ) {
		$result      = false;
		$user_number = 50;
		$offset      = ( $step - 1 ) * $user_number;
		$user_search = $datas['ywpar_type_user_search'];
		$args        = array(
			'offset'      => $offset,
			'number'      => $user_number,
			'count_total' => 1,
			'fields'      => 'ID',
		);

		switch ( $user_search ) {
			case 'everyone':
				break;
			case 'role_list':
				$args['role__in'] = isset( $datas['ywpar_user_role'] ) ? $datas['ywpar_user_role'] : array();
				break;
			case 'role_list_excluded':
				$args['role__not_in'] = isset( $datas['ywpar_user_role'] ) ? $datas['ywpar_user_role'] : array();
				break;
			case 'customers_list':
				$args['include'] = isset( $datas['ywpar_customer_list'] ) ? $datas['ywpar_customer_list'] : array();
				break;
			case 'customers_list_excluded':
				$args['exclude'] = isset( $datas['ywpar_customer_list'] ) ? $datas['ywpar_customer_list'] : array();
				break;
		}

		$users_query = new WP_User_Query( $args );
		if ( $users_query->get_total() ) {
			$percentage = ceil( 100 * ( $user_number * $step ) / $users_query->get_total() );
			$percentage = $percentage > 100 ? 100 : $percentage;
			$result     = array(
				'total'      => $users_query->get_total(),
				'percentage' => $percentage,
				'users'      => $users_query->get_results(),
				'next_step'  => ( ( $user_number * $step ) < $users_query->get_total() ) ? ++$step : 'done',
			);
		}

		return $result;
	}
}

if ( ! function_exists( 'ywpar_date_placeholders' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywpar_date_placeholders() {
		/**
		 * Get Date placeholders
		 *
		 * @return  array
		 * @since   1.6.0
		 * @author  Alberto Ruggiero, Emanuela Castorina
		 */
		return apply_filters(
			'ywpar_date_placeholders',
			array(
				'yy-mm-dd' => __( 'YYYY-MM-DD', 'yith-woocommerce-points-and-rewards' ),
				'yy/mm/dd' => __( 'YYYY/MM/DD', 'yith-woocommerce-points-and-rewards' ),
				'mm-dd-yy' => __( 'MM-DD-YYYY', 'yith-woocommerce-points-and-rewards' ),
				'mm/dd/yy' => __( 'MM/DD/YYYY', 'yith-woocommerce-points-and-rewards' ),
				'dd-mm-yy' => __( 'DD-MM-YYYY', 'yith-woocommerce-points-and-rewards' ),
				'dd/mm/yy' => __( 'DD/MM/YYYY', 'yith-woocommerce-points-and-rewards' ),
			)
		);

	}
}

if ( ! function_exists( 'ywpar_get_date_formats' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywpar_get_date_formats() {
		/**
		 * Get Date formats
		 *
		 * @return  array
		 * @since   1.6.0
		 * @author  Alberto Ruggiero, Emanuela Castorina
		 */
		return apply_filters(
			'ywpar_date_formats',
			array(
				'yy-mm-dd' => 'Y-m-d',
				'yy/mm/dd' => 'Y/m/d',
				'mm-dd-yy' => 'm-d-Y',
				'mm/dd/yy' => 'm/d/Y',
				'dd-mm-yy' => 'd-m-Y',
				'dd/mm/yy' => 'd/m/Y',
			)
		);

	}
}
if ( ! function_exists( 'ywpar_get_date_patterns' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywpar_get_date_patterns() {

		/**
		 * Get Date patterns
		 *
		 * @return  array
		 * @since   1.6.0
		 * @author  Alberto Ruggiero, Emanuela Castorina
		 */
		return apply_filters(
			'ywpar_date_patterns',
			array(
				'yy-mm-dd' => '([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
				'yy/mm/dd' => '([0-9]{4})\/(0[1-9]|1[012])\/(0[1-9]|1[0-9]|2[0-9]|3[01])',
				'mm-dd-yy' => '(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])-([0-9]{4})',
				'mm/dd/yy' => '(0[1-9]|1[012])\/(0[1-9]|1[0-9]|2[0-9]|3[01])\/([0-9]{4})',
				'dd-mm-yy' => '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})',
				'dd/mm/yy' => '(0[1-9]|1[0-9]|2[0-9]|3[01])\/(0[1-9]|1[012])\/([0-9]{4})',
			)
		);

	}
}

if ( function_exists( 'icl_st_is_registered_string' ) && ! function_exists( 'ywpar_translate_string_filter' ) ) {
	add_action( 'init', 'ywpar_translate_string_filter', 10, 4 );
	function ywpar_translate_string_filter() {

		$options = get_option( 'yit_ywpar_options' );
		if ( ! $options ) {
			return;
		}
		$context = 'admin_texts_yit_ywpar_options';
		// check if it is necessary start

		$names = array(
			'[yit_ywpar_options]points_label_singular'    => 'ywpar_points_label_singular',
			'[yit_ywpar_options]points_label_plural'      => 'ywpar_points_label_plural',
			'[yit_ywpar_options]label_order_completed'    => 'ywpar_label_order_completed',
			'[yit_ywpar_options]label_order_cancelled'    => 'ywpar_label_order_cancelled',
			'[yit_ywpar_options]label_admin_action'       => 'ywpar_label_admin_action',
			'[yit_ywpar_options]label_reviews_exp'        => 'ywpar_label_reviews_exp',
			'[yit_ywpar_options]label_login_exp'          => 'ywpar_label_login_exp',
			'[yit_ywpar_options]label_points_exp'         => 'ywpar_label_points_exp',
			'[yit_ywpar_options]label_amount_spent_exp'   => 'ywpar_label_amount_spent_exp',
			'[yit_ywpar_options]label_checkout_threshold_exp' => 'ywpar_label_checkout_threshold_exp',
			'[yit_ywpar_options]label_num_of_orders_exp'  => 'ywpar_label_num_of_orders_exp',
			'[yit_ywpar_options]label_expired_points'     => 'ywpar_label_expired_points',
			'[yit_ywpar_options]label_order_refund'       => 'ywpar_label_order_refund',
			'[yit_ywpar_options]label_refund_deleted'     => 'ywpar_label_refund_deleted',
			'[yit_ywpar_options]label_redeemed_points'    => 'ywpar_label_redeemed_points',
			'[yit_ywpar_options]label_apply_discounts'    => 'ywpar_label_apply_discounts',
			'[yit_ywpar_options]single_product_message'   => 'ywpar_single_product_message',
			'[yit_ywpar_options]cart_message'             => 'ywpar_cart_message',
			'[yit_ywpar_options]checkout_message'         => 'ywpar_checkout_message',
			'[yit_ywpar_options]rewards_cart_message'     => 'ywpar_rewards_cart_message',
			'[yit_ywpar_options]expiration_email_content' => 'ywpar_expiration_email_content',
			'[yit_ywpar_options]update_point_email_content' => 'ywpar_update_point_email_content',
			'[yit_ywpar_options]my_account_page_label'    => 'ywpar_my_account_page_label',
		);

		global $sitepress;
		$langs = $sitepress->get_ls_languages();

		foreach ( $names as $key => $new_name ) {
			$old_value = '';
			if ( isset( $options[ str_replace( '[yit_ywpar_options]', '', $key ) ] ) ) {
				$old_value = $options[ str_replace( '[yit_ywpar_options]', '', $key ) ];
			}

			$is_translated = icl_st_string_has_translations( 'admin_texts_' . $new_name, $new_name );

			if ( $is_translated ) {
				continue;
			}

			foreach ( $langs as $key_l => $lang ) {
				// $old_translated  = icl_st_string_has_translations( $context, $key );
				$tr = wpml_translate_single_string_filter( $old_value, $context, $key, $key_l );
				icl_register_string( 'admin_texts_' . $new_name, $new_name, $tr, false, $key_l );
			}

			icl_unregister_string( $context, $key );
		}
	}
}
