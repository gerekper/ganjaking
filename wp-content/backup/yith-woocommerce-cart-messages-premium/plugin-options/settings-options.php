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

	'settings' => array(

		'section_general_settings'     => array(
			'name' => __( 'General settings', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_general',
		),

		'show_in_cart'                 => array(
			'name'    => __( 'Show in cart page', 'yith-woocommerce-cart-messages' ),
			'desc'    => '',
			'id'      => 'ywcm_show_in_cart',
			'default' => 'yes',
			'type'    => 'checkbox',
		),

		'show_in_checkout'             => array(
			'name'    => __( 'Show in checkout page', 'yith-woocommerce-cart-messages' ),
			'desc'    => '',
			'id'      => 'ywcm_show_in_checkout',
			'default' => 'yes',
			'type'    => 'checkbox',
		),

		'show_in_shop_page'            => array(
			'name'    => __( 'Show in shop page', 'yith-woocommerce-cart-messages' ),
			'desc'    => '',
			'id'      => 'ywcm_show_in_shop_page',
			'default' => 'no',
			'type'    => 'checkbox',
		),

		'show_in_single_product'       => array(
			'name'    => __( 'Show in single product page', 'yith-woocommerce-cart-messages' ),
			'desc'    => '',
			'id'      => 'ywcm_show_in_single_product',
			'default' => 'no',
			'type'    => 'checkbox',
		),


		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_general_end',
		),
	),
);
