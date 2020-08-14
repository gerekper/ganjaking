<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WACP_Admin {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WACP_Admin
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
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WACP_VERSION;

		/**
		 * The plugin panel object
		 *
		 * @var object
		 */
		protected $panel;

		/**
		 * Premium tab template file name
		 *
		 * @var string
		 */
		protected $premium = 'premium.php';

		/**
		 * Added to Cart Popup panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_wacp_panel';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WACP_Admin
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

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WACP_DIR . '/' . basename( YITH_WACP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'yith_wacp_premium', array( $this, 'premium_tab' ) );
		}

		/**
		 * Action Links. Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $links An array of action links.
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
				'general' => esc_html__( 'Settings', 'yith-woocommerce-added-to-cart-popup' ),
			);

			if ( ! ( defined( 'YITH_WACP_PREMIUM' ) && YITH_WACP_PREMIUM ) ) {
				$admin_tabs['premium'] = esc_html__( 'Premium Version', 'yith-woocommerce-added-to-cart-popup' );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => esc_html_x( 'YITH WooCommerce Added to Cart Popup', 'Plugin name', 'yith-woocommerce-added-to-cart-popup' ),
				'menu_title'       => esc_html_x( 'Added to Cart Popup', 'Plugin name', 'yith-woocommerce-added-to-cart-popup' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => apply_filters( 'yith_wacp_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_WACP_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WACP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return   void
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WACP_TEMPLATE_PATH . '/admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}

		}

		/**
		 * Add plugin row metas
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array  $new_row_meta_args
		 * @param mixed  $plugin_meta
		 * @param string $plugin_file
		 * @param mixed  $plugin_data
		 * @param mixed  $status
		 * @return array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {

			if ( defined( 'YITH_WACP_INIT' ) && YITH_WACP_INIT === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WACP_SLUG;

				if ( defined( 'YITH_WACP_PREMIUM' ) ) {
					$new_row_meta_args['is_premium'] = true;
				}
			}

			return $new_row_meta_args;
		}
	}
}

/**
 * Unique access to instance of YITH_WACP_Admin class
 *
 * @since 1.0.0
 * @return YITH_WACP_Admin
 */
function YITH_WACP_Admin() { // phpcs:ignore
	return YITH_WACP_Admin::get_instance();
}
