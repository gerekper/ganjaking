<?php
/**
 * Cart discount metabox options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$discount_pricing_mode = ywdpd_discount_pricing_mode();
$last_priority         = ywdpd_get_last_priority( 'cart' ) + 1;
$key                   = uniqid();
return array(
	'label'    => __( 'Pricing Discount Settings', 'ywdpd' ),
	'pages'    => 'ywdpd_discount', // or array( 'post-type1', 'post-type2').
	'context'  => 'normal', // ('normal', 'advanced', or 'side').
	'priority' => 'default',
	'tabs'     => array(

		'settings' => array(
			'label'  => __( 'Settings', 'ywdpd' ),
			'fields' => apply_filters(
				'ywdpd_cart_discount_metabox',
				array(
					'discount_type'       => array(
						'type' => 'hidden',
						'std'  => 'cart',
						'val'  => 'cart',
					),
					'key'                 => array(
						'type' => 'hidden',
						'std'  => $key,
						'val'  => $key,
					),
					'active'              => array(
						'label' => __( 'Active', 'ywdpd' ),
						'desc'  => __( 'Choose if activate or deactivate', 'ywdpd' ),
						'type'  => 'onoff',
						'std'   => 'yes',
					),
					'priority'            => array(
						'label' => __( 'Priority', 'ywdpd' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => $last_priority,
					),
					'schedule_from'       => array(
						'label' => __( 'Discount Schedule from', 'ywdpd' ),
						'desc'  => '',
						'type'  => 'text',
					),
					'schedule_to'         => array(
						'label' => __( 'Discount Schedule to', 'ywdpd' ),
						'desc'  => '',
						'type'  => 'text',
					),
					'discount_combined'   => array(
						'label' => __( 'Discount Combined', 'ywdpd' ),
						'desc'  => __( 'Choose to combine this cart discount with other coupons', 'ywdpd' ),
						'type'  => 'onoff',
						'std'   => 'no',
					),
					'allow_free_shipping' => array(
						'label' => __( 'Allow free shipping', 'ywdpd' ),
						'type'  => 'onoff',
						'std'   => 'no',
						'desc'  => __( 'Enable this option if the coupon grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'ywdpd' ),
					),


					/***************
					 * DISCOUNT TABLES
					 */
					'rules'               => array(
						'label'   => __( 'Discount Rules', 'ywdpd' ),
						'desc'    => '',
						'type'    => 'cart_discount',
						'private' => false,
					),

					'discount_rule'       => array(
						'label' => __( 'Discount Type', 'ywdpd' ),
						'desc'  => '',
						'type'  => 'cart_discount_type',
					),

				)
			),

		),
	),
);
