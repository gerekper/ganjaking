<?php
/**
 * Cart Message Options
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


return array(

	'cart-messages' => array(
		'cart-messages_list' => array(
			'type'      => 'post_type',
			'post_type' => 'ywcm_message',
		),
	),
);
