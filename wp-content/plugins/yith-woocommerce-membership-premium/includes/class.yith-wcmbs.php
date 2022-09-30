<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS' ) ) {
	/**
	 * YITH WooCommerce Membership
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMBS {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS
		 * @since 1.0.0
		 */
		protected static $_instance;

		public static $membership_post_type = 'ywcmbs-membership';

		/**
		 * @var YITH_WCMBS_WP_Compatibility
		 */
		public $wp;

		/**
		 * @var YITH_WCMBS_Endpoints
		 */
		public $endpoints;

		/**
		 * @var YITH_WCMBS_Builders
		 */
		public $builders;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS
		 * @since 1.0.0
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
		}

		/**
		 * Constructor
		 *
		 * @return YITH_WCMBS
		 * @since 1.0.0
		 */
		public function __construct() {
			YITH_WCMBS_Install::init();

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			add_action( 'yith_wcmbs_delete_transients', array( YITH_WCMBS_Manager(), 'delete_transients' ) );

			// Register Membership Post Type
			YITH_WCMBS_Post_Types::init();
			$this->wp = YITH_WCMBS_WP_Compatibility::get_instance();

			YITH_WCMBS_AJAX::get_instance();
			YITH_WCMBS_Products_Manager();
			YITH_WCMBS_Compatibility();
			YITH_WCMBS_Protected_Media();
			YITH_WCMBS_Cron();
			YITH_WCMBS_Reports();

			YITH_WCMBS_Protected_Links::get_instance();

			YITH_WCMBS_Shortcodes();
			YITH_WCMBS_Messages_Manager_Frontend();
			YITH_WCMBS_Notifier();

			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			// Set membership on user registration
			add_action( 'user_register', array( $this, 'apply_membership_on_user_registration' ), 10, 1 );

			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_membership_shipping_methods' ) );

			// Class admin
			if ( is_admin() ) {
				YITH_WCMBS_Admin();
			} else {
				YITH_WCMBS_Frontend();
			}

			YITH_WCMBS_Orders();

			$this->endpoints = YITH_WCMBS_Endpoints::get_instance();
			$this->builders  = YITH_WCMBS_Builders::get_instance();

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		public function load_privacy() {
			require_once YITH_WCMBS_INCLUDES_PATH . '/class.yith-wcmbs-privacy.php';
		}

		public function add_membership_shipping_methods( $methods ) {
			$methods['membership_free_shipping'] = 'WC_Shipping_Membership_Free_Shipping';
			$methods['membership_flat_rate']     = 'WC_Shipping_Membership_Flat_Rate';

			return $methods;
		}

		/**
		 * Set Membership on user registration
		 *
		 * @param $user_id
		 */
		public function apply_membership_on_user_registration( $user_id ) {

			$enabled  = yith_wcmbs_settings()->get_option( 'yith-wcmbs-memberships-on-user-register-enabled' );
			$plan_ids = yith_wcmbs_settings()->get_option( 'yith-wcmbs-memberships-on-user-register' );
			$enabled  = apply_filters( 'yith_wcmbs_apply_membership_on_user_register', $enabled, $user_id );

			if ( 'yes' === $enabled && $plan_ids && is_array( $plan_ids ) ) {
				$plan_ids = array_map( 'absint', $plan_ids );
				$member   = YITH_WCMBS_Members()->get_member( $user_id );

				foreach ( $plan_ids as $plan_id ) {
					$member->create_membership( $plan_id );
				}
			}
		}


		/**
		 * Load Plugin Framework
		 *
		 * @return void
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}


		/**
		 * register Widget for Messages
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function register_widgets() {
			register_widget( 'YITH_WCBSL_Messages_Widget' );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.4.0
		 */
		public function register_plugin_for_activation() {
			if ( ! function_exists( 'YIT_Plugin_Licence' ) ) {
				require_once '../plugin-fw/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCMBS_INIT, YITH_WCMBS_SECRET_KEY, YITH_WCMBS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.4.0
		 */
		public function register_plugin_for_updates() {
			if ( ! function_exists( 'YIT_Upgrade' ) ) {
				require_once '../plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCMBS_SLUG, YITH_WCMBS_INIT );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMBS class
 *
 * @return YITH_WCMBS
 * @since 1.0.0
 */
function YITH_WCMBS() {
	return YITH_WCMBS::get_instance();
}