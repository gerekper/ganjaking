<?php
/**
 * Pricing rules options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

return apply_filters(
	'yit_ywdpd_pricing_rules_options',
	array(
		'discount_mode'    => array(
			'bulk'          => __( 'Quantity Discount', 'ywdpd' ),
			'special_offer' => __( 'Special Offer', 'ywdpd' ),
			'exclude_items' => __( 'Exclude items from rules', 'ywdpd' ),
		),

		'quantity_based'   => array(
			'cart_line'                => __( 'Item quantity in cart line', 'ywdpd' ),
			'single_product'           => __( 'Single product', 'ywdpd' ),
			'single_variation_product' => __( 'Single product variation', 'ywdpd' ),
			'cumulative'               => __( 'Sum of all products in list or category list', 'ywdpd' ),
		),

		'apply_to'         => array(
			'all_products'             => __( 'All products', 'ywdpd' ),
			'products_list'            => __( 'Include a list of products', 'ywdpd' ),
			'products_list_excluded'   => __( 'Exclude a list of products', 'ywdpd' ),
			'categories_list'          => __( 'Include a list of categories', 'ywdpd' ),
			'categories_list_excluded' => __( 'Exclude a list of categories', 'ywdpd' ),
			'tags_list'                => __( 'Include a list of tags', 'ywdpd' ), // @since 1.1.0
			'tags_list_excluded'       => __( 'Exclude a list of tags', 'ywdpd' ), // @since 1.1.0
		),

		'apply_adjustment' => array(
			'same_product'             => __( 'Same product', 'ywdpd' ),
			'all_products'             => __( 'All products', 'ywdpd' ),
			'products_list'            => __( 'Include a list of products', 'ywdpd' ),
			'products_list_excluded'   => __( 'Exclude a list of products', 'ywdpd' ),
			'categories_list'          => __( 'Include a list of categories', 'ywdpd' ),
			'categories_list_excluded' => __( 'Exclude a list of categories', 'ywdpd' ),
			'tags_list'                => __( 'Include a list of tags', 'ywdpd' ), // @since 1.1.0
			'tags_list_excluded'       => __( 'Exclude a list of tags', 'ywdpd' ), // @since 1.1.0
		),

		'type_of_discount' => array(
			'percentage'  => __( 'Percentage Discount', 'ywdpd' ),
			'price'       => __( 'Price Discount', 'ywdpd' ),
			'fixed-price' => __( 'Fixed Price', 'ywdpd' ),
		),

		'user_rules'       => array(
			'everyone'                => __( 'Everyone', 'ywdpd' ),
			'role_list'               => __( 'Include a list of roles', 'ywdpd' ),
			'role_list_excluded'      => __( 'Exclude a list of roles', 'ywdpd' ),
			'customers_list'          => __( 'Include a list of customers', 'ywdpd' ),
			'customers_list_excluded' => __( 'Exclude a list of customers', 'ywdpd' ),
		),
	)
);
