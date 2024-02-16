<?php
/**
 * Redsys Status Paid
 *
 * List of status paid.
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
