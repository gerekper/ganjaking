<?php
/**
 * WooCommerce Products Compare Admin.
 *
 * @since 1.2.0
 */

namespace KoiLab\WC_Products_Compare\Admin;

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
		if ( WC_PRODUCTS_COMPARE_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/woocommerce-products-compare/' ),
			esc_attr_x( 'View WooCommerce Products Compare documentation', 'aria-label: documentation link', 'woocommerce-products-compare' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-products-compare' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=853117' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-products-compare' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-products-compare' )
		);

		return $links;
	}
}

class_alias( Admin::class, 'Themesquad\WC_Products_Compare\Admin\Admin' );
