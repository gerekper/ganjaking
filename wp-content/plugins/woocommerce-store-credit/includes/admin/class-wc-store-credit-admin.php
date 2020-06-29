<?php
/**
 * WooCommerce Store Credit Admin.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin class.
 */
class WC_Store_Credit_Admin {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 15 );
		add_action( 'admin_init', array( $this, 'add_notices' ), 30 );
		add_action( 'current_screen', array( $this, 'setup_screen' ), 20 );
		add_action( 'check_ajax_referer', array( $this, 'setup_screen' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'woocommerce_screen_ids', array( $this, 'wc_screen_ids' ) );
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
		add_filter( 'plugin_action_links_' . WC_STORE_CREDIT_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Includes any classes we need within admin.
	 *
	 * @since 3.0.0
	 */
	public function includes() {
		include_once 'wc-store-credit-admin-functions.php';
		include_once 'wc-store-credit-meta-box-functions.php';
		include_once 'class-wc-store-credit-admin-notices.php';
		include_once 'class-wc-store-credit-admin-send-credit-page.php';
		include_once 'meta-boxes/class-wc-store-credit-meta-box-coupon-data.php';
		include_once 'meta-boxes/class-wc-store-credit-meta-box-product-data.php';
	}

	/**
	 * Adds menu items.
	 *
	 * @since 3.0.0
	 */
	public function admin_menu() {
		$send_credit_page = add_submenu_page(
			'woocommerce',
			_x( 'Send Store Credit', 'page title', 'woocommerce-store-credit' ),
			_x( 'Send Store Credit', 'menu title', 'woocommerce-store-credit' ),
			'manage_woocommerce',
			wc_store_credit_get_send_credit_menu_slug(),
			array( 'WC_Store_Credit_Admin_Send_Credit_Page', 'output' )
		);

		add_action( 'load-' . $send_credit_page, array( 'WC_Store_Credit_Admin_Send_Credit_Page', 'init' ) );
	}

	/**
	 * Adds the admin notices.
	 *
	 * @since 3.1.0
	 */
	public function add_notices() {
		// There is an installer/updater notice.
		if ( WC_Store_Credit_Admin_Notices::has_notices() ) {
			return;
		}

		if ( current_user_can( 'manage_woocommerce' ) && ! wc_coupons_enabled() ) {
			WC_Store_Credit_Admin_Notices::add_notice( 'enable_coupons' );
		}
	}

	/**
	 * Looks at the current screen and loads the correct list table handler.
	 *
	 * @since 3.1.0
	 */
	public function setup_screen() {
		// Not available in customizer.
		if ( ! function_exists( 'wc_store_credit_get_current_screen_id' ) ) {
			include_once 'wc-store-credit-admin-functions.php';
		}

		$screen_id = wc_store_credit_get_current_screen_id();

		switch ( $screen_id ) {
			case 'edit-shop_coupon':
				include_once 'list-tables/class-wc-store-credit-admin-list-table-coupons.php';
				new WC_Store_Credit_Admin_List_Table_Coupons();
				break;
		}

		/*
		 * Ensure the table handler is only loaded once.
		 * Prevents multiple loads if a plugin calls check_ajax_referer many times.
		 */
		remove_action( 'current_screen', array( $this, 'setup_screen' ) );
		remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {
		$suffix                = wc_store_credit_get_scripts_suffix();
		$screen_id             = wc_store_credit_get_current_screen_id();
		$send_credit_screen_id = wc_store_credit_get_send_credit_screen_id();

		if ( in_array( $screen_id, array( 'edit-shop_coupon', $send_credit_screen_id ), true ) ) {
			wp_enqueue_script( 'wc-store-credit-admin-customer-search', WC_STORE_CREDIT_URL . "assets/js/admin/customer-search{$suffix}.js", array( 'wc-enhanced-select' ), WC_STORE_CREDIT_VERSION, true );
		}

		if ( 'shop_coupon' === $screen_id ) {
			wp_enqueue_script( 'wc-store-credit-admin-meta-boxes-coupon', WC_STORE_CREDIT_URL . "assets/js/admin/meta-boxes-coupon{$suffix}.js", array( 'wc-admin-coupon-meta-boxes' ), WC_STORE_CREDIT_VERSION, true );
		}

		if ( 'product' === $screen_id ) {
			wp_enqueue_style( 'wc-store-credit-admin', WC_STORE_CREDIT_URL . 'assets/css/admin.css', array(), WC_STORE_CREDIT_VERSION );
			wp_enqueue_script( 'wc-store-credit-admin-meta-boxes-product', WC_STORE_CREDIT_URL . "assets/js/admin/meta-boxes-product{$suffix}.js", array( 'wc-admin-product-meta-boxes' ), WC_STORE_CREDIT_VERSION, true );
		}

		// Register script for backward compatibility.
		if ( $send_credit_screen_id ) {
			wp_register_script( 'wc-store-credit-admin-send-credit', WC_STORE_CREDIT_URL . "assets/js/admin/send-credit{$suffix}.js", array( 'wc-store-credit-admin-customer-search' ), WC_STORE_CREDIT_VERSION, true );
		}
	}

	/**
	 * Filters the WooCommerce screen ids.
	 *
	 * @since 3.0.0
	 *
	 * @param array $screen_ids The screen ids.
	 * @return array
	 */
	public function wc_screen_ids( $screen_ids ) {
		// Add the 'Send Store Credit' page to the list.
		$screen_ids[] = wc_store_credit_get_send_credit_screen_id();

		return $screen_ids;
	}

	/**
	 * Adds the plugin settings page.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings pages.
	 * @return array An array with the settings pages.
	 */
	public function add_settings_page( $settings ) {
		$settings[] = include 'class-wc-store-credit-admin-settings.php';

		return $settings;
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 3.0.0
	 *
	 * @param array $links The plugin links.
	 * @return array The filtered plugin links.
	 */
	public function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( wc_store_credit_get_settings_url() ),
			_x( 'View WooCommerce Store Credit settings', 'aria-label: settings link', 'woocommerce-store-credit' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-store-credit' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins page.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_STORE_CREDIT_BASENAME === $file ) {
			$row_meta = array(
				'docs' => sprintf(
					'<a href="%1$s" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://docs.woocommerce.com/document/woocommerce-store-credit/' ),
					esc_attr_x( 'View WooCommerce Store Credit documentation', 'aria-label: documentation link', 'woocommerce-store-credit' ),
					esc_html_x( 'Docs', 'plugin row link', 'woocommerce-store-credit' )
				),
			);

			$links = array_merge( $links, $row_meta );
		}

		return $links;
	}
}

return new WC_Store_Credit_Admin();
