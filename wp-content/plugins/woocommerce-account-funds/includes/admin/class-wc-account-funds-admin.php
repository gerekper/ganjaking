<?php
/**
 * WooCommerce Account Funds Admin
 *
 * @package WC_Account_Funds/Admin
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Admin
 */
class WC_Account_Funds_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );

		// Plugin action links.
		add_filter( 'plugin_action_links_' . WC_ACCOUNT_FUNDS_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @since 2.3.7
	 */
	public function includes() {
		include_once 'wc-account-funds-admin-functions.php';
		include_once 'class-wc-account-funds-admin-notices.php';
		include_once 'class-wc-account-funds-admin-system-status.php';
		include_once 'class-wc-account-funds-admin-users.php';
		include_once 'class-wc-account-funds-admin-refunds.php';
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @since 2.6.0
	 */
	public function enqueue_scripts() {
		if ( ! wc_account_funds_is_settings_page() ) {
			return;
		}

		$suffix = wc_account_funds_get_scripts_suffix();

		wp_enqueue_script( 'wc-account-funds-settings', WC_ACCOUNT_FUNDS_URL . "assets/js/admin/settings{$suffix}.js", array( 'jquery' ), WC_ACCOUNT_FUNDS_VERSION, true );
	}

	/**
	 * Adds the settings page.
	 *
	 * @since 2.6.0
	 *
	 * @param array $settings The settings pages.
	 * @return array An array with the settings pages.
	 */
	public function add_settings_page( $settings ) {
		$settings[] = include 'class-wc-account-funds-admin-settings.php';

		return $settings;
	}

	/**
	 * Adds custom links to the plugins page.
	 *
	 * @since 2.2.0
	 *
	 * @param array $links The plugin links.
	 * @return array
	 */
	public function action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=wc-settings&tab=account_funds' ) ),
			_x( 'View WooCommerce Account Funds settings', 'aria-label: settings link', 'woocommerce-account-funds' ),
			_x( 'Settings', 'plugin action link', 'woocommerce-account-funds' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds custom links to this plugin on the plugins screen.
	 *
	 * @since 2.2.0
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( WC_ACCOUNT_FUNDS_BASENAME !== $file ) {
			return $links;
		}

		$links['docs'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( 'https://woo.com/document/account-funds/' ),
			esc_attr_x( 'View WooCommerce Account Funds documentation', 'aria-label: documentation link', 'woocommerce-account-funds' ),
			esc_html_x( 'Docs', 'plugin row link', 'woocommerce-account-funds' )
		);

		$links['changelog'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( 'https://woo.com/changelogs/woocommerce-account-funds/changelog.txt' ),
			esc_attr_x( 'View WooCommerce Account Funds changelog', 'aria-label: changelog link', 'woocommerce-account-funds' ),
			esc_html_x( 'Changelog', 'plugin row link', 'woocommerce-account-funds' )
		);

		$links['support'] = sprintf(
			'<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://woo.com/my-account/create-a-ticket?select=18728' ),
			esc_attr_x( 'Open a support ticket at Woo.com', 'aria-label: support link', 'woocommerce-account-funds' ),
			esc_html_x( 'Support', 'plugin row link', 'woocommerce-account-funds' )
		);

		return $links;
	}
}

new WC_Account_Funds_Admin();
