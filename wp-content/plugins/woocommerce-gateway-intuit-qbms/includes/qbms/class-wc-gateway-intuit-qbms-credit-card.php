<?php
/**
 * WooCommerce Intuit QBMS
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit QBMS to newer
 * versions in the future. If you wish to customize WooCommerce Intuit QBMS for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-intuit-qbms/
 *
 * @package   WC-Intuit-QBMS/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;

/**
 * Intuit QBMS Credit Card Payment Gateway
 *
 * Handles all purchases, displaying saved cards, etc
 *
 * This is a direct credit card gateway that supports card types, charge,
 * authorization, tokenization, subscriptions and pre-orders.
 *
 * This gateway modifies the standard framework payment gateway with the
 * addition of a "Require Card Verification" option which requires the CSC
 * even for tokenized transactions (to support Intuit QBMS strict Fraud setting
 * option).  With "Require Card Verification" enabled subscription/pre-order
 * support is sacrified.
 *
 * @since 1.0
 */
class WC_Gateway_Intuit_QBMS_Credit_Card extends WC_Gateway_Intuit_QBMS {


	/** @var string whether CSC is required for *all* (including tokenized) transactions, 'yes' or 'no' */
	protected $require_csc;


	/**
	 * Initialize the gateway
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Intuit_Payments::QBMS_CREDIT_CARD_ID,
			wc_intuit_payments(),
			array(
				'method_title'       => __( 'Intuit QBMS', 'woocommerce-gateway-intuit-payments' ),
				'method_description' => __( 'Allow customers to securely pay using their credit cards with Intuit QBMS.', 'woocommerce-gateway-intuit-payments' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_TOKENIZATION,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_CHARGE_VIRTUAL,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
					self::FEATURE_REFUNDS,
					self::FEATURE_VOIDS,
					self::FEATURE_DETAILED_CUSTOMER_DECLINE_MESSAGES,
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
				 ),
				'payment_type' => 'credit-card',
				'environments' => array(
					'production' => __( 'Production', 'woocommerce-gateway-intuit-payments' ),
					'test'       => __( 'Test', 'woocommerce-gateway-intuit-payments' ),
				),
				// 'shared_settings'    => $this->shared_settings_names, // Commented out until/if QBMS really supports echeck
			)
		);

		// add a test amount input to the payment form
		add_filter( 'wc_' . $this->get_id() . '_payment_form_description', array( $this, 'render_test_amount_input' ) );
	}


	/**
	 * Adds the enable Card Security Code form fields.  Intuit QBMS has a
	 * stringent CSC fraud setting which when enabled will decline any
	 * credit card transactions that are missing the CSC, including tokenized
	 * transaction.  Since not everyone will necessarily have that enabled,
	 * we add a "Require CSC" form field for those who do.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::add_csc_form_fields()
	 * @param array $form_fields gateway form fields
	 * @return array $form_fields gateway form fields
	 */
	protected function add_csc_form_fields( $form_fields ) {

		$form_fields = parent::add_csc_form_fields( $form_fields );

		$form_fields['require_csc'] = array(
			'title'    => __( 'Require Card Verification', 'woocommerce-gateway-intuit-payments' ),
			'label'    => __( 'Require the Card Security Code (CV2) for all transactions', 'woocommerce-gateway-intuit-payments' ),
			'desc_tip' => __( 'Enabling this field will require the CSC even for tokenized transactions, and will disable support for WooCommerce Subscriptions and WooCommerce Pre-Orders.  Enable this if you have configured your merchant settings to Reject Transaction if CSC is not available.', 'woocommerce-gateway-intuit-payments' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		);

		return $form_fields;

	}


	/**
	 * Display settings page with some additional javascript for hiding
	 * conditional fields.  The "Require CSC" field will be shown only when
	 * the "Enable CSC" and "Tokenization" are enabled
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		// add inline javascript to show the "require csc" field when the "enable csc" and "tokenization" fields are both checked
		ob_start();
		?>
			$( '#woocommerce_<?php echo $this->get_id(); ?>_enable_csc, #woocommerce_<?php echo $this->get_id(); ?>_tokenization' ).change( function() {

				if ( $( '#woocommerce_<?php echo $this->get_id(); ?>_enable_csc' ).is( ':checked' ) && $( '#woocommerce_<?php echo $this->get_id(); ?>_tokenization' ).is( ':checked' ) ) {
					$( '#woocommerce_<?php echo $this->get_id(); ?>_require_csc' ).closest( 'tr' ).show();
				} else {
					$( '#woocommerce_<?php echo $this->get_id(); ?>_require_csc' ).closest( 'tr' ).hide();
				}

			} ).change();
		<?php

		wc_enqueue_js( ob_get_clean() );

	}


	/** Frontend Methods ******************************************************/


	/**
	 * Gets the default values used to pre-fill a valid test account number when
	 * in testing mode.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway::get_payment_method_defaults()
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
	 * Render a test amount input after the payment form description. Admins
	 * can use this to override the order total and set a specific amount for
	 * testing error conditions
	 *
	 * @since 2.0.0
	 * @param string $desc payment form description HTML
	 * @return string
	 */
	public function render_test_amount_input( $desc ) {

		if ( $this->is_test_environment() ) {

			// convenience for testing error conditions
			$test_conditions = array(
				'10200_comm'        => __( 'CC Processing Gateway comm error', 'woocommerce-gateway-intuit-payments' ),
				'10201_login'       => __( 'Processing Gateway login error', 'woocommerce-gateway-intuit-payments' ),
				'10301_ccinvalid'   => __( 'Invalid CC account number', 'woocommerce-gateway-intuit-payments' ),
				'10400_insufffunds' => __( 'Insufficient funds', 'woocommerce-gateway-intuit-payments' ),
				'10401_decline'     => __( 'Transaction declined', 'woocommerce-gateway-intuit-payments' ),
				'10403_acctinvalid' => __( 'Invalid merchant account', 'woocommerce-gateway-intuit-payments' ),
				'10404_referral'    => __( 'Declined pending voice auth', 'woocommerce-gateway-intuit-payments' ),
				'10406_capture'     => __( 'Capture error', 'woocommerce-gateway-intuit-payments' ),
				'10500_general'     => __( 'General error', 'woocommerce-gateway-intuit-payments' ),
				'10000_avscvdfail'  => __( 'AVS Failure', 'woocommerce-gateway-intuit-payments' ),
			);

			ob_start();

			echo '<select name="wc-intuit-qbms-credit-card-test-condition">';
				echo '<option value="">' . __( 'Test an Error Condition:', 'woocommerce-gateway-intuit-payments' ) . '</option>';
				foreach ( $test_conditions as $key => $value ) {
					echo '<option value="' . $key . '">' . esc_html( $value ) . '</option>';
				}
			echo '</select>';

			$desc .= ob_get_clean();
		}

		return $desc;
	}


	/**
	 * Returns true if tokenization takes place prior authorization/charge
	 * transaction.
	 *
	 * Defaults to false but can be overridden by child gateway class
	 *
	 * @since 1.2.1-1
	 * @see Framework\SV_WC_Payment_Gateway_Direct::tokenize_before_sale()
	 * @return boolean true if there is a tokenization request that is issued
	 *         before a authorization/charge transaction
	 */
	public function tokenize_before_sale() {
		return true;
	}


	/**
	 * Add any Intuit QBMS specific payment and transaction information as
	 * class members of \WC_Order instance.  Added members can include:
	 *
	 * $order->capture->request_id  - an application-supplied value that identifies the transaction
	 * $order->payment->test_condition - a convenience for testing error conditions while in test mode
	 *
	 * @since 1.0
	 * @see WC_Gateway_Intuit_QBMS::get_order()
	 * @param int $order_id order ID being processed
	 * @return \WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		// add common order members
		$order = parent::get_order( $order_id );

		// add intuit credit card-specific order members

		// a convenience for testing error conditions while in test mode, this is passed as the NameOnCard
		if ( $this->is_environment( 'test' ) && Framework\SV_WC_Helper::get_posted_value( 'wc-intuit-qbms-credit-card-test-condition' ) ) {
			$order->payment->test_condition = Framework\SV_WC_Helper::get_posted_value( 'wc-intuit-qbms-credit-card-test-condition' );
		}

		return $order;
	}


	/**
	 * Adds any gateway-specific transaction data to the order
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::add_payment_gateway_transaction_data()
	 * @param \WC_Order $order the order object
	 * @param WC_Intuit_QBMS_API_Response $response the transaction response
	 */
	public function add_payment_gateway_transaction_data( $order, $response ) {

		// transaction results (CustomerCreditCardWalletAuthRs doesn't return a client trans id)
		if ( $response->get_client_trans_id() ) {
			$this->update_order_meta( $order, 'client_trans_id', $response->get_client_trans_id() );
		}

		if ( $this->perform_credit_card_charge( $order ) ) {
			// performing a cc charge returns a number of more response elements than a simple auth
			$this->update_order_meta( $order, 'merchant_account_number', $response->get_merchant_account_number() );
			$this->update_order_meta( $order, 'recon_batch_id',          $response->get_recon_batch_id() );
			$this->update_order_meta( $order, 'payment_grouping_code',   $response->get_payment_grouping_code() );
			$this->update_order_meta( $order, 'txn_authorization_stamp', $response->get_txn_authorization_stamp() );
		}

	}


	/** Capture Methods ***********************************************************************************************/


	/**
	 * Builds the capture handler instance.
	 *
	 * @since 2.7.3
	 */
	public function init_capture_handler() {

		$this->capture_handler = new WC_Gateway_Intuit_QBMS_Capture_Handler( $this );
	}


	/**
	 * Adds payment and transaction information to the order object.
	 *
	 * Standard information can include:
	 *
	 * $order->capture->request_id - an application-supplied value that identifies the transaction
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Direct::get_order_for_capture()
	 *
	 * @since 1.1.0
	 *
	 * @param int $order_id order ID being processed
	 * @param float $amount capture amount, defaults to the order total
	 * @return \WC_Order object with payment and transaction information attached
	 */
	public function get_order_for_capture( $order, $amount = null ) {

		$order = parent::get_order_for_capture( $order );

		// this is used to identify the transaction and prevent duplicate transactions
		//  as might occur during a network outage.  Not really making use of this at
		//  the moment since there's no real way to test.  For further info:
		//  https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Error_Recovery
		$order->capture->request_id = $order->get_id() . '-' . rand();

		return $order;
	}


	/**
	 * Add payment and transaction information as class members of \WC_Order
	 * instance for use in credit card refund transactions.  Standard information
	 * can include:
	 *
	 * $order->refund->request_id - an application-supplied value that identifies the transaction
	 *
	 * @since 2.0.0
	 * @param \WC_Order|int $order the order object or ID
	 * @param float $amount the refund amount
	 * @param string $reason Optional. The refund reason text
	 * @return \WC_Order
	 */
	protected function get_order_for_refund( $order, $amount, $reason ) {

		$order = parent::get_order_for_refund( $order, $amount, $reason );

		// this is used to identify the transaction and prevent duplicate transactions
		//  as might occur during a network outage.  Not really making use of this at
		//  the moment since there's no real way to test.  For further info:
		//  https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Error_Recovery
		$order->refund->request_id = $order->get_id() . '-' . rand();

		return $order;
	}


	/**
	 * Determines if the refund ended up being a void.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order order
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response refund response
	 * @return bool
	 */
	protected function maybe_void_instead_of_refund( $order, $response ) {

		return $response->is_void();
	}


	/** Subscriptions/Pre-Orders **********************************************/


	/**
	 * Tweak the labels shown when editing the payment method for a Subscription
	 *
	 * @hooked from Framework\SV_WC_Payment_Gateway_Integration_Subscriptions
	 *
	 * @since 1.7.1
	 * @see Framework\SV_WC_Payment_Gateway_Integration_Subscriptions::admin_add_payment_meta()
	 * @param array $meta payment meta
	 * @param \WC_Subscription $subscription subscription being edited, unused
	 * @return array
	 */
	public function subscriptions_admin_add_payment_meta( $meta, $subscription ) {

		if ( isset( $meta[ $this->get_id() ] ) ) {

			$meta[ $this->get_id() ]['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['label'] = __( 'Wallet Token ID', 'woocommerce-gateway-intuit-payments' );
		}

		return $meta;
	}


	/**
	 * Validate the payment meta for a Subscription by ensuring the wallet token
	 * ID is numeric
	 *
	 * @since 1.7.1
	 * @see Framework\SV_WC_Payment_Gateway_Integration_Subscriptions::admin_validate_payment_meta()
	 * @param array $meta payment meta
	 * @throws \Exception if payment profile/customer profile IDs are not numeric
	 */
	public function subscriptions_admin_validate_payment_meta( $meta ) {

		// wallet token ID (payment_token) must be numeric
		if ( ! ctype_digit( (string) $meta['post_meta'][ $this->get_order_meta_prefix() . 'payment_token' ]['value'] ) ) {
			throw new Exception( __( 'Wallet Token ID must be numeric.', 'woocommerce-gateway-intuit-payments' ) );
		}
	}


	/**
	 * Returns meta keys to be excluded when copying over meta data when:
	 *
	 * + a renewal order is created from a subscription
	 * + the user changes their payment method for a subscription
	 * + processing the upgrade from Subscriptions 1.5.x to 2.0.x
	 *
	 * @since 1.7.1
	 * @param array $meta_keys
	 * @return array
	 */
	public function subscriptions_get_excluded_order_meta_keys( $meta_keys ) {

		$meta_keys[] = $this->get_order_meta_prefix() . 'merchant_account_number';
		$meta_keys[] = $this->get_order_meta_prefix() . 'recon_batch_id';
		$meta_keys[] = $this->get_order_meta_prefix() . 'payment_grouping_code';
		$meta_keys[] = $this->get_order_meta_prefix() . 'txn_authorization_stamp';
		$meta_keys[] = $this->get_order_meta_prefix() . 'client_trans_id';

		return $meta_keys;
	}


	/**
	 * Returns true if this gateway with its current configuration supports
	 * subscriptions.  Requiring CSC for all transactions removes support for
	 * subscriptions
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::supports_subscriptions()
	 * @return boolean true if the gateway supports subscriptions
	 */
	public function supports_subscriptions() {

		return parent::supports_subscriptions() && ! $this->csc_required();
	}


	/**
	 * Returns true if this gateway with its current configuration supports
	 * pre-orders.  Requiring CSC for all transactions removes support for
	 * pre-orders
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::supports_pre_orders()
	 * @return boolean true if the gateway supports pre-orders
	 */
	public function supports_pre_orders() {

		return parent::supports_pre_orders() && ! $this->csc_required();
	}


	/**
	 * Returns true if the CSC is required for all transactions, including
	 * tokenized
	 *
	 * @since 1.0
	 * @return boolean true if the CSC is required for all transactions, even tokenized
	 */
	public function csc_required() {
		return $this->csc_enabled() && 'yes' === $this->require_csc;
	}


}
