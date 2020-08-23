<?php
/**
 * WooCommerce Twilio SMS Notifications
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Twilio SMS Notifications plugin main class.
 *
 * @since 1.0
 */
class WC_Twilio_SMS extends Framework\SV_WC_Plugin {


	/** version number */
	const VERSION = '1.15.0';

	/** @var WC_Twilio_SMS single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'twilio_sms';

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-twilio-sms-notifications';

	/** @var \WC_Twilio_SMS_Admin instance */
	protected $admin;

	/** @var \WC_Twilio_SMS_AJAX instance */
	protected $ajax;

	/** @var \WC_Twilio_SMS_API instance */
	private $api;

	/** @var \SkyVerge\WooCommerce\Twilio_SMS\Integrations\Bookings instance */
	private $bookings;


	/**
	 * Sets up main plugin class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-twilio-sms-notifications',
			)
		);

		// Load classes
		$this->includes();

		// Add opt-in checkbox to checkout
		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_opt_in_checkbox' ) );

		// Process opt-in checkbox after order is processed
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'process_opt_in_checkbox' ) );

		// GDPR compliance: delete customer opt in for receiving SMS updates, when order is anonymized
		add_action( 'woocommerce_privacy_remove_order_personal_data', array( $this, 'erase_opt_in' ) );

		// Add order status hooks, at priority 11 as Order Status Manager adds
		// custom statuses at 10
		add_action( 'init', array( $this, 'add_order_status_hooks' ), 11 );

		// this integration requires that WooCommerce Bookings be enabled
		// checking this here rather than the Bookings constructor so we can use is_plugin_active()
		if ( $this->is_plugin_active( 'woocommerce-bookings.php' ) ) {

			$this->bookings = new \SkyVerge\WooCommerce\Twilio_SMS\Integrations\Bookings( $this );
		}
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.12.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Twilio_SMS\Lifecycle( $this );
	}


	/**
	 * Loads required classes
	 *
	 * @since 1.0
	 */
	private function includes() {

		// a message length calculator helper to estimate the number of parts an SMS may be split into
		require_once( $this->get_plugin_path() . '/includes/Message_Length_Calculator.php' );

		// the URL shortener class helps shortening URLs in SMS messages
		require_once( $this->get_plugin_path() . '/includes/class-wc-twilio-sms-url-shortener.php' );

		// the notification handler sends SMS notifications
		require_once( $this->get_plugin_path() . '/includes/class-wc-twilio-sms-notification.php' );

		// the bookings integration
		require_once( $this->get_plugin_path() . '/includes/Integrations/Bookings/Bookings.php' );
		require_once( $this->get_plugin_path() . '/includes/Integrations/Bookings/NotificationSchedule.php' );

		// the response handler manages creating XML response message
		if ( isset( $_REQUEST['wc_twilio_sms_response'] ) ) {
			$this->load_class( '/includes/class-wc-twilio-sms-response.php', 'WC_Twilio_SMS_Response' );
		}

		// load admin classes
		if ( is_admin() ) {
			$this->admin_includes();
		}
	}


	/**
	 * Loads admin classes
	 *
	 * @since 1.0
	 */
	private function admin_includes() {

		// admin
		$this->admin = $this->load_class( '/includes/admin/class-wc-twilio-sms-admin.php', 'WC_Twilio_SMS_Admin' );

		// AJAX
		$this->ajax = $this->load_class( '/includes/class-wc-twilio-sms-ajax.php', 'WC_Twilio_SMS_AJAX' );
	}


	/**
	 * Return admin class instance
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Twilio_SMS_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Return ajax class instance
	 *
	 * @since 1.8.0
	 *
	 * @return \WC_Twilio_SMS_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Returns the Bookings integration class instance
	 *
	 * @since 1.12.0
	 *
	 * @return \SkyVerge\WooCommerce\Twilio_SMS\Integrations\Bookings instance
	 */
	public function get_bookings_instance() {

		return $this->bookings;
	}


	/**
	 * Add hooks for the opt-in checkbox and customer / admin order status changes
	 *
	 * @since 1.1
	 */
	public function add_order_status_hooks() {

		$statuses = wc_get_order_statuses();

		// Customer order status change hooks
		foreach ( array_keys( $statuses ) as $status ) {

			$status_slug = ( 'wc-' === substr( $status, 0, 3 ) ) ? substr( $status, 3 ) : $status;

			add_action( 'woocommerce_order_status_' . $status_slug, array( $this, 'send_customer_notification' ) );
		}

		// Admin new order hooks
		add_action( 'woocommerce_order_status_pending_to_on-hold', [ $this, 'send_admin_new_order_notification' ] );
		add_action( 'woocommerce_order_status_failed_to_on-hold',  [ $this, 'send_admin_new_order_notification' ] );
		add_action( 'woocommerce_payment_complete',                [ $this, 'send_admin_new_order_notification' ] );
	}


