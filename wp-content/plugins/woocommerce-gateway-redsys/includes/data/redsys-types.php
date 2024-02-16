<?php
/**
 * Redsys Types
 *
 * List of Redsys types.
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
 * Get Redsys types.
 *
 * @return array
 */
function redsys_return_types() {

	return array(
		'redsys',
		'masterpass',
		'redsysbank',
		'bizumredsys',
		'iupay',
		'insite',
		'preauthorizationsredsys',
		'directdebitredsys',
		'webserviceredsys',
		'paygold',
		'bizumcheckout',
		'googlepayredirecredsys',
		'googlepayredsys',
		'applepayredsys',
	);
}
