<?php
/**
 * WooCommerce Quick View Admin.
 *
 * @since 1.7.0
 */

namespace Themesquad\WC_Quick_View\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 1.7.0
	 */
	public static function init() {
		add_filter( 'plugin_action_links_' . WC_QUICK_VIEW_BASENAME, array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 1.7.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public static function action_links( $links ) {
		$reports_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-settings#wc_quick_view-description' ) ),
			_x( 'View WooCommerce Quick View settings', 'aria-label: settings link', 'woocommerce-quick-view' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-quick-view' )
		);

		array_unshift( $links, $reports_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_QUICK_VIEW_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/document/woocommerce-quick-view/' ),
			esc_attr_x( 'View WooCommerce Quick View documentation', 'aria-label: documentation link', 'woocommerce-quick-view' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-quick-view' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woocommerce.com/my-account/create-a-ticket?select=187509' ),
			esc_attr_x( 'Open a support ticket at WooCommerce.com', 'aria-label: support link', 'woocommerce-quick-view' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-quick-view' )
		);

		return $links;
	}
}
