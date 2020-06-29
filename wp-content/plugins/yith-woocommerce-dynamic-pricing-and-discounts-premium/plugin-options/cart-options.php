<?php
/**
 * Cart options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$cart = array(

	'cart' => array(
		'home' => array(
			'type'      => 'post_type',
			'post_type' => 'ywdpd_discount',
		),
	),
);

return apply_filters( 'ywdpd_panel_cart_rules_tab', $cart );
