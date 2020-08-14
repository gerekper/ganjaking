<?php
/**
 * Admin init class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin' ) ) {
	/**
	 * Initiator class. Create and populate admin views.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Admin
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Wishlist panel
		 *
		 * @var string Panel hookname
		 * @since 2.0.0
		 */
		protected $_panel = null;

		/**
		 * Link to landing page on yithemes.com
		 *
		 * @var string
		 * @since 2.0.0
		 */
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-wishlist/';

		/**
		 * Tab name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $tab;

		/**
		 * Plugin options
		 *
		 * @var array
		 * @since 1.0.0
		 */
		public $options;

		/**
		 * List of available tab for wishlist panel
		 *
		 * @var array
		 * @access public
		 * @since 2.0.0
		 */
		public $available_tabs = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Admin
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor of the class
		 *
		 * @return \YITH_WCWL_Admin
		 * @since 2.0.0
		 */
		public function __construct() {
			// install plugin, or update from older versions.
			add_action( 'init', array( $this, 'install' ) );

			// init admin processing.
			add_action( 'init', array( $this, 'init' ) );

			// enqueue scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 20 );

			// plugin panel options.
			add_filter( 'yith_plugin_fw_panel_wc_extra_row_classes', array( $this, 'mark_options_disabled' ), 10, 23 );

			// add plugin links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCWL_DIR . 'init.php' ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 5 );

			// register wishlist panel.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_wcwl_premium_tab', array( $this, 'print_premium_tab' ) );

			// add a post display state for special WC pages.
			add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
		}

		/* === ADMIN GENERAL === */

		/**
		 * Add a post display state for special WC pages in the page list table.
		 *
		 * @param array   $post_states An array of post display states.
		 * @param WP_Post $post        The current post object.
		 */
		public function add_display_post_states( $post_states, $post ) {
			if ( get_option( 'yith_wcwl_wishlist_page_id' ) == $post->ID ) {
				$post_states['yith_wcwl_page_for_wishlist'] = __( 'Wishlist Page', 'yith-woocommerce-wishlist' );
			}

			return $post_states;
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		public function init() {
			$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'unminified/' : '';
			$this->available_tabs = apply_filters(
				'yith_wcwl_available_admin_tabs',
				array(
					'settings'        => __( 'General settings', 'yith-woocommerce-wishlist' ),
					'add_to_wishlist' => __( 'Add to wishlist options', 'yith-woocommerce-wishlist' ),
					'wishlist_page'   => __( 'Wishlist page options', 'yith-woocommerce-wishlist' ),
					'premium'         => __( 'Premium Version', 'yith-woocommerce-wishlist' ),
				)
			);

			wp_register_style( 'yith-wcwl-font-awesome', YITH_WCWL_URL . 'assets/css/font-awesome.min.css', array(), '4.7.0' );
			wp_register_style( 'yith-wcwl-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), '3.0.1' );
			wp_register_style( 'yith-wcwl-admin', YITH_WCWL_URL . 'assets/css/admin.css', array( 'yith-wcwl-font-awesome' ), YITH_WCWL_Frontend()->version );
			wp_register_script( 'yith-wcwl-admin', YITH_WCWL_URL . 'assets/js/' . $prefix . 'admin/yith-wcwl.js', array( 'jquery', 'wc-backbone-modal', 'jquery-blockui' ), YITH_WCWL_Frontend()->version );
		}

		/**
		 * Run the installation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			if ( wp_doing_ajax() ) {
				return;
			}

			$stored_db_version = get_option( 'yith_wcwl_db_version' );

			if ( ! $stored_db_version || ! YITH_WCWL_Install()->is_installed() ) {
				// fresh installation.
				YITH_WCWL_Install()->init();
			} elseif ( version_compare( $stored_db_version, YITH_WCWL_DB_VERSION, '<' ) ) {
				// update database.
				YITH_WCWL_Install()->update( $stored_db_version );
				do_action( 'yith_wcwl_updated' );
			}

			// Plugin installed.
			do_action( 'yith_wcwl_installed' );
		}

		/**
		 * Adds plugin actions link
		 *
		 * @param mixed $links Available action links.
		 * @return array
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcwl_panel', defined( 'YITH_WCWL_PREMIUM' ) );
			return $links;
		}

		/**
		 * Adds plugin row meta
		 *
		 * @param array  $new_row_meta_args Array of meta for current plugin.
		 * @param array  $plugin_meta Not in use.
		 * @param string $plugin_file Current plugin iit file path.
		 * @param array  $plugin_data Plugin info.
		 * @param string $status Plugin status.
		 * @param string $init_file Wishlist plugin init file.
		 * @return array
		 * @since 2.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCWL_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']      = 'yith-woocommerce-wishlist';

			}

			if ( defined( 'YITH_WCWL_PREMIUM' ) ) {
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/* === WISHLIST SUBPANEL SECTION === */

		/**
		 * Register wishlist panel
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_panel() {

			$args = array(
				'create_menu_page' => true,
				'parent_slug'   => '',
				'page_title'    => __( 'YITH WooCommerce Wishlist', 'yith-woocommerce-wishlist' ),
				'menu_title'    => __( 'Wishlist', 'yith-woocommerce-wishlist' ),
				'plugin_slug'   => YITH_WCWL_SLUG,
				'plugin_description' => __( 'Allows your customers to create and share lists of products that they want to purchase on your e-commerce.', 'yith-woocommerce-wishlist' ),
				'capability'    => apply_filters( 'yith_wcwl_settings_panel_capability', 'manage_options' ),
				'parent'        => '',
				'class'         => function_exists( 'yith_set_wrapper_class' ) ? yith_set_wrapper_class() : '',
				'parent_page'   => 'yith_plugin_panel',
				'page'          => 'yith_wcwl_panel',
				'admin-tabs'    => $this->available_tabs,
				'options-path'  => YITH_WCWL_DIR . 'plugin-options',
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCWL_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Adds yith-disabled class
		 * Adds class to fields when required, and when disabled state cannot be achieved any other way (eg. by dependencies)
		 *
		 * @param array $classes Array of field extra classes.
		 * @param array $field   Array of field data.
		 *
		 * @return array Filtered array of extra classes
		 */
		public function mark_options_disabled( $classes, $field ) {
			if ( isset( $field['id'] ) && 'yith_wfbt_enable_integration' == $field['id'] && ! ( defined( 'YITH_WFBT' ) && YITH_WFBT ) ) {
				$classes[] = 'yith-disabled';
			}

			return $classes;
		}

		/**
		 * Load admin style.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			global $woocommerce, $pagenow;

			if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'yith_wcwl_panel' == $_GET['page'] ) {
				wp_enqueue_style( 'yith-wcwl-admin' );
				wp_enqueue_script( 'yith-wcwl-admin' );

				if ( isset( $_GET['tab'] ) && 'popular' == $_GET['tab'] ) {
					wp_enqueue_style( 'yith-wcwl-material-icons' );
					wp_enqueue_editor();
				}
			}
		}

		/**
		 * Prints tab premium of the plugin
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function print_premium_tab() {
			$premium_tab = YITH_WCWL_DIR . 'templates/admin/wishlist-panel-premium.php';

			if ( file_exists( $premium_tab ) ) {
				include( $premium_tab );
			}
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Admin class
 *
 * @return \YITH_WCWL_Admin
 * @since 2.0.0
 */
function YITH_WCWL_Admin() {
	return defined( 'YITH_WCWL_PREMIUM' ) ? YITH_WCWL_Admin_Premium::get_instance() : YITH_WCWL_Admin::get_instance();
}
