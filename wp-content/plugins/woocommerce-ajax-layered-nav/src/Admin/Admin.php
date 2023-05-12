<?php
/**
 * WooCommerce Ajax Layered Navigation Admin.
 *
 * @since 2.0.0
 */

namespace Themesquad\WC_Ajax_Layered_Nav\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 2.0.0
	 */
	public static function init() {
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_AJAX_LAYERED_NAV_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/ajax-enabled-enhanced-layered-navigation/' ),
			esc_attr_x( 'View Ajax-Enabled Enhanced Layered Navigation documentation', 'aria-label: documentation link', 'woocommerce-ajax-layered-nav' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-ajax-layered-nav' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=18675' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-ajax-layered-nav' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-ajax-layered-nav' )
		);

		return $links;
	}
}
