<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointments;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Frontend;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * WooCommerce Local Pickup Plus main class.
 *
 * @since 1.0.0
 *
 * @method \WC_Local_Pickup_Plus_Lifecycle get_lifecycle_handler()
 */
class WC_Local_Pickup_Plus extends Framework\SV_WC_Plugin {


	/** @var string plugin version */
	const VERSION = '2.11.0';

	/** shipping method ID */
	const SHIPPING_METHOD_ID = 'local_pickup_plus';

	/** shipping method class name */
	const SHIPPING_METHOD_CLASS_NAME = 'WC_Shipping_Local_Pickup_Plus';

	/** @var \WC_Local_Pickup_Plus single instance of this plugin */
	protected static $instance;

	/** @var bool whether the shipping method has been loaded while doing AJAX */
	private static $ajax_loaded = false;

	/** @var \WC_Local_Pickup_Plus_Pickup_Locations pickup locations handler instance */
	private $pickup_locations;

	/** @var Appointments appointments handler instance */
	private $appointments;

	/** @var string|\WC_Shipping_Local_Pickup_Plus Local pickup plus shipping class name or object */
	private $shipping_method;

	/** @var \WC_Local_Pickup_Plus_Geocoding_API geocoding API handler instance */
	private $geocoding;

	/** @var \WC_Local_Pickup_Plus_Geolocation geolocation handler instance */
	private $geolocation;

	/** @var \WC_Local_Pickup_Plus_Products products handler instance */
	private $products;

	/** @var \WC_Local_Pickup_Plus_Orders orders handler instance */
	private $orders;

	/** @var \WC_Local_Pickup_Plus_Packages packages handler instance */
	private $packages;

	/** @var \WC_Local_Pickup_Plus_Admin admin instance */
	private $admin;

	/** @var Frontend frontend instance */
	private $frontend;

	/** @var \WC_Local_Pickup_Plus_Ajax AJAX instance */
	private $ajax;

	/** @var \WC_Local_Pickup_Plus_Session session handler instance */
	private $session;

	/** @var \WC_Local_Pickup_Plus_Integrations integrations instance */
	private $integrations;

	/** @var bool whether geocoding features are enabled */
	private $geocoding_enabled;

	/** @var bool whether logging is enabled */
	private $logging_enabled;

	/** @var bool whether custom tables have been set */
	private $tables_exist = false;


	/**
	 * Sets up the main plugin class.
	 *
	 * @since 1.4
	 */
	public function __construct() {

		parent::__construct(
			self::SHIPPING_METHOD_ID,
			self::VERSION,
			[
				/** @see \WC_Shipping_Local_Pickup_Plus::load_textdomain() also */
				'text_domain'   => 'woocommerce-shipping-local-pickup-plus',
				'supports_hpos' => true,
			]
		);

		$this->shipping_method = self::SHIPPING_METHOD_CLASS_NAME;

		// add class to WooCommerce Shipping Methods
		add_filter( 'woocommerce_shipping_methods',       array( $this, 'add_shipping_method' ) );
		// make sure one instance of the Shipping class is set
		add_action( 'wc_shipping_local_pickup_plus_init', array( $this, 'set_shipping_method' ) );
	}


