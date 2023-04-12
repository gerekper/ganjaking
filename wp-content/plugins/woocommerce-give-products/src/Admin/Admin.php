<?php
/**
 * WooCommerce Give Products Admin.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Give_Products\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 1.2.0
	 */
	public static function init() {
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_GIVE_PRODUCTS_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/woocommerce-give-products/' ),
			esc_attr_x( 'View WooCommerce Give Products documentation', 'aria-label: documentation link', 'woocommerce-give-products' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-give-products' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=521947' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-give-products' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-give-products' )
		);

		return $links;
	}
}
