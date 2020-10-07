<?php
/**
 * Plugin Name: WooCommerce Bookings
 * Plugin URI: https://woocommerce.com/products/woocommerce-bookings/
 * Description: Setup bookable products such as for reservations, services and hires.
 * Version: 1.15.29
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-bookings
 * Domain Path: /languages
 * Tested up to: 5.5
 * WC tested up to: 4.4
 * WC requires at least: 2.6
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 390890:911c438934af094c2b38d5560b9f50f3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Files.FileName

/**
 * WooCommerce fallback notice.
 *
 * @since 1.13.0
 */
function woocommerce_bookings_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Bookings requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-bookings' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! defined( 'WC_BOOKINGS_ABSPATH' ) ) {
	define( 'WC_BOOKINGS_ABSPATH', dirname( __FILE__ ) . '/' );
}
// Action scheduler must be included before 'plugins_loaded' priority 0, the recommended way to included here as soon as the plugin file is loaded. https://actionscheduler.org/usage/.
$action_scheduler_exists = include WC_BOOKINGS_ABSPATH . 'vendor/woocommerce/action-scheduler/action-scheduler.php';

if ( false === $action_scheduler_exists ) {
	throw new Exception( 'vendor/woocommerce/action-scheduler/action-scheduler.php missing please run `composer install`' );
}

/**
 * Option key name for triggering activation notices.
 *
 * @since 1.14.4
 */
if ( ! defined( 'WC_BOOKINGS_ACTIVATION_NOTICE_KEY' ) ) {
	define( 'WC_BOOKINGS_ACTIVATION_NOTICE_KEY', 'woocommerce_bookings_show_activation_notice' );
}

register_activation_hook( __FILE__, 'woocommerce_bookings_activate' );

/**
 * Activation hook.
 */
function woocommerce_bookings_activate() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_bookings_missing_wc_notice' );
		return;
	}

	// Flag to trigger activation notice after includes have been loaded.
	update_option( WC_BOOKINGS_ACTIVATION_NOTICE_KEY, true );

	// Register the rewrite endpoint before permalinks are flushed.
	add_rewrite_endpoint( apply_filters( 'woocommerce_bookings_account_endpoint', 'bookings' ), EP_PAGES );

	// Flush Permalinks.
	flush_rewrite_rules();
}


