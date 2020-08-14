<?php
/**
 * Admin class
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCAS' ) ) {
	exit; } // Exit if accessed directly

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
		 * Panel object
		 *
		 * @var Panel Object
		 */
		protected $_panel;

		/**
		 * Premium tab template file name.
		 *
		 * @var string
		 */
		protected $_premium = 'premium.php';

		/**
		 * Premium version landing link.
		 *
		 * @var string
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-ajax-search/';

		/**
		 * Ajax Search panel page.
		 *
		 * @var string
		 */
		protected $_panel_page = 'yith_wcas_panel';


		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'init', array( $this, 'gutenberg_integration' ) );
			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCAS_DIR . '/' . basename( YITH_WCAS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			add_action( 'yith_ajax_search_premium', array( $this, 'premium_tab' ) );

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
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param string $links | links plugin array.
		 *
		 * @return   mixed
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, false );
			return $links;
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
				'premium'  => __( 'Premium Version', 'yith-woocommerce-ajax-search' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_WCAS_SLUG,
				'page_title'       => __( 'YITH WooCommerce Ajax Search', 'yith-woocommerce-ajax-search' ),
				'menu_title'       => __( 'Ajax Search', 'yith-woocommerce-ajax-search' ),
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
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_WCAS_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}

		}

		/**
		 * Add the action links to plugin admin page
		 *
		 * @param   string $new_row_meta_args  Plugin Meta New args.
		 * @param   string $plugin_meta        Plugin Meta.
		 * @param   string $plugin_file        Plugin file.
		 * @param   array  $plugin_data        Plugin data.
		 * @param   string $status             Status.
		 * @param   string $init_file          Init file.
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAS_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YITH_WCAS_SLUG;
			}

			return $new_row_meta_args;
		}



		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

	}
}
