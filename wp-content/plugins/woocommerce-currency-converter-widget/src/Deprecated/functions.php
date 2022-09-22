<?php
/**
 * Deprecated functions.
 *
 * This file is autoloaded via composer.json.
 *
 * @since 1.7.0
 */

/**
 * WooCommerce Deactivated Notice.
 *
 * @deprecated 1.7.0
 */
function wc_currency_converter_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Currency Converter requires %s to be installed and active.', 'woocommerce-currency-converter-widget' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
