<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

$section1 = array(
	'message_single_product_title'     => array(
		'name' => __( 'Single Product Message Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_single_product_title',
	),

	'enabled_single_product_message'   => array(
		'name'      => __( 'Enable Single Product Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_single_product_message',
	),

	'single_product_message_position'  => array(
		'name'      => __( 'Message position', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'type'      => 'yith-field',
		'options'   => array(
			'before_add_to_cart' => __( 'Before "Add to cart" button', 'yith-woocommerce-points-and-rewards' ),
			'after_add_to_cart'  => __( 'After "Add to cart" button', 'yith-woocommerce-points-and-rewards' ),
			'before_excerpt'     => __( 'Before excerpt', 'yith-woocommerce-points-and-rewards' ),
			'after_excerpt'      => __( 'After excerpt', 'yith-woocommerce-points-and-rewards' ),
			'after_meta'         => __( 'After product meta', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'before_add_to_cart',
		'id'        => 'ywpar_single_product_message_position',
		'deps'      => array(
			'id'    => 'ywpar_enabled_single_product_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'single_product_message'           => array(
		'name'      => __( 'Single Product Page Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => _x( '{points} number of points earned;<br>{points_label} label of points;<br>{price_discount_fixed_conversion} the value corresponding to points ','do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x( 'If you purchase this product you will earn <strong>{points}</strong> {points_label} worth {price_discount_fixed_conversion}!','do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_single_product_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_single_product_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_single_product_title_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_single_product_title_end',
	),

	// MESSAGE IN LOOP
	'message_loop_title'               => array(
		'name' => __( 'Show Message in Loop', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_loop_title',
	),

	'enabled_loop_message'             => array(
		'name'      => __( 'Show Message in Loop', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_enabled_loop_message',
	),

	'loop_message'                     => array(
		'name'      => __( 'Loop Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => _x( '{points} number of points earned;<br>{points_label} label of points;<br>{price_discount_fixed_conversion} the value corresponding to points','do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x( '<strong>{points}</strong> {points_label}', 'do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_loop_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_loop_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_loop_title_end'           => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_loop_title_end',
	),

	// MESSAGE ON CART
	'message_on_cart_title'            => array(
		'name' => __( 'Show Message on Cart page', 'yith-woocommerc e-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_on_cart_title',
	),

	'enabled_cart_message'             => array(
		'name'      => __( 'Show Message in Cart', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_cart_message',
	),

	'cart_message'                     => array(
		'name'      => __( 'Cart Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x( 'If you proceed to checkout, you will earn <strong>{points}</strong> {points_label}!', 'do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_cart_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_cart_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_on_cart_title_end'        => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_on_cart_title_end',
	),

	// MESSAGE ON CHECKOUT
	'message_on_checkout_title'        => array(
		'name' => __( 'Show Message on Checkout Page', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_on_checkout_title',
	),
	'enabled_checkout_message'         => array(
		'name'      => __( 'Show Message in Checkout', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_checkout_message',
	),

	'checkout_message'                 => array(
		'name'      => __( 'Checkout Message', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x( 'If you proceed to checkout, you will earn <strong>{points}</strong> {points_label}!', 'do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_checkout_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_checkout_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_on_checkout_title_end'    => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_on_checkout_title_end',
	),

	'message_reward_title'             => array(
		'name' => __( 'Show Reward Message in Cart/Checkout', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_message_reward_title',
	),

	'enabled_rewards_cart_message'     => array(
		'name'      => __( 'Show Reward Message in Cart/Checkout', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enabled_rewards_cart_message',
	),

	'rewards_cart_message'             => array(
		'name'      => __( 'Reward Message on Cart/Checkout page', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'yith-type' => 'textarea',
		'type'      => 'yith-field',
		'default'   => _x( 'Use <strong>{points}</strong> {points_label} for a <strong>{max_discount}</strong> discount on this order!', 'do not translate the text inside the brackets', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_rewards_cart_message',
		'deps'      => array(
			'id'    => 'ywpar_enabled_rewards_cart_message',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'message_reward_title_end'         => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_message_reward_title_end',
	),


);

return array( 'messages' => $section1 );
