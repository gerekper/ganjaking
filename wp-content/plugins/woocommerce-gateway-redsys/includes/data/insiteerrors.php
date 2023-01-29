<?php
/**
 * Insite Errors
 *
 * List of insite errors.
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get insite errors.
 *
 * @return array
 */
function redsys_return_insiteerrors() {
	return array(
		'msg1'  => 'You have to fill in the data of the card',
		'msg2'  => 'The credit card is required',
		'msg3'  => 'The credit card must be numerical',
		'msg4'  => 'The credit card cannot be negative',
		'msg5'  => 'The expiration month of the card is required.',
		'msg6'  => 'The expiration month of the credit card must be numerical',
		'msg7'  => 'The credit card\'s expiration month is incorrect',
		'msg8'  => 'The year of expiry of the card is mandatory.',
		'msg9'  => 'The year of expiry of the card must be numerical',
		'msg10' => 'The year of expiry of the card cannot be negative',
		'msg11' => 'The security code on the card is not the correct length',
		'msg12' => 'The security code on the credit card must be numerical',
		'msg13' => 'The security code on the credit card cannot be negative',
		'msg14' => 'The security code is not required for your card',
		'msg15' => 'The length of the credit card is not correct',
		'msg16' => 'You must enter a valid credit card number (without spaces or dashes).',
		'msg17' => 'Incorrect validation by the commerce',
	);
}
