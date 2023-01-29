<?php
/**
 * Redsys Status Paid
 *
 * List of status paid.
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get status paid.
 *
 * @return array
 */
function redsys_return_status_paid() {

	$status = array();
	$status = array(
		'pending',
		'redsys-pbankt',
		'redsys-wait',
		'cancelled',
		'failed',
		'on-hold',
		'pending-deposit',
		'redsys-pre',
	);
	return $status;
}
