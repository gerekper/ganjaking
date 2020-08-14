<?php
/**
 * Admin class
 *
 * @author Yithemes
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAS_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAS_Admin {
		/**
		 * Plugin options
		 *
		 * @var array
		 * @access public
		 * @since 1.0.0
		 */
		public $options = array();

		/**
		 * Panel Object
		 *
		 * @var $_panel Panel Object
		 */
		protected $_panel;


		/**
		 * Panel Page Name
		 *
		 * @var string Ajax Search panel page
		 */
		protected $_panel_page = 'yith_wcas_panel';


		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// Actions.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'init', array( $this, 'gutenberg_integration' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCAS_DIR . '/' . basename( YITH_WCAS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// YITH WCAS Loaded.
			do_action( 'yith_wcas_loaded' );
		}

		/**
		 * Gutenberg Integration
		 */
		public function gutenberg_integration() {
			if ( function_exists( 'yith_plugin_fw_gutenberg_add_blocks' ) ) {
				$blocks = include_once YITH_WCAS_DIR . 'plugin-options/gutenberg/blocks.php';
				yith_plugin_fw_gutenberg_add_blocks( $blocks );
			}
		}



		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array(
				'settings' => __( 'Settings', 'yith-woocommerce-ajax-search' ),
				'search'   => __( 'Search', 'yith-woocommerce-ajax-search' ),
				'output'   => __( 'Output', 'yith-woocommerce-ajax-search' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => _x( 'YITH WooCommerce Ajax Search', 'Plugin name, no translate', 'yith-woocommerce-ajax-search' ),
				'menu_title'       => _x( 'Ajax Search', 'Plugin name, no translate', 'yith-woocommerce-ajax-search' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'class'            => yith_set_wrapper_class(),
				'options-path'     => YITH_WCAS_DIR . '/plugin-options',
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_action( 'woocommerce_admin_field_yith_wcas_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
		}


		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCAS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCAS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCAS_INIT, YITH_WCAS_SECRET_KEY, YITH_WCAS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_WCAS_SLUG, YITH_WCAS_INIT );
		}


		/**
		 * Add the action links to plugin admin page
		 *
		 * @param array $links Links plugin array.
		 *
		 * @return mixed
		 * @use plugin_action_links_{$plugin_file_name}
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * Add the action links to plugin admin page.
		 *
		 * @param   string $new_row_meta_args  Plugin Meta New args.
		 * @param   string $plugin_meta        Plugin Meta.
		 * @param   string $plugin_file        Plugin file.
		 * @param   array  $plugin_data        Plugin data.
		 * @param   string $status             Status.
		 * @param   string $init_file          Init file.
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAS_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WCAS_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}


	}
}
