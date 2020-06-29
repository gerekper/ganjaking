<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBSL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCBSL_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Admin behaviors.
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	class YITH_WCBSL_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCBSL_Admin
		 * @since 1.0.0
		 */
		protected static $_instance;

		/** @var YIT_Plugin_Panel_WooCommerce $_panel Panel Object */
		protected $_panel;

		/** @var string Premium version landing link */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-best-sellers/';

		/** @var string panel page */
		protected $_panel_page = 'yith_wcbsl_panel';

		/** @var string documentation url */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-best-sellers/';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCBSL
		 * @since 1.0.0
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			YITH_WCBSL_Install();

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBSL_DIR . '/' . basename( YITH_WCBSL_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Premium Tabs
			add_action( 'yith_wcbsl_premium_tab', array( $this, 'show_premium_tab' ) );
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCBSL_PREMIUM' ) );
		}

		/**
		 * plugin_row_meta
		 * add the action links to plugin admin page
		 *
		 * @param $row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 *
		 * @return   array
		 * @since    1.0
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			$init = defined( 'YITH_WCBSL_FREE_INIT' ) ? YITH_WCBSL_FREE_INIT : YITH_WCBSL_INIT;

			if ( $init === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCBSL_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCBSL_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs_free = array(
				'settings' => _x( 'Settings', 'tab name in "YIT Plugins" menu', 'yith-woocommerce-best-sellers' ),
				'premium'  => _x( 'Premium Version', 'tab name in "YIT Plugins" menu', 'yith-woocommerce-best-sellers' ),
			);

			$admin_tabs = apply_filters( 'yith_wcbsl_settings_admin_tabs', $admin_tabs_free );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_WCBSL_SLUG,
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'WooCommerce Best Sellers',
				'menu_title'       => 'Best Sellers',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCBSL_DIR . '/plugin-options',
			);


			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCBSL_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'yith_wcbsl_admin_style', YITH_WCBSL_ASSETS_URL . '/css/admin.css' );
		}

		/**
		 * Show premium landing tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function show_premium_tab() {
			$landing = YITH_WCBSL_TEMPLATE_PATH . '/premium.php';
			file_exists( $landing ) && require( $landing );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}
	}
}

/**
 * Unique access to instance of YITH_WCBSL_Admin class
 *
 * @return YITH_WCBSL_Admin|YITH_WCBSL_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCBSL_Admin() {
	return YITH_WCBSL_Admin::get_instance();
}