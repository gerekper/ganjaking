<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP' ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page
	 *
	 * @since 1.0.0
	 */
	final class YITH_WCMAP {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP
		 */
		protected static $instance;

		/**
		 * Items class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Items
		 */
		public $items = null;

		/**
		 * Banners class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Banners
		 */
		public $banners = null;

		/**
		 * Admin class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Admin
		 */
		public $admin = null;

		/**
		 * Frontend class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Frontend
		 */
		public $frontend = null;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCMAP
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
		}

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		private function __construct() {

			$this->load_classes();

			// Load Plugin Framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			// Email register.
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
		}

		/**
		 * Load required classes
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		protected function load_classes() {

			include_once 'class.yith-wcmap-avatar.php';
			include_once 'class.yith-wcmap-banners.php';
			$this->banners = new YITH_WCMAP_Banners();

			include_once 'class.yith-wcmap-items.php';
			$this->items = new YITH_WCMAP_Items();

			// Class admin.
			if ( $this->is_admin() ) {
				include_once 'admin/functions.yith-wcmap-admin.php';
				include_once 'admin/class.yith-wcmap-admin.php';
				$this->admin = new YITH_WCMAP_Admin();
			} else { // Class frontend.
				include_once 'class.yith-wcmap-frontend.php';
				$this->frontend = new YITH_WCMAP_Frontend();
			}

			// Load compatibilities.
			add_action( 'init', array( $this, 'load_compatibilities' ), 10 );
		}

		/**
		 * Check if is admin or not and load the correct class
		 *
		 * @since  1.1.2
		 * @author Francesco Licandro
		 * @return bool
		 */
		public function is_admin() {
			$check_ajax    = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$check_context = isset( $_REQUEST['context'] ) && 'frontend' === sanitize_text_field( wp_unslash( $_REQUEST['context'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$is_admin      = is_admin() && ! ( $check_ajax && $check_context );

			return apply_filters( 'yith_wcmap_is_admin_request', $is_admin );
		}

		/**
		 * Load compatibility classes
		 *
		 * @access public
		 * @since  2.3
		 * @author Francesco Licandro
		 */
		public function load_compatibilities() {

			$plugins = array_filter( $this->get_compatible_plugins() );
			if ( empty( $plugins ) ) {
				return;
			}

			// Load abstract class.
			include_once YITH_WCMAP_DIR . 'includes/compatibilities/abstract.yith-wcmap-compatibility.php';

			foreach ( array_keys( $plugins ) as $plugin ) {
				$file = 'class.yith-wcmap-' . str_replace( '_', '-', $plugin ) . '-compatibility.php';
				if ( file_exists( YITH_WCMAP_DIR . 'includes/compatibilities/' . $file ) ) {
					include_once YITH_WCMAP_DIR . 'includes/compatibilities/' . $file;
				}
			}
		}

		/**
		 * Return an array of compatible plugins
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return array
		 */
		protected function get_compatible_plugins() {
			$plugins = array(
				'wishlist'         => defined( 'YITH_WCWL' ) && YITH_WCWL,
				'one-click'        => defined( 'YITH_WOCC_PREMIUM' ) && YITH_WOCC_PREMIUM,
				'waiting-list'     => defined( 'YITH_WCWTL_PREMIUM' ) && YITH_WCWTL_PREMIUM,
				'request-quote'    => defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM,
				'membership'       => defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM,
				'subscriptions'    => defined( 'YITH_YWSBS_PREMIUM' ) && YITH_YWSBS_PREMIUM,
				'gift-cards'       => defined( 'YITH_YWGC_PREMIUM' ) && YITH_YWGC_PREMIUM,
				'payouts'          => defined( 'YITH_PAYOUTS_PREMIUM' ) && YITH_PAYOUTS_PREMIUM,
				'stripe-connect'   => defined( 'YITH_WCSC_PREMIUM' ) && YITH_WCSC_PREMIUM,
				'refund-requests'  => defined( 'YITH_WCARS_PREMIUM' ) && YITH_WCARS_PREMIUM,
				'bookings'         => defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM,
				'funds'            => defined( 'YITH_FUNDS_PREMIUM' ) && YITH_FUNDS_PREMIUM,
				'points'           => defined( 'YITH_YWPAR_PREMIUM' ) && YITH_YWPAR_PREMIUM,
				'auctions'         => defined( 'YITH_WCACT_PREMIUM' ) && YITH_WCACT_PREMIUM,
				'advanced-reviews' => defined( 'YITH_YWAR_PREMIUM' ) && YITH_YWAR_PREMIUM,
				'wt-smart-coupon'  => class_exists( 'WT_MyAccount_SmartCoupon' ),
				'tinv-wishlist'    => class_exists( 'TInvWL' ) && shortcode_exists( 'ti_wishlistsview' ),
				'wc-membership'    => class_exists( 'WC_Memberships' ),
				'wc-subscriptions' => class_exists( 'WC_Subscriptions' ),
				'wc-api-manager'   => class_exists( 'WooCommerce_API_Manager' ),
			);

			return apply_filters( 'yith_wcmap_get_plugins_endpoints_array', $plugins );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Filters woocommerce available mails, to add plugin related ones
		 *
		 * @since  2.5.0
		 * @author Francesco Licandro
		 * @param array $emails An array of registered emails.
		 * @return array
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YITH_WCMAP_Verify_Account'] = include YITH_WCMAP_DIR . 'includes/email/class.yith-wcmap-verify-account.php';
			return $emails;
		}


		/**
		 * Register plugins for activation tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCMAP_DIR . 'plugin-fw/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCMAP_INIT, YITH_WCMAP_SECRET_KEY, YITH_WCMAP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCMAP_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCMAP_SLUG, YITH_WCMAP_INIT );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMAP class
 *
 * @since 1.0.0
 * @return YITH_WCMAP
 */
function YITH_WCMAP() { // phpcs:ignore
	return YITH_WCMAP::get_instance();
}
