<?php
/**
 * Deactivate Redsys plugins
 *
 * @package WooCommerce Redsys Gateway
 * @since 2.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
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
