<?php
/**
 * Plugin Name: WooCommerce Account Funds
 * Plugin URI: https://woocommerce.com/products/account-funds/
 * Description: Allow customers to deposit funds into their accounts and pay with account funds during checkout.
 * Version: 2.9.1
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.2
 * Text Domain: woocommerce-account-funds
 * Domain Path: /languages/
 *
 * WC requires at least: 3.7
 * WC tested up to: 7.6
 * Woo: 18728:a6fcf35d3297c328078dfe822e00bd06
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Account_Funds
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once dirname( __FILE__ ) . '/woo-includes/woo-functions.php';
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'a6fcf35d3297c328078dfe822e00bd06', '18728' );

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_Account_Funds_Requirements', false ) ) {
	require_once dirname( __FILE__ ) . '/includes/class-wc-account-funds-requirements.php';
}

if ( ! WC_Account_Funds_Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_ACCOUNT_FUNDS_FILE' ) ) {
	define( 'WC_ACCOUNT_FUNDS_FILE', __FILE__ );
}

/**
 * WC_Account_Funds
 */
class WC_Account_Funds {

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $version = '2.9.1';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 2.2.0
	 */
	private function define_constants() {
		$this->define( 'WC_ACCOUNT_FUNDS_VERSION', $this->version );
		$this->define( 'WC_ACCOUNT_FUNDS_PATH', plugin_dir_path( WC_ACCOUNT_FUNDS_FILE ) );
		$this->define( 'WC_ACCOUNT_FUNDS_URL', plugin_dir_url( WC_ACCOUNT_FUNDS_FILE ) );
		$this->define( 'WC_ACCOUNT_FUNDS_BASENAME', plugin_basename( WC_ACCOUNT_FUNDS_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 2.2.0
	 *
	 * @param string      $name  The constant name.
	 * @param string|bool $value The constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Includes the necessary files.
	 *
	 * @since 2.2.0
	 */
	private function includes() {
		/**
		 * Class autoloader.
		 */
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-autoloader.php';

		/**
		 * Interfaces.
		 */
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/interfaces/interface-wc-account-funds-integration.php';

		/**
		 * Core classes.
		 */
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/wc-account-funds-functions.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-order-query.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-installer.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-emails.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-my-account.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-integrations.php';

		if ( wc_account_funds_is_request( 'admin' ) ) {
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/admin/class-wc-account-funds-admin.php';
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/admin/class-wc-account-funds-admin-product.php';
		}

		if ( wc_account_funds_is_request( 'frontend' ) ) {
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-checkout.php';
			include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-register.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.2.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'gateway_init' ), 0 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ) );
		add_action( 'init', array( $this, 'init' ) );

		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_filter( 'woocommerce_data_stores', array( $this, 'add_data_stores' ) );

		register_activation_hook( WC_ACCOUNT_FUNDS_FILE, array( $this, 'activate' ) );
	}

	/**
	 * Init Gateway
	 */
	public function gateway_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-gateway-account-funds.php';
	}

	/**
	 * Load Widget
	 */
	public function widgets_init() {
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-widget.php';
	}

