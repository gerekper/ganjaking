<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Terms and Condtions Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCTC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCTC_Admin' ) ) {
	/**
	 * WooCommerce Terms and Conditions Popup Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCTC_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCTC_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * @var string official documentation url
		 */
		protected $_official_documentation = 'https://yithemes.com/docs-plugins/yith-woocommerce-terms-conditions-popup/';

		/**
		 * @var string live demo url
		 */
		protected $_live_demo = 'https://plugins.yithemes.com/yith-woocommerce-terms-conditions/';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCTC_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCTC_Admin
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->available_tabs = apply_filters( 'yith_wctc_available_admin_tabs', array(
				'settings' => __( 'Settings', 'yit' ),
				'layout'   => __( 'Layout', 'yit' )
			) );

			// register plugin panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// enqueue admin scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// register pointer
			add_action( 'admin_init', array( $this, 'register_pointer' ) );

			// register plugin row meta
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 5 );
			add_filter( 'plugin_action_links_' . YITH_WCTC_INIT, array( $this, 'action_links' ) );

			// register metabox
			add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ) );
		}

		/* === INIT PLUGIN PANEL === */

		/**
		 * Enqueue scripts and stuff for admin panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			$screen = get_current_screen();

			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'unminified/' : '';
			$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			wp_register_style( 'yith-wctc', YITH_WCTC_URL . 'assets/css/admin/yith-wctc.css', array(), YITH_WCTC_VERSION );
			wp_register_script( 'yith-wctc', YITH_WCTC_URL . 'assets/js/admin/' . $path . 'yith-wctc' . $suffix . '.js', array( 'jquery' ), YITH_WCTC_VERSION, true );

			if ( 'yith-plugins_page_yith_wctc_panel' == $screen->id ) {
				wp_enqueue_script( 'yith-wctc' );
			}

			if ( 'shop_order' == $screen->id ) {
				wp_enqueue_style( 'yith-wctc' );
			}
		}

		/**
		 * Register panel for "Terms & Conditions" settings
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Terms and Conditions Popup', 'yith-woocommerce-terms-conditions-premium' ),
				'menu_title'       => __( 'Terms and Conditions Popup', 'yith-woocommerce-terms-conditions-premium' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => 'yith_wctc_panel',
				'admin-tabs'       => $this->available_tabs,
				'options-path'     => YITH_WCTC_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCTC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/* === POINTER SECTION === */

		/**
		 * Register pointers for notify plugin updates to user
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_pointer() {

			if ( ! class_exists( 'YIT_Pointers' ) ) {
				include_once( '../plugin-fw/lib/yit-pointers.php' );
			}

			$args[] = array(
				'screen_id'  => 'plugins',
				'pointer_id' => 'yith_wctc_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'YITH WooCommerce Terms & Conditions Popup', 'yith-woocommerce-terms-conditions-premium' ),
					__( 'In YIT Plugins tab you can find YITH WooCommerce Terms & Conditions Popup options. From this menu you can access all settings of YITH plugins activated.', 'yith-woocommerce-terms-conditions-premium' )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'       => YITH_WCTC_INIT
			);

			YIT_Pointers()->register( $args );
		}

		/* === METABOX SECTION === */

		/**
		 * Register metabox to show T&C preferences
		 *
		 * @return void
		 */
		public function add_order_metabox() {
			add_meta_box( 'yith_wctc_user_preferences', __( 'Terms & Conditions agreement', 'yith-woocommerce-terms-conditions-premium' ), array(
				$this,
				'print_user_preferences_metabox'
			), 'shop_order', 'side' );
		}

		/**
		 * Print metabox to show T&C preferences
		 *
		 * @param $post \WP_Post Current post
		 *
		 * @return void
		 */
		public function print_user_preferences_metabox( $post ) {
			$order = wc_get_order( $post );

			if ( ! $order ) {
				return;
			}

			$terms_type          = yit_get_prop( $order, '_yith_wctc_terms_type', true );
			$hide_checkboxes     = yit_get_prop( $order, '_yith_wctc_hide_checkboxes', true );
			$terms_accepted      = yit_get_prop( $order, '_yith_wctc_terms_accepted', true );
			$privacy_accepted    = yit_get_prop( $order, '_yith_wctc_privacy_accepted', true );
			$last_terms_update   = yit_get_prop( $order, '_yith_wctc_last_terms_update', true );
			$last_privacy_update = yit_get_prop( $order, '_yith_wctc_last_privacy_update', true );

			include( YITH_WCTC_DIR . 'templates/admin/metaboxes/user-preferences-metabox.php' );
		}

		/* === PLUGIN LINK SECTION === */

		/**
		 * Adds plugin row meta
		 *
		 * @param $plugin_meta array Array of unfiltered plugin meta
		 * @param $plugin_file string Plugin base file path
		 *
		 * @return array Filtered array of plugin meta
		 * @since 1.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCTC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']             = YITH_WCTC_SLUG;
				$new_row_meta_args['live_demo']['url'] = $this->_live_demo;
				$new_row_meta_args['is_premium']       = true;
			}


			return $new_row_meta_args;
		}

		/**
		 * Add plugin action links
		 *
		 * @param mixed $links Plugins links array
		 *
		 * @return array Filtered link array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wctc_panel', true, YITH_WCTC_SLUG );

			return $links;
		}
	}
}

/**
 * Unique access to instance of YITH_WCTC_Admin class
 *
 * @return \YITH_WCTC_Admin
 * @since 1.0.0
 */
function YITH_WCTC_Admin() {
	return YITH_WCTC_Admin::get_instance();
}

YITH_WCTC_Admin();