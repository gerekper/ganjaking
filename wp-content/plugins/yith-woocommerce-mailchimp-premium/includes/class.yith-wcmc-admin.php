<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Admin' ) ) {
	/**
	 * WooCommerce Mailchimp Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMC_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * List of available tab for mailchimp panel
		 *
		 * @var array
		 * @access public
		 * @since  1.0.0
		 */
		public $available_tabs = array();

		/**
		 * Landing url
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-mailchimp/';

		/**
		 * Documentation url
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-mailchimp/';

		/**
		 * Live demo url
		 * @var string Live demo url
		 * @since 1.0.0
		 */
		public $live_demo_url = 'https://plugins.yithemes.com/yith-woocommerce-mailchimp/';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMC_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/* === REGISTER AND PRINT MAILCHIMP PANEL === */

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCMC_Admin
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->available_tabs = apply_filters( 'yith_wcmc_available_admin_tabs', array(
				'integration' => __( 'Integration', 'yith-woocommerce-mailchimp' ),
				'checkout'    => __( 'Checkout', 'yith-woocommerce-mailchimp' ),
				'premium'     => __( 'Premium Version', 'yith-woocommerce-mailchimp' )
			) );

			// register mailchimp panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'woocommerce_admin_field_yith_wcmc_integration_status', array(
				$this,
				'print_custom_yith_wcmc_integration_status'
			) );
			add_action( 'yith_wcmc_premium_tab', array( $this, 'print_premium_tab' ) );

			// handle licence changing
			add_action( 'update_option_yith_wcmc_mailchimp_api_key', array( $this, 'delete_old_key_options' ), 10, 2 );

			// register plugin actions and row meta
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMC_DIR . 'init.php' ), array(
				$this,
				'action_links'
			) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register metabox to show user preferences within the order
			add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ) );

			// enqueue style
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}

		/**
		 * Enqueue scripts and stuffs
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			global $pagenow;
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
			$screen = get_current_screen();

			if ( $screen && in_array( $screen->id, array( 'shop_order', 'yith-plugins_page_yith_wcmc_panel' ) ) ) {
				wp_enqueue_style( 'yith-wcmc-admin', YITH_WCMC_URL . 'assets/css/admin/yith-wcmc.css', array(), YITH_WCMC_VERSION );
				wp_enqueue_script( 'yith-wcmc-admin', YITH_WCMC_URL . 'assets/js/admin' . $path . '/yith-wcmc' . $prefix . '.js', array(
					'jquery',
					'jquery-blockui'
				), YITH_WCMC_VERSION, true );

				wp_localize_script( 'yith-wcmc-admin', 'yith_wcmc', array(
					'labels'             => array(
						'update_list_button'   => __( 'Update Lists', 'yith-woocommerce-mailchimp' ),
						'update_group_button'  => __( 'Update Groups', 'yith-woocommerce-mailchimp' ),
						'update_field_button'  => __( 'Update Fields', 'yith-woocommerce-mailchimp' ),
						'connect_store'        => __( 'Connect Store', 'yith-woocommerce-mailchimp' ),
						'confirm_store_delete' => __( "Are you sure you want to disconnect your store?\n
This will delete all store data from your Mailchimp account, including Products, Coupons, Orders and Customers", 'yith-woocommerce-mailchimp' )
					),
					'actions'            => array(
						'do_request_via_ajax_action'       => 'do_request_via_ajax',
						'retrieve_lists_via_ajax_action'   => 'retrieve_lists_via_ajax',
						'retrieve_groups_via_ajax_action'  => 'retrieve_groups_via_ajax',
						'retrieve_fields_via_ajax_action'  => 'retrieve_fields_via_ajax',
						'disconnect_store_via_ajax_action' => 'disconnect_store_via_ajax'
					),
					'ajax_request_nonce' => wp_create_nonce( 'yith_wcmc_ajax_request' )
				) );
			}
		}

		/**
		 * Register panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Mailchimp', 'yith-woocommerce-mailchimp' ),
				'menu_title'       => __( 'Mailchimp', 'yith-woocommerce-mailchimp' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => 'yith_wcmc_panel',
				'admin-tabs'       => $this->available_tabs,
				'options-path'     => YITH_WCMC_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCMC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Output integration status filed
		 *
		 * @param $value array Array representing the field to print
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcmc_integration_status( $value ) {
			$result = YITH_WCMC()->do_request( 'get' );

			$user_id  = isset( $result['account_id'] ) ? $result['account_id'] : false;
			$username = isset( $result['username'] ) ? $result['username'] : false;
			$name     = isset( $result['account_name'] ) ? $result['account_name'] : false;
			$email    = isset( $result['email'] ) ? $result['email'] : false;

			include( YITH_WCMC_DIR . 'templates/admin/types/integration-status.php' );
		}

		/**
		 * Prints tab premium of the plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_premium_tab() {
			$premium_tab = YITH_WCMC_DIR . 'templates/admin/mailchimp-panel-premium.php';

			if ( file_exists( $premium_tab ) ) {
				include( $premium_tab );
			}
		}

		/**
		 * Add metabox to order edit page
		 *
		 * @return void
		 * @since 1.1.3
		 */
		public function add_order_metabox() {
			add_meta_box( 'yith_wcmc_user_preferences', __( 'Mailchimp status', 'yith-woocommerce-mailchimp' ), array(
				$this,
				'print_user_preferences_metabox'
			), 'shop_order', 'side' );
		}

		/**
		 * Print metabox, to highlight user preferences
		 *
		 * @return void
		 * @var $post \WP_Post Current order
		 *
		 */
		public function print_user_preferences_metabox( $post ) {
			$order = wc_get_order( $post );

			if ( ! $order ) {
				return;
			}

			$show_checkbox       = yit_get_prop( $order, '_yith_wcmc_show_checkbox', true );
			$submitted_value     = yit_get_prop( $order, '_yith_wcmc_submitted_value', true );
			$customer_subscribed = yit_get_prop( $order, '_yith_wcmc_customer_subscribed', true );
			$personal_data       = yit_get_prop( $order, '_yith_wcmc_personal_data', true );

			include( YITH_WCMC_DIR . 'templates/admin/metaboxes/user-preferences-metabox.php' );
		}

		/**
		 * Register plugins action links
		 *
		 * @param array $links Array of current links
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcmc_panel', defined( 'YITH_WCMC_PREMIUM' ) );

			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCMC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCMC_SLUG;
			}

			if ( defined( 'YITH_WCMC_PREMIUM' ) ) {
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/**
		 * Delete options specific to an API Key
		 *
		 * @param $old_value string Old key value
		 * @param $value     string New key value
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_old_key_options( $old_value, $value ) {
			delete_transient( 'yith_wcmc_' . md5( $old_value ) );
			delete_option( 'yith_wcmc_mailchimp_list' );
		}
	}
}

/**
 * Unique access to instance of YITH_WCMC_Admin class
 *
 * @return \YITH_WCMC_Admin
 * @since 1.0.0
 */
function YITH_WCMC_Admin() {
	return YITH_WCMC_Admin::get_instance();
}