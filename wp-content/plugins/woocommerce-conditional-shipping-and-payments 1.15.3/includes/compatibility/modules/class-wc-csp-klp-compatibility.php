<?php
/**
 * WC_CSP_KLP_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Klarna Payments Compatibility.
 *
 * @version  1.7.6
 */
class WC_CSP_KLP_Compatibility {

	/**
	 * Klarna payment option IDs => names.
	 * @var array
	 */
	public static $klarna_options;

	/**
	 * Initialization.
	 */
	public static function init() {

		self::$klarna_options = array(
			'klarna_payments_pay_now'       => __( 'Pay Now', 'woocommerce-conditional-shipping-and-payments' ),
			'klarna_payments_pay_later'     => __( 'Pay Later', 'woocommerce-conditional-shipping-and-payments' ),
			'klarna_payments_pay_over_time' => __( 'Slice It', 'woocommerce-conditional-shipping-and-payments' )
		);

		// Make individual payment options available in post-boxes.
		add_action( 'woocommerce_csp_admin_payment_gateway_option_default', array( __CLASS__, 'add_admin_options' ), 10, 4 );
		add_action( 'woocommerce_csp_admin_payment_gateway_option_description', array( __CLASS__, 'admin_option_description' ), 10, 3 );
		add_action( 'woocommerce_csp_restricted_payment_gateway_title', array( __CLASS__, 'restricted_option_title' ), 10, 2 );

		// Hide Klarna options if needed.
		add_action( 'woocommerce_review_order_after_order_total', array( __CLASS__, 'hide_klarna_options' ), 11 );

		// Validate chosen Klarna option.
		add_action( 'woocommerce_csp_payment_gateway_restricted', array( __CLASS__, 'restrict_klarna_options' ), 10, 3 );
	}

	/**
	 * Modify gateway title in admin restriction descriptions.
	 *
	 * @param  string  $title
	 * @param  string  $gateway_id
	 * @param  array   $gateways
	 * @return string
	 */
	public static function admin_option_description( $description, $gateway_id, $gateways ) {

		if ( isset( self::$klarna_options[ $gateway_id ] ) ) {

			$klarna      = isset( $gateways[ 'klarna_payments' ] ) ? $gateways[ 'klarna_payments' ] : false;
			$description = '';

			if ( $klarna ) {
				$description = is_callable( array( $klarna, 'get_method_title' ) ) ? $klarna->get_method_title() : $klarna->method_title;
			}

			$description .= ' &ndash; ' . self::$klarna_options[ $gateway_id ];
		}

		return $description;
	}

	/**
	 * Make individual payment options available in post-boxes.
	 *
	 * @param  string              $option_html
	 * @param  string              $gateway_id
	 * @param  WC_Payment_Gateway  $gateway
	 * @param  array               $gateways
	 * @return array
	 */
	public static function add_admin_options( $option_html, $gateway_id, $gateway, $gateways ) {

		if ( 'klarna_payments' !== $gateway_id ) {
			return $option_html;
		}

		$title = is_callable( array( $gateway, 'get_method_title' ) ) ? $gateway->get_method_title() : $gateway->method_title;

		foreach ( self::$klarna_options as $klarna_option_id => $klarna_option_title ) {
			$option_title = $title . ' &ndash; ' . $klarna_option_title;
			$option_html .= '<option value="' . esc_attr( $klarna_option_id ) . '" ' . selected( in_array( $klarna_option_id, $gateways ), true, false ) . '>' . $option_title . '</option>';
		}

		return $option_html;
	}

	/**
	 * See individual Klarna Payments options as restricted.
	 *
	 * @param  WC_Payment_Gateway  $gateway
	 * @param  array               $restricted_gateways
	 * @return boolean
	 */
	public static function restrict_klarna_options( $is_restricted, $gateway, $restricted_gateways ) {

		if ( $gateway->id === 'klarna_payments' ) {

			$posted_gateway_id = isset( $_POST[ 'payment_method' ] ) ? wc_clean( $_POST[ 'payment_method' ] ) : false;

			if ( in_array( $posted_gateway_id, $restricted_gateways ) || in_array( 'klarna_payments', $restricted_gateways ) ) {
				$is_restricted = true;
			}
		}

		return $is_restricted;
	}

	/**
	 * Modify restricted gateway title.
	 *
	 * @param  string              $title
	 * @param  WC_Payment_Gateway  $gateway
	 * @return string
	 */
	public static function restricted_option_title( $title, $gateway ) {

		if ( $gateway->id === 'klarna_payments' ) {

			$posted_gateway_id = isset( $_POST[ 'payment_method' ] ) ? wc_clean( $_POST[ 'payment_method' ] ) : false;

			if ( $posted_gateway_id && isset( self::$klarna_options[ $posted_gateway_id ] ) ) {
				$klarna_option_title = self::$klarna_options[ $posted_gateway_id ];
				$title              .= ' &ndash; ' . $klarna_option_title;
			}
		}

		return $title;
	}

	/**
	 * Hide Klarna options.
	 *
	 * @return void
	 */
	public static function hide_klarna_options() {

		$klarna_options = WC()->session->get( 'klarna_payments_categories' );

		if ( ! empty( $klarna_options ) ) {

			$raw_gateways = WC()->payment_gateways->payment_gateways();

			if ( ! isset( $raw_gateways[ 'klarna_payments' ] ) ) {
				return;
			}

			$restriction = WC_CSP()->restrictions->get_restriction( 'payment_gateways' );

			foreach ( $klarna_options as $klarna_option ) {

				$klarna_option_id = 'klarna_payments_' . ( isset( $klarna_option->identifier ) ? $klarna_option->identifier : $klarna_option[ 'identifier' ] );

				$raw_gateways[ $klarna_option_id ]     = clone $raw_gateways[ 'klarna_payments' ];
				$raw_gateways[ $klarna_option_id ]->id = $klarna_option_id;
			}

			unset( $raw_gateways[ 'klarna_payments' ] );

			$gateways = $restriction->exclude_payment_gateways( $raw_gateways, true );

			foreach ( $klarna_options as $klarna_option_key => $klarna_option ) {

				$klarna_option_id = 'klarna_payments_' . ( isset( $klarna_option->identifier ) ? $klarna_option->identifier : $klarna_option[ 'identifier' ] );

				if ( ! isset( $gateways[ $klarna_option_id ] ) ) {
					unset( $klarna_options[ $klarna_option_key ] );
				}
			}

			WC()->session->set( 'klarna_payments_categories', $klarna_options );
		}
	}
}

WC_CSP_KLP_Compatibility::init();
