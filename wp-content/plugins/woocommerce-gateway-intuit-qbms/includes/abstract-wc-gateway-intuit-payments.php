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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * The base gateway class.
 *
 * @since 2.0.0
 *
 * @method \WC_Intuit_Payments get_plugin()
 */
abstract class WC_Gateway_Inuit_Payments extends Framework\SV_WC_Payment_Gateway_Direct {


	/** the sandbox environment identifier */
	const ENVIRONMENT_SANDBOX = 'sandbox';

	/** the production API endpoint */
	const API_ENDPOINT = 'https://api.intuit.com';

	/** the sandbox API endpoint */
	const API_ENDPOINT_SANDBOX = 'https://sandbox.api.intuit.com';

	/** @var string OAuth 2.0 version */
	const OAUTH_VERSION_2 = '2.0';


	/** @var \WC_Intuit_Payments_API|null the API instance */
	protected $api;

	// OAuth 2.0

	/** @var string the merchant's app's client ID */
	protected $client_id;

	/** @var string the merchant's app's client secret */
	protected $client_secret;

	/** @var string the merchant's sandbox app's client ID */
	protected $sandbox_client_id;

	/** @var string the merchant's sandbox app's client secret */
	protected $sandbox_client_secret;

	/** @var SkyVerge\WooCommerce\Intuit\Handlers\Connection connection handler instance */
	private $connection_handler;


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.0.0
	 * @param string $id the gateway ID
	 * @param array $args the gateway args
	 */
	public function __construct( $id, $args ) {

		// set the default args shared across gateways
		$args = wp_parse_args( $args, array(
			'method_description' => __( 'Intuit Payments Gateway provides a seamless and secure checkout process for your customers', 'woocommerce-gateway-intuit-payments' ),
			'supports'           => array(),
			'environments'       => array(
				self::ENVIRONMENT_PRODUCTION => __( 'Production', 'woocommerce-gateway-intuit-payments' ),
				self::ENVIRONMENT_SANDBOX    => __( 'Sandbox', 'woocommerce-gateway-intuit-payments' ),
			),
			'shared_settings' => array(
				'client_id',
				'client_secret',
				'sandbox_client_id',
				'sandbox_client_secret',
				'connect_button',
			),
		) );

		// add any gateway-specific supports
		$args['supports'] = array_unique( array_merge( $args['supports'], array(
			self::FEATURE_PRODUCTS,
			self::FEATURE_PAYMENT_FORM,
			self::FEATURE_REFUNDS,
			self::FEATURE_VOIDS,
			self::FEATURE_CUSTOMER_ID,
		) ) );

		parent::__construct( $id, wc_intuit_payments(), $args );

		$this->connection_handler = new SkyVerge\WooCommerce\Intuit\Handlers\Connection( $this );

		// add a test case input to the payment form
		if ( $this->is_test_environment() ) {
			add_filter( 'wc_' . $this->get_id() . '_payment_form_description', array( $this, 'render_test_case_field' ) );
		}

		// add hidden inputs that client-side JS populates with token/last 4 of account number
		add_action( 'wc_' . $this->get_id() . '_payment_form', array( $this, 'render_hidden_inputs' ) );

		// remove card number/csc input names so they're not POSTed
		add_filter( 'wc_' . $this->get_id() . '_payment_form_default_payment_form_fields', array( $this, 'remove_payment_form_field_input_names' ) );

		// enqueue the admin scripts & styles
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	}


	/**
	 * Initializes the payment form instance.
	 *
	 * @since 2.7.4
	 *
	 * @return SkyVerge\WooCommerce\Intuit\Handlers\Payment_Form
	 */
	public function init_payment_form_instance() {

		return new SkyVerge\WooCommerce\Intuit\Handlers\Payment_Form( $this );
	}


	/**
	 * Gets the connection handler instance.
	 *
	 * @since 2.4.0
	 *
	 * @return \SkyVerge\WooCommerce\Intuit\Handlers\Connection
	 */
	public function get_connection_handler() {

		return $this->connection_handler;
	}


