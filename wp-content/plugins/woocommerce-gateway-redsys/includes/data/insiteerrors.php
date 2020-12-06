<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_return_insiteerrors() {
	return array(
		'msg1'  => esc_html( 'You have to fill in the data of the card' , 'woocommerce-redsys' ),
		'msg2'  => esc_html( 'The credit card is required' , 'woocommerce-redsys' ),
		'msg3'  => esc_html( 'The credit card must be numerical' , 'woocommerce-redsys' ),
		'msg4'  => esc_html( 'The credit card cannot be negative' , 'woocommerce-redsys' ),
		'msg5'  => esc_html( 'The expiration month of the card is required.' , 'woocommerce-redsys' ),
		'msg6'  => esc_html( 'The expiration month of the credit card must be numerical' , 'woocommerce-redsys' ),
		'msg7'  => esc_html( 'The credit card\'s expiration month is incorrect' , 'woocommerce-redsys' ),
		'msg8'  => esc_html( 'The year of expiry of the card is mandatory.' , 'woocommerce-redsys' ),
		'msg9'  => esc_html( 'The year of expiry of the card must be numerical' , 'woocommerce-redsys' ),
		'msg10' => esc_html( 'The year of expiry of the card cannot be negative' , 'woocommerce-redsys' ),
		'msg11' => esc_html( 'The security code on the card is not the correct length' , 'woocommerce-redsys' ),
		'msg12' => esc_html( 'The security code on the credit card must be numerical' , 'woocommerce-redsys' ),
		'msg13' => esc_html( 'The security code on the credit card cannot be negative' , 'woocommerce-redsys' ),
		'msg14' => esc_html( 'The security code is not required for your card' , 'woocommerce-redsys' ),
		'msg15' => esc_html( 'The length of the credit card is not correct' , 'woocommerce-redsys' ),
		'msg16' => esc_html( 'You must enter a valid credit card number (without spaces or dashes).' , 'woocommerce-redsys' ),
		'msg17' => esc_html( 'Incorrect validation by the commerce' , 'woocommerce-redsys' ),
	);
}
