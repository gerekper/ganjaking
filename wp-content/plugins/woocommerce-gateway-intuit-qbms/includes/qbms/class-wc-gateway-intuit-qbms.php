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
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-QBMS/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * Intuit QBMS Payment Gateway Parent Class
 *
 * Functionality which is shared between the credit card and echeck gateways
 *
 * @since 1.0
 */
class WC_Gateway_Intuit_QBMS extends Framework\SV_WC_Payment_Gateway_Direct {


	/** The production URL endpoint */
	const PRODUCTION_URL_ENDPOINT = 'https://merchantaccount.quickbooks.com/j/AppGateway';

	/** The test URL endpoint (PTC) */
	const TEST_URL_ENDPOINT       = 'https://merchantaccount.ptc.quickbooks.com/j/AppGateway';

	/** The production merchant center URL */
	const PRODUCTION_MERCHANT_CENTER_URL = 'https://merchantcenter.intuit.com';

	/** The test merchant center URL */
	const TEST_MERCHANT_CENTER_URL = 'https://merchantcenter.ptcfe.intuit.com';

	/** The default production app login */
	const PRODUCTION_APP_LOGIN_DEFAULT = 'checkout-production.skyverge.com';

	/** The default test app login */
	const TEST_APP_LOGIN_DEFAULT = 'checkout-test.skyverge.com';

	/** The default production app ID */
	const PRODUCTION_APP_ID_DEFAULT = '826867585';

	/** The default test app ID */
	const TEST_APP_ID_DEFAULT = '1013327659';

	/** The default test connection ticket */
	const TEST_CONNECTION_TICKET = 'SDK-TGT-135-weg1SdawqWvudlXrSMSHPw';

	/** @var string production application login */
	protected $application_login;

	/** @var string production application id */
	protected $application_id;

	/** @var string production connection ticket */
	protected $connection_ticket;

	/** @var string test application login */
	protected $test_application_login;

	/** @var string test application id */
	protected $test_application_id;

	/** @var string test connection ticket */
	protected $test_connection_ticket;

	/** @var WC_Intuit_QBMS_API instance */
	protected $api;

	/** @var array shared settings names */
	protected $shared_settings_names = array( 'application_login', 'application_id', 'connection_ticket', 'test_application_login', 'test_application_id', 'test_connection_ticket' );


	/**
	 * Initialize the gateway
	 *
	 * @since 1.0
	 * @param string $id the gateway id
	 * @param Framework\SV_WC_Payment_Gateway_Plugin $plugin the parent plugin class
	 * @param array $args gateway arguments
	 */
	public function __construct( $id, $plugin, $args ) {

		parent::__construct( $id, $plugin, $args );

		// maybe add some settings descriptions, once we have the current settings values available
		$this->maybe_add_settings_descriptions();

		// render the "View Transaction" link in the order edit page
		add_action( 'admin_footer', array( $this, 'render_view_transaction_form' ) );
	}


	/**
	 * Gets the gateway-specifics JS script handle.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_gateway_js_handle() {

		return 'wc-' . $this->get_id_dasherized();
	}


	/**
	 * Conditionally add some settings descriptions to provide some optional
	 * contextual help based on what's already been configured.
	 *
	 * @since 1.0
	 */
	protected function maybe_add_settings_descriptions() {

		$application_login_description = $this->application_login ? '' :
			sprintf( __( 'You can create your own production Application Login and Application ID in the %1$sIntuit Partner Program%2$s, or use the default.  This is optional.', 'woocommerce-gateway-intuit-payments' ),
				'<a href="https://developer.intuit.com">', '</a>' );

		$connection_ticket_description =
			sprintf( __( 'Generate a new production connection ticket for the configured Application ID by following %1$sthis link%2$s', 'woocommerce-gateway-intuit-payments' ),
				'<a class="js-intuit-qbms-connection-ticket" href="https://merchantaccount.quickbooks.com/j/sdkconnection?appid=' . $this->get_application_id( 'production' ) . '&sessionEnabled=false">', '</a>' );

		$test_application_login_description = $this->test_application_login ? '' :
			sprintf( __( 'You can create your own test Application Login and Application ID in the %1$sIntuit Partner Program%2$s, or use the default.  This is optional.', 'woocommerce-gateway-intuit-payments' ),
					'<a href="https://developer.intuit.com">', '</a>' );

		$test_connection_ticket_description =
			sprintf( __( 'Generate a new test connection ticket for the configured Application ID by following %1$sthis link%2$s', 'woocommerce-gateway-intuit-payments' ),
				'<a class="js-intuit-qbms-test-connection-ticket" href="https://merchantaccount.ptc.quickbooks.com/j/sdkconnection?appid=' . $this->get_application_id( 'test' ) . '&sessionEnabled=false">', '</a>' );

		$this->form_fields['application_login']['description']      = $application_login_description;
		$this->form_fields['connection_ticket']['description']      = $connection_ticket_description;
		$this->form_fields['test_application_login']['description'] = $test_application_login_description;
		$this->form_fields['test_connection_ticket']['description'] = $test_connection_ticket_description;

	}



