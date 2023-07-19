<?php
/**
 * Redsys Types
 *
 * List of Redsys types.
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com
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
	);
}
