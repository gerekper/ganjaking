<?php
/**
 * WC_OD Admin
 *
 * @package WC_OD/Admin
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Admin', false ) ) {
	/**
	 * Class WC_OD_Admin
	 */
	class WC_OD_Admin {

		/**
		 * Constructor.
		 *
		 * @since 1.5.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'load-woocommerce_page_wc-orders', array( $this, 'order_list_table' ), 5 );

			add_filter( 'plugin_action_links_' . WC_OD_BASENAME, array( $this, 'plugin_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Admin init.
		 *
		 * @since 2.4.3
		 */
		public function init() {
			$this->includes();

			add_action( 'current_screen', array( $this, 'setup_screen' ), 20 );
			add_action( 'check_ajax_referer', array( $this, 'setup_screen' ), 20 );
		}

		/**
		 * Include any classes we need within admin.
		 *
		 * @since 1.5.0
		 */
		public function includes() {
			include_once 'wc-od-admin-functions.php';
			include_once 'class-wc-od-admin-notices.php';
			include_once 'class-wc-od-admin-meta-boxes.php';
			include_once 'class-wc-od-admin-system-status.php';
			include_once 'wc-od-admin-init.php';

			if ( defined( 'DOING_AJAX' ) ) {
				include_once 'wc-od-admin-ajax-functions.php';
			}

			// The WooCommerce settings page.
			if ( WC_OD_Utils::is_woocommerce_settings_page() ) {
				include_once 'class-wc-od-admin-settings.php';
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * @since 1.5.0
		 */
		public function enqueue_scripts() {
			$screen_id = wc_od_get_current_screen_id();
			$suffix    = wc_od_get_scripts_suffix();

			if ( wc_od_get_order_admin_screen() === $screen_id ) {
				wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.13.18' );
				wp_enqueue_style( 'wc-od-admin', WC_OD_URL . 'assets/css/wc-od-admin.css', array( 'woocommerce_admin_styles' ), WC_OD_VERSION );

				wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.13.18', true );
				wp_enqueue_script( 'wc-od-admin-meta-boxes-order', WC_OD_URL . "assets/js/admin/meta-boxes-order{$suffix}.js", array( 'jquery', 'jquery-timepicker' ), WC_OD_VERSION, true );
			}
		}

		/**
		 * Adds custom meta boxes.
		 *
		 * @since 1.5.0
		 * @deprecated 2.4.0
		 */
		public function add_meta_boxes() {
			wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Admin_Meta_Boxes::init()' );
		}

		/**
		 * Looks at the current screen and loads the correct list table handler.
		 *
		 * @since 2.4.0
		 */
		public function setup_screen() {
			$screen_id = wc_od_get_current_screen_id();

			if ( 'edit-shop_order' === $screen_id ) {
				$this->order_list_table();
			}

			// Prevents multiple loads if a plugin calls check_ajax_referer many times.
			remove_action( 'current_screen', array( $this, 'setup_screen' ), 20 );
			remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ), 20 );
		}

		/**
		 * Loads order list table.
		 *
		 * @since 2.4.0
		 */
		public function order_list_table() {
			include_once 'list-table/class-wc-od-admin-list-table-orders.php';
			new WC_OD_Admin_List_Table_Orders();
		}

		/**
		 * Adds custom links to the plugins page.
		 *
		 * @since 1.6.0
		 *
		 * @param array $links The plugin links.
		 * @return array The filtered plugin links.
		 */
		public function plugin_action_links( $links ) {
			$settings_link = sprintf(
				'<a href="%1$s" aria-label="%2$s">%3$s</a>',
				wc_od_get_settings_url(),
				_x( 'View WooCommerce Order Delivery settings', 'aria-label: settings link', 'woocommerce-order-delivery' ),
				_x( 'Settings', 'plugin action link', 'woocommerce-order-delivery' )
			);

			array_unshift( $links, $settings_link );

			return $links;
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @since 1.6.0
		 *
		 * @param mixed $links Plugin Row Meta.
		 * @param mixed $file  Plugin Base file.
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( WC_OD_BASENAME !== $file ) {
				return $links;
			}

			$links['docs'] = sprintf(
				'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
				esc_url( 'https://woo.com/document/woocommerce-order-delivery/' ),
				esc_attr_x( 'View WooCommerce Order Delivery documentation', 'aria-label: documentation link', 'woocommerce-order-delivery' ),
				esc_html_x( 'Docs', 'plugin row link', 'woocommerce-order-delivery' )
			);

			$links['support'] = sprintf(
				'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
				esc_url( 'https://woo.com/my-account/create-a-ticket?select=976514' ),
				esc_attr_x( 'Open a support ticket at Woo.com', 'aria-label: support link', 'woocommerce-order-delivery' ),
				esc_html_x( 'Support', 'plugin row link', 'woocommerce-order-delivery' )
			);

			return $links;
		}
	}
}

return new WC_OD_Admin();
