<?php
/**
 * Admin class extended
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Admin_Extended', false ) ) {
	/**
	 * Admin class extended.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Admin_Extended extends YITH_WCMAP_Admin {

		/**
		 * Class construct
		 *
		 * @since  3.12.0
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			// Add action links.
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
		}

		/**
		 * Retrieve the documentation URL.
		 *
		 * @return string
		 */
		protected function get_doc_url(): string {
			return 'https://docs.yithemes.com/yith-woocommerce-customize-myaccount-page/';
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$premium_tab = array (
				'features' => array(
					array(
						'title'       => __( 'Choose between <b>3 different menu styles</b>: no borders, modern or simple', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Choose between <b>3 different menu styles</b>: no borders, modern or simple', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Customize the account color scheme', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Customize the account color scheme', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Create groups of <b>nested endpoints</b>', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Create groups of <b>nested endpoints</b>', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Add custom URL links', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Add custom URL links', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Upload custom icons to visually enhance the endpoints', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Upload custom icons to visually enhance the endpoints', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Show endpoints only to specific user roles', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Show endpoints only to specific user roles', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Create <b>custom banners</b> to show as endpoint content', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Create <b>custom banners</b> to show as endpoint content', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Add Google reCAPTCHA (v2) to the register form on My Account', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Add Google reCAPTCHA (v2) to the register form on My Account', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( 'Block specific email domains so users cannot create an account with those domains', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( 'Block specific email domains so users cannot create an account with those domains', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( '<b>Allow users to upload their own custom profile pictures</b>', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( '<b>Allow users to upload their own custom profile pictures</b>', 'yith-woocommerce-customize-myaccount-page' ),
					),
					array(
						'title'       => __( '<b>Regular updates, translations and premium support</b>', 'yith-woocommerce-customize-myaccount-page' ),
						'description' => __( '<b>Regular updates, translations and premium support</b>', 'yith-woocommerce-customize-myaccount-page' ),
					),
				)
			);

			/**
			 * APPLY_FILTERS: yith_wcmap_admin_panel_args
			 *
			 * Filters the array with the arguments to build the plugin panel.
			 *
			 * @param array $args Array of arguments.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_admin_panel_args',
				array(
					'ui_version'       => 2,
					'create_menu_page' => true,
					'parent_slug'      => '',
					'plugin_slug'      => YITH_WCMAP_SLUG,
					'page_title'       => 'YITH WooCommerce Customize My Account Page',
					'menu_title'       => 'Customize My Account Page',
					'capability'       => 'manage_options',
					'parent'           => '',
					'class'            => yith_set_wrapper_class(),
					'parent_page'      => 'yith_plugin_panel',
					'page'             => self::PANEL_PAGE,
					'admin-tabs'       => $this->get_admin_tabs(),
					'options-path'     => YITH_WCMAP_DIR . '/plugin-options',
					'premium_tab'      => $premium_tab,
					'is_premium'       => defined( 'YITH_WCMAP_PREMIUM' ),
					'is_extended'      => defined( 'YITH_WCMAP_EXTENDED' ),
					'help_tab'         => array(
						'doc_url' => $this->get_doc_url(),
					),
				)
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMAP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Plugin row meta. Add the action links to plugin admin page
		 *
		 * @since    1.0
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
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WCMAP_EXTENDED_INIT' ) && YITH_WCMAP_EXTENDED_INIT === $plugin_file ) {
				$new_row_meta_args['slug']        = YITH_WCMAP_SLUG;
				$new_row_meta_args['is_extended'] = true;
			}

			return $new_row_meta_args;
		}
	}
}
