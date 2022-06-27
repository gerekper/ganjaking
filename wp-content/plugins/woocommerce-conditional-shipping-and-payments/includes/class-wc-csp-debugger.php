<?php
/**
 * WC_CSP_Debugger class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.11.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CSP Debugger
 *
 * @class    WC_CSP_Debugger
 * @version  1.13.0
 */
class WC_CSP_Debugger {

	/**
	 * Property to store if the debugger is running.
	 *
	 * @var boolean
	 */
	private static $is_running = false;

	/**
	 * Property to store if the debugger is enabled.
	 *
	 * @var boolean
	 */
	private static $enabled = false;

	/**
	 * Property to store the payment gateway restriction class.
	 *
	 * @var WC_CSP_Restrict_Payment_Gateways
	 */
	private static $payment_gateways_restriction = false;

	/**
	 * Property to store the shipping method restriction class.
	 *
	 * @var WC_CSP_Restrict_Shipping_Methods
	 */
	private static $shipping_methods_restriction = false;

	/**
	 * Property to store the shipping country restriction class.
	 *
	 * @var WC_CSP_Restrict_Shipping_Countries
	 */
	private static $shipping_destinations_restriction = false;

	/**
	 * Initialization and hooks.
	 */
	public static function init() {

		add_action( 'init', array( __CLASS__, 'initialize_debugger' ), 10 );

	}

	/**
	 * Initialize debugger.
	 *
	 * @return void
	 */
	public static function initialize_debugger() {

		self::$enabled = wc_csp_debug_enabled();

		if ( ! self::$enabled ) {
			return;
		}

		self::$payment_gateways_restriction      = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
		self::$shipping_methods_restriction      = WC_CSP()->restrictions->get_restriction( 'shipping_methods' );
		self::$shipping_destinations_restriction = WC_CSP()->restrictions->get_restriction( 'shipping_countries' );

		add_action( 'woocommerce_review_order_before_cart_contents', array( __CLASS__, 'add_debug_notices' ), 20 );
	}

	/**
	 * Return if the debugger is currently running.
	 *
	 * @return boolean
	 */
	public static function is_running() {
		return self::$is_running;
	}

	/**
	 * Adds debug arguments force include restriction data
	 *
	 * @param  bool   $include_data
	 * @param  array  $restriction
	 * @param  array  $payload
	 * @param  array  $args
	 *
	 * @return bool
	 */
	public static function force_rule_map_to_include_restriction_data( $include_data, $restriction, $payload, $args ) {

		if ( self::is_running() ) {
			return true;
		}
		return $include_data;
	}

	/**
	 * Adds debug variables to shipping packages to invalidate cache.
	 *
	 * @param  array  $packages
	 *
	 * @return array
	 */
	public static function add_debug_variables_to_packages( $packages ) {

		foreach ( $packages as $package_key => $package ) {
			WC_CSP_Restriction::add_extra_package_variable( $packages[ $package_key ], 'wc_csp_debug', microtime() );
		}

		return $packages;
	}

	/**
	 * Adds checkout notices for all debug messages.
	 *
	 * @return void
	 */
	public static function add_debug_notices() {

		if ( ! self::$enabled ) {
			return;
		}

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			return;
		}

		// Payment methods.
		$payment_gateways_debug = self::prepare_debug_info_excluded_payment_gateways();
		if ( ! empty( $payment_gateways_debug ) ) {
			wc_add_notice( $payment_gateways_debug, 'notice' );
		}

		// Shipping methods.
		$shipping_methods_debug = self::prepare_debug_info_excluded_shipping_methods();
		if ( ! empty( $shipping_methods_debug ) ) {
			wc_add_notice( $shipping_methods_debug, 'notice' );
		}

