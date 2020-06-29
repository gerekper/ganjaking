<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_1 as Framework;

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @method \WC_Gateway_Chase_Paymentech get_gateway()
 */
class WC_Chase_Paymentech extends Framework\SV_WC_Payment_Gateway_Plugin {


	/** string version number */
	const VERSION = '1.14.7';

	/** @var WC_Chase_Paymentech single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'chase_paymentech';

	/** string plugin text domain, DEPRECATED as of 1.5.0 */
	const TEXT_DOMAIN = 'woocommerce-gateway-chase-paymentech';

	/** string the gateway class name */
	const CREDIT_CARD_GATEWAY_CLASS_NAME = 'WC_Gateway_Chase_Paymentech';

	/** string the gateway id */
	const CREDIT_CARD_GATEWAY_ID = 'chase_paymentech';

	/** @var \WC_Chase_Paymentech_Certification_Handler the certification handler instance **/
	protected $certification_handler;


	/**
	 * Setup main plugin class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain' => 'woocommerce-gateway-chase-paymentech',
				'gateways'    => [
					self::CREDIT_CARD_GATEWAY_ID => self::CREDIT_CARD_GATEWAY_CLASS_NAME,
				],
				'dependencies' => [
					'php_extensions' => [ 'SimpleXML', 'xmlwriter', 'dom' ],
				],
				'require_ssl' => true,
				'supports'    => [
					self::FEATURE_CAPTURE_CHARGE,
					self::FEATURE_MY_PAYMENT_METHODS,
				],
				'currencies' => [ 'USD' ],
			]
		);

		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

		// AJAX handler to handle errors
		add_action( 'wp_ajax_wc_payment_gateway_' . $this->get_id() . '_handle_error',        array( $this, 'handle_transaction_error' ) );
		add_action( 'wp_ajax_nopriv_wc_payment_gateway_' . $this->get_id() . '_handle_error', array( $this, 'handle_transaction_error' ) );

		// process our mini checkout form on the Pay Page (for supporting direct tokenized transactions)
		add_action( 'wp_ajax_wc-' . $this->get_id_dasherized() . '-checkout',                 array( $this, 'process_checkout' ) );
		add_action( 'wp_ajax_nopriv_wc-' . $this->get_id_dasherized() . '-checkout',          array( $this, 'process_checkout' ) );

		// update our mini checkout form on the Pay Page
		add_action( 'wp_ajax_wc-' . $this->get_id_dasherized() . '-update-checkout',          array( $this, 'update_checkout' ) );
		add_action( 'wp_ajax_nopriv_wc-' . $this->get_id_dasherized() . '-update-checkout',   array( $this, 'update_checkout' ) );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.12.0
	 */
	public function init_plugin() {

		parent::init_plugin();

		// gateway class
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-chase-paymentech.php' );

		// token handler
		require_once( $this->get_plugin_path() . '/includes/class-wc-chase-paymentech-payment-tokens-handler.php' );

		// certification handler
		if ( $this->get_gateway()->is_certification_mode() ) {

			require_once( $this->get_plugin_path() . '/includes/class-wc-chase-paymentech-certification-handler.php' );

			$this->certification_handler = new WC_Chase_Paymentech_Certification_Handler( $this->get_gateway() );
		}

		// response helper
		require_once( $this->get_plugin_path() . '/includes/class-wc-chase-paymentech-response-message-helper.php' );
	}


	/**
	 * Main Chase Paymentech Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.3.0
	 *
	 * @see wc_chase_paymentech()
	 * @return WC_Chase_Paymentech
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Gets the sales page URL.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/chase-paymentech/';
	}


	/**
	 * Gets the plugin documentation URL
	 *
	 * @since 1.4.0
	 *
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {

		return 'http://docs.woocommerce.com/document/woocommerce-chase-paymentech/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.4.0
	 *
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Function used to init any gateway template functions,
	 * making them pluggable by plugins and themes.
	 *
	 * @since 1.0
	 */
	public function include_template_functions() {

		require_once( $this->get_plugin_path() . '/includes/wc-gateway-chase-paymentech-template.php' );
	}


