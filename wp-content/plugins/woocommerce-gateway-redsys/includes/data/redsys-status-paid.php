<?php
/**
 * Copyright: (C) 2013 - 2023 José Conti
 *
 * @package WooCommerce Redsys Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Copyright: (C) 2013 - 2023 José Conti
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