	/**
	 * Declares compatibility with the WC features.
	 *
	 * @since 2.7.3
	 */
	public function declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WC_ACCOUNT_FUNDS_FILE, true );
		}
	}

	/**
	 * Init plugin.
	 */
	public function init() {
		// TODO: Move these includes to the method 'WC_Account_Funds->includes()'.
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-product-deposit.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-product-topup.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-cart-manager.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-deposits-manager.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-order-manager.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-shortcodes.php';

		$this->admin_init();
		$this->load_plugin_textdomain();
	}

	/**
	 * Load admin
	 */
	public function admin_init() {
		if ( ! is_admin() ) {
			return;
		}

		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-reports.php';
		include_once WC_ACCOUNT_FUNDS_PATH . 'includes/class-wc-account-funds-privacy.php';
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-account-funds', false, dirname( WC_ACCOUNT_FUNDS_BASENAME ) . '/languages' );
	}

	/**
	 * Activation
	 */
	public function activate() {
		WC_Account_Funds_Installer::install();
		WC_Account_Funds_Installer::flush_rewrite_rules();
	}

	/**
	 * Get a users funds amount
	 *
	 * @param  int     $user_id
	 * @param  boolean $formatted
	 * @return string
	 */
	public static function get_account_funds( $user_id = null, $formatted = true, $exclude_order_id = 0 ) {
		$funds   = 0;
		$user_id = ( $user_id ? $user_id : get_current_user_id() );

		if ( $user_id ) {
			$funds = WC_Account_Funds_Manager::get_user_funds( $user_id );

			$orders_ids = wc_get_orders(
				array(
					'type'        => 'shop_order',
					'limit'       => -1,
					'return'      => 'ids',
					'customer_id' => $user_id,
					'funds_query'  => array(
						array(
							'key'   => '_funds_removed',
							'value' => '0',
						),
						array(
							'key'     => '_funds_used',
							'value'   => '0',
							'compare' => '>',
						),
					),
				)
			);

			foreach ( $orders_ids as $order_id ) {
				if ( $exclude_order_id === $order_id ) {
					continue;
				}

				if ( WC()->session && ! empty( WC()->session->order_awaiting_payment ) && $order_id == WC()->session->order_awaiting_payment ) {
					continue;
				}

				$order = wc_get_order( $order_id );
				$funds = $funds - floatval( $order->get_meta( '_funds_used', true ) );
			}
		}

		return ( $formatted ? wc_price( $funds ) : $funds );
	}

	/**
	 * Register the gateway for use
	 */
	public function register_gateway( $methods ) {
		$methods[] = 'WC_Gateway_Account_Funds';
		return $methods;
	}

	/**
	 * Add AF-related data stores.
	 *
	 * @since 2.1.3
	 *
	 * @version 2.1.3
	 *
	 * @param array $data_stores Data stores.
	 *
	 * @return array Data stores.
	 */
	public function add_data_stores( $data_stores ) {
		if ( ! class_exists( 'WC_Product_Topup_Data_Store' ) ) {
			require_once 'includes/class-wc-product-topup-data-store.php';
		}

		$data_stores['product-topup'] = 'WC_Product_Topup_Data_Store';

		return $data_stores;
	}

	/**
	 * Add funds to user account.
	 *
	 * @since 2.0.0
	 * @deprecated 2.7.0
	 *
	 * @param int   $customer_id Customer ID.
	 * @param float $amount      Amount of funds to add.
	 */
	public static function add_funds( $customer_id, $amount ) {
		wc_deprecated_function( __FUNCTION__, '2.7.0', 'WC_Account_Funds_Manager::increase_user_funds()' );
		WC_Account_Funds_Manager::increase_user_funds( $customer_id, $amount );
	}

	/**
	 * Remove funds from user account.
	 *
	 * @since 2.0.0
	 * @deprecated 2.7.0
	 *
	 * @param int   $customer_id Customer ID.
	 * @param float $amount      Amount of funds to remove.
	 */
	public static function remove_funds( $customer_id, $amount ) {
		wc_deprecated_function( __FUNCTION__, '2.7.0', 'WC_Account_Funds_Manager::decrease_user_funds()' );
		WC_Account_Funds_Manager::decrease_user_funds( $customer_id, $amount );
	}

	/**
	 * Classes that need to be loaded early.
	 *
	 * @since 2.0.12
	 * @deprecated 2.2.0
	 */
	public function init_early() {
		wc_deprecated_function( __FUNCTION__, '2.2', 'WC_Account_Funds->includes()' );
	}

	/**
	 * Add custom action links on the plugin screen.
	 *
	 * @since 2.0.0
	 * @deprecated 2.2.0
	 *
	 * @param mixed $actions Plugin Actions Links.
	 * @return array
	 */
	public function plugin_action_links( $actions ) {
		wc_deprecated_function( __FUNCTION__, '2.2', 'WC_Account_Funds_Admin->action_links()' );

		return $actions;
	}

	/**
	 * Add scripts to checkout process
	 *
	 * @since 2.0.0
	 * @deprecated 2.2.0
	 */
	public function checkout_scripts() {
		wc_deprecated_function( __FUNCTION__, '2.2', 'WC_Account_Funds_Checkout->enqueue_scripts()' );
	}

	/**
	 * Perform version check. Update routine will be performed if current
	 * plugin's version doesn't match with installed version.
	 *
	 * @deprecated 2.3.7
	 */
	public function version_check() {
		wc_deprecated_function( __FUNCTION__, '2.3.7', 'WC_Account_Funds_Installer::check_version()' );
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @since 2.0.0
	 * @deprecated 2.8.0
	 *
	 * @param array $emails The email classes.
	 * @return array
	 */
	public function add_email_classes( $emails ) {
		wc_deprecated_function( __FUNCTION__, '2.8.0', 'WC_Account_Funds_Emails->email_classes()' );

		return $emails;
	}
}

new WC_Account_Funds();
