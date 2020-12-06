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
			add_action( 'init', array( $this, 'includes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			add_filter( 'plugin_action_links_' . WC_OD_BASENAME, array( $this, 'plugin_action_links' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Include any classes we need within admin.
		 *
		 * @since 1.5.0
		 */
		public function includes() {
			include_once 'wc-od-admin-functions.php';
			include_once 'class-wc-od-admin-notices.php';
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

			if ( 'shop_order' === $screen_id ) {
				wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.11.14' );
				wp_enqueue_style( 'wc-od-admin', WC_OD_URL . 'assets/css/wc-od-admin.css', array( 'woocommerce_admin_styles' ), WC_OD_VERSION );

				wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.11.14', true );
				wp_enqueue_script( 'wc-od-admin-meta-boxes-order', WC_OD_URL . "assets/js/admin/meta-boxes-order{$suffix}.js", array( 'jquery', 'jquery-timepicker' ), WC_OD_VERSION, true );
			}
		}

		/**
		 * Adds custom meta boxes.
		 *
		 * @since 1.5.0
		 */
		public function add_meta_boxes() {
			add_meta_box( 'woocommerce-order-delivery', _x( 'Delivery', 'meta box title', 'woocommerce-order-delivery' ), 'WC_OD_Meta_Box_Order_Delivery::output', 'shop_order', 'side', 'default' );
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
			if ( WC_OD_BASENAME === $file ) {
				$row_meta = array(
					'docs'     => sprintf(
						'<a href="%1$s" aria-label="%2$s">%3$s</a>',
						esc_url( 'https://docs.woocommerce.com/document/woocommerce-order-delivery/' ),
						esc_attr_x( 'View WooCommerce Order Delivery documentation', 'aria-label: documentation link', 'woocommerce-order-delivery' ),
						esc_html_x( 'Docs', 'plugin row link', 'woocommerce-order-delivery' )
					),
					'whatsnew' => sprintf(
						'<a href="%1$s" aria-label="%2$s">%3$s</a>',
						esc_url( 'https://docs.woocommerce.com/document/woocommerce-order-delivery/version-1-8/' ),
						esc_attr(
							/* translators: %s plugin version */
							sprintf( _x( 'What\'s New in WooCommerce Order Delivery %s', 'aria-label: what\'s new link', 'woocommerce-order-delivery' ), '1.8' )
						),
						esc_html_x( 'What\'s New', 'plugin row link', 'woocommerce-order-delivery' )
					),
				);

				$links = array_merge( $links, $row_meta );
			}

			return $links;
		}
	}
}

return new WC_OD_Admin();
