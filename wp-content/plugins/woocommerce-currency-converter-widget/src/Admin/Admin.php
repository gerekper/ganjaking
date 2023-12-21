<?php
/**
 * WooCommerce Currency Converter Admin.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin init.
	 *
	 * @since 1.8.0
	 */
	public static function init() {
		add_filter( 'plugin_action_links_' . WC_CURRENCY_CONVERTER_BASENAME, array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 1.8.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public static function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=open_exchange_rates' ) ),
			_x( 'View WooCommerce Currency Converter Widget settings', 'aria-label: settings link', 'woocommerce-currency-converter-widget' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-currency-converter-widget' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_CURRENCY_CONVERTER_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/document/currency-converter-widget/' ),
			esc_attr_x( 'View WooCommerce Currency Converter Widget documentation', 'aria-label: documentation link', 'woocommerce-currency-converter-widget' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-currency-converter-widget' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/my-account/create-a-ticket?select=18651' ),
			esc_attr_x( 'Open a support ticket at Woo.com', 'aria-label: support link', 'woocommerce-currency-converter-widget' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-currency-converter-widget' )
		);

		return $links;
	}
}

class_alias( Admin::class, 'Themesquad\WC_Currency_Converter\Admin\Admin' );