	/**
	 * Render JavaScript to handle the connection ticket dynamic URL which is
	 * built based on the application ID
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::admin_options()
	 */
	public function admin_options() {

		parent::admin_options();

		ob_start();
		?>
			$( '#woocommerce_intuit_qbms_application_id' ).on( 'keyup change input', function() {

				var url = $( this ).val() ? $( this ).val() : $( this ).attr( 'placeholder' );

				$( 'a.js-intuit-qbms-connection-ticket' ).attr( 'href', 'https://merchantaccount.quickbooks.com/j/sdkconnection?appid=' + url + '&sessionEnabled=false' );

			} );

			$( '#woocommerce_intuit_qbms_test_application_id' ).on( 'keyup change input', function() {

				var url = $( this ).val() ? $( this ).val() : $( this ).attr( 'placeholder' );

				$( 'a.js-intuit-qbms-test-connection-ticket' ).attr( 'href', 'https://merchantaccount.ptc.quickbooks.com/j/sdkconnection?appid=' + url + '&sessionEnabled=false' );

			} );
		<?php

		wc_enqueue_js( ob_get_clean() );

	}


	/**
	 * Returns an array of form fields specific for this method
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		return array(

			'connection_settings' => array(
				'title' => esc_html__( 'Connection Settings', 'woocommerce-plugin-framework' ),
				'type'  => 'title',
			),

			'application_login' => array(
				'title'    => __( 'Application Login', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your application login value, this will be something like appname.domainname.com', 'woocommerce-gateway-intuit-payments' ),
				'placeholder' => self::PRODUCTION_APP_LOGIN_DEFAULT,
			),

			'application_id' => array(
				'title'    => __( 'Application ID', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your application ID value.', 'woocommerce-gateway-intuit-payments' ),
				'placeholder' => self::PRODUCTION_APP_ID_DEFAULT,
			),

			'connection_ticket' => array(
				'title'    => __( 'Connection Ticket', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your unique connection ticket value.', 'woocommerce-gateway-intuit-payments' ),
			),

			'test_application_login' => array(
				'title'    => __( 'Application Login', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your application login value, this will be something like appname.domainname.com', 'woocommerce-gateway-intuit-payments' ),
				'placeholder' => self::TEST_APP_LOGIN_DEFAULT,
			),

			'test_application_id' => array(
				'title'    => __( 'Application ID', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your application ID value.', 'woocommerce-gateway-intuit-payments' ),
				'placeholder' => self::TEST_APP_ID_DEFAULT,
			),

			'test_connection_ticket' => array(
				'title'    => __( 'Connection Ticket', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your unique connection ticket value.', 'woocommerce-gateway-intuit-payments' ),
				'placeholder' => self::TEST_CONNECTION_TICKET,
			),
		);

	}


	/**
	 * Returns true if the gateway is properly configured to perform transactions.
	 * Intuit QBMS requires: application id, application login, and connection ticket
	 * for proper configuration
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::is_configured()
	 * @return boolean true if the gateway is properly configured
	 */
	public function is_configured() {

		$is_configured = parent::is_configured();

		// missing configuration
		if ( ! $this->get_application_id() || ! $this->get_application_login() || ! $this->get_connection_ticket() ) {
			$is_configured = false;
		}

		return $is_configured;

	}