	/**
	 * Checks if required PHP extensions are loaded and adds an admin notice
	 * for any missing extensions.  Also plugin settings can be checked
	 * as well.
	 *
	 * @since 1.2.0
	 *
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// encourage new installs to register with Chase
		if ( ! $this->get_admin_notice_handler()->is_notice_dismissed( 'register-with-chase' ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <strong>, %2$s - </strong>, %3$s - <a>, %4$s - </a> */
				__( 'Thank you for your purchase of %1$sWooCommerce Chase Paymentech%2$s!  For the absolute fastest and best onboarding experience, please don\'t forget to %3$sregister with Chase%4$s.', 'woocommerce-gateway-chase-paymentech' ),
				'<strong>', '</strong>',
				'<a href="http://www.chasepaymentech.com/referral_smg.html?referralParty=Woothemes">', '</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'register-with-chase', array( 'always_show_on_settings' => false ) );
		}
	}


	/**
	 * Returns true if the gateway supports the charge capture operation and it
	 * can be invoked
	 *
	 * @since 1.2.0
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Plugin::can_capture_charge()
	 * @param Framework\SV_WC_Payment_Gateway $gateway the payment gateway
	 * @return boolean true if the gateway supports the charge capture operation and it can be invoked
	 */
	public function can_capture_charge( $gateway ) {

		wc_deprecated_function( __METHOD__, '1.12.0', '\SkyVerge\WooCommerce\Chase_Paymentech\Capture::is_order_ready_for_capture()' );

		return $this->supports_capture_charge() && $gateway->supports_credit_card_capture() && $this->get_gateway()->is_direct_api_configured();
	}



	/** AJAX methods ******************************************************/


	/**
	 * Handle any transaction errors by handing off to the gateway
	 *
	 * @since 1.0
	 */
	public function handle_transaction_error() {

		$this->get_gateway()->handle_transaction_error();
	}


	/**
	 * Pay page tokenized payment method checkout process, adapted from
	 * WooCommerce core
	 *
	 * @since 1.0
	 */
	public function process_checkout() {

		$order_id = isset( $_POST['order_id'] ) ? $_POST['order_id'] : 0;

		// Validate
		$this->get_gateway()->validate_fields();

		// sanity check for the order ID
		if ( ! $order_id ) {
			Framework\SV_WC_Helper::wc_add_notice( __( 'Invalid order ID', 'woocommerce-gateway-chase-paymentech' ), 'error' );
		}

		// Process
		if ( Framework\SV_WC_Helper::wc_notice_count( 'error' ) == 0 ) {

			// Process Payment
			$result = $this->get_gateway()->process_payment( $_POST['order_id'] );

			// Redirect to success/confirmation/payment page
			if ( isset( $result['result'] ) && 'success' === $result['result'] ) {

				$result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );

				if ( is_ajax() ) {
					echo '<!--WC_START-->' . json_encode( $result ) . '<!--WC_END-->';
					exit;
				} else {
					wp_redirect( $result['redirect'] );
					exit;
				}

			}
		}

		// If we reached this point then there were errors
		ob_start();
		wc_print_notices();
		$messages = ob_get_clean();

		echo '<!--WC_START-->' . json_encode(
			array(
				'result'   => 'failure',
				'messages' => $messages,
			)
		) . '<!--WC_END-->';

		exit;
	}


	/**
	 * Update pay page checkout form
	 *
	 * @since 1.0
	 */
	public function update_checkout() {

		check_ajax_referer( 'update-checkout', 'security' );

		// Render the form
		$this->get_gateway()->payment_page( $_POST['order_id'] );
		exit;
	}


	/** Certification Mode ******************************************************/


	/**
	 * Get the certification handler.
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Chase_Paymentech_Certification_Handler the handler object
	 */
	public function get_certification_handler() {

		return $this->certification_handler;
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0
	 *
	 * @see Framework\SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Chase Paymentech Gateway', 'woocommerce-gateway-chase-paymentech' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.12.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );
		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Chase_Paymentech\Lifecycle( $this );
	}

} // end \WC_Chase_Paymentech class


/**
 * Returns the One True Instance of Chase Paymentech.
 *
 * @since 1.3.0
 *
 * @return WC_Chase_Paymentech
 */
function wc_chase_paymentech() {

	return WC_Chase_Paymentech::instance();
}