		// Shipping destinations.
		$shipping_destinations_debug = self::prepare_debug_info_excluded_shipping_destinations();
		if ( ! empty( $shipping_destinations_debug ) ) {
			wc_add_notice( $shipping_destinations_debug, 'notice' );
		}
	}

	/**
	 * Prepares the notice data for the payment gateways that CSP has excluded.
	 * Sends the data to the renderer for parsing and creating notices.
	 *
	 * @return void
	 */
	public static function prepare_debug_info_excluded_payment_gateways() {

		// Add and remove the following filters when done.
		self::$is_running = true;
		remove_filter( 'woocommerce_available_payment_gateways', array( self::$payment_gateways_restriction, 'exclude_payment_gateways' ) );
		add_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_rule_map_to_include_restriction_data' ), 100, 4 );

		$results  = array();
		$gateways = WC()->payment_gateways->get_available_payment_gateways();

		foreach ( $gateways as $gateway ) {

			$result = self::$payment_gateways_restriction->validate_checkout(
				array(
					'check_gateway' => $gateway->id,
				)
			);

			$results[ $gateway->id ] = array();
			if ( $result->has_messages() ) {
				$results[ $gateway->id ][ 'global' ]  = $result->get_messages( 'payment_gateway_excluded_by_global_restriction' );
				$results[ $gateway->id ][ 'product' ] = $result->get_messages( 'payment_gateway_excluded_by_product_restriction' );
			}
		}

		add_filter( 'woocommerce_available_payment_gateways', array( self::$payment_gateways_restriction, 'exclude_payment_gateways' ) );
		remove_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_rule_map_to_include_restriction_data' ), 100 );
		self::$is_running = false;

		return self::construct_debug_html( $results, 'payment_gateways' );
	}

	/**
	 * Prepares the notice data for the shipping methods/packages that CSP has excluded.
	 * Sends the data to the renderer for parsing and creating notices.
	 *
	 * @return void
	 */
	public static function prepare_debug_info_excluded_shipping_methods() {

		self::$is_running = true;
		remove_filter( 'woocommerce_package_rates', array( self::$shipping_methods_restriction, 'exclude_package_shipping_methods' ), 10 );
		add_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_rule_map_to_include_restriction_data' ), 100, 4 );
		add_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'add_debug_variables_to_packages' ), 10 );

		WC()->cart->calculate_totals();
		$shipping_packages = apply_filters( 'woocommerce_csp_shipping_packages', WC()->shipping->get_packages() );

		$results = array();
		if ( empty( $shipping_packages ) ) {
			return;
		}

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

			foreach ( $shipping_packages as $package_index => $package ) {

				if ( empty( $package[ 'rates' ] ) ) {
					continue;
				}

				foreach ( $package[ 'rates' ] as $rate_id => $rate ) {

					$rate_id           = $rate->get_id();
					$canonical_rate_id = $rate_id;

					$method_id   = $rate->get_method_id();
					$instance_id = $rate->get_instance_id();

					if ( $method_id && $instance_id ) {
						$canonical_rate_id = $method_id . ':' . $instance_id;
					}

					$result = self::$shipping_methods_restriction->validate_checkout(
						array(
							'check_package_index' => $package_index,
							'check_rate'          => $rate_id,
						)
					);

					// Try again if the canonical rate id is not the same as the rate id.
					if ( $rate_id !== $canonical_rate_id && ! $result->has_messages() ) {

						$rate_id = $canonical_rate_id;
						$result  = self::$shipping_methods_restriction->validate_checkout(
							array(
								'check_package_index' => $package_index,
								'check_rate'          => $rate_id,
							)
						);
					}

					$results[ $package_index ][ $rate_id ] = array(
						'rate_label' => $rate->get_label(),
					);

					if ( $result->has_messages() ) {
						$results[ $package_index ][ $rate_id ][ 'global' ]  = $result->get_messages( 'shipping_method_excluded_by_global_restriction' );
						$results[ $package_index ][ $rate_id ][ 'product' ] = $result->get_messages( 'shipping_method_excluded_by_product_restriction' );
					}
				}
			}
		}

		// Reset previous state and re-calculate excluded Shipping Methods.
		add_filter( 'woocommerce_package_rates', array( self::$shipping_methods_restriction, 'exclude_package_shipping_methods' ), 10, 2 );
		remove_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_rule_map_to_include_restriction_data' ), 100 );
		remove_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'add_debug_variables_to_packages' ), 10 );
		self::$is_running = false;

		WC()->cart->calculate_totals();

		return self::construct_debug_html( $results, 'shipping_methods' );
	}

	/**
	 * Prepares the notice data for the shipping packages that CSP has excluded because of the selected shipping destination.
	 * Sends the data to the renderer for parsing and creating notices.
	 *
	 * @return void
	 */
	public static function prepare_debug_info_excluded_shipping_destinations() {

		self::$is_running = true;

		$results = array();
		$result  = self::$shipping_destinations_restriction->validate_checkout();

		if ( $result->has_messages() ) {
			foreach ( $result->get_messages() as $message ) {
				$shipping_package_index = $message[ 'debug_info' ][ 'shipping_package_index' ];
				if ( 'country_excluded_by_global_restriction' === $message[ 'code' ] ) {
					$results[ $shipping_package_index ][ 'global' ][] = $message;
				} elseif ( 'country_excluded_by_product_restriction' === $message[ 'code' ] ) {
					$results[ $shipping_package_index ][ 'product' ][] = $message;
				}
			}
		}

		self::$is_running = false;

		return self::construct_debug_html( $results, 'shipping_destinations' );
	}

	/**
	 * Parses the results and renders notices.
	 *
	 * @param  array   $results
	 * @param  string  $restriction_type
	 *
	 * @return void
	 */
	private static function construct_debug_html( $results, $restriction_type ) {

		$markup = '';
		switch ( $restriction_type ) {
			case 'payment_gateways':

				$heading = sprintf(
					'<p><strong>%1$s &mdash; %2$s</strong></p>',
					__( 'Conditional Shipping and Payments Debug Data', 'woocommerce-conditional-shipping-and-payments' ),
					__( 'Payment Gateways', 'woocommerce-conditional-shipping-and-payments' )
				);

				$markup   = $heading . '<ul>';
				$gateways = WC()->payment_gateways->payment_gateways();

				foreach ( $results as $gateway_id => $result ) {

					if ( ! isset( $gateways[ $gateway_id ] ) ) {
						continue;
					}

					$counter = 0;
					if ( isset( $result[ 'product' ] ) ) {
						$counter += count( $result[ 'product' ] );
					}
					if ( isset( $result[ 'global' ] ) ) {
						$counter += count( $result[ 'global' ] );
					}

					/* @var WC_Payment_Gateway $gateway */
					$gateway = $gateways[ $gateway_id ];

					$gateway_title_markup = sprintf(
						_n( '%1$s&nbsp;&nbsp;<small>(%2$s rule active)</small>', '%1$s&nbsp;&nbsp;<small>(%2$s rules active)</small>', $counter, 'woocommerce-conditional-shipping-and-payments' ),
						is_callable( array( $gateway, 'get_method_title' ) ) ? $gateway->get_method_title() : $gateway->method_title,
						$counter
					);

					$gateway_markup = '<li>' . $gateway_title_markup;

					foreach ( $result as $level => $messages ) {

						if ( empty( $messages ) || ! is_array( $messages ) ) {
							continue;
						}

						$gateway_markup .= '<ul>';
						foreach ( $messages as $message ) {
							$gateway_markup .= self::prepare_message_markup( $level, $message );
						}
						$gateway_markup .= '</ul>';
					}

					$gateway_markup .= '</li>';
					$markup         .= $gateway_markup;
				}

				$markup .= '</ul>';

				break;

			case 'shipping_methods':

				$heading = sprintf(
					'<p><strong>%1$s &mdash; %2$s</strong></p>',
					__( 'Conditional Shipping and Payments Debug Data', 'woocommerce-conditional-shipping-and-payments' ),
					__( 'Shipping Methods', 'woocommerce-conditional-shipping-and-payments' )
				);

				$markup .= $heading;

				$package_displayed_index = 0;
				$packages_count          = count( $results );

				if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

					if ( ! $packages_count ) {
						$markup .= '<p>' . __( 'No shipping options found.', 'woocommerce-conditional-shipping-and-payments' ) . '</p>';
						break;
					}

					foreach ( $results as $package_index => $packages ) {

						// We're not reusing $package_index as it might be like 2021_12_11_monthly_synced_0
						$package_displayed_index++;

						if ( $packages_count > 1 ) {
							$package_title_markup = sprintf(
								'<p>%1$s</p>',
								__( 'Package', 'woocommerce-conditional-shipping-and-payments' ) . ' ' . $package_displayed_index
							);

							$markup .= $package_title_markup;
						}

						$markup .= '<ul>';
						foreach ( $packages as $rate_id => $result ) {

							$counter = 0;
							if ( isset( $result[ 'product' ] ) ) {
								$counter += count( $result[ 'product' ] );
							}
							if ( isset( $result[ 'global' ] ) ) {
								$counter += count( $result[ 'global' ] );
							}

							$method_name         = $result[ 'rate_label' ];
							$method_markup       = '';
							$method_title_markup = sprintf(
								_n( '%1$s&nbsp;&nbsp;<small>(%2$s rule active)</small>', '%1$s&nbsp;&nbsp;<small>(%2$s rules active)</small>', $counter, 'woocommerce-conditional-shipping-and-payments' ),
								$method_name,
								$counter
							);

							$method_markup .= '<li>' . $method_title_markup;
							foreach ( $result as $level => $messages ) {

								if ( empty( $messages ) || ! is_array( $messages ) ) {
									continue;
								}

								$method_markup .= '<ul>';
								foreach ( $messages as $message ) {
									$method_markup .= self::prepare_message_markup( $level, $message );
								}
								$method_markup .= '</ul>';
							}

							$method_markup .= '</li>';
							$markup        .= $method_markup;
						}

						$markup .= '</ul>';
					}

				} else {

					$markup .= __( 'WooCommerce 3.2+ is required to generate debug data for Shipping Methods.', 'woocommerce-conditional-shipping-and-payments' );
				}

				break;

			case 'shipping_destinations':

				$heading = sprintf(
					'<p><strong>%1$s &mdash; %2$s</strong></p>',
					__( 'Conditional Shipping and Payments Debug Data', 'woocommerce-conditional-shipping-and-payments' ),
					__( 'Shipping Destinations', 'woocommerce-conditional-shipping-and-payments' )
				);
				$markup  .= $heading;

				$packages_count = count( $results );
				if ( ! $packages_count ) {
					$markup .= '<p>' . __( 'The current destination is not restricted by any rule.', 'woocommerce-conditional-shipping-and-payments' ) . '</p>';
					break;
				}

				foreach ( $results as $package_index => $result ) {

					$counter = 0;
					if ( isset( $result[ 'product' ] ) ) {
						$counter += count( $result[ 'product' ] );
					}
					if ( isset( $result[ 'global' ] ) ) {
						$counter += count( $result[ 'global' ] );
					}

					if ( $packages_count > 1 ) {
						$package_title_markup = sprintf(
							_n( '%1$s&nbsp;&nbsp;<small>(%2$s rule active)</small>', '%1$s&nbsp;&nbsp;<small>(%2$s rules active)</small>', $counter, 'woocommerce-conditional-shipping-and-payments' ),
							__( 'Package', 'woocommerce-conditional-shipping-and-payments' ) . ' ' . $package_index,
							$counter
						);

						$markup .= '<p>' . $package_title_markup . '</p>';
					}

					$package_markup = '';
					$package_markup .= '<ul>';
					foreach ( $result as $level => $messages ) {

						if ( empty( $messages ) || ! is_array( $messages ) ) {
							continue;
						}
						foreach ( $messages as $message ) {
							$package_markup .= self::prepare_message_markup( $level, $message );
						}
					}

					$package_markup .= '</ul>';
					$markup         .= $package_markup;
				}

				break;
		}

		return $markup;
	}

	/**
	 * Prepares message markup.
	 *
	 * @param  string  $level
	 * @param  array   $message
	 *
	 * @return string
	 */
	private static function prepare_message_markup( $level, $message ) {

		if ( 'product' === $level ) {
			$product    = $message[ 'debug_info' ][ 'product' ];
			$product_id = $product->get_parent_id() ? absint( $product->get_parent_id() ) : absint( $product->get_id() );
		}

		if ( ! isset( $message[ 'debug_info' ][ 'description' ] ) ) {
			$restriction                              = WC_CSP()->restrictions->get_restriction( $message[ 'debug_info' ][ 'restriction_id' ] );
			$message[ 'debug_info' ][ 'description' ] = $restriction->get_options_description( $message[ 'debug_info' ] );
		}

		$message_markup = sprintf(
			'<li>' . _x( '%1$s #%2$s: <a href="%3$s" target="_blank">%4$s</a>', 'rule name followed by index and url', 'woocommerce-conditional-shipping-and-payments' ) . '</li>',
			'product' === $level ? $product->get_name() : __( 'Global', 'woocommerce-conditional-shipping-and-payments' ),
			$message[ 'debug_info' ][ 'index' ] + 1,
			'product' === $level
				? admin_url( 'post.php?post=' . $product_id . '&action=edit' )
				: admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $message[ 'debug_info' ][ 'restriction_id' ] . '&view_rule=' . $message[ 'debug_info' ][ 'index' ] ),
			isset( $message[ 'debug_info' ][ 'description' ] ) ? $message[ 'debug_info' ][ 'description' ] : ''
		);

		return $message_markup;
	}

}

WC_CSP_Debugger::init();
