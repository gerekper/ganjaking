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
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * The main class for the Intuit Payments Gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.
 *
 * This plugin contains two distinct "integrations," each with their own set of
 * gateways. The primary integration is Payments, and integrations with the
 * latest QuickBooks Payments API. The second is the legacy QBMS API that was
 * used before v2.0.0.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments extends Framework\SV_WC_Payment_Gateway_Plugin {


	/** string the plugin version number */
	const VERSION = '3.2.1';

	/** string the plugin id */
	const PLUGIN_ID = 'intuit_payments';

	/** string the credit card gateway class name */
	const CREDIT_CARD_CLASS_NAME = 'WC_Gateway_Inuit_Payments_Credit_Card';

	/** string the credit card gateway ID */
	const CREDIT_CARD_ID = 'intuit_payments_credit_card';

	/** string the eCheck gateway class name */
	const ECHECK_CLASS_NAME = 'WC_Gateway_Inuit_Payments_eCheck';

	/** string the eCheck gateway ID */
	const ECHECK_ID = 'intuit_payments_echeck';

	/** @var \WC_Intuit_Payments_AJAX the Payments AJAX instance */
	protected $payments_ajax_instance;

	/** The legacy QBMS gateways **********************************************/

	/** string the ID for the QBMS group of gateways */
	const QBMS_PLUGIN_ID = 'intuit_qbms';

	/** string the QBMS credit card gateway class name */
	const QBMS_CREDIT_CARD_CLASS_NAME = 'WC_Gateway_Intuit_QBMS_Credit_Card';

	/** string the QBMS credit card gateway ID */
	const QBMS_CREDIT_CARD_ID = 'intuit_qbms_credit_card';

	/** string the QBMS eCheck gateway class name */
	const QBMS_ECHECK_CLASS_NAME = 'WC_Gateway_Intuit_QBMS_eCheck';

	/** string the QBMS eCheck gateway ID */
	const QBMS_ECHECK_ID = 'intuit_qbms_echeck';

	/** @var \WC_Intuit_Payments single instance of this plugin */
	protected static $instance;


	/**
	 * Sets up the main plugin class.
	 *
	 * @see Framework\SV_WC_Plugin::__construct()
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'gateways'           => $this->get_available_gateways(),
				'require_ssl'        => true,
				'supports'           => [
					self::FEATURE_CUSTOMER_ID,
					self::FEATURE_CAPTURE_CHARGE,
					self::FEATURE_MY_PAYMENT_METHODS,
				],
				'dependencies'       => $this->get_active_integration_dependencies(),
				'supported_features' => [
					'hpos'   => true,
					'blocks' => [
						'cart'     => true,
						'checkout' => false,
					],
				],
			]
		);

		// include required files
		$this->includes();

		// register connection scripts earlier so that they are available when the setup wizard is rendered and the gateway scripts are enqueued
		add_action( 'init', [ $this, 'register_connection_scripts' ] );
	}


	/**
	 * Loads any required files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		$plugin_path = $this->get_plugin_path();

		require_once( $plugin_path . '/src/Handlers/Payment_Form.php' );
		require_once( $plugin_path . '/src/Handlers/Connection.php' );
		require_once( $plugin_path . '/src/abstract-wc-gateway-intuit-payments.php' );
		require_once( $plugin_path . '/src/class-wc-gateway-intuit-payments-credit-card.php' );
		require_once( $plugin_path . '/src/class-wc-gateway-intuit-payments-echeck.php' );
		require_once( $plugin_path . '/src/api/class-wc-intuit-payments-api-oauth-helper.php' );
		require_once( $plugin_path . '/src/class-wc-intuit-payments-ajax.php' );

		$this->payments_ajax_instance = new WC_Intuit_Payments_AJAX( $this );

		require_once( $plugin_path . '/src/class-wc-intuit-payments-tokens-handler.php' );

		if ( is_admin() ) {
			require_once( $plugin_path . '/src/class-wc-intuit-payments-admin-token-editor.php' );
		}
	}


	/**
	 * Registers the JavaScript library used to add the Connect to QuickBooks button
	 *
	 * The script is enqueued as a dependency on {@see \WC_Gateway_Inuit_Payments::load_admin_scripts()}
	 * and {@see \SkyVerge\WooCommerce\Intuit\Admin\Setup_Wizard::load_scripts_styles()}
	 *
	 * @since 2.5.0
	 */
	public function register_connection_scripts() {

		// the fourth parameter ($ver) must be explicit null to fully remove it from the script import URL
		wp_register_script(
			'wc-intuit-payments-connect',
			'https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere-1.3.3.js',
			[],
			null
		);
	}


	/** Integration switching methods *****************************************/


	/**
	 * Gets the required dependencies for the active integration.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_active_integration_dependencies(): array {

		return [
			'php_extensions' => [ 'openssl', 'json' ],
			'php_functions'  => [],
			'php_settings'   => [],
		];
	}


	/**
	 * Gets the gateways available for activation.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_available_gateways() {

		return [
			self::CREDIT_CARD_ID => self::CREDIT_CARD_CLASS_NAME,
			self::ECHECK_ID      => self::ECHECK_CLASS_NAME,
		];
	}


	/**
	 * Gets the active integration ID.
	 *
	 * This is considered the active "set" of gateways, either the Legacy QBMS or
	 * Payments API Credit Card & eCheck gateways.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_active_integration() {

		return get_option( 'wc_intuit_payments_active_integration', self::PLUGIN_ID );
	}


	/**
	 * Determines if the legacy QBMS gateway is active.
	 *
	 * @deprecated
	 *
	 * @since 2.0.0
	 * @since 3.0.0 always returns false
	 *
	 * @return bool
	 */
	public function is_qbms_active() {

		return false;
	}


	/**
	 * Handles switching between the active integration.
	 *
	 * @deprecated
	 * @since 2.0.0
	 */
	public function change_integration() {
		_doing_it_wrong( __FUNCTION__, __( 'Switching between integrations is no longer available.', 'woocommerce-gateway-intuit-payments' ), '3.0.0' );
	}


	/** Admin methods *********************************************************/


	/**
	 * Adds a notice when gateways are switched.
	 *
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// if we detected an invalid connection error, show a notice
		if ( 'no' === get_option( 'wc_intuit_payments_connected' ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - payment gateway name, %2$s - <strong> tag, %3$s - </strong> tag, %4$s - <a> tag, %5$s - </a> tag */
				__( '%1$s: Your connection to Intuit appears to be invalid. If you are having issues with payment processing, please %2$sDisconnect from Quickbooks%3$s and Connect again from the %4$sgateway settings%5$s.', 'woocommerce-gateway-intuit-payments' ),
				'<strong>' . $this->get_plugin_name() . '</strong>',
				'<strong>', '</strong>',
				'<a href="' . esc_url( $this->get_settings_url() ) . '#woocommerce_intuit_payments_credit_card_connection_settings">', '</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'bad-connection', array(
				'always_show_on_settings' => true,
				'notice_class'            => 'error',
			) );
		}

		$this->maybe_add_intuit_qbms_retired_admin_notice();
	}


	/**
	 * Checks whether to display the QBMS retired admin notice.
	 *
	 * @since 3.0.0
	 */
	private function maybe_add_intuit_qbms_retired_admin_notice() {

		if ( 'yes' === get_option( 'wc_intuit_payments_show_intuit_qbms_retired_admin_notice' ) ) {
			$this->add_intuit_qbms_retired_admin_notice();
		}
	}


	/**
	 * Displays the QBMS retired admin notice.
	 *
	 * @since 3.0.0
	 */
	private function add_intuit_qbms_retired_admin_notice() {

		$message = sprintf(
			/** translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </a> tag */
			__( '%1$sHeads up!%2$s The QBMS gateway has been retired. %3$sClick here for instructions%4$s on using the Intuit Payments gateway and please %5$scontact support%6$s with any questions or concerns.', 'woocommerce-gateway-intuit-payments' ),
			'<strong>', '</strong>',
			'<a href="https://docs.woocommerce.com/document/woocommerce-intuit-qbms/#oauth-migration" target="_blank">', '</a>',
			'<a href="https://woocommerce.com/my-account/create-a-ticket/" target="_blank">', '</a>'
		);

		$this->get_admin_notice_handler()->add_admin_notice(
			$message,
			$this->get_gateway()->get_id_dasherized() . '-qbms-retired-notice',
			[
				'dismissible'             => $this->get_gateway()->is_configured(),
				'always_show_on_settings' => false,
				'notice_class'            => 'notice-warning',
			]
		);
	}


	/** Encryption Methods ****************************************************/


	/**
	 * Encrypts a connection credential for storage.
	 *
	 * @since 2.3.0
	 *
	 * @param string $data the credential value
	 * @return string
	 */
	public function encrypt_credential( $data ) {

		$data = trim( $data );

		if ( empty( $data ) ) {
			return '';
		}

		if ( function_exists( 'openssl_encrypt' ) ) {
			$vector = openssl_random_pseudo_bytes( $this->get_encryption_vector_length() );
			$data   = openssl_encrypt( $data, $this->get_encryption_method(), $this->get_encryption_key(), 0, $vector );
		}

		return base64_encode( $vector . $data );
	}


	/**
	 * Decrypts a connection credential for use.
	 *
	 * @since 2.3.0
	 *
	 * @param string $data the encrypted credential value
	 * @return string
	 */
	public function decrypt_credential( $data ) {

		if ( empty( $data ) ) {
			return '';
		}

		$data = base64_decode( $data );

		if ( function_exists( 'openssl_decrypt' ) ) {

			$vector_length = $this->get_encryption_vector_length();
			$vector        = substr( $data, 0, $vector_length );
			$data          = substr( $data, $vector_length );
			$data          = openssl_decrypt( $data, $this->get_encryption_method(), $this->get_encryption_key(), 0, $vector );
		}

		return trim( $data );
	}


	/**
	 * Decrypts a connection credential for use using mcrypt.
	 *
	 * This is only to upgrade connection credentials that previously used
	 * mcrypt.
	 *
	 * @since 2.3.0
	 *
	 * @param string $data the encrypted credential value
	 * @return string
	 */
	public function decrypt_credential_legacy( $data ) {

		if ( empty( $data ) ) {
			return '';
		}

		$decrypted = base64_decode( $data );

		$iv = substr( $decrypted, 0, 16 );

		$decrypted = substr( $decrypted, 16 );

		return trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $this->get_encryption_key(), $decrypted, MCRYPT_MODE_CBC, $iv ) );
	}


	/**
	 * Gets the key used to encrypt the connection credentials.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	private function get_encryption_key() {

		return md5( wp_salt(), true );
	}


	/**
	 * Gets the vector length for encrypting credentials.
	 *
	 * @since 2.3.0
	 *
	 * @return int
	 */
	private function get_encryption_vector_length() {

		return openssl_cipher_iv_length( $this->get_encryption_method() );
	}


	/**
	 * Gets the method used for encrypting credentials.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	private function get_encryption_method() {

		$available_methods = openssl_get_cipher_methods();
		$preferred_method  = 'AES-128-CBC';

		$method = in_array( $preferred_method, $available_methods, true ) ? $preferred_method : $available_methods[0];

		return $method;
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the one true Intuit Payments plugin instance.
	 *
	 * @see wc_intuit_payments()
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Intuit_Payments
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the My Payment Methods handler instance.
	 *
	 * @since 2.7.4
	 *
	 * @return \SkyVerge\WooCommerce\Intuit\Handlers\My_Payment_Methods
	 */
	protected function get_my_payment_methods_instance() {

		require_once( $this->get_plugin_path() . '/src/Handlers/My_Payment_Methods.php' );

		return new \SkyVerge\WooCommerce\Intuit\Handlers\My_Payment_Methods( $this );
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-intuit-qbms/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/create-a-ticket/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * Used for the 'Reviews' plugin action and review prompts.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/intuit-qbms/';
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_plugin_name()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Intuit Payments Gateway', 'woocommerce-gateway-intuit-payments' );
	}


	/**
	 * Gets the "Configure Credit Cards" or "Configure eCheck" plugin action links that go
	 * directly to the gateway settings page.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Plugin::get_settings_url()
	 *
	 * @since 2.0.0
	 * @param string $gateway_id the gateway ID
	 * @return string
	 */
	public function get_settings_link( $gateway_id = null ) {

		if ( self::ECHECK_ID === $gateway_id ) {
			$label = __( 'Configure eChecks', 'woocommerce-gateway-intuit-payments' );
		} else if ( self::CREDIT_CARD_ID === $gateway_id ) {
			$label = __( 'Configure Credit Cards', 'woocommerce-gateway-intuit-payments' );
		} else {
			$label = __( 'Configure', 'woocommerce-gateway-intuit-payments' );
		}

		return sprintf( '<a href="%s">%s</a>',
			$this->get_settings_url( $gateway_id ),
			$label
		);
	}


	/**
	 * Gets __FILE__
	 *
	 * @since 2.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 2.3.3
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Intuit\Lifecycle( $this );
	}


	/**
	 * Builds the Setup Wizard handler class.
	 *
	 * @since 2.5.0
	 */
	protected function init_setup_wizard_handler() {

		parent::init_setup_wizard_handler();

		require_once $this->get_payment_gateway_framework_path() . '/admin/abstract-sv-wc-payment-gateway-plugin-admin-setup-wizard.php';
		require_once $this->get_plugin_path() . '/src/admin/Setup_Wizard.php';

		$this->setup_wizard_handler = new \SkyVerge\WooCommerce\Intuit\Admin\Setup_Wizard( $this );
	}


}

/**
 * Gets the one true instance of Intuit Payments.
 *
 * @since 2.0.0
 *
 * @return \WC_Intuit_Payments
 */
function wc_intuit_payments() {
	return WC_Intuit_Payments::instance();
}
