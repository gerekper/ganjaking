<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce FreshBooks main plugin class.
 *
 * @since 3.0
 */
class WC_FreshBooks extends Framework\SV_WC_Plugin {


	/** string version number */
	const VERSION = '3.14.1';

	/** @var WC_FreshBooks single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'freshbooks';

	/** @var \WC_FreshBooks_Admin instance */
	protected $admin;

	/** @var \WC_FreshBooks_Settings instance */
	protected $settings;

	/** @var \WC_FreshBooks_Orders_Admin instance */
	protected $orders_admin;

	/** @var \WC_FreshBooks_Products_Admin instance */
	protected $products_admin;

	/** @var \WC_FreshBooks_Webhooks instance */
	protected $webhooks;

	/** @var \WC_FreshBooks_Handler instance */
	protected $handler;

	/** @var \WC_FreshBooks_API instance */
	private $api;

	/** @var \WC_FreshBooks_Frontend instance */
	protected $frontend;


	/**
	 * Sets up main plugin class.
	 *
	 * @since 3.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'    => 'woocommerce-freshbooks',
				'php_extensions' => array(
					'xmlwriter',
				),
			)
		);

		$this->includes();

		// Subscriptions support
		if ( $this->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			// don't copy over FreshBooks invoice meta from the original order to the subscription (subscription objects should not have an invoice)
			add_filter( 'wcs_subscription_meta', array( $this, 'subscriptions_remove_subscription_order_meta' ), 10, 3 );

			// TODO we dropped support for Subscriptions 1.5.x since 3.10.0, but this hook is for an upgrade script for installations migrating to 2.0.0 so we can keep this around a bit longer {FN 2017-03-21}
			// don't copy over FreshBooks invoice meta to subscription object during upgrade from 1.5.x to 2.0
			add_filter( 'wcs_upgrade_subscription_meta_to_copy', array( $this, 'subscriptions_remove_subscription_order_meta_during_upgrade' ) );

			// don't copy over FreshBooks invoice meta from the subscription to the renewal order
			add_filter( 'wcs_renewal_order_meta', array( $this, 'subscriptions_remove_renewal_order_meta' ) );
		}

		// maybe disable API logging
		if ( 'on' !== get_option( 'wc_freshbooks_debug_mode' ) ) {
			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10 );
		}
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 3.12.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new SkyVerge\WooCommerce\FreshBooks\Lifecycle( $this );
	}


	/**
	 * Loads any required files.
	 *
	 * @internal
	 *
	 * @since 3.0
	 */
	public function includes() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-freshbooks-order.php' );

		$this->handler     = $this->load_class( '/includes/class-wc-freshbooks-handler.php', 'WC_FreshBooks_Handler' );
		$this->webhooks    = $this->load_class( '/includes/class-wc-freshbooks-webhooks.php', 'WC_FreshBooks_Webhooks' );

