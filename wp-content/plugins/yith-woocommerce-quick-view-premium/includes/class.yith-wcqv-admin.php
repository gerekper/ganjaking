<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Quick View
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCQV' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCQV_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCQV_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCQV_Admin
		 */
		protected static $instance;

		/**
		 * Plugin options
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $options = array();

		/**
		 * Panel Object
		 *
		 * @since 1.0.0
		 * @var $_panel
		 */
		protected $panel;

		/**
		 * Quick View panel page
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $panel_page = 'yith_wcqv_panel';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCQV_Admin
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			// Add panel options.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCQV_DIR . '/' . basename( YITH_WCQV_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// YITH WCQV Loaded!
			do_action( 'yith_wcqv_loaded' );
		}


		/**
		 * Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $links An array of plugin links.
		 *
		 * @return   array
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true );
			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'general' => __( 'General', 'yith-woocommerce-quick-view' ),
				'product' => __( 'Product', 'yith-woocommerce-quick-view' ),
				'style'   => __( 'Style', 'yith-woocommerce-quick-view' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'YITH WooCommerce Quick View', 'options page title', 'yith-woocommerce-quick-view' ),
				'menu_title'       => _x( 'Quick View', 'plugin menu title', 'yith-woocommerce-quick-view' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCQV_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Plugin Row Meta
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 * @param array    $new_row_meta_args An array of plugin row meta.
		 * @param string[] $plugin_meta       An array of the plugin's metadata,
		 *                                    including the version, author,
		 *                                    author URI, and plugin URI.
		 * @param string   $plugin_file       Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data       An array of plugin data.
		 * @param string   $status            Status of the plugin. Defaults are 'All', 'Active',
		 *                                    'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                    'Drop-ins', 'Search', 'Paused'.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WCQV_INIT' ) && YITH_WCQV_INIT === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCQV_SLUG;

				if ( defined( 'YITH_WCQV_PREMIUM' ) ) {
					$new_row_meta_args['is_premium'] = true;
				}
			}
			return $new_row_meta_args;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCQV_INIT, YITH_WCQV_SECRET_KEY, YITH_WCQV_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCQV_SLUG, YITH_WCQV_INIT );
		}
	}
}

/**
 * Unique access to instance of YITH_WCQV_Admin class
 *
 * @since 1.0.0
 * @return YITH_WCQV_Admin
 */
function YITH_WCQV_Admin() { // phpcs:ignore
	return YITH_WCQV_Admin::get_instance();
}
