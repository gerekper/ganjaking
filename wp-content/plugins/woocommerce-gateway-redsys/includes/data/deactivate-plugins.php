<?php
/**
 * Deactivate Redsys plugins
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get plugins to deactivate.
 *
 * @return array
 */
function plugins_to_deactivate() {

	return array(
		'/woo-redsys-gateway-light/woocommerce-redsys.php',
		'/redsysoficial/class-wc-redsys.php',
		'/redsys/class-wc-redsys.php',
		'/bizum/class-wc-bizum.php',
		'/woocommerce-sermepa-payment-gateway/wc_redsys_payment_gateway.php',
		'/redsyspur/class-wc-redsys.php',
	);
}
