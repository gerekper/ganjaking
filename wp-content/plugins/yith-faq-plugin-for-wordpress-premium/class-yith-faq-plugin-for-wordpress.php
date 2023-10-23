<?php
/**
 * Main plugin class
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Plugin_For_WordPress' ) ) {

	/**
	 * Main class
	 *
	 * @class   YITH_FAQ_Plugin_For_WordPress
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress
	 */
	class YITH_FAQ_Plugin_For_WordPress {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_FAQ_Plugin_For_WordPress
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $panel = null;

		/**
		 *  Premium version landing link
		 *
		 * @var string
		 */
		protected $premium_landing = 'https://yithemes.com/themes/plugins/yith-faq-plugin-for-wordpress/';

		/**
		 * YITH FAQ Plugin for WordPress panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith-faq-plugin-for-wordpress';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_FAQ_Plugin_For_WordPress
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {

			// Load plugin framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_FWP_DIR . '/' . basename( YITH_FWP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );

			$this->includes();
			$this->upgrade_options();

		}

		/**
		 * Upgrade options to version 2.0
		 *
		 * @return void
		 * @since  2.0.0
		 */
		private function upgrade_options() {

			$db_version = get_option( 'yith_faq_db_version', false );

			if ( ! $db_version ) {

				$old_options = get_option( 'yit_faq_options' );
				$new_options = yfwp_get_default();

				if ( ! empty( $old_options ) ) {
					if ( 'yes' === $old_options['customize-search'] ) {
						$new_options['search-button'] = array(
							'background'       => $old_options['search-color'],
							'background-hover' => $old_options['search-color-hover'],
							'icon'             => $old_options['search-icon-color'],
							'icon-hover'       => $old_options['search-icon-color-hover'],
						);
					}

					if ( 'yes' === $old_options['customize-category'] ) {
						$new_options['filters-layout'] = 'pill';
						$new_options['filters-colors'] = array(
							'background'        => $old_options['category-color'],
							'background-hover'  => $old_options['category-color-hover'],
							'background-active' => $old_options['category-color-hover'],
							'text'              => $old_options['category-text-color'],
							'text-hover'        => $old_options['category-text-color-hover'],
							'text-active'       => $old_options['category-text-color-hover'],
						);
					}

					if ( 'yes' === $old_options['customize-icons'] ) {
						$new_options['icon-colors'] = array(
							'background'        => $old_options['icon-background-color'],
							'background-hover'  => $old_options['icon-background-color'],
							'background-active' => $old_options['icon-background-color'],
							'icon'              => $old_options['icon-color'],
							'icon-hover'        => $old_options['icon-color'],
							'icon-active'       => $old_options['icon-color'],
						);
					}

					if ( 'yes' === $old_options['customize-link'] ) {
						$new_options['faq-copy-button-color'] = array(
							'background'       => $old_options['link-color'],
							'background-hover' => $old_options['link-color-hover'],
							'icon'             => $old_options['link-icon-color'],
							'icon-hover'       => $old_options['link-icon-color-hover'],
						);
					}

					if ( 'yes' === $old_options['customize-navigation'] ) {
						$new_options['pagination-layout'] = 'squared';
						$new_options['pagination-colors'] = array(
							'background'        => $old_options['navigation-color'],
							'background-hover'  => $old_options['navigation-color-hover'],
							'background-active' => $old_options['navigation-color-hover'],
							'text'              => $old_options['navigation-text-color'],
							'text-hover'        => $old_options['navigation-text-color-hover'],
							'text-active'       => $old_options['navigation-text-color-hover'],
						);
					}
				}
				update_option( 'yit_faq_wp_options', $new_options );
				update_option( 'yith_faq_db_version', YITH_FWP_VERSION );

			}

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		private function includes() {
			include_once 'includes/functions.yith-faq.php';
			include_once 'includes/class-yith-faq-settings.php';
			include_once 'includes/class-yith-faq-post-type.php';
			include_once 'includes/class-yith-faq-taxonomy.php';
			include_once 'includes/class-yith-faq-shortcode-post-type.php';
			include_once 'includes/abstracts/class-yith-faq-shortcode.php';
			include_once 'includes/shortcodes/class-yith-faq-shortcode-base.php';
			include_once 'includes/shortcodes/class-yith-faq-shortcode-summary.php';
			include_once 'includes/shortcodes/class-yith-faq-shortcode-preset.php';
			include_once 'includes/admin/list-tables/class-yith-custom-table.php';
			include_once 'includes/admin/list-tables/class-yith-faq-shortcodes-table.php';

			if ( function_exists( 'WC' ) ) {
				include_once 'includes/compatibilities/woocommerce/class-yith-faq-woocommerce.php';
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'shortcodes'    => esc_html__( 'FAQ Shortcodes', 'yith-faq-plugin-for-wordpress' ),
				'customization' => esc_html__( 'Customization', 'yith-faq-plugin-for-wordpress' ),
			);

			$args = array(
				'create_menu_page' => true,
				'plugin_slug'      => YITH_FWP_SLUG,
				'is_premium'       => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH FAQ for WordPress & WooCommerce',
				'menu_title'       => 'FAQ',
				'capability'       => 'manage_options',
				'parent'           => 'faq_wp',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_FWP_DIR . 'plugin-options',
				'class'            => yith_set_wrapper_class(),
			);

			$this->panel = new YIT_Plugin_Panel( $args );

		}

		/**
		 * Get Panel page slug
		 *
		 * @return  string
		 * @since   2.0.0
		 */
		public function get_panel_page() {
			return $this->panel_page;
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @return  void
		 * @since   1.0.0
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
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array $links links plugin array.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->panel_page, true, YITH_FWP_SLUG );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array  $new_row_meta_args Row meta args.
		 * @param array  $plugin_meta       Plugin meta.
		 * @param string $plugin_file       Plugin File.
		 * @param array  $plugin_data       Plugin data.
		 * @param string $status            Status.
		 * @param string $init_file         Init file.
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_FWP_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_FWP_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_FWP_INIT, YITH_FWP_SECRET_KEY, YITH_FWP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_FWP_SLUG, YITH_FWP_INIT );
		}

	}

}
