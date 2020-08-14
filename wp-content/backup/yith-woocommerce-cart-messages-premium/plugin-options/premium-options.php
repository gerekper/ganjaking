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
} // Exit if accessed directly


return array(
	'premium' => array(
		'home' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_woocommerce_cart_messages_premium',
		),
	),
);
