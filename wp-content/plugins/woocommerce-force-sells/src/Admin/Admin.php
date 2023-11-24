<?php
/**
 * WooCommerce Force Sells Admin.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_Force_Sells\Admin;

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
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
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
		if ( WC_FORCE_SELLS_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/document/force-sells/' ),
			esc_attr_x( 'View WooCommerce Force Sells documentation', 'aria-label: documentation link', 'woocommerce-force-sells' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-force-sells' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/my-account/create-a-ticket?select=18678' ),
			esc_attr_x( 'Open a support ticket at Woo.com', 'aria-label: support link', 'woocommerce-force-sells' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-force-sells' )
		);

		return $links;
	}
}
