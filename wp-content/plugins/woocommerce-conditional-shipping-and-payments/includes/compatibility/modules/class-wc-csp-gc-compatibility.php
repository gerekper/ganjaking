<?php
/**
 * WC_CSP_Gift_Cards_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Gift Cards Compatibility.
 *
 * @version 1.15.0
 */
class WC_CSP_Gift_Cards_Compatibility {

	/**
	 * Used for sharing memory between hooks.
	 * @var WC_CSP_Check_Result
	 */
	public static $shared_result;

	/**
	 * Used for saving all GC affected global rules.
	 * @var array
	 */
	private static $affected_rules;

	/**
	 * Initialization.
	 */
	public static function init() {

		// Load additional conditions.
		self::load_conditions();

		// Add Gift Cards as payment gateway option.
		add_action( 'woocommerce_csp_admin_payment_gateway_option', array( __CLASS__, 'add_gift_cards_payment_gateway' ), 11, 4 );

		// Search affected rules and cache them.
		add_action( 'init', array( __CLASS__, 'cache_affected_rules' ) );

		// Validate.
		add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'validate' ), 9 );
		add_action( 'woocommerce_review_order_before_order_total', array( __CLASS__, 'validate' ), 9 );
		add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate' ) );

		// Admin validate processing for sanity.
		add_filter( 'woocommerce_csp_process_admin_global_fields', array( __CLASS__, 'remove_gift_cards_condition' ), 10, 3 );
		add_filter( 'woocommerce_csp_admin_payment_gateway_option_description', array( __CLASS__, 'get_option_desciption' ), 10, 2 );
	}

	/**
	 * Filter option description for GC payment gateway.
	 *
	 * @param  string  $gateway_description
	 * @param  string  $key
	 * @return string
	 */
	public static function get_option_desciption( $gateway_description, $key ) {

		$gc_gateway = self::get_mock_payment_gateway();
		if ( $gc_gateway->id === $key ) {
			$gateway_description = $gc_gateway->title;
		}

		return $gateway_description;
	}

	/**
	 * Filter and validate conditions saved when using Gift Cards as payment gateway.
	 *
	 * @param  array   $processed_data
	 * @param  array   $posted_data
	 * @param  string  $restriction_id
	 * @return array
	 */
	public static function remove_gift_cards_condition( $processed_data, $posted_data, $restriction_id ) {

		if ( 'payment_gateways' !== $restriction_id ) {
			return $processed_data;
		}

		$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
		$rules       = $restriction->get_global_restriction_data();

		$error_rules = array();

		if ( in_array( self::get_mock_payment_gateway()->id, $processed_data[ 'gateways' ] ) ) {

			// Disallow Gift Cards used condition.
			if ( ! empty( $processed_data[ 'conditions' ] ) ) {

				foreach ( $processed_data[ 'conditions' ] as $condition_key => $condition_data ) {
					if ( 'gift_cards' === $condition_data[ 'condition_id' ] ) {
						unset( $processed_data[ 'conditions' ][ $condition_key ] );
						WC_CSP_Admin_Notices::add_notice( sprintf( __( 'Failed to save Gift Cards condition in rule #%d. The Gift Cards condition is not supported by Gift Cards payment gateway.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ), 'error', true );
					}
				}
			}
		}

		return $processed_data;
	}

	/**
	 * Find GC affected global rules.
	 *
	 * @return void
	 */
	public static function cache_affected_rules() {

		if ( is_admin() ) {
			return;
		}

		if ( ! is_null( self::$affected_rules ) ) {
			return self::$affected_rules;
		}

		$gc_gateway              = self::get_mock_payment_gateway();
		$restriction             = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
		$global_restriction_data = $restriction->get_global_restriction_data();
		$affected_rules          = array();

		foreach ( $global_restriction_data as $index => $restriction_data ) {
			if ( 'payment_gateways' !== $restriction_data[ 'restriction_id' ]  || empty( $restriction_data[ 'gateways' ] ) ) {
				continue;
			}

			if ( in_array( $gc_gateway->id, $restriction_data[ 'gateways' ] ) ) {
				$affected_rules[ $index ] = $restriction_data;
			}
		}

		self::$affected_rules = $affected_rules;
		return self::$affected_rules;
	}

	/**
	 * Whether or not rules have show excluded enabled.
	 *
	 * @return bool
	 */
	protected static function has_show_excluded() {
		$has_show_excluded = false;

		foreach ( self::$affected_rules as $index => $rule ) {
			if ( isset( $rule[ 'show_excluded' ] ) && 'yes' === $rule[ 'show_excluded' ] ) {
				$has_show_excluded = true;
				break;
			}
		}

		return $has_show_excluded;
	}

	/**
	 * Whether or not rules have static notices enabled.
	 *
	 * @return bool
	 */
	protected static function has_excluded_notices() {
		$has_excluded_notices = false;

		foreach ( self::$affected_rules as $index => $rule ) {
			if ( isset( $rule[ 'show_excluded' ] ) && 'yes' === $rule[ 'show_excluded' ] ) {
				if ( isset( $rule[ 'show_excluded_notices' ] ) && 'yes' === $rule[ 'show_excluded_notices' ] ) {
					$has_excluded_notices = true;
					break;
				}
			}
		}

		return $has_excluded_notices;
	}

	/**
	 * Validate.
	 *
	 * @return void
	 */
	public static function validate() {

		// Early exit.
		if ( empty( self::$affected_rules ) ) {
			return;
		}

		$is_checkout_validation = did_action( 'woocommerce_after_checkout_validation' );
		$is_using_giftcards     = ! empty( WC_GC()->giftcards->get() );

		// Has show excluded rule?
		if ( ! self::has_show_excluded() ) {

			// Has violations? Make sure to get them even if show excluded is off. (include_data=true)
			$result = self::validate_gift_cards_as_gateway();

			if ( ! $result->has_messages() ) {
				return;
			} else {

				add_filter( 'woocommerce_gc_disable_ui', '__return_true' );
				WC_GC()->cart->destroy_cart_session();
				WC()->cart->calculate_totals();

				if ( $is_checkout_validation && $is_using_giftcards ) {
					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}
				}

				return;
			}

		// Show excluded is ON at some rule.
		} else {

			if ( $is_checkout_validation || ! self::has_excluded_notices() ) {

				if ( ! $is_using_giftcards ) {
					return;
				}

				// Get messages.
				$result = self::validate_gift_cards_as_gateway( array( 'include_data' => true ) );
				if ( $result->has_messages() && $is_checkout_validation ) {
					foreach ( $result->get_messages() as $message ) {
						wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
					}
				}

				return;
			}

			if ( self::has_excluded_notices() ) {

				// Get static messages.
				self::$shared_result = self::validate_gift_cards_as_gateway( array( 'context' => 'check' ) );

				if ( self::$shared_result->has_messages() && ( $is_using_giftcards || ( WC_GC()->account->has_balance() && ! WC_GC()->cart->cart_contains_gift_card() ) ) ) {
					add_action( 'woocommerce_gc_totals_after_gift_cards', array( __CLASS__, 'print_static_notices' ) );
				}
			}
		}
	}

	/**
	 * Get shared static notices.
	 *
	 * @param  string $message
	 * @return string
	 */
	public static function get_static_messages( $message = '' ) {

		$messages = array();
		if ( ! is_null( self::$shared_result ) && self::$shared_result->has_messages() ) {
			foreach ( self::$shared_result->get_messages() as $message ) {
				$messages[] = $message[ 'text' ];
			}
		}

		return implode( '<br/>', $messages );
	}

	/**
	 * Print shared static notices.
	 *
	 * @return void
	 */
	public static function print_static_notices() {

		$output = '';
		if ( ! is_null( self::$shared_result ) && self::$shared_result->has_messages() ) {
			foreach ( self::$shared_result->get_messages() as $message ) {
				ob_start();
				echo '<tr><th></th><td>';
					echo '<div class="woocommerce-info">' . self::get_static_messages() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</td></tr>';
				$output .= ob_get_clean();
			}
		}

		echo wp_kses_post($output);
	}

	/**
	 * Print shared static notices.
	 *
	 * @param  array  $args
	 * @return WC_CSP_Check_Result
	 */
	public static function validate_gift_cards_as_gateway( $args = array() ) {

		$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );
		$result      = new WC_CSP_Check_Result();

		// Setup environment.
		$gc_gateway                   = self::get_mock_payment_gateway();
		$gateways                     = array( $gc_gateway->id => $gc_gateway );
		$args[ 'check_gateway' ]      = $gc_gateway->id;
		$args[ 'available_gateways' ] = $gateways;

		if ( is_checkout_pay_page() ) {

			global $wp;

			if ( isset( $wp->query_vars[ 'order-pay' ] ) && ( $order = wc_get_order( (int) $wp->query_vars[ 'order-pay' ] ) ) ) {

				$args[ 'order' ]        = $order;
				$args[ 'include_data' ] = true;
			}
		}

		/* ----------------------------------------------------------------- */
		/* Global Restrictions
		/* ----------------------------------------------------------------- */

		$map = $restriction->get_matching_rules_map( self::$affected_rules, $gateways, $args );
		if ( ! empty( $map ) ) {

			foreach ( $map as $rule_index => $excluded_gateway_ids ) {

				if ( ! empty( $excluded_gateway_ids ) ) {
					$result->add( 'payment_gateway_excluded_by_global_restriction', $restriction->get_resolution_message( self::$affected_rules[ $rule_index ], 'global', $args ) );
				}
			}
		}

		return $result;
	}

	/**
	 * Display Gift cards payment gateway after the last element.
	 *
	 * @param  string              $gateway_id
	 * @param  WC_Payment_Gateway  $gateway
	 * @param  array               $gateways
	 * @return void
	 */
	public static function add_gift_cards_payment_gateway( $gateway_id, $gateway, $gateways, $field_type ) {

		if ( 'product' === $field_type ) {
			return;
		}

		// If is last one.
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		$keys             = array_keys( $payment_gateways );
		$gateway          = self::get_mock_payment_gateway();

		if ( $gateway_id === end( $keys ) ) {
			echo '<option value="' . esc_attr( $gateway->id ) . '" ' . selected( in_array( $gateway->id, $gateways ), true, false ) . '>' . esc_html( $gateway->title ) . '</option>';
		}
	}

	/**
	 * Get an object to be treated as a WC_Payment_Gateway.
	 *
	 * @return object
	 */
	public static function get_mock_payment_gateway() {
		// Fake a payment gateway object.
		$payment_gateway_object        = new stdClass();
		$payment_gateway_object->title = __( 'Gift Cards', 'woocommerce-conditional-shipping-and-payments' );
		$payment_gateway_object->id    = 'wc_gc_gift_cards_as_gateway';

		return $payment_gateway_object;
	}

	/**
	 * Load additional conditions by adding to the global conditions array.
	 *
	 * @return void
	 */
	public static function load_conditions() {

		$load_conditions = array(
			'WC_CSP_Condition_Gift_Card_Product',
			'WC_CSP_Condition_Gift_Cards'
		);

		if ( is_array( WC_CSP()->conditions->conditions ) ) {

			foreach ( $load_conditions as $condition ) {

				$condition = new $condition();
				WC_CSP()->conditions->conditions[ $condition->id ] = $condition;
			}
		}
	}
}

WC_CSP_Gift_Cards_Compatibility::init();
