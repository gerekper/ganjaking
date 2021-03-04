<?php
/**
 * WooCommerce Intuit Payments
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_4 as Framework;

/**
 * The credit card gateway class.
 *
 * @since 2.0.0
 */
class WC_Gateway_Inuit_Payments_Credit_Card extends WC_Gateway_Inuit_Payments {


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Intuit_Payments::CREDIT_CARD_ID,
			array(
				'method_title' => __( 'Intuit Payments Credit Card', 'woocommerce-gateway-intuit-payments' ),
				'supports'     => array(
					self::FEATURE_CARD_TYPES,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
				),
				'payment_type' => self::PAYMENT_TYPE_CREDIT_CARD,
			)
		);
	}


	/**
	 * Returns the description of this gateway for admin screens.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public function get_method_description() {

		$method_description = $this->method_description;

		if ( ! $this->is_configured() ) {

			$setup_wizard_link = sprintf(
				/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
				esc_html__( 'Click %1$shere%2$s to launch the Intuit Payments setup wizard.', 'woocommerce-gateway-intuit-payments' ),
				sprintf( '<a href="%s">', esc_attr( $this->get_plugin()->get_setup_wizard_handler()->get_setup_url() ) ),
				'</a>'
			);

			$method_description = sprintf( '%s. %s', $method_description, $setup_wizard_link );
		}

		/** @see \WC_Payment_Gateway::get_method_description() WooCommerce core filter */
		return apply_filters( 'woocommerce_gateway_method_description', $method_description, $this );
	}


	/**
	 * Gets the payment form field defaults.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {
			$defaults['account-number'] = '4111111111111111';
		}

		return $defaults;
	}


	/**
	 * Gets the credit card test case options.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_test_case_options() {

		return array(
			'emulate=10201' => __( 'Payment system error', 'woocommerce-gateway-intuit-payments' ),
			'emulate=10301' => __( 'Card number is invalid', 'woocommerce-gateway-intuit-payments' ),
			'emulate=10401' => __( 'General decline', 'woocommerce-gateway-intuit-payments' ),
		);
	}


	/**
	 * Removes the input names for the credit card number and CSC fields so
	 * they're not POSTed to the server.
	 *
	 * @since 2.0.0
	 * @param array $fields the payment form fields
	 * @return array
	 */
	public function remove_payment_form_field_input_names( $fields ) {

		$fields['card-number']['name'] = '';

		if ( isset( $fields['card-csc']['name'] ) ) {
			$fields['card-csc']['name'] = '';
		}

		return $fields;
	}


	/**
	 * Renders hidden inputs on the payment form for the card token & last four.
	 *
	 * These are populated by the client-side JS after successful tokenization.
	 *
	 * @since 2.0.0
	 */
	public function render_hidden_inputs() {

		parent::render_hidden_inputs();

		// card type
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . sanitize_html_class( $this->get_id_dasherized() ) . '-card-type' );
	}


	/**
	 * Validate the provided credit card fields.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::validate_credit_card_fields()
	 * @param bool $is_valid whether the fields are valid
	 * @return bool whether the fields are valid
	 */
	protected function validate_credit_card_fields( $is_valid ) {

		$valid_card_types = array(
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_VISA,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_MASTERCARD,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DISCOVER,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_DINERSCLUB,
			Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_JCB,
		);

		// card type
		if ( ! in_array( Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-card-type' ), $valid_card_types, true ) ) {

			Framework\SV_WC_Helper::wc_add_notice( __( 'Provided card type is invalid.', 'woocommerce-gateway-intuit-payments' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * The CSC field is verified client-side and thus always valid.
	 *
	 * @since 4.0.0
	 * @param string $field
	 * @return bool
	 */
	protected function validate_csc( $field ) {

		return true;
	}


	/**
	 * Gets the order object with payment information added.
	 *
	 * @since 2.0.0
	 * @param int $order_id the order ID
	 * @return \WC_Order the order object
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		if ( isset( $order->payment->js_token ) ) {

			// expiry month/year
			list( $order->payment->exp_month, $order->payment->exp_year ) = array_map( 'trim', explode( '/', Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-expiry' ) ) );

			// card data
			$order->payment->card_type = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-card-type' );
		}

		$order->payment->is_recurring = false;

		// add the recurring flag to Subscriptions renewal orders
		if ( $this->get_plugin()->is_subscriptions_active() ) {

			$order_id = $order->get_id();

			$order->payment->is_recurring = Framework\SV_WC_Plugin_Compatibility::is_wc_subscriptions_version_gte( '2.0' ) ? wcs_order_contains_renewal( $order_id ) : WC_Subscriptions_Order::order_contains_subscription( $order_id );
		}

		return $order;
	}


	/**
	 * Adds an order notice to held orders that require further action.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway::mark_order_as_held()
	 */
	public function mark_order_as_held( $order, $message, $response = null ) {

		parent::mark_order_as_held( $order, $message, $response );

		if ( $response && $response->get_status_message() ) {

			// if this was an authorization, mark as invalid for capture
			if ( $this->perform_credit_card_authorization( $order ) ) {
				$this->update_order_meta( $order, 'auth_can_be_captured', 'no' );
			}

			if ( $response->get_status_message() !== $message ) {
				$order->add_order_note( $response->get_status_message() );
			}
		}
	}


	/**
	 * Determines if the refund ended up being a void.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response refund response
	 * @return bool
	 */
	protected function maybe_void_instead_of_refund( $order, $response ) {

		return $response->is_void();
	}


	/**
	 * Gets meta data for an order.
	 *
	 * @since 2.6.0
	 *
	 * @param int|\WC_Order $order order object
	 * @param string $key meta key
	 * @return bool|string|array
	 */
	public function get_order_meta( $order, $key ) {

		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		$value = $order->get_meta( $this->get_order_meta_prefix() . $key );

		if ( in_array( $key, [ 'payment_token', 'customer_id' ] ) && ! metadata_exists( 'post', $order->get_id(), $this->get_order_meta_prefix() . $key ) ) {

			switch ( $key ) {

				case 'payment_token':
					$value = $order->get_meta( '_wc_intuit_qbms_credit_card_payment_token' );
				break;

				case 'customer_id':
					$value = $order->get_meta( '_wc_intuit_qbms_credit_card_customer_id' );
				break;
			}

			if ( $value ) {
				$order->update_meta_data( $this->get_order_meta_prefix() . $key, $value );
				$order->save_meta_data();
			}
		}

		return $value;
	}


	/**
	 * Gets the customer ID of a user.
	 *
	 * Tries to get the legacy QBMS customer ID if it exists.
	 *
	 * @since 2.6.0
	 *
	 * @param int $user_id WordPress user ID
	 * @param array $args
	 * @return string
	 */
	public function get_customer_id( $user_id, $args = array() ) {

		$args = wp_parse_args( $args, [
			'environment_id' => $this->get_environment(),
		] );

		$environment_id   = $args['environment_id'];
		$qbms_meta_key    = 'wc_intuit_qbms_customer_id' . ( ! $this->is_production_environment( $environment_id ) ? '_test' : '' );
		$qbms_customer_id = get_user_meta( $user_id, $qbms_meta_key, true );
		$payments_tokens  = get_user_meta( $user_id, $this->get_payment_tokens_handler()->get_user_meta_name( $environment_id ), true );

		// if there is no QBMS ID or they have Payments API tokens, bail
		if ( ! $qbms_customer_id || is_array( $payments_tokens ) ) {
			return parent::get_customer_id( $user_id, $args );
		}

		update_user_meta( $user_id, $this->get_customer_id_user_meta_name( $environment_id ), $qbms_customer_id );

		return $qbms_customer_id;
	}


}