	/**
	 * Loads plugin classes.
	 *
	 * @since 2.0.0
	 */
	private function includes() {

		$plugin_path = $this->get_plugin_path();

		// load helper functions
		require_once( $plugin_path . '/src/functions/wc-local-pickup-plus-functions.php' );

		// static class for custom post types handling
		require_once( $plugin_path . '/src/class-wc-local-pickup-plus-post-types.php' );

		// include the Shipping method class
		require_once( $plugin_path . '/src/class-wc-shipping-local-pickup-plus.php' );

		// geocoding API handler
		$this->geocoding        = $this->load_class( '/src/api/class-wc-local-pickup-plus-geocoding-api.php', 'WC_Local_Pickup_Plus_Geocoding_API' );
		// geolocation handler
		$this->geolocation      = $this->load_class( '/src/class-wc-local-pickup-plus-geolocation.php', 'WC_Local_Pickup_Plus_Geolocation' );
		// init session handler
		$this->session          = $this->load_class( '/src/class-wc-local-pickup-plus-session.php', 'WC_Local_Pickup_Plus_Session' );
		// products handler
		$this->products         = $this->load_class( '/src/class-wc-local-pickup-plus-products.php', 'WC_Local_Pickup_Plus_Products' );
		// orders handler
		$this->orders           = $this->load_class( '/src/class-wc-local-pickup-plus-orders.php', 'WC_Local_Pickup_Plus_Orders' );
		// packages handler
		$this->packages         = $this->load_class( '/src/class-wc-local-pickup-plus-packages.php', 'WC_Local_Pickup_Plus_Packages' );
		// init pickup locations
		$this->pickup_locations = $this->load_class( '/src/class-wc-local-pickup-plus-pickup-locations.php', 'WC_Local_Pickup_Plus_Pickup_Locations' );
		// init appointments handler instance
		$this->appointments     = $this->load_class( '/src/Appointments/Appointments.php', Appointments::class ); // phpcs:ignore

		require_once( $plugin_path . '/src/Appointments/Timezones.php' );
		require_once( $plugin_path . '/src/Appointments/Appointment.php' );

		// init UI handlers
		if ( is_admin() ) {
			// admin side
			$this->admin    = $this->load_class( '/src/admin/class-wc-local-pickup-plus-admin.php', 'WC_Local_Pickup_Plus_Admin' );
		} else {
			// frontend side
			$this->frontend = $this->load_class( '/src/frontend/Frontend.php', 'SkyVerge\WooCommerce\Local_Pickup_Plus\Frontend' );
		}

		// load ajax methods
		if ( wp_doing_ajax() ) {
			$this->ajax = $this->load_class( '/src/class-wc-local-pickup-plus-ajax.php', 'WC_Local_Pickup_Plus_Ajax' );
		}

		// init integrations classes
		$this->integrations = $this->load_class( '/src/integrations/class-wc-local-pickup-plus-integrations.php', 'WC_Local_Pickup_Plus_Integrations' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function init_plugin() {

		// static class for custom post types handling
		require_once( $this->get_plugin_path() . '/src/class-wc-local-pickup-plus-post-types.php' );

		\WC_Local_Pickup_Plus_Post_Types::init();

		$this->includes();

		// loads the local pickup plus class from the 'woocommerce_update_shipping_method' AJAX action early, which otherwise would not be loaded in time to update
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( ( isset( $_REQUEST['wc-ajax'] ) && 'update_order_review' === $_REQUEST['wc-ajax'] ) || ( isset( $_REQUEST['action' ] ) && 'woocommerce_update_shipping_method' === $_REQUEST['action'] ) ) ) {

			if ( false === self::$ajax_loaded ) {

				$this->load_shipping_method();

				self::$ajax_loaded = true;
			}
		}
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 2.4.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/class-wc-local-pickup-plus-lifecycle.php' );

		$this->lifecycle_handler = new \WC_Local_Pickup_Plus_Lifecycle( $this );
	}


	/**
	 * Adds the Shipping Method to WooCommerce.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[]|\WC_Shipping_Method[] $methods array of hipping method class names or objects
	 * @return string[]|\WC_Shipping_Method[]
	 */
	public function add_shipping_method( $methods ) {

		if ( ! array_key_exists( self::SHIPPING_METHOD_ID, $methods ) ) {

			// Since the shipping method is always constructed, we'll pass it in to the register filter so it doesn't have to be re-instantiated;
			// so, the following will be either the class name, or the class object if we've already instantiated it.
			$methods[ self::SHIPPING_METHOD_ID ] = $this->get_shipping_method_instance();
		}

		return $methods;
	}


	/**
	 * Sets the Local Pickup Plus shipping method.
	 *
	 * In this way, if shipping methods are loaded more than once during a request,
	 * we can avoid instantiating the class a second time and duplicating action hooks.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Shipping_Local_Pickup_Plus $local_pickup_plus Local Pickup Plus shipping class
	 */
	public function set_shipping_method( \WC_Shipping_Local_Pickup_Plus $local_pickup_plus ) {

		if ( ! $this->shipping_method instanceof \WC_Shipping_Local_Pickup_Plus ) {

			$this->shipping_method = $local_pickup_plus;
		}
	}


	/**
	 * Ensures the shipping method class is loaded.
	 *
	 * @since 2.0.0
	 */
	public function load_shipping_method() {

		$this->get_shipping_method_instance();
	}


	/**
	 * Gets the Local Pickup Plus shipping method main instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Shipping_Local_Pickup_Plus Local pickup plus shipping method
	 */
	public function get_shipping_method_instance() {

		if ( ! $this->shipping_method instanceof \WC_Shipping_Local_Pickup_Plus ) {

			if ( ! class_exists( 'WC_Shipping_Local_Pickup_Plus' ) ) {
				require_once( $this->get_plugin_path() . '/src/class-wc-shipping-local-pickup-plus.php' );
			}

			$this->shipping_method = new \WC_Shipping_Local_Pickup_Plus();
		}

		return $this->shipping_method;
	}


	/**
	 * Gets the pickup locations handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Locations
	 */
	public function get_pickup_locations_instance() {

		return $this->pickup_locations;
	}


	/**
	 * Gets the appointments handler instance.
	 *
	 * @since 2.7.0
	 *
	 * @return Appointments
	 */
	public function get_appointments_instance() {

		return $this->appointments;
	}


	/**
	 * Gets the geocoding API instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Geocoding_API
	 */
	public function get_geocoding_api_instance() {

		return $this->geocoding;
	}


	/**
	 * Gets the geolocation instance.
	 *
	 * @since 2.1.1
	 *
	 * @return \WC_Local_Pickup_Plus_Geolocation
	 */
	public function get_geolocation_instance() {

		return $this->geolocation;
	}


	/**
	 * Gets the session handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Session
	 */
	public function get_session_instance() {

		return $this->session;
	}


	/**
	 * Gets the products handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Products
	 */
	public function get_products_instance() {

		return $this->products;
	}


	/**
	 * Gets the orders handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Orders
	 */
	public function get_orders_instance() {

		return $this->orders;
	}


	/**
	 * Gets the packages handler instance.
	 *
	 * @since 2.3.1
	 *
	 * @return \WC_Local_Pickup_Plus_Packages
	 */
	public function get_packages_instance() {

		return $this->packages;
	}


	/**
	 * Gets the admin instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Gets the frontend instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Gets the ajax instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Ajax
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Gets the integrations instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Gets the main Local Pickup Plus instance.
	 *
	 * Ensures only one instance loaded at one time.
	 *
	 * @see \wc_local_pickup_plus()
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Local_Pickup_Plus
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 1.5
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.5
	 *
	 * @return string the full path and filename of the plugin file
	 */
	public function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * For Local Pickup Plus is non-standard.
	 *
	 * @since 1.5
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/local-pickup-plus/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/local-pickup-plus/';
	}


