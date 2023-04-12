<?php
/**
 * Deprecated functions.
 *
 * This file is autoloaded via composer.json.
 *
 * @since 1.2.0
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.0.22
 * @deprecated 1.2.0
 */
function woocommerce_photography_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Photography requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-photography' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Install method.
 *
 * @deprecated 1.2.0
 */
function woocommerce_photography_install() {
	$includes = plugin_dir_path( WC_PHOTOGRAPHY_FILE ) . 'includes/';

	include_once $includes . 'class-wc-photography-taxonomies.php';
	include_once $includes . 'class-wc-photography-install.php';

	WC_Photography_Install::install();
}