	/**
	 * Send customer an SMS when their order status changes
	 *
	 * @since 1.1
	 *
	 * @param int $order_id
	 */
	public function send_customer_notification( $order_id ) {

		$notification = new \WC_Twilio_SMS_Notification( $order_id );

		$notification->send_automated_customer_notification();
	}


	/**
	 * Send admins an SMS when a new order is received
	 *
	 * @since 1.1
	 *
	 * @param int $order_id
	 */
	public function send_admin_new_order_notification( $order_id ) {

		$notification = new \WC_Twilio_SMS_Notification( $order_id );

		$notification->send_admin_notification();
	}


	/**
	 * Returns the Twilio SMS API object
	 *
	 * @since 1.1
	 *
	 * @return \WC_Twilio_SMS_API the API object
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		// Load API
		require_once( $this->get_plugin_path() . '/includes/class-wc-twilio-sms-api.php' );

		$account_sid = get_option( 'wc_twilio_sms_account_sid', '' );
		$auth_token  = get_option( 'wc_twilio_sms_auth_token', '' );
		$from_number = get_option( 'wc_twilio_sms_from_number', '' );

		$options = array();

		if ( $asid = get_option( 'wc_twilio_sms_asid' ) ) {
			$options['asid'] = $asid;
		}

		return $this->api = new \WC_Twilio_SMS_API( $account_sid, $auth_token, $from_number, $options );
	}


	/**
	 * Adds checkbox to checkout page for customer to opt-in to SMS notifications
	 *
	 * @since 1.0
	 */
	public function add_opt_in_checkbox() {

		// use previous value or default value when loading checkout page
		if ( ! empty( $_POST['wc_twilio_sms_optin'] ) ) {
			$value = wc_clean( $_POST['wc_twilio_sms_optin'] );
		} else {
			$value = ( 'checked' === get_option( 'wc_twilio_sms_checkout_optin_checkbox_default', 'unchecked' ) ) ? 1 : 0;
		}

		/**
		 * Filters the optin label at checkout.
		 *
		 * @since 1.12.0
		 *
		 * @param string $label the checkout label
		 */
		$optin_label = apply_filters( 'wc_twilio_sms_checkout_optin_label', get_option( 'wc_twilio_sms_checkout_optin_checkbox_label', '' ) );

		if ( ! empty( $optin_label ) ) {

			// output checkbox
			woocommerce_form_field( 'wc_twilio_sms_optin', array(
				'type'  => 'checkbox',
				'class' => array( 'form-row-wide' ),
				'label' => $optin_label,
			), $value );
		}
	}


	/**
	 * Save opt-in as order meta
	 *
	 * TODO: This method will later need to instantiate an order / use a WC Data method. {BR 2017-02-22}
	 *
	 * @since 1.0
	 *
	 * @param int $order_id order ID for order being processed
	 */
	public function process_opt_in_checkbox( $order_id ) {

		if ( ! empty( $_POST['wc_twilio_sms_optin'] ) ) {
			update_post_meta( $order_id, '_wc_twilio_sms_optin', 1 );
		}
	}


	/**
	 * Removes the SMS Notification opt in when an order is anonymized and personal data erased.
	 *
	 * @internal
	 *
	 * @since 1.10.1
	 *
	 * @param \WC_Order $order an order being erased by privacy request
	 */
	public function erase_opt_in( $order ) {

		if ( $order instanceof \WC_Order ) {

			$order->delete_meta_data( '_wc_twilio_sms_optin' );
			$order->save_meta_data();
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the main Twilio SMS instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.4.0
	 *
	 * @return \WC_Twilio_SMS
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Twilio SMS Notifications', 'woocommerce-twilio-sms-notifications' );
	}


	/**
	 * Returns the full path and filename of the plugin file.
	 *
	 * @since 1.2
	 *
	 * @return string
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page
	 *
	 * @since 1.2
	 *
	 * @param string $_ unused
	 * @return string
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=twilio_sms' );
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/twilio-sms-notifications/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/twilio-sms-notifications/';
	}


	/**
	 * Returns true if on the plugin settings page.
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && isset( $_GET['tab'] ) && 'twilio_sms' === $_GET['tab'];
	}


	/**
	 * Log messages to WooCommerce error log if logging is enabled.
	 *
	 * /wp-content/woocommerce/logs/twilio-sms.txt
	 *
	 * @since 1.1
	 *
	 * @param string $content message to log
	 * @param string $_ unused
	 */
	public function log( $content, $_ = null ) {

		if ( 'yes' === get_option( 'wc_twilio_sms_log_errors' ) ) {

			parent::log( $content );
		}
	}


}


/**
 * Returns the One True Instance of Twilio SMS.
 *
 * @since 1.12.0
 *
 * @return WC_Twilio_SMS
 */
function wc_twilio_sms() {

	return \WC_Twilio_SMS::instance();
}
