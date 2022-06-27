<?php
/**
 * WooCommerce CSP Extend Store API.
 *
 * A class to extend the store public API with CSP related data
 *
 * @package WooCommerce Conditional Shipping and Payments
 * @since   1.13.0
 */

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\Exceptions\InvalidCartException;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

class WC_CSP_Store_API {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'woocommerce-conditional-shipping-and-payments';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		// Extend StoreAPI.
		self::extend_store();

		add_action( 'woocommerce_store_api_cart_errors', array( __CLASS__, 'validate_cart' ), 10, 2 );
		add_action( 'woocommerce_store_api_checkout_order_processed', array( __CLASS__, 'checkout_order_processed' ) );
	}

	/**
	 * Register cart data handler.
	 */
	public static function extend_store() {

		if ( function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			woocommerce_store_api_register_endpoint_data(
				array(
					'endpoint'        => CartSchema::IDENTIFIER,
					'namespace'       => self::IDENTIFIER,
					'data_callback'   => array( 'WC_CSP_Store_API', 'extend_cart_data' ),
					'schema_callback' => array( 'WC_CSP_Store_API', 'extend_cart_schema' ),
					'schema_type'     => ARRAY_A,
				)
			);
		}
	}

	/**
	 * Validate all payment restrictions in the cart state.
	 *
	 * @return array
	 */
	public static function extend_cart_data() {

		$cart_data = array(
			'restrictions' => array(
				'gateways' => self::get_payment_methods_cart_state()
			)
		);

		// Add debugger if enabled.
		if ( wc_csp_debug_enabled() ) {
			$cart_data[ 'debugger' ] = self::get_debugger_cart_state();
		}

		return $cart_data;
	}

	/**
	 * Calculate CSP debug messages.
	 *
	 * @return array
	 */
	protected static function get_debugger_cart_state() {
		$debug_messages = array();
		$debug_messages[ 'gateways' ]           = WC_CSP_Debugger::prepare_debug_info_excluded_payment_gateways();
		$debug_messages[ 'shipping_methods' ]   = WC_CSP_Debugger::prepare_debug_info_excluded_shipping_methods();
		$debug_messages[ 'shipping_countries' ] = WC_CSP_Debugger::prepare_debug_info_excluded_shipping_destinations();
		return $debug_messages;
	}

	/**
	 * Forces all restrictions to behave as if Show Excluded was off.
	 *
	 * @return boolean
	 */
	public static function force_include_data() {
		return true;
	}

	/**
	 * Validate all payment restrictions and format the data to be added in the cart state.
	 *
	 * @return array
	 */
	protected static function get_payment_methods_cart_state() {
		$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );

		remove_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );
		$all_gateways = WC()->payment_gateways->get_available_payment_gateways();
		add_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );

		// Part 1: Get gateways that show excluded is off.
		$remaining_gateways = $restriction->exclude_payment_gateways( $all_gateways, true );

		// Part 2: Get messages regardless if show excluded on/off.
		remove_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );
		add_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_include_data' ), 999 );

		// Part 3. Construct state.
		$gateway_state = array();
		foreach ( $all_gateways as $gateway ) {
			$is_hidden                     = ! array_key_exists( $gateway->id, $remaining_gateways );
			$gateway_state[ $gateway->id ] = array(
				'is_hidden'   => $is_hidden,
				'is_excluded' => $is_hidden,
				'message'     => '',
			);

			// Bail out early.
			if ( $is_hidden ) {
				continue;
			}

			$result = $restriction->validate_checkout(
				array(
					'check_gateway' => $gateway->id,
				)
			);

			if ( $result->has_messages() ) {
				$resolution_message = '';
				foreach ( $result->get_messages() as $message ) {
					$resolution_message .= $message[ 'text' ] . '<br/>'; // TODO: We need to implement a mutliple error handler for this(?)
				}

				$gateway_state[ $gateway->id ][ 'is_hidden' ]   = false;
				$gateway_state[ $gateway->id ][ 'is_excluded' ] = true;
				$gateway_state[ $gateway->id ][ 'message' ]     = $resolution_message;
			}
		}

		remove_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_include_data' ), 999 );
		add_filter( 'woocommerce_available_payment_gateways', array( $restriction, 'exclude_payment_gateways' ) );

		return $gateway_state;
	}

	/**
	 * Validates cart (destination, shipping method) restrictions in order payment context.
	 *
	 * @param  \WP_Error  $errors  Errors.
	 * @param  \WC_Cart   $cart    Cart object.
	 */
	public static function validate_cart( $errors, $cart ) {

		/*
		 * === Debt Alert ===
		 *
		 * Cart session (shipping destination/method restrictions) + payment method (payment gateway restrictions) validation is currently limited to run when attempting to place an order.
		 * This is in line with the default behavior of CSP.
		 * Ideally, when 'Show Excluded' is enabled for a restriction, validation should populate the Store API 'errors' field and the Cart and Checkout Blocks should give live feedback to the user.
		 *
		 */
		if ( ! WC_CSP_Core_Compatibility::is_store_api_request( 'checkout', 'POST' ) ) {
			return;
		}

		$restrictions = array(
			'shipping_methods',
			'shipping_countries',
		);

		foreach ( $restrictions as $restriction_type ) {

			// TODO: Performance improvements - Check and run validate checkout for Show Excluded only in the cart context.
			$result = WC_CSP()->restrictions->get_restriction( $restriction_type )->validate_checkout( array() );

			if ( $result->has_messages() ) {
				foreach ( $result->get_messages() as $message_key => $message ) {
					$code = self::IDENTIFIER . "-error-{$restriction_type}-{$message_key}";
					$errors->add(
						$code,
						html_entity_decode( wp_strip_all_tags( $message[ 'text' ] ), ENT_QUOTES | ENT_SUBSTITUTE )
					);
				}
			}
		}
	}

	/**
	 * Validates the order payment gateway.
	 *
	 * @throws InvalidCartException
	 *
	 * @param  \WC_Order  $order  Order object.
	 */
	public static function checkout_order_processed( $order ) {

		// Bail out early.
		if ( ! $order->needs_payment() ) {
			return;
		}

		// Validate selected payment.
		add_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_include_data' ), 999 );
		$payment_method   = $order->get_payment_method();
		$restriction_type = 'payment_gateways';
		$restriction      = WC_CSP()->restrictions->get_restriction( $restriction_type );
		$result           = $restriction->validate_checkout(
			array(
				'check_gateway' => $payment_method
			)
		);
		remove_filter( 'woocommerce_csp_rule_map_include_restriction_data', array( __CLASS__, 'force_include_data' ), 999 );

		// Return error if necessary.
		if ( $result->has_messages() ) {

			$errors = new \WP_Error();
			$code   = self::IDENTIFIER . "-error-{$restriction_type}-0";

			foreach ( $result->get_messages() as $message_key => $message ) {
				$code = self::IDENTIFIER . "-error-{$restriction_type}-{$message_key}";
				$errors->add(
					$code,
					html_entity_decode( wp_strip_all_tags( $message[ 'text' ] ), ENT_QUOTES | ENT_SUBSTITUTE )
				);
			}

			throw new InvalidCartException(
				'woocommerce_csp_payment_error',
				$errors,
				409
			);
		}
	}

	/**
	 * Register csp schema into cart endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_schema() {

		$schema = array(
			'restrictions' => array(
				'description' => __( 'Restrictions related cart data.', 'woocommerce-conditional-shipping-and-payments' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'items'       => array(
					'gateways' => array(
						'description' => __( 'Payment gateways restrictions data.', 'woocommerce-conditional-shipping-and-payments' ),
						'type'        => 'array',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
						'items'       => array(
							'is_hidden'   => array(
								'description' => __( 'Whether the payment method is hidden in the checkout.', 'woocommerce-conditional-shipping-and-payments' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'is_excluded' => array(
								'description' => __( 'Whether the payment method is excluded in the checkout.', 'woocommerce-conditional-shipping-and-payments' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'message'     => array(
								'description' => __( 'Payment method resolution message, if any.', 'woocommerce-conditional-shipping-and-payments' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
						)
					),
				)
			)
		);

		if ( wc_csp_debug_enabled() ) {
			$schema[ 'debugger' ] = array(
				'description' => __( 'Debugger messages.', 'woocommerce-conditional-shipping-and-payments' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'items'       => array(
					'gateways' => array(
						'description' => __( 'The debug message for payment methods.', 'woocommerce-conditional-shipping-and-payments' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'shipping_methods' => array(
						'description' => __( 'The debug message for shipping methods.', 'woocommerce-conditional-shipping-and-payments' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'shipping_countries' => array(
						'description' => __( 'The debug message for shipping countries.', 'woocommerce-conditional-shipping-and-payments' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
				)
			);
		}

		return $schema;
	}
}