if ( ! class_exists( 'WC_Bookings' ) ) :

	define( 'WC_BOOKINGS_VERSION', '1.15.29' ); // WRCS: DEFINED_VERSION.
	define( 'WC_BOOKINGS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
	define( 'WC_BOOKINGS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
	define( 'WC_BOOKINGS_MAIN_FILE', __FILE__ );
	define( 'WC_BOOKINGS_GUTENBERG_EXISTS', function_exists( 'register_block_type' ) ? true : false );
	if ( ! defined( 'WC_BOOKINGS_CONNECT_WOOCOMMERCE_URL' ) ) {
		define( 'WC_BOOKINGS_CONNECT_WOOCOMMERCE_URL', 'https://connect.woocommerce.com' );
	}

	if ( ! defined( 'WC_BOOKINGS_DEBUG' ) ) {
		define( 'WC_BOOKINGS_DEBUG', false );
	}

	/**
	 * WC Bookings class
	 */
	class WC_Bookings {
		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 1.13.0
		 */
		protected static $_instance = null;

		/**
		 * Constructor.
		 *
		 * @since 1.13.0
		 */
		public function __construct() {
			$this->includes();
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

			// Do migrations.
			WC_Bookings_Install::init();
			$this->wpdb_table_fix();
			$this->init();

			/*
			 * Show activation notice.
			 *
			 * Large priority ensures this occurs after WooCommerce Admin has loaded.
			 */
			add_action( 'plugins_loaded', array( $this, 'show_activation_notice' ), 100 );
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @access public
		 * @param  mixed $links Plugin Row Meta.
		 * @param  mixed $file  Plugin Base file.
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( plugin_basename( WC_BOOKINGS_MAIN_FILE ) === $file ) {
				$row_meta = array(
					'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_bookings_docs_url', 'https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-extensions/woocommerce-bookings/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-bookings' ) ) . '">' . __( 'Docs', 'woocommerce-bookings' ) . '</a>',
					'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_bookings_support_url', 'https://woocommerce.com/my-account/tickets/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support', 'woocommerce-bookings' ) ) . '">' . __( 'Premium Support', 'woocommerce-bookings' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * Main Bookings Instance.
		 *
		 * Ensures only one instance of Bookings is loaded or can be loaded.
		 *
		 * @since 1.13.0
		 * @return WC_Bookings
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.13.0
		 */
		public function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce-bookings' ), WC_BOOKINGS_VERSION );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.13.0
		 */
		public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-bookings' ), WC_BOOKINGS_VERSION );
		}

		/**
		 * Cleanup on plugin deactivation.
		 *
		 * @since 1.11
		 */
		public function deactivate() {
			if ( class_exists( 'WC_Admin_Notes' ) ) {
				WC_Bookings_Inbox_Notice::remove_activity_panel_inbox_notes();
			} else {
				WC_Admin_Notices::remove_notice( 'woocommerce_bookings_activation' );
			}
		}

		/**
		 * Load Classes.
		 *
		 * @throws Exception When composer install hasn't been ran.
		 */
		public function includes() {
			$loader = include_once WC_BOOKINGS_ABSPATH . 'vendor/autoload.php';

			if ( ! $loader ) {
				throw new Exception( 'vendor/autoload.php missing please run `composer install`' );
			}

			require_once WC_BOOKINGS_ABSPATH . 'includes/wc-bookings-functions.php';
		}

		/**
		 * Init all the classes.
		 */
		private function init() {
			// Cache.
			new WC_Bookings_Cache();

			// Initialize.
			new WC_Bookings_Init();
			WC_Bookings_Timezone_Settings::instance();

			// WC AJAX.
			new WC_Bookings_WC_Ajax();

			WC_Booking_Form_Handler::init();
			new WC_Booking_Order_Manager();
			new WC_Product_Booking_Manager();
			new WC_Booking_Cron_Manager();
			WC_Bookings_Google_Calendar_Connection::instance();
			new WC_Booking_Coupon();

			if ( class_exists( 'WC_Product_Addons' ) ) {
				new WC_Bookings_Addons();
			}
			if ( class_exists( 'WC_Deposits' ) ) {
				new WC_Bookings_Deposits();
			}

			if ( class_exists( 'WC_Abstract_Privacy' ) ) {
				new WC_Booking_Privacy();
			}

			new WC_Booking_Email_Manager();
			new WC_Booking_Cart_Manager();
			new WC_Booking_Checkout_Manager();
			new WC_Bookings_REST_API();

			if ( is_admin() ) {
				new WC_Bookings_Menus();
				new WC_Bookings_Report_Dashboard();
				new WC_Bookings_Admin();
				new WC_Bookings_Ajax();
				new WC_Bookings_Admin_Add_Ons();
				new WC_Booking_Products_Export();
				new WC_Booking_Products_Import();
				new WC_Bookings_Tracks();
			}
		}

		/**
		 * Need to correct table names for meta to work.
		 */
		public function wpdb_table_fix() {
			global $wpdb;
			$wpdb->bookings_availabilitymeta = $wpdb->prefix . 'wc_bookings_availabilitymeta';
			$wpdb->tables[]                  = 'wc_bookings_availabilitymeta';
		}

		/**
		 * Shows admin notice after activation.
		 *
		 * Notices are triggered by a flag in options so they can be triggered once on activation
		 * and then actually shown once all necessary resources have been loaded.
		 *
		 * @since 1.14.4
		 */
		public function show_activation_notice() {
			if ( false !== get_option( WC_BOOKINGS_ACTIVATION_NOTICE_KEY ) ) {
				delete_option( WC_BOOKINGS_ACTIVATION_NOTICE_KEY );
				if ( class_exists( 'WC_Admin_Notes' ) ) {
					WC_Bookings_Inbox_Notice::add_activity_panel_inbox_welcome_note();
				} else {
					$notice_html = '<strong>' . esc_html__( 'Bookings has been activated!', 'woocommerce-bookings' ) . '</strong><br><br>';
					/* translators: 1: href link to list of bookings, 2: booking post type link */
					$notice_html .= sprintf( __( '<a href="%1$s">Add or edit a product</a> to manage bookings in the Product Data section for individual products and then go to the <a href="%2$s" target="_blank">Bookings page</a> to manage them individually.', 'woocommerce-bookings' ), admin_url( 'post-new.php?post_type=product&bookable_product=1' ), admin_url( 'edit.php?post_type=wc_booking' ) );
					WC_Admin_Notices::add_custom_notice( 'woocommerce_bookings_activation', $notice_html );
				}
			}
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_bookings_init', 10 );

function woocommerce_bookings_init() {
	load_plugin_textdomain( 'woocommerce-bookings', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_bookings_missing_wc_notice' );
		return;
	}

	$GLOBALS['wc_bookings'] = WC_Bookings::instance();
}
