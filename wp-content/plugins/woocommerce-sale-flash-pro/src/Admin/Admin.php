<?php
/**
 * WooCommerce Sales Flash Pro Admin.
 *
 * @since 1.3.0
 */

namespace Themesquad\WC_Sale_Flash_Pro\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 1.3.0
	 */
	public static function init() {
		add_filter( 'plugin_action_links_' . WC_SALE_FLASH_PRO_BASENAME, array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 1.3.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public static function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-settings' ) ),
			_x( 'View WooCommerce Sale Flash Pro settings', 'aria-label: settings link', 'woocommerce-sale-flash-pro' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-sale-flash-pro' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_SALE_FLASH_PRO_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/sale-flash-pro/' ),
			esc_attr_x( 'View WooCommerce Sale Flash Pro documentation', 'aria-label: documentation link', 'woocommerce-sale-flash-pro' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-sale-flash-pro' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=18591' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-sale-flash-pro' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-sale-flash-pro' )
		);

		return $links;
	}
}