	/**
	 * Add any Intuit QBMS specific payment and transaction information as
	 * class members of \WC_Order instance.  Added members can include:
	 *
	 * $order->payment->request_id - an application-supplied value that identifies the transaction
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::get_order()
	 * @param int $order_id order ID being processed
	 * @return \WC_Order object with payment and transaction information attached
	 */
	public function get_order( $order_id ) {

		// add common order members
		$order = parent::get_order( $order_id );

		// add intuit-specific order members

		// this is used to identify the transaction and prevent duplicate transactions
		//  as might occur during a network outage.  Not really making use of this at
		//  the moment since there's no real way to test.  For further info:
		//  https://developer.intuit.com/docs/030_qbms/0060_documentation/error_handling#QBMS_Error_Recovery
		$order->payment->request_id = $order->unique_transaction_ref;

		return $order;
	}


	/**
	 * Build the Payment Tokens Handler class instance.
	 *
	 * @since 1.9.0
	 * @return \WC_Intuit_QBMS_Payment_Token_Handler
	 */
	protected function build_payment_tokens_handler() {

		return new WC_Intuit_QBMS_Payment_Token_Handler( $this );
	}


	/**
	 * Gets the Customer ID user meta name.
	 *
	 * @since 2.0.0
	 * @param string $environment the environment name
	 * @return string
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		// no leading underscore since this is meant to be visible to the admin
		return 'wc_' . WC_Intuit_Payments::QBMS_PLUGIN_ID . '_customer_id' . ( ! $this->is_production_environment( $environment_id ) ? '_' . $environment_id : '' );
	}


	/**
	 * Renders the hidden "View Transaction" form on the edit shop order admin page,
	 * to post to the Intuit QBMS Merchant Center to view the transaction.
	 * There's no URL query string that will display a transaction for you, but we
	 * can search for the transaction and find it for the user.
	 *
	 * @since 2.0.0
	 * @see WC_Gateway_Intuit_QBMS::get_transaction_url()
	 */
	public function render_view_transaction_form() {
		global $post;

		$screen = get_current_screen();

		// bail if not on the order edit admin page
		if ( ! $screen || 'post' !== $screen->base || 'shop_order' !== $screen->post_type || ! $post  ) {
			return;
		}

		$order = wc_get_order( $post->ID );

		// bail if a different payment method was used
		if ( ! $order || $this->get_id() !== $order->get_payment_method( 'edit' ) ) {
			return;
		}

		// search form parameters
		$transaction_id = $this->get_order_meta( $order, 'trans_id', true );
		$date           = $order->get_date_paid( 'edit' ) ? $order->get_date_paid( 'edit' )->date( 'm/d/Y' ) : '';

		// render a search form to post to the Intuit merchant center to view the transaction with the most open search parameters possible
		?>
		<form id="js-wc-<?php echo esc_attr( $this->get_id_dasherized() ); ?>-transaction-search" action="<?php echo esc_url( $this->get_transaction_url( $order ) ); ?>" method="POST">
			<input type="hidden" name="transactionId"           value="<?php echo $transaction_id; ?>" />
			<input type="hidden" name="Search"                  value="Search" />
			<input type="hidden" name="reportPage"              value="TransactionFlexReporting" />
			<input type="hidden" name="ims.mp.isAdvancedSearch" value="true" />
			<input type="hidden" name="fromDate"                value="<?php echo $date; ?>" />
			<input type="hidden" name="toDate"                  value="<?php echo $date; ?>" />
			<input type="hidden" name="ccTypeVisa"              value="VISA" />
			<input type="hidden" name="ccTypeMC"                value="MC" />
			<input type="hidden" name="ccTypeDisc"              value="DISC" />
			<input type="hidden" name="ccTypeAmex"              value="AMEX" />
			<input type="hidden" name="ccTypeDiners"            value="DINERS" />
			<input type="hidden" name="ccTypeJcb"               value="JCB" />
			<input type="hidden" name="pinDebit"                value="all" />
			<input type="hidden" name="txnTypeA"                value="A" />
			<input type="hidden" name="txnTypeS"                value="S" />
			<input type="hidden" name="txnTypeD"                value="D" />
			<input type="hidden" name="txnTypeC"                value="C" />
			<input type="hidden" name="txnTypeV"                value="V" />
			<input type="hidden" name="response"                value="all" />
			<input type="hidden" name="auto_reject"             value="all" />
			<input type="hidden" name="swiped"                  value="all" />
			<input type="hidden" name="mobilePayment"           value="all" />
			<input type="hidden" name="paypagePayment"          value="all" />
			<input type="hidden" name="recurring"               value="all" />
			<input type="hidden" name="comCard"                 value="all" />
		</form>
		<?php

		wc_enqueue_js( '$( ".order_number a" ).click( function() { $( "#js-wc-' . esc_js( $this->get_id_dasherized() ) . '-transaction-search" ).submit(); return false; } );' );
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the merchant account transaction advanced search URL for the
	 * given order.  The QBMS merchant account center doesn't allow you to link
	 * directly to a given transaction, but we can post a search request to
	 * return the one we want
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_transaction_url()
	 * @see WC_Intuit_Payments::render_view_transaction_form()
	 * @param \WC_Order $order the order object
	 * @return string transaction url
	 */
	public function get_transaction_url( $order ) {

		$merchant_center_url = ( 'test' == $this->get_order_meta( $order, 'environment' ) ? self::TEST_MERCHANT_CENTER_URL : self::PRODUCTION_MERCHANT_CENTER_URL );

		$this->view_transaction_url = $merchant_center_url . '/portal/authsec/portal/qbms/Search+and+reporting/Search+for+advanced+transactions/SearchForAdvTransactions?action=1';

		return parent::get_transaction_url( $order );
	}


	/**
	 * Get the API object
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway::get_api()
	 * @return WC_Intuit_QBMS_API API instance
	 */
	public function get_api() {

		if ( isset( $this->api ) ) {
			return $this->api;
		}

		// Gateway API:
		//  WC_Intuit_QBMS_API - The main API class responsible for communication with the Intuit QBMS API
		//  WC_Intuit_QBMS_API_Request - Represents an API request
		//  WC_Intuit_QBMS_API_Response - Abstranct class that represents an API response
		//  WC_Intuit_QBMS_API_Credit_Card_Authorization_Response - Represents a credit card authorization response
		//  WC_Intuit_QBMS_API_Credit_Card_Charge_Response - Represents a credit card charge response
		//  WC_Intuit_QBMS_API_Wallet_Add_Response - Represents a payment method tokenization response

		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-request.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-credit-card-authorization-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-credit-card-charge-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-credit-card-capture-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-credit-card-refund-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-customer-check-debit-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-wallet-add-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-wallet-update-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-wallet-delete-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-wallet-query-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/qbms/api/class-wc-intuit-qbms-api-merchant-account-query-response.php' );

		return $this->api = new WC_Intuit_QBMS_API( $this->get_id(), $this->get_api_endpoint(), $this->get_application_login(), $this->get_application_id(), $this->get_connection_ticket() );
	}


	/**
	 * Returns the API URL endpoint for the configured environment and API version
	 *
	 * @since 1.0
	 * @return string the API URL endpoint
	 */
	public function get_api_endpoint() {
		return $this->is_production_environment() ? self::PRODUCTION_URL_ENDPOINT : self::TEST_URL_ENDPOINT;
	}


	/**
	 * Returns the application login based on the current environment
	 *
	 * @since 1.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the application login to use
	 */
	public function get_application_login( $environment_id = null ) {

		if ( is_null( $environment_id ) )
			$environment_id = $this->get_environment();

		if ( 'production' == $environment_id ) {
			$application_login = $this->application_login ? $this->application_login : self::PRODUCTION_APP_LOGIN_DEFAULT;
		} else {
			$application_login = $this->test_application_login ? $this->test_application_login : self::TEST_APP_LOGIN_DEFAULT;
		}

		return $application_login;
	}


	/**
	 * Returns the application id based on the current environment
	 *
	 * @since 1.0
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string the application id to use
	 */
	public function get_application_id( $environment_id = null ) {

		if ( is_null( $environment_id ) )
			$environment_id = $this->get_environment();

		if ( 'production' == $environment_id ) {
			$application_id = $this->application_id ? $this->application_id : self::PRODUCTION_APP_ID_DEFAULT;
		} else {
			$application_id = $this->test_application_id ? $this->test_application_id : self::TEST_APP_ID_DEFAULT;
		}

		return $application_id;
	}


	/**
	 * Returns the connection ticket based on the current environment
	 *
	 * @since 1.0
	 * @return string the connection ticket to use
	 */
	public function get_connection_ticket() {

		if ( $this->is_production_environment() ) {
			$connection_ticket = $this->connection_ticket;
		} else {
			$connection_ticket = $this->test_connection_ticket ? $this->test_connection_ticket : self::TEST_CONNECTION_TICKET;
		}

		return preg_replace( '/[\x00-\x20\x7F-\xFF]/', '', $connection_ticket );
	}

}
