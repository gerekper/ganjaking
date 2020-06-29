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
$currency = get_woocommerce_currency();
$section1 = array(
	'points_title'                                      => array(
		'name' => __( 'Awarding of points', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_points_title',
	),

	'enable_points_upon_sales'                          => array(
		'name'      => __( 'Enable automatic awarding of points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you disable this option, you will still be able to assign points manually.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enable_points_upon_sales',
	),

	'earn_points_conversion_rate'                       => array(
		'name'      => __( 'Assign points for every product purchased', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __(
			'Choose how many points per product will be earned based on the currency.
Please, note: points are awarded on a product basis and not on the cart total. ',
			'yith-woocommerce-points-and-rewards'
		),
		'yith-type' => 'options-conversion',
		'type'      => 'yith-field',
		'default'   => array(
			$currency => array(
				'points' => 1,
				'money'  => 10,
			),
		),
		'id'        => 'ywpar_earn_points_conversion_rate',
	),

	'user_role_enabled'                                 => array(
		'name'      => __( 'User Roles enabled to earn points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select user roles entitled to earn points', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'css'       => 'min-width:300px',
		'multiple'  => true,
		'id'        => 'ywpar_user_role_enabled',
		'options'   => array_merge( array( 'all' => __( 'All', 'yith-woocommerce-points-and-rewards' ) ), yith_ywpar_get_roles() ),
		'default'   => array( 'all' ),
	),


	'enable_conversion_rate_for_role'                   => array(
		'name'      => __( 'Assign different amounts of points based on the user role', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose the rule you want to apply to assign a different number of points according to the user roles', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywpar_enable_conversion_rate_for_role',
		'default'   => 'no',
	),

	'conversion_rate_level'                             => array(
		'name'      => '',
		'desc'      => __( 'Priority Level Conversion', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'radio',
		'type'      => 'yith-field',
		'options'   => array(
			'low'  => __( 'Use the rule with the Lowest Conversion Rate', 'yith-woocommerce-points-and-rewards' ),
			'high' => __( 'Use the rule with the Highest Conversion Rate', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'high',
		'id'        => 'ywpar_conversion_rate_level',
		'deps'      => array(
			'id'    => 'ywpar_enable_conversion_rate_for_role',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'earn_points_role_conversion_rate'                  => array(
		'name'                   => '',
		'desc'                   => __( '', 'yith-woocommerce-points-and-rewards' ),
		'yith-type'              => 'options-role-conversion',
		'type'                   => 'yith-field',
		'default'                => array(
			'role_conversion' => array(
				array(
					'role'    => 'administrator',
					$currency => array(
						'points' => 1,
						'money'  => 10,
					),
				),
			),
		),
		'id'                     => 'ywpar_earn_points_role_conversion_rate',
		'deps'                   => array(
			'id'    => 'ywpar_enable_conversion_rate_for_role',
			'value' => 'yes',
			'type'  => 'hide',
		),
		'yith-sanitize-callback' => 'ywpar_option_role_convertion_sanitize',
	),

	'order_status_to_earn_points'                       => array(
		'name'      => __( 'Choose when assigning points to customers', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select on which order status awarding points to customers.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'css'       => 'min-width:300px',
		'multiple'  => true,
		'id'        => 'ywpar_order_status_to_earn_points',
		'options'   => ywpar_get_order_status_to_earn_points(),
		'default'   => array( 'woocommerce_order_status_completed', 'woocommerce_payment_complete', 'woocommerce_order_status_processing' ),
	),
	'exclude_product_on_sale'                           => array(
		'name'      => __( 'Exclude on-sale products from generating points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable if you don\'t want to assign points to the purchase of on-sale products', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_exclude_product_on_sale',
		'default'   => 'no',
	),
	'assign_points_to_registered_guest'                 => array(
		'name'      => __( 'Assign points to guests that has a registered billing email', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose if assign points to guests if the billing email match with a registered user email.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_assign_points_to_registered_guest',
		'default'   => 'no',
	),
	'assign_older_orders_points_to_new_registered_user' => array(
		'name'      => __( 'Check past orders', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose whether to assign points to newly registered users if they have used the same billing email address for previous orders, so to calculate the total points including orders placed as guests.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_assign_older_orders_points_to_new_registered_user',
		'default'   => 'no',
	),
	'remove_points_coupon'                              => array(
		'name'      => __( 'Remove points when coupons are used', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you use coupons, their value will be removed from cart total and consequently points gained will be reduced as well.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywpar_remove_points_coupon',
		'default'   => 'yes',
	),
	'remove_point_refund_order'                         => array(
		'name'      => __( 'Enable removal of points for total or partial refunds', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable to remove points when applying a total or partial refund of the order', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_remove_point_refund_order',
		'default'   => 'yes',
	),
	'round_points_down_up'                              => array(
		'name'      => __( 'Points Rounding', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select how to round the points. For example, if points are 1.5 and Round Up is selected, points will be 2. If Round Down is selected, points will be 1.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'id'        => 'ywpar_points_round_type',
		'options'   => array(
			'up'   => __( 'Round Up', 'yith-woocommerce-points-and-rewards' ),
			'down' => __( 'Round Down', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'down',
		'deps'      => array(
			'id'    => 'ywpar_enable_points_upon_sales',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'disable_point_earning_while_reedeming'             => array(
		'name'      => __( 'Disable point awarding while redeeming', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Do not award points on orders in which the user is redeeming points.', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_disable_earning_while_reedeming',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'deps'      => array(
			'id'    => 'ywpar_enable_points_upon_sales',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),
	'points_title_end'                                  => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_points_title_end',
	),

	'rewards_point_option'                              => array(
		'name' => __( 'Redeeming points', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_rewards_point_option',
	),

	'enable_rewards_points'                             => array(
		'name'      => __( 'Enable points redemption', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'If you disable this option, you will still be able to manage points manually.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enable_rewards_points',
	),

	'conversion_rate_method'                            => array(
		'name'      => __( 'Reward Conversion Method', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose how to apply the discount. The discount can either be a percent or a fixed amount.', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'default'   => 'fixed',
		'options'   => array(
			'fixed'      => __( 'Fixed Price Discount', 'yith-woocommerce-points-and-rewards' ),
			'percentage' => __( 'Percentage Discount', 'yith-woocommerce-points-and-rewards' ),
		),
		'id'        => 'ywpar_conversion_rate_method',
	),

	'rewards_conversion_rate'                           => array(
		'name'      => __( 'Reward Conversion Rate', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose how to calculate the discount when customers use their available points.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'options-conversion',
		'type'      => 'yith-field',
		'class'     => 'fixed_method',
		'default'   => array(
			$currency => array(
				'points' => 100,
				'money'  => 1,
			),
		),
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'fixed',
			'type'  => 'hide',
		),
		'id'        => 'ywpar_rewards_conversion_rate',
	),

	'rewards_percentage_conversion_rate'                => array(
		'name'      => __( 'Reward Conversion Rate', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose how to calculate the discount when customers use their available points.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'options-percentage-conversion',
		'type'      => 'yith-field',
		'default'   => array(
			$currency => array(
				'points'   => 20,
				'discount' => 5,
			),
		),
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'percentage',
			'type'  => 'hide',
		),
		'id'        => 'ywpar_rewards_percentual_conversion_rate',
	),


	'user_role_redeem_enabled'                          => array(
		'name'      => __( 'User Role enabled to redeem points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select user roles entitled to redeem points', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'css'       => 'min-width:300px',
		'multiple'  => true,
		'id'        => 'ywpar_user_role_redeem_enabled',
		'options'   => array_merge( array( 'all' => __( 'All', 'yith-woocommerce-points-and-rewards' ) ), yith_ywpar_get_roles() ),
		'default'   => array( 'all' ),
	),

	'rewards_points_for_role'                           => array(
		'name'      => __( 'Assign different amounts of points based on the user role', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Choose the rule you want to apply to REDEEM a different number of points according to the user roles', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'id'        => 'ywpar_rewards_points_for_role',
		'default'   => 'no',
	),

	'rewards_points_level'                              => array(
		'name'      => '',
		'desc'      => __( 'Priority Level for Reward Points', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'radio',
		'type'      => 'yith-field',
		'options'   => array(
			'low'  => __( 'Use the rule with lowest rewards', 'yith-woocommerce-points-and-rewards' ),
			'high' => __( 'Use the rule with highest rewards', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'high',
		'id'        => 'ywpar_rewards_points_level',
		'deps'      => array(
			'id'    => 'ywpar_rewards_points_for_role',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'rewards_points_role_rewards_fixed_conversion_rate' => array(
		'name'                   => '',
		'desc'                   => __( '', 'yith-woocommerce-points-and-rewards' ),
		'yith-type'              => 'options-role-conversion',
		'type'                   => 'yith-field',
		'default'                => array(
			'role_conversion' => array(
				array(
					'role'    => 'administrator',
					$currency => array(
						'points' => 1,
						'money'  => 10,
					),
				),
			),
		),
		'yith-sanitize-callback' => 'ywpar_option_role_convertion_sanitize',
		'id'                     => 'ywpar_rewards_points_role_rewards_fixed_conversion_rate',
	),

	'rewards_points_role_rewards_percentage_conversion_rate' => array(
		'name'                   => '',
		'desc'                   => __( '', 'yith-woocommerce-points-and-rewards' ),
		'yith-type'              => 'options-role-percentage-conversion',
		'type'                   => 'yith-field',
		'default'                => array(
			'role_conversion' => array(
				array(
					'role'    => 'administrator',
					$currency => array(
						'points'   => 20,
						'discount' => 5,
					),
				),
			),
		),
		'yith-sanitize-callback' => 'ywpar_option_role_convertion_sanitize',
		'id'                     => 'ywpar_rewards_points_role_rewards_percentage_conversion_rate',

	),

	'autoapply_points_cart_checkout'                    => array(
		'name'      => __( 'Automatically reedem points on Cart/Checkout Page', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'The customer points are automatically applied on the cart/checkout page', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
		'id'        => 'ywpar_autoapply_points_cart_checkout',


	),

	'rewards_point_option_end'                          => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_rewards_point_option_end',
	),

	'min_max_option'                                    => array(
		'name' => __( 'Redeeming points - Restrictions', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_min_max_option',
	),


	// showed if conversion_rate_method == percentage.
	'max_percentual_discount'                           => array(
		'name'      => __( 'Maximum discount', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( '( in %) Set maximum discount percentage allowed in cart when redeeming points.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'text',
		'type'      => 'yith-field',
		'id'        => 'ywpar_max_percentual_discount',
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'percentage',
			'type'  => 'hide',
		),
		'default'   => 50,
	),


	// showed if conversion_rate_method == fixed.
	'max_points_discount'                               => array(
		'name'      => __( 'Maximum discount', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Set maximum product discount allowed in cart when redeeming points. Leave blank to disable.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'text',
		'type'      => 'yith-field',
		'id'        => 'ywpar_max_points_discount',
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'fixed',
			'type'  => 'hide',
		),
	),

	// showed if conversion_rate_method == fixed.
	'minimum_amount_discount_to_redeem'                 => array(
		'name'      => __( 'Minimum Discount Required to Redeem', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Set minimum amount of discount to redeem points. Leave blank to disable.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'text',
		'type'      => 'yith-field',
		'id'        => 'ywpar_minimum_amount_discount_to_redeem',
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'fixed',
			'type'  => 'hide',
		),
	),

	// showed if conversion_rate_method == fixed.
	'max_points_product_discount'                       => array(
		'name'      => __( 'Maximum discount for single product', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Set maximum product discount allowed when redeeming points per product. Leave blank to disable.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'text',
		'type'      => 'yith-field',
		'id'        => 'ywpar_max_points_product_discount',
		'deps'      => array(
			'id'    => 'ywpar_conversion_rate_method',
			'value' => 'fixed',
			'type'  => 'hide',
		),
	),


	'minimum_amount_to_redeem'                          => array(
		'name'      => __( 'Minimum Amount to Redeem', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Set minimum amount in the cart to redeem points. Leave blank to disable.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'text',
		'type'      => 'yith-field',
		'default'   => '',
		'id'        => 'ywpar_minimum_amount_to_redeem',
	),

	'allow_free_shipping_to_redeem'                     => array(
		'name'      => __( 'Allow free shipping to Redeem', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Check this box if the coupon grants free shipping. A free shipping method must be enabled and set up for your shipping zones to generate "a valid free shipping coupon".', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_allow_free_shipping_to_redeem',
		'default'   => 'no',
	),

	'other_coupons'                                     => array(
		'name'      => __( 'Redeem Points and WooCommerce Coupons', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Select if you want to allow the use of point-redemption coupons, WooCommerce coupons or both.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'radio',
		'type'      => 'yith-field',
		'options'   => array(
			'both'      => __( 'Use both coupons', 'yith-woocommerce-points-and-rewards' ),
			'ywpar'     => __( 'Use only points-redemption coupon', 'yith-woocommerce-points-and-rewards' ),
			'wc_coupon' => __( 'Use only WooCommerce coupons', 'yith-woocommerce-points-and-rewards' ),
		),
		'default'   => 'both',
		'id'        => 'ywpar_other_coupons',
	),

	'remove_point_order_deleted'                        => array(
		'name'      => __( 'Enable points removal for cancelled orders', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable if you want to remove earned points when cancelling the order', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_remove_point_order_deleted',
		'default'   => 'yes',
	),

	'reassing_redeemed_points_refund_order'             => array(
		'name'      => __( 'Reassign points for total or partial refunds', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable if you want to reassign customers the redeemed points after a total refund.', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_reassing_redeemed_points_refund_order',
		'default'   => 'no',
	),

	'coupon_delete_after_use'                           => array(
		'name'      => __( 'Delete the coupon when used', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable to delete the coupon after its use', 'yith-woocommerce-points-and-rewards' ),
		'yith-type' => 'onoff',
		'type'      => 'yith-field',
		'id'        => 'ywpar_coupon_delete_after_use',
		'default'   => 'yes',
	),


	'min_max_option_end'                                => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_min_max_option_end',
	),

	'expiration_title'                                  => array(
		'name' => __( 'Expiration Options', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_expiration_title',
	),
	'enable_expiration_point'                           => array(
		'name'      => __( 'Enable points expiration', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => __( 'Enable if you want to set an expiration (in days) for the points', 'yith-woocommerce-points-and-rewards' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
		'id'        => 'ywpar_enable_expiration_point',
	),


	'days_before_expiration'                            => array(
		'desc'              => __( 'Points are valid for (days)', 'yith-woocommerce-points-and-rewards' ),
		'type'              => 'yith-field',
		'yith-type'         => 'number',
		'default'           => 0,
		'custom_attributes' => 'style="width:70px"',
		'id'                => 'ywpar_days_before_expiration',
		'deps'              => array(
			'id'    => 'ywpar_enable_expiration_point',
			'value' => 'yes',
			'type'  => 'hide',
		),
	),

	'expiration_title_end'                              => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_expiration_title_end',
	),


	'other_title'                                       => array(
		'name' => __( 'Other Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_other_title',
	),


	'apply_points_previous_order'                       => array(
		'name'            => __( 'Apply Points to Previous Orders', 'yith-woocommerce-points-and-rewards' ),
		'desc'            => __( 'Starting from - Optional: Leave blank to apply to all orders', 'yith-woocommerce-points-and-rewards' ),
		'type'            => 'yith-field',
		'yith-type'       => 'points-previous-order',
		'label'           => __( 'Apply Points', 'yith-woocommerce-points-and-rewards' ),
		'show_datepicker' => true,
		'id'              => 'ywpar_apply_points_previous_order',
	),

	// from 1.1.1.
	'reset_points'                                      => array(
		'name'         => __( 'Reset Points', 'yith-woocommerce-points-and-rewards' ),
		'desc'         => __( 'Click on the button to reset all the points earned and redeemed by customers', 'yith-woocommerce-points-and-rewards' ),
		'type'         => 'yith-field',
		'yith-type'    => 'text-button',
		'button-class' => 'ywrac_reset_points',
		'button-name'  => __( 'Reset Points', 'yith-woocommerce-points-and-rewards' ),
		'id'           => 'ywpar_reset_points',

	),


	'other_title_end'                                   => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_other_title_end',
	),
);

return apply_filters( 'ywpar_points_settings', array( 'points' => $section1 ) );