	/**
	 * Gets the JS script params to localize for the gateway-specific JS.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_gateway_js_localized_script_params() {

		$helper = new Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper();

		return array(
			'api_url'        => $this->get_api_endpoint() . '/quickbooks/v4/payments/tokens',
			'ajax_log'       => $this->debug_log(),
			'ajax_log_nonce' => wp_create_nonce( 'wc_' . $this->get_plugin()->get_id() . '_log_js_data' ),
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'id_dasherized'  => $this->get_id_dasherized(),
			'generic_error'  => $helper->get_user_message( 'error' ),
		);
	}


	/**
	 * Gets the form fields specific for this gateway.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array
	 */
	protected function get_method_form_fields() {

		$is_connected = false;

		return [

			// OAuth 2.0 fields

			'client_id' => [
				'title'    => __( 'Client ID', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Intuit Developer app client ID.', 'woocommerce-gateway-intuit-payments' ),
				'disabled' => $is_connected,
			],

			'client_secret' => [
				'title'    => __( 'Client Secret', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Intuit Developer app client secret.', 'woocommerce-gateway-intuit-payments' ),
				'disabled' => $is_connected,
			],

			'sandbox_client_id' => [
				'title'    => __( 'Client ID', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'text',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'Your Intuit Developer app client ID.', 'woocommerce-gateway-intuit-payments' ),
				'disabled' => $is_connected,
			],

			'sandbox_client_secret' => [
				'title'    => __( 'Client Secret', 'woocommerce-gateway-intuit-payments' ),
				'type'     => 'password',
				'class'    => 'environment-field sandbox-field',
				'desc_tip' => __( 'Your Intuit Developer app client secret.', 'woocommerce-gateway-intuit-payments' ),
				'disabled' => $is_connected,
			],

			'connect_button' => [
				'title' => __( 'Payments Account', 'woocommerce-gateway-intuit-payments' ),
				'type'  => 'connect_button',
			],
		];
	}


	/**
	 * Tweaks the environment form field to disabled if already connected.
	 *
	 * @since 2.4.0
	 *
	 * @param array $form_fields existing form fields
	 * @return array
	 */
	protected function add_environment_form_fields( $form_fields ) {

		$form_fields = parent::add_environment_form_fields( $form_fields );

		$form_fields['environment']['disabled'] = false;

		return $form_fields;
	}


	/**
	 * Enqueues the admin scripts & styles.
	 *
	 * @since 2.0.0
	 */
	public function load_admin_scripts() {

		if ( $this->get_plugin()->is_plugin_settings() && $this->get_id() === Framework\SV_WC_Helper::get_requested_value( 'section' ) ) {

			wp_enqueue_script( 'wc-intuit-payments-admin', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-intuit-payments-admin.min.js', array( 'jquery', 'wc-intuit-payments-connect' ), $this->get_plugin()->get_version() );

			wp_localize_script( 'wc-intuit-payments-admin', 'wc_intuit_payments', [
				'is_plugin_settings' => $this->get_plugin()->is_plugin_settings(),
				'gateway_id'         => $this->get_id(),
				'is_connected'       => $this->is_connected(),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'connect_url'        => $this->get_connection_handler()->get_connect_url(),
				'disconnect_nonce'   => wp_create_nonce( $this->get_connection_handler()->get_disconnect_action_name() ),
				'i18n' => [
					'ays_disconnect' => esc_html__( 'Are you sure you wish to disconnect from your QuickBooks account?', 'woocommerce-gateway-intuit-payments' ),
				],
			] );

			wp_enqueue_style( 'wc-intuit-payments-admin', $this->get_plugin()->get_plugin_url() . '/assets/css/admin/wc-intuit-payments-admin.min.css', $this->get_plugin()->get_version() );
		}
	}


	/**
	 * Generates a "Connect to QuickBooks button" to begin the oAuth flow.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key the field key
	 * @param array $data the field params
	 * @return string
	 */
	public function generate_connect_button_html( $key, $data ) {

		$data = wp_parse_args( $data, array(
			'title'       => '',
			'class'       => '',
			'description' => '',
		) );

		// load the settings so we can accurately check config
		$this->load_settings();

		ob_start();

		?>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
			</th>
			<td class="forminp <?php echo esc_attr( $data['class'] ); ?>">

				<div class="js-wc-intuit-payments-not-configured hidden"
				     data-environment="<?php echo esc_attr( $this->get_environment() ); ?>"
				     data-oauth-configured="<?php echo esc_attr( $this->is_configured() ? 'yes' : 'no' ); ?>"
				>
					<button class="button" disabled="disabled"><?php esc_html_e( 'Connect with Quickbooks', 'woocommerce-gateway-intuit-payments' ); ?></button>
					<p class="description"><?php esc_html_e( 'Please save your credentials before connecting with QuickBooks.', 'woocommerce-gateway-intuit-payments' ); ?></p>
				</div>

				<?php if ( $this->is_connected() ) : ?>

					<a href="#" class="js-wc-intuit-payments-disconnect button" data-gateway-id="<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Disconnect from QuickBooks', 'woocommerce-gateway-intuit-payments' ); ?></a>

				<?php elseif ( $this->is_configured() ) : ?>

					<a href="#" class="js-wc-intuit-payments-connect"><?php esc_html_e( 'Connect with QuickBooks', 'woocommerce-gateway-intuit-payments' ); ?></a>

					<p class="description js-wc-intuit-payments-connect-description">

						<?php printf(
							esc_html__( 'Please make sure your app\'s Redirect URI is configured exactly as:%s', 'woocommerce-gateway-intuit-payments' ),
							'<br /><strong>' . esc_url( $this->get_connection_handler()->get_redirect_url() ) . '</strong>'
						); ?>

					</p>

				<?php endif; ?>

			</td>
		</tr>

		<?php

		return ob_get_clean();
	}


	/**
	 * Determines if the gateway is available for processing payments.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_available() {

		return parent::is_available() && $this->is_connected();
	}


	/**
	 * Determines if the gateway is properly configured to connect.
	 *
	 * @since 2.0.0
	 *
	 * @param string|null $unused (optional) unused param
	 * @return bool
	 */
	public function is_configured( $unused = null ) {

		$is_configured = $this->get_client_id() && $this->get_client_secret();

		return parent::is_configured() && $is_configured;
	}


	/**
	 * Determines if the merchant has gone through the oAuth flow.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	protected function is_connected() {

		return $this->get_connection_handler()->get_access_token();
	}


	/**
	 * Adds a test case field to the payment form.
	 *
	 * @link https://developer.intuit.com/docs/0100_quickbooks_online/0200_dev_guides/payments/testing
	 *
	 * @since 2.0.0
	 * @param string $desc payment form description HTML
	 * @return string
	 */
	public function render_test_case_field( $desc ) {

		// Bail if adding a new payment method, as these test cases have no effect
		if ( is_add_payment_method_page() ) {
			return $desc;
		}

		$options = $this->get_test_case_options();

		if ( ! empty( $options ) ) {

			ob_start();

			echo '<p>' . esc_html__( 'Error Test Case', 'woocommerce-gateway-intuit-payments' ) . '</p>';

			echo '<select name="wc-' . sanitize_html_class( $this->get_id_dasherized() ) . '-test-case">';

				echo '<option value="">' . esc_html__( 'None', 'woocommerce-gateway-intuit-payments' ) . '</option>';

				foreach ( $options as $key => $value ) {
					echo '<option value="' . $key . '">' . esc_html( $value ) . '</option>';
				}

			echo '</select>';

			$desc .= ob_get_clean();
		}

		return $desc;
	}


	/**
	 * Gets the gateway test case options.
	 *
	 * Gateways can override this with their own test case values.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_test_case_options() {

		return array();
	}


	/**
	 * Removes the input names for sensitive payment form fields so they're not
	 * POSTed to the server.
	 *
	 * Concrete gateways need to override this to specify which inputs.
	 *
	 * @since 2.0.0
	 * @param array $fields the payment form fields
	 */
	abstract public function remove_payment_form_field_input_names( $fields );


	/**
	 * Renders hidden inputs on the payment form for the JS token & last four.
	 *
	 * These are populated by the client-side JS after successful tokenization.
	 *
	 * @since 2.0.0
	 */
	public function render_hidden_inputs() {

		// token
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . sanitize_html_class( $this->get_id_dasherized() ) . '-js-token' );

		// account last four
		printf( '<input type="hidden" id="%1$s" name="%1$s" />', 'wc-' . sanitize_html_class( $this->get_id_dasherized() ) . '-last-four' );

		// If adding a new payment method, add some first & last name fields
		if ( is_add_payment_method_page() || is_checkout_pay_page() ) {

			$user = get_userdata( get_current_user_id() );

			// first name
			printf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', 'billing_first_name', $user->billing_first_name );

			// last name
			printf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', 'billing_last_name', $user->billing_last_name );
		}
	}


	/**
	 * Validate the provided payment fields.
	 *
	 * This primarily ensures the data is safe to set on the order object in
	 * get_order() below.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::validate_fields()
	 * @return bool whether the fields are valid
	 */
	public function validate_fields() {

		$is_valid = parent::validate_fields();

		// when using a saved method, there is no further validation required
		if ( Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-payment-token' ) ) {
			return $is_valid;
		}

		// last four
		if ( preg_match( '/\D/', Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-last-four' ) ) ) {

			Framework\SV_WC_Helper::wc_add_notice( __( 'Provided last four is invalid.', 'woocommerce-gateway-intuit-payments' ), 'error' );
			$is_valid = false;
		}

		// token
		if ( ! Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-js-token' ) ) {

			Framework\SV_WC_Helper::wc_add_notice( __( 'Provided token is invalid.', 'woocommerce-gateway-intuit-payments' ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
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

		// set the JS-generated token if this is a new payment method
		// neither gateway needs to post any sensitive payment details
		if ( ! isset( $order->payment->token ) ) {

			$order->payment->js_token = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-js-token' );

			$order->payment->account_number = $order->payment->last_four = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-last-four' );
		}

		// if a test case was set
		if ( $this->is_test_environment() ) {
			$order->payment->test_case = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-test-case' );
		}

		return $order;
	}


	/**
	 * Determines if payment methods should be tokenized before payment.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function tokenize_before_sale() {

		return true;
	}


	/**
	 * Builds the payment tokens handler class instance.
	 *
	 * @since 2.3.0
	 *
	 * @return \WC_Intuit_Payments_Tokens_Handler
	 */
	protected function build_payment_tokens_handler() {

		return new WC_Intuit_Payments_Tokens_Handler( $this );
	}


	/**
	 * Gets the API class instance.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway::get_api()
	 * @return \WC_Intuit_Payments_API
	 */
	public function get_api() {

		if ( $this->api instanceof WC_Intuit_Payments_API ) {
			return $this->api;
		}

		$path = wc_intuit_payments()->get_plugin_path() . '/includes/api/';

		$files = array(

			// base
			'class-wc-intuit-payments-api',

			// requests
			'requests/abstract-wc-intuit-payments-api-request',
			'requests/abstract-wc-intuit-payments-api-payment-request',
			'requests/class-wc-intuit-payments-api-credit-card-request',
			'requests/class-wc-intuit-payments-api-echeck-request',
			'requests/class-wc-intuit-payments-api-oauth-request',
			'requests/class-wc-intuit-payments-api-oauth2-request',
			'requests/class-wc-intuit-payments-api-payment-method-request',

			// responses
			'responses/abstract-wc-intuit-payments-api-response',
			'responses/abstract-wc-intuit-payments-api-payment-response',
			'responses/abstract-wc-intuit-payments-api-payment-refund-response',
			'responses/class-wc-intuit-payments-api-credit-card-response',
			'responses/class-wc-intuit-payments-api-credit-card-refund-response',
			'responses/class-wc-intuit-payments-api-echeck-response',
			'responses/class-wc-intuit-payments-api-echeck-refund-response',
			'responses/class-wc-intuit-payments-api-oauth-response',
			'responses/class-wc-intuit-payments-api-oauth2-response',
			'responses/class-wc-intuit-payments-api-oauth-management-response',
			'responses/class-wc-intuit-payments-api-payment-method-response',
			'responses/class-wc-intuit-payments-api-get-payment-methods-response',
		);

		foreach ( $files as $file ) {
			require_once( $path . $file . '.php' );
		}

		return $this->api = new WC_Intuit_Payments_API( $this );
	}


	/**
	 * Gets the environment API endpoint.
	 *
	 * @since 2.0.0
	 * @param string $environment_id Optional. One of 'sandbox' or 'production'. Defaults to current configured environment
	 * @return string
	 */
	public function get_api_endpoint( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_test_environment( $environment_id ) ? self::API_ENDPOINT_SANDBOX : self::API_ENDPOINT;
	}


	/**
	 * Determines if the current gateway environment is configured to 'sandbox'.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway::is_test_environment()
	 * @param string $environment_id optional. the environment ID to check, otherwise defaults to the gateway current environment
	 * @return bool
	 */
	public function is_test_environment( $environment_id = null ) {

		// if an environment is passed in, check that
		if ( null !== $environment_id ) {
			return self::ENVIRONMENT_SANDBOX === $environment_id;
		}

		// otherwise default to checking the current environment
		return $this->is_environment( self::ENVIRONMENT_SANDBOX );
	}


	/** oAuth 2.0 methods *****************************************************/


	/**
	 * Gets the merchant's app's client ID.
	 *
	 * @since 2.3.5-dev.1
	 *
	 * @param string $environment_id environment ID
	 * @return string
	 */
	public function get_client_id( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_test_environment( $environment_id ) ? $this->sandbox_client_id : $this->client_id;
	}


	/**
	 * Gets the merchant's app's client secret.
	 *
	 * @since 2.3.5-dev.1
	 *
	 * @param string $environment_id environment ID
	 * @return string
	 */
	public function get_client_secret( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_test_environment( $environment_id ) ? $this->sandbox_client_secret : $this->client_secret;
	}


	/** Deprecated methods ********************************************************************************************/


	/**
	 * Initiates the oAuth process.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function begin_oauth() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Handles the OAuth 2 response.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 */
	public function oauth_authorize() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Handles the legacy OAuth 1 authorization response.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 */
	public function oauth_authorize_legacy() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Reconnects the current oAuth account.
	 *
	 * This regenerates the oAuth tokens and re-schedules the cron event.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function oauth_reconnect() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Disconnects the current oAuth account.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function oauth_disconnect() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Stores oAuth data on connect or reconnect.
	 *
	 * Stores the token expiration date & schedules a cron event to auto-reconnect
	 * in the future.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function store_oauth_data() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Gets the Intuit API access token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_access_token()' );

		return $this->get_connection_handler()->get_access_token();
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_refresh_token() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_refresh_token()' );

		return $this->get_connection_handler()->get_refresh_token();
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_access_token_expiry() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_access_token_expiry()' );

		return $this->get_connection_handler()->get_access_token_expiry();
	}


	/**
	 * Sets the Intuit API access token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @param string $token token to set
	 * @return string
	 */
	public function set_access_token( $token ) {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::set_access_token()' );

		return $this->get_connection_handler()->set_access_token( $token );
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @param string $token token to set
	 * @return string
	 */
	public function set_refresh_token( $token ) {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::set_refresh_token()' );

		return $this->get_connection_handler()->set_refresh_token( $token );
	}


	/**
	 * Sets the Intuit API token expiry.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @param string $expiry expiry timestamp
	 * @return string
	 */
	public function set_access_token_expiry( $expiry ) {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::set_access_token_expiry()' );

		return $this->get_connection_handler()->set_access_token_expiry( $expiry );
	}


	/**
	 * Gets the option name prefix for the oAuth 2.0 tokens.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.1.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_token_option_name() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_token_option_name()' );

		return $this->get_connection_handler()->get_token_option_name();
	}


	/**
	 * Gets the OAuth redirect URI.
	 *
	 * This tells Intuit where to redirect back to after auth, and must match
	 * the Intuit app's configured URI exactly.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.3.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_auth_redirect_uri() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_redirect_uri()' );

		return $this->get_connection_handler()->get_redirect_url();
	}


	/**
	 * Gets the oAuth token.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_oauth_token() {

		wc_deprecated_function( __METHOD__, '2.4.0', SkyVerge\WooCommerce\Intuit\Handlers\Connection::class . '::get_access_token()' );

		return $this->get_connection_handler()->get_access_token();
	}


	/**
	 * Gets the oAuth token secret.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 *
	 * @return string
	 */
	public function get_oauth_token_secret() {

		wc_deprecated_function( __METHOD__, '2.4.0' );

		return '';
	}


	/**
	 * Determines if the currently connected account is able to reconnect.
	 *
	 * This checks the current date/time against the expiration window.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function can_reconnect() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Resets the scheduled reconnection cron event.
	 *
	 * TODO: remove after 2020-08 or in v3.0.0 {CW 2019-08-15}
	 *
	 * @since 2.0.0
	 * @deprecated 2.4.0
	 */
	public function reset_reconnect_cron_event() {

		wc_deprecated_function( __METHOD__, '2.4.0' );
	}


	/**
	 * Gets the OAuth version to use when connecting to the Intuit API.
	 *
	 * Defaults to 2.0 if not set.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.1.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @return string
	 */
	public function get_oauth_version() {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return self::OAUTH_VERSION_2;
	}


	/**
	 * Sets the OAuth version property - does not persist.
	 *
	 * This is used during plugin upgrade to update the OAuth version without
	 * needing to reload the entire gateway.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @param string $version OAuth version
	 */
	public function set_oauth_version( $version ) {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );
	}


	/**
	 * Gets the merchant's app's consumer key.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.0.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @param string $environment_id environment ID
	 * @return string
	 */
	public function get_consumer_key( $environment_id = null ) {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return '';
	}


	/**
	 * Gets the merchant's app's consumer secret.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.0.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @param string $environment_id environment ID
	 * @return string
	 */
	public function get_consumer_secret( $environment_id = null ) {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return '';
	}


}