	/**
	 * Gets the shipping method configuration URL.
	 *
	 * @since 1.5
	 *
	 * @param string $plugin_id the plugin identifier
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . strtolower( self::SHIPPING_METHOD_ID ) );
	}


	/**
	 * Returns true if on the shipping method settings page.
	 *
	 * @since 1.5
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'], $_GET['section'] )
		       && $_GET['page']    === 'wc-settings'
		       && $_GET['tab']     === 'shipping'
		       && $_GET['section'] === strtolower( self::SHIPPING_METHOD_ID );
	}


	/**
	 * Checks whether the plugin is using geocoding services for matching locations.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function geocoding_enabled() {

		if ( ! is_bool( $this->geocoding_enabled ) && ( $shipping_method = $this->get_shipping_method_instance() ) ) {

			$has_api_key = $shipping_method && $shipping_method->get_google_maps_api_key();

			/**
			 * Switch whether using geocoding features.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $use_geocoding whether to use geocoding features (true) or not (false)
			 */
			$this->geocoding_enabled = (bool) apply_filters( 'wc_local_pickup_plus_geocoding_enabled', ! empty( $has_api_key ) );
		}

		return $this->geocoding_enabled;
	}


	/**
	 * Checks if logging is enabled in settings.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function logging_enabled() {

		if ( ! is_bool( $this->logging_enabled ) && ( $shipping_method = $this->get_shipping_method_instance() ) ) {
			$this->logging_enabled = 'yes' === $shipping_method->get_option( 'enable_logging', 'no' );
		}

		return $this->logging_enabled;
	}


	/**
	 * Don't log API requests/responses when doing geocoding API calls when logging is disabled.
	 * @see \WC_Local_Pickup_Plus_Geocoding_API::get_coordinates()
	 *
	 * Overrides framework parent method:
	 * @see Framework\SV_WC_Plugin::add_api_request_logging()
	 * @see Framework\SV_WC_API_Base::broadcast_request()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_api_request_logging() {

		if ( has_action( 'wc_' . $this->get_id() . '_api_request_performed' ) ) {

			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10 );
		}
	}


	/**
	 * Checks that pickup locations custom tables exist otherwise create them.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $create whether to create tables if they do not exist
	 * @return bool whether tables did exist or not
	 */
	public function check_tables( $create = true ) {
		global $wpdb;

		if ( ! $this->tables_exist ) {

			$lifecycle_handler = $this->get_lifecycle_handler();

			foreach ( $lifecycle_handler->get_table_names() as $table_name ) {

				if ( $table_name !== $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) {

					if ( true === $create ) {

						$lifecycle_handler->create_tables();

						$this->tables_exist = true;
					}

					break;
				}
			}
		}

		return $this->tables_exist;
	}


	/**
	 * Checks if Local Pickup Plus is the only available shipping option.
	 *
	 * @since 2.9.3
	 *
	 * @return bool true if Local Pickup Plus is enabled and is the only available shipping option
	 */
	public function is_the_only_available_shipping_method() {

		if ( ! wc_local_pickup_plus_shipping_method()->is_enabled() ) {

			return false;
		}

		foreach ( \WC_Shipping_Zones::get_zones() as $zone ) {

			foreach ( $zone[ 'shipping_methods' ] as $shipping_method ) {

				// a single zone with a shipping method is enough to break and return false
				if ( method_exists( $shipping_method, 'is_enabled' ) && $shipping_method->is_enabled() ) {

					return false;
				}
			}
		}

		return true;
	}


	/** Admin methods ******************************************************/


	/**
	 * Renders a notice if WC shipping is disabled.
	 *
	 * @since 2.4.3
	 *
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		if ( wc_local_pickup_plus_shipping_method()->is_enabled() && ! wc_shipping_enabled() ) {

			$this->get_admin_notice_handler()->add_admin_notice( sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, , %3$s - <a> tag, %4$s - </a> tag */
				__( 'Uh oh! It looks like %1$sshipping is disabled%2$s for your store, so Local Pickup Plus is unavailable. Please %3$senable shipping%4$s to enable store pickup.', 'woocommerce-shipping-local-pickup-plus' ),
				'<strong>', '</strong>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings' ) ) . '">', '</a>'
			), 'wc-shipping-disabled-notice', [
				'notice_class' => 'error',
			] );
		}
	}


}


/**
 * Returns the One True Instance of Local Pickup Plus.
 *
 * @since 2.0.0
 *
 * @return \WC_Local_Pickup_Plus
 */
function wc_local_pickup_plus() {

	return \WC_Local_Pickup_Plus::instance();
}
