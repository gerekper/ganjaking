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
			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			add_action( 'yith_wcmbs_delete_transients', array( YITH_WCMBS_Manager(), 'delete_transients' ) );

			// Register Membership Post Type
			add_action( 'init', array( $this, 'register_membership_post_type' ) );

			if ( defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM ) {
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

				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					add_filter( 'woocommerce_shipping_methods', array( $this, 'add_membership_shipping_methods' ) );
				}
			}

			// Class admin
			if ( is_admin() ) {
				YITH_WCMBS_Admin();
			} else {
				YITH_WCMBS_Frontend();
			}

			YITH_WCMBS_Orders();
		}

		public function load_privacy() {
			require_once YITH_WCMBS_INCLUDES_PATH . '/class.yith-wcmbs-privacy.php';
		}

		/**
		 * Register Membership custom post type
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function register_membership_post_type() {

			$labels = array(
				'menu_name'          => _x( 'All Memberships', 'plugin name in admin WP menu', 'yith-woocommerce-membership' ),
				'all_items'          => __( 'All Memberships', 'yith-woocommerce-membership' ),
				'name'               => __( 'All Memberships', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Membership', 'yith-woocommerce-membership' ),
				'new_item'           => __( 'New Membership', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Membership', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New Membership', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Membership', 'yith-woocommerce-membership' ),
				'view'               => __( 'View Membership', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View Membership', 'yith-woocommerce-membership' ),
				'search_items'       => __( 'Search Memberships', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Membership not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Membership not found in trash', 'yith-woocommerce-membership' ),
			);


			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => false,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
			);

			if ( defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM ) {
				$args['show_in_menu'] = 'edit.php?post_type=yith-wcmbs-plan';
				$args['show_ui']      = true;
			}

			register_post_type( self::$membership_post_type, $args );
			remove_post_type_support( self::$membership_post_type, 'title' );
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
			$plan_ids = get_option( 'yith-wcmbs-memberships-on-user-register', false );

			$apply = apply_filters( 'yith_wcmbs_apply_membership_on_user_register', true, $user_id );

			if ( $apply && $plan_ids && is_array( $plan_ids ) ) {
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