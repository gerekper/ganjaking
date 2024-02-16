<?php
/**
 * WooCommerce 360° Image Admin.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_360_Image\Admin;

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
		add_filter( 'plugin_action_links_' . WC_360_IMAGE_BASENAME, array( __CLASS__, 'action_links' ) );
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
			esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=wc360' ) ),
			_x( 'View WooCommerce 360° Image settings', 'aria-label: settings link', 'woocommerce-360-image' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-360-image' )
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
		if ( WC_360_IMAGE_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/document/woocommerce-360-image/' ),
			esc_attr_x( 'View WooCommerce 360° Image documentation', 'aria-label: documentation link', 'woocommerce-360-image' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-360-image' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/my-account/create-a-ticket?select=512186' ),
			esc_attr_x( 'Open a support ticket at Woo.com', 'aria-label: support link', 'woocommerce-360-image' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-360-image' )
		);

		return $links;
	}
}
