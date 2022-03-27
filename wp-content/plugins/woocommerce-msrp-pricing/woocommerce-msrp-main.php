<?php

class woocommerce_msrp_main {

	public function run() {
		global $woocommerce_msrp_import_export,
			   $woocommerce_msrp_shortcodes,
			   $woocommerce_msrp_frontend,
			   $woocommerce_msrp_admin;

		$active_plugins = array_merge(
			apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ),
			array_keys( get_site_option( 'active_sitewide_plugins', [] ) )
		);
		// Bail if WooCommerce is not active.
		if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
			return;
		}

		/**
		 * Support for import / export in WooCommerce 3.1+
		 */

		$woocommerce_msrp_import_export = new WoocommerceMsrpImportExport();

		/**
		 * Main plugin operations.
		 */
		if ( is_admin() ) {
			$woocommerce_msrp_admin = new woocommerce_msrp_admin();
		}
		$woocommerce_msrp_frontend = new woocommerce_msrp_frontend();

		/**
		 * Shortcode support.
		 */
		$woocommerce_msrp_shortcodes = new woocommerce_msrp_shortcodes( $woocommerce_msrp_frontend );

		add_action( 'init', [ $this, 'init' ] );
	}

	public function init() {
		$domain = 'woocommerce_msrp';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/woocommerce_msrp/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce_msrp', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
}
