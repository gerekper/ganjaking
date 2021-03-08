<?php
/**
 * WooCommerce Store Credit Admin Menu.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.5.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;

/**
 * WC_Store_Credit_Admin_Menu class.
 */
class WC_Store_Credit_Admin_Menu {

	/**
	 * Init.
	 *
	 * @since 3.5.0
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 15 );
		add_action( 'admin_menu', array( __CLASS__, 'register_nav_items' ), 20 );
	}

	/**
	 * Registers the WordPress menu items.
	 *
	 * @since 3.5.0
	 */
	public static function register_menu() {
		$send_credit_page = add_submenu_page(
			'woocommerce',
			_x( 'Send Store Credit', 'page title', 'woocommerce-store-credit' ),
			_x( 'Send Store Credit', 'menu title', 'woocommerce-store-credit' ),
			'manage_woocommerce',
			wc_store_credit_get_send_credit_menu_slug(),
			array( 'WC_Store_Credit_Admin_Send_Credit_Page', 'output' )
		);

		add_action( 'load-' . $send_credit_page, array( 'WC_Store_Credit_Admin_Send_Credit_Page', 'init' ) );

		if ( function_exists( 'wc_admin_connect_page' ) ) {
			wc_admin_connect_page(
				array(
					'id'        => 'store-credit-send-credit',
					'parent'    => 'store-credit',
					'screen_id' => wc_store_credit_get_send_credit_screen_id(),
					'title'     => __( 'Send Store Credit', 'woocommerce-store-credit' ),
				)
			);
		}
	}

	/**
	 * Registers the navigation items in the WC Navigation Menu.
	 *
	 * @since 3.5.0
	 */
	public static function register_nav_items() {
		if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Menu' ) ) {
			return;
		}

		Menu::add_plugin_category(
			array(
				'id'         => 'store-credit',
				'title'      => __( 'Store Credit', 'woocommerce-store-credit' ),
				'capability' => 'manage_woocommerce',
				'parent'     => 'woocommerce',
			)
		);

		Menu::add_plugin_item(
			array(
				'id'         => 'store-credit-send-credit',
				'title'      => __( 'Send Store Credit', 'woocommerce-store-credit' ),
				'capability' => 'manage_woocommerce',
				'url'        => wc_store_credit_get_send_credit_menu_slug(),
				'parent'     => 'store-credit',
			)
		);

		Menu::add_plugin_item(
			array(
				'id'         => 'store-credit-settings',
				'title'      => __( 'Settings', 'woocommerce-store-credit' ),
				'capability' => 'manage_woocommerce',
				'url'        => wc_store_credit_get_settings_url(),
				'parent'     => 'store-credit',
			)
		);
	}
}

WC_Store_Credit_Admin_Menu::init();
