<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Category_Accordion' ) ) {

	/**
	 * YITH_WC_Category_Accordion
	 */
	class YITH_WC_Category_Accordion {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Category_Accordion
		 * @since 1.0.0
		 */
		protected static $instance = null;
		/**
		 * YITH WooCommerce Category Accordion Premium panel
		 *
		 * @var $panel
		 */
		protected $panel = null;
		/**
		 * YITH WooCommerce Category Accordion Premium panel page
		 *
		 * @var $panel_page
		 */
		protected $panel_page = 'yith_wc_category_accordion';
		/**
		 * YITH WooCommerce Category Accordion Premium premium
		 *
		 * @var $premium
		 */
		protected $premium = 'premium.php';
		/**
		 * YITH WooCommerce Category Accordion Premium suffix
		 *
		 * @var $suffix
		 */
		public $suffix = '';

		/**
		 * __construct function
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->includes();
			// Load Plugin Framework !
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			// Load Custom Table !
			add_action( 'plugins_loaded', array( $this, 'load_custom_table' ), 20 );
			// Add action links !

			add_filter(
				'plugin_action_links_' . plugin_basename( YWCCA_DIR . '/' . basename( YWCCA_FILE ) ),
				array(
					$this,
					'action_links',
				)
			);

			// Change updated message
			add_filter( 'post_updated_messages', array( $this, 'filter_post_updated_messages' ), 10, 1 );
			add_filter('bulk_post_updated_messages', array($this, 'filter_bulk_post_updated_messages'), 10, 2);

			// Add row meta!
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			// Add menu field under YITH_PLUGIN!
			add_action( 'yith_wc_category_accordion_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_menu', array( $this, 'add_category_accordion_menu' ), 5 );

			// Add metabox and tabs!
			add_action( 'admin_init', array( $this, 'add_metabox' ), 10 );

			// Changing the placeholder!
			add_filter( 'enter_title_here', array( $this, 'ywcca_change_placeholder_text' ) );

			// Save accordion button!
			add_action( 'ywcca_add_save_button', array( $this, 'show_save_button' ) );

			// Register widget!
			add_action( 'widgets_init', array( $this, 'register_accordion_widget' ) );
			// Enqueue style!
			add_action( 'wp_enqueue_scripts', array( $this, 'register_style_script' ), 15 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_style_admin_script' ), 15 );
			add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'register_style_script' ) );
			add_action( 'elementor/frontend/before_enqueue_styles', array( $this, 'register_style_script' ) );
			$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		}

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Category_Accordion
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Plugin_fw_loader
		 *
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Include all files.
		 *
		 * @since 2.0.0
		 */
		public function includes() {

			require_once YWCCA_INC . 'class.yith-category-accordion-post-types.php';
			require_once YWCCA_INC . 'class.yith-category-accordion-version-compatibility.php';
			require_once YWCCA_INC . 'class.yith-category-accordion-widget.php';
			require_once YWCCA_INC . 'functions.yith-category-accordion.php';
			require_once YWCCA_INC . 'class.yith-category-accordion-shortcode.php';
			require_once YWCCA_INC . 'elementor/class-yith-wc-category-accordion-elementor.php';
		}

		/**
		 * Load_custom_table
		 *
		 * @return void
		 */
		public function load_custom_table() {
			require_once YWCCA_INC . 'admin/class.yith-category-accordion-table.php';
		}

		/**
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param mixed $links | links plugin array.
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @use plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, defined( 'YWCCA_PREMIUM' ), YWCCA_SLUG );

			return $links;
		}

		/**
		 * Plugin_row_meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param mixed $new_row_meta_args new_row_meta_args.
		 * @param mixed $plugin_meta plugin_meta.
		 * @param mixed $plugin_file plugin_file.
		 * @param mixed $plugin_data plugin_data.
		 * @param mixed $status status.
		 * @param string $init_file init_file.
		 *
		 * @return   array
		 * @since    1.0
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCCA_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YWCCA_SLUG;

			}

			return $new_row_meta_args;
		}


		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return  void
		 * @author YITH
		 * @since   1.0.0
		 */
		public function premium_tab() {
			$premium_tab_template = YWCCA_TEMPLATE_PATH . '/admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Change accordion updated messages
		 *
		 * @param $array
		 *
		 * @return mixed
		 */
		function filter_post_updated_messages( $messages ) {

			$messages['yith_cacc'] = array(
				1 => __( 'Accordion updated.' ),
				6 => __( 'Accordion published.' ),
				7 => __( 'Accordion saved.' ),
			);
			return $messages;
		}

		/**
		 * Change bulk accordions updated messages
		 *
		 * @param $bulk_messages
		 * @param $bulk_counts
		 * @return mixed
		 */
		function filter_bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
			$bulk_messages['yith_cacc'] = isset( $bulk_messages['yith_cacc'] ) ? $bulk_messages['yith_cacc'] : array();
			$bulk_messages['yith_cacc']['deleted'] = _n( '%s accordion permanently deleted.', '%s accordions permanently deleted.', $bulk_counts['deleted'] );

			return $bulk_messages;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_category_accordion_menu() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = apply_filters(
				'yith_category_accordion_admin_tabs',
				array(
					'settings' => array(
						'title'       => __( 'General Options', 'yith-woocommerce-category-accordion' ),
						'icon'        => 'settings',
						'description' => __( "Set the general options for the accordion styles.", 'yith-woocommerce-category-accordion' )
					),
					'accordions' => array(
						'title'       => __( 'Accordion Styles', 'yith-woocommerce-category-accordion' ),
						'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  											<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
										  </svg>',
						'description' => __( 'Customize the style of the accordion sections shown in your shop. Read the documentation to learn how to use the shortcodes > <a href="https://docs.yithemes.com/yith-woocommerce-category-accordion/premium-version-settings/shortcode/" target="_blank">Shortcode Settings</a>', 'yith-woocommerce-category-accordion' )
					),
				)
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Category Accordion',
				'menu_title'       => 'Category Accordion',
				'capability'       => 'manage_options',
				'class'            => yith_set_wrapper_class(),
				'parent_page'      => 'yith_plugin_panel',
				'is_premium'       => true,
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCCA_DIR . '/plugin-options',
				'plugin-slug'      => YWCCA_SLUG,
				'ui_version'       => 2,
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Register_accordion_widget
		 *
		 * @return void
		 */
		public function register_accordion_widget() {
			register_widget( 'YITH_Category_Accordion_Widget' );
		}

		/**
		 * Register CSS Styles for admin panel
		 *
		 * @return void
		 */
		public function register_style_admin_script() {
			wp_register_style( 'ywcca_metabox_style', YWCCA_ASSETS_URL . 'css/ywcca_admin.css', array(), YWCCA_VERSION );
		}

		/**
		 * Register style and script
		 *
		 * @since 1.0.46
		 */
		public function register_style_script() {
			wp_register_style( 'ywcca_accordion_style', YWCCA_ASSETS_URL . 'css/ywcca_style.css', array(), YWCCA_VERSION );
			wp_register_script( 'ywcca_accordion', YWCCA_ASSETS_URL . 'js/' . yit_load_js_file( 'ywcca_accordion.js' ), array( 'jquery' ), YWCCA_VERSION, true );

			$ywcca_params = apply_filters( 'ywcca_script_params', array() );

			wp_localize_script( 'ywcca_accordion', 'ywcca_params', $ywcca_params );
		}

		/**
		 * Add_metabox
		 *
		 * @return void
		 */
		public function add_metabox() {
			$post_id = isset( $_REQUEST['post'] ) ? wp_unslash( $_REQUEST['post'] ) : false;
			if ( ( $post_id && 'yith_cacc' === get_post_type( $post_id ) ) || ( isset( $_REQUEST['post_type'] ) && 'yith_cacc' === wp_unslash( $_REQUEST['post_type'] ) ) ) {

				if ( ! function_exists( 'YIT_Metabox' ) ) {
					require_once YWCCA_DIR . 'plugin-fw/yit-plugin.php';
				}

				wp_enqueue_style( 'ywcca_metabox_style' );

				$general_style               = require_once YWCCA_DIR . 'plugin-options/metabox/general-style.php';
				$title_options_tab           = require_once YWCCA_DIR . 'plugin-options/metabox/title-options.php';
				$parent_category_options_tab = require_once YWCCA_DIR . 'plugin-options/metabox/parent-options.php';
				$child_category_options_tab  = require_once YWCCA_DIR . 'plugin-options/metabox/child-options.php';

				$box = YIT_Metabox( 'ywcacc_metabox_' );
				$box->init( $general_style );

				$box->add_tab( $title_options_tab, 'after', 'settings' );
				$box->add_tab( $parent_category_options_tab, 'after', 'title_options' );
				$box->add_tab( $child_category_options_tab, 'after', 'parent_options' );

				remove_meta_box( 'submitdiv', 'yith_cacc', 'side' );

			}

		}

		/**
		 * Ywcca_change_placeholder_text
		 *
		 * @param mixed $title title.
		 *
		 * @return string
		 */
		public function ywcca_change_placeholder_text( $title ) {
			$screen = get_current_screen();

			if ( 'yith_cacc' === $screen->post_type ) {
				$title = __( 'New accordion style name', 'yith-woocommerce-category-accordion' );
			}

			return $title;
		}

		/**
		 * Show_save_button
		 *
		 * @param array $field field.
		 *
		 * @return void
		 * @since 2.0.0
		 * @author YITH
		 */
		public function show_save_button( $field ) {
			require YWCCA_TEMPLATE_PATH . 'fields/save-accordion.php';
		}

	}
}