		if ( is_admin() ) {
			$this->admin_includes();
		} else {
			$this->frontend = $this->load_class( '/includes/class-wc-freshbooks-frontend.php', 'WC_FreshBooks_Frontend' );
		}
	}


	/**
	 * Loads required admin files.
	 *
	 * @since 3.0
	 */
	private function admin_includes() {

		// add settings page
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		$this->admin          = $this->load_class( '/includes/admin/class-wc-freshbooks-admin.php', 'WC_FreshBooks_Admin' );
		$this->orders_admin   = $this->load_class( '/includes/admin/class-wc-freshbooks-orders-admin.php', 'WC_FreshBooks_Orders_Admin' );
		$this->products_admin = $this->load_class( '/includes/admin/class-wc-freshbooks-products-admin.php', 'WC_FreshBooks_Products_Admin' );
	}


	/**
	 * Gets the admin handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the settings handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Settings
	 */
	public function get_settings_instance() {

		return $this->settings;
	}


	/**
	 * Gets the orders admin handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Orders_Admin
	 */
	public function get_orders_admin_instance() {

		return $this->orders_admin;
	}


	/**
	 * Gets the frontend handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Gets the products admin handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Products_Admin
	 */
	public function get_products_admin_instance() {

		return $this->products_admin;
	}


	/**
	 * Gets the webhooks handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Webhooks
	 */
	public function get_webhooks_instance() {

		return $this->webhooks;
	}


	/**
	 * Gets the Freshbooks handler instance.
	 *
	 * @since 3.9.0
	 *
	 * @return \WC_FreshBooks_Handler
	 */
	public function get_handler_instance() {

		return $this->handler;
	}


	/**
	 * Lazy loads the FreshBooks API wrapper.
	 *
	 * @since 3.0
	 *
	 * @return \WC_FreshBooks_API instance
	 * @throws Framework\SV_WC_API_Exception missing API URL or authentication token settings
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$api_url              = get_option( 'wc_freshbooks_api_url' );
		$authentication_token = get_option( 'wc_freshbooks_authentication_token' );

		// bail if required info is not available
		if ( ! $api_url || ! $authentication_token ) {
			throw new Framework\SV_WC_API_Exception( __( 'Missing API URL or Authentication Token', 'woocommerce-freshbooks' ) );
		}

		// bail if API URL does not appear to be valid
		if ( false === strpos( $api_url, 'freshbooks.com/api/2.1/xml-in' ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'Incorrect API URL', 'woocommerce-freshbooks' ) );
		}

		// load API files
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-freshbooks-api-request.php' );
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-freshbooks-api-response.php' );
		require_once( $this->get_plugin_path() . '/includes/api/class-wc-freshbooks-api.php' );

		return $this->api = new \WC_FreshBooks_API( $api_url, $authentication_token );
	}


	/**
	 * Adds a settings page.
	 *
	 * @internal
	 *
	 * @since 3.2.0
	 *
	 * @param array $settings
	 * @return array
	 */
	public function add_settings_page( $settings ) {

		if ( ! $this->settings instanceof \WC_FreshBooks_Settings ) {
			$this->settings = $this->load_class( '/includes/admin/class-wc-freshbooks-settings.php', 'WC_FreshBooks_Settings' );
		}

		$settings[] = $this->settings;

		return $settings;
	}


	/**
	 * Renders any admin notices.
	 *
	 * @internal
	 *
	 * @since 3.2.0
	 */
	public function add_delayed_admin_notices() {

		parent::add_delayed_admin_notices();

		// onboarding!
		if ( ! get_option( 'wc_freshbooks_api_url' ) ) {

			if ( get_option( 'wc_freshbooks_upgraded_from_v2' ) ) {
				$message = __( 'Thanks for upgrading to the latest WooCommerce FreshBooks plugin! Please double-check your %1$sinvoice settings%2$s.', 'woocommerce-freshbooks' );
			} else {
				$message = __( 'Thanks for installing the WooCommerce FreshBooks plugin! To get started, please %1$sadd your FreshBooks API credentials%2$s. ', 'woocommerce-freshbooks' );
			}

			$this->get_admin_notice_handler()->add_admin_notice( sprintf( $message, '<a href="' . $this->get_settings_url() . '">', '</a>' ), 'welcome-notice', array( 'notice_class' => 'updated' ) );
		}
	}


	/**
	 * Handles renewal meta data in WooCommerce Subscriptions.
	 *
	 * Don't copy invoice meta to renewal orders from the WC_Subscription object.
	 * Generally the subscription object should not have any order-specific meta.
	 * This allows an invoice to be created for each renewal order.
	 *
	 * @internal
	 *
	 * @since 3.5.1
	 *
	 * @param array $order_meta order meta to copy
	 * @return array
	 */
	public function subscriptions_remove_renewal_order_meta( $order_meta ) {

		$meta_keys = $this->subscriptions_get_order_meta_keys();

		foreach ( $order_meta as $index => $meta ) {

			if ( in_array( $meta['meta_key'], $meta_keys, false ) ) {
				unset( $order_meta[ $index ] );
			}
		}

		return $order_meta;
	}


	/**
	 * Removes the FreshBooks meta when creating a subscription object from an order at checkout.
	 *
	 * Subscriptions aren't true orders so they shouldn't have a FreshBooks invoice.
	 *
	 * @internal
	 *
	 * @since 3.5.1
	 *
	 * @param array $order_meta meta on order
	 * @param \WC_Subscription $to_order order meta is being copied to
	 * @param \WC_Order $from_order order meta is being copied from
	 * @return array
	 */
	public function subscriptions_remove_subscription_order_meta( $order_meta, $to_order, $from_order ) {

		// only when copying from an order to a subscription
		if ( $to_order instanceof WC_Subscription && $from_order instanceof WC_Order ) {

			$meta_keys = $this->subscriptions_get_order_meta_keys();

			foreach ( $order_meta as $index => $meta ) {

				if ( in_array( $meta['meta_key'], $meta_keys, false ) ) {
					unset( $order_meta[ $index ] );
				}
			}
		}

		return $order_meta;
	}


	/**
	 * Handles WooCommerce Subscriptions 1.x => 2.x updates.
	 *
	 * @internal
	 *
	 * @since 3.5.1
	 *
	 * @param array $order_meta meta to copy
	 * @return array
	 */
	public function subscriptions_remove_subscription_order_meta_during_upgrade( $order_meta ) {

		foreach ( $this->subscriptions_get_order_meta_keys() as $meta_key ) {

			if ( isset( $order_meta[ $meta_key ] ) ) {
				unset( $order_meta[ $meta_key ] );
			}
		}

		return $order_meta;
	}


	/**
	 * Gets an array of meta keys used by FreshBooks in Subscription objects.
	 *
	 * @since 3.5.1
	 *
	 * @return array
	 */
	protected function subscriptions_get_order_meta_keys() {

		return array(
			'_wc_freshbooks_invoice_id',
			'_wc_freshbooks_client_id',
			'_wc_freshbooks_payment_id',
			'_wc_freshbooks_invoice_status',
			'_wc_freshbooks_invoice',
		);
	}


	/**
	 * Gets the default FreshBooks payment type settings.
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	public function get_default_fb_payment_type_mapping() {

		$defaults = array(
			// Bank transfer gateways.
			'authorize_net_aim_echeck'       => 'Bank Transfer',
			'authorize_net_cim_echeck'       => 'Bank Transfer',
			'bacs'                           => 'Bank Transfer',
			'cybersource_sa_sop_echeck'      => 'Bank Transfer',
			'dwolla'                         => 'Bank Transfer',
			'netbilling_echeck'              => 'Bank Transfer',

			// Cash gateways.
			'cod'                            => 'Cash',

			// Check gateways.
			'cheque'                         => 'Check',

			// Credit card gateways.
			'authorize_net_aim'              => 'Credit Card',
			'authorize_net_cim_credit_card'  => 'Credit Card',
			'beanstream'                     => 'Credit Card',
			'braintree_credit_card'          => 'Credit Card',
			'chase_paymentech'               => 'Credit Card',
			'cybersource'                    => 'Credit Card',
			'cybersource_sa_sop_credit_card' => 'Credit Card',
			'elavon_vm'                      => 'Credit Card',
			'firstdata'                      => 'Credit Card',
			'intuit_qbms'                    => 'Credit Card',
			'moneris'                        => 'Credit Card',
			'netbilling'                     => 'Credit Card',
			'realex'                         => 'Credit Card',
			'realex_redirect'                => 'Credit Card',
			'securenet'                      => 'Credit Card',
			'simplify_commerce'              => 'Credit Card',
			'usaepay'                        => 'Credit Card',
			'wepay'                          => 'Credit Card',

			// PayPal gateways.
			'paypal'                         => 'PayPal',
			'braintree_paypal'               => 'PayPal',
			'paypal_express'                 => 'PayPal',
		);

		return $defaults;
	}


	/**
	 * Gets the current FreshBooks payment type settings.
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	public function get_fb_payment_type_mapping() {

		// Merge current mapping into default mapping.
		$mapping = array_merge(
			$this->get_default_fb_payment_type_mapping(),
			\WC_Admin_Settings::get_option( 'wc_freshbooks_payment_type_mapping', array() )
		);

		// Remove mapping settings for gateways that aren't installed.
		$mapping = array_intersect_key(
			$mapping,
			WC()->payment_gateways()->payment_gateways()
		);

		return $mapping;
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 3.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce FreshBooks', 'woocommerce-freshbooks' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 3.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin configuration URL.
	 *
	 * @since 3.0
	 *
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=freshbooks' );
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 3.5.0
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-freshbooks/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 3.12.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-freshbooks/';
	}


	/**
	 * Returns the main FreshBooks instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 3.3.0
	 *
	 * @return \WC_FreshBooks
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}


/**
 * Returns the One True Instance of FreshBooks.
 *
 * @since 3.3.0
 *
 * @return \WC_FreshBooks
 */
function wc_freshbooks() {

	return \WC_FreshBooks::instance();
}
