<?php
/**
 * General options
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$settings = array(

	'general' => array(

		'header' => array(

			array(
				'name' => __( 'General Settings', 'ywdpd' ),
				'type' => 'title',
			),

			array( 'type' => 'close' ),
		),


		'settings' => array(

			array( 'type' => 'open' ),

			array(
				'id'   => 'enabled',
				'name' => __( 'Enable Dynamic Pricing and Discounts', 'ywdpd' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'yes',
			),

			array(
				'id'   => 'coupon_label',
				'name' => __( 'Coupon Label', 'ywdpd' ),
				'desc' => __( 'Name of the coupon showed in cart if there are discounts in cart (add a single word)', 'ywdpd' ),
				'type' => 'text',
				'std'  => __( 'DISCOUNT', 'ywdpd' ),
			),


			array(
				'id'   => 'show_note_on_products',
				'name' => __( 'Display notes on products', 'ywdpd' ),
				'desc' => __( 'Display notes on "Apply to" products and on "Apply Adjustment to" products - available for price rules', 'ywdpd' ),
				'type' => 'on-off',
				'std'  => 'no',
			),
			array(
				'id'      => 'show_note_on_products_place',
				'name'    => __( 'Position of notes in products', 'ywdpd' ),
				'desc'    => __( 'Position of notes on "Apply to" products and on "Apply Adjustment to" products - available for price rules', 'ywdpd' ),
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'before_add_to_cart' => __( 'Before "Add to cart" button', 'ywdpd' ),
					'after_add_to_cart'  => __( 'After "Add to cart" button', 'ywdpd' ),
					'before_excerpt'     => __( 'Before excerpt', 'ywdpd' ),
					'after_excerpt'      => __( 'After excerpt', 'ywdpd' ),
					'after_meta'         => __( 'After product meta', 'ywdpd' ),

				),
				'std'     => 'before_add_to_cart',
			),

			array(
				'id'   => 'show_quantity_table',
				'name' => __( 'Display Quantity Table', 'ywdpd' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'no',
			),
			array(
				'id'   => 'update_price_on_qty',
				'name' => __( 'Change product price when customer changes quantity', 'ywdpd' ),
				'desc' => __( 'Every time the customer changes the quantity, the updated price will be showed', 'ywdpd' ),
				'type' => 'on-off',
				'std'  => 'yes'
			),
			array(
				'id'   => 'default_qty_selected',
				'name' => __( 'Select the default quantity in the table', 'ywdpd' ),
				'desc' => __('Automatically select the first active quantity rule','ywdpd'),
				'type' => 'on-off',
				'std'  => 'no'
			),

			array(
				'id'   => 'show_quantity_table_schedule',
				'name' => __( 'Display Expiring Date In Quantity Table', 'ywdpd' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'yes',
			),

			array(
				'id'   => 'show_quantity_table_label',
				'name' => __( 'Quantity Table Title', 'ywdpd' ),
				'desc' => __( 'Title of the Quantity Table', 'ywdpd' ),
				'type' => 'text',
				'std'  => __( 'Discount per Quantity', 'ywdpd' ),
			),
			'quantity_table_orientation' => array(
				'id'   => 'quantity_table_orientation',
				'name' => __( 'Show the quantity table', 'ywdpd' ),
				'desc' => __( 'Choose how show the table price', 'ywdpd' ),
				'type' => 'radio',

				'options' => array(
					'vertical'   => __( 'Vertical', 'ywdpd' ),
					'horizontal' => __( 'Horizontal', 'ywdpd' ),
				),
				'std'     => 'horizontal',
			),
			array(
				'id'   => 'show_quantity_table_label_quantity',
				'name' => __( 'Label for Quantity', 'ywdpd' ),
				'desc' => '',
				'type' => 'text',
				'std'  => __( 'Quantity', 'ywdpd' ),
			),

			array(
				'id'   => 'show_quantity_table_label_price',
				'name' => __( 'Label for Price', 'ywdpd' ),
				'desc' => '',
				'type' => 'text',
				'std'  => __( 'Price', 'ywdpd' ),
			),

			array(
				'id'      => 'show_quantity_table_place',
				'name'    => __( 'Display Quantity Table Position', 'ywdpd' ),
				'desc'    => '',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'before_add_to_cart' => __( 'Before "Add to cart" button', 'ywdpd' ),
					'after_add_to_cart'  => __( 'After "Add to cart" button', 'ywdpd' ),
					'before_excerpt'     => __( 'Before excerpt', 'ywdpd' ),
					'after_excerpt'      => __( 'After excerpt', 'ywdpd' ),
					'after_meta'         => __( 'After product meta', 'ywdpd' ),

				),
				'std'     => 'before_add_to_cart',
			),

			array(
				'id'   => 'show_minimum_price',
				'name' => __( 'Show minimum price for products with quantity discount enabled', 'ywdpd' ),
				'desc' => __( 'The discount is visible only if the discount starts from quantity equal to 1', 'ywdpd' ),
				'type' => 'on-off',
				'std'  => 'no',
			),
			array(
				'id'   => 'price_format',
				'name' => __( 'Price format', 'ywdpd' ),
				'desc' => __( 'You can use: %original_price%, %discounted_price%, %percentual_discount%. Note: enable the above option to see the minimum discounted amount', 'ywdpd' ),
				'type' => 'text',
				'std'  => __( '<del>%original_price%</del> %discounted_price%', 'ywdpd' ),
			),

			array(
				'id'      => 'calculate_discounts_tax',
				'name'    => __( 'Calculate cart discount starting from', 'ywdpd' ),
				'desc'    => '',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'tax_excluded' => __( 'Subtotal - tax excluded', 'ywdpd' ),
					'tax_included' => __( 'Subtotal - tax included', 'ywdpd' ),
				),
				'std'     => 'tax_excluded',
			),

			array(
				'id'   => 'enable_shop_manager',
				'name' => __( 'Enable Shop Manager to edit these options', 'ywdpd' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'no',
			),

		),
	),
);

if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	$settings['general']['settings'][] = array(
		'id'   => 'wpml_extend_to_translated_object',
		'name' => __( 'Extend the rules to translated contents', 'ywdpd' ),
		'desc' => '',
		'type' => 'on-off',
		'std'  => 'no',
	);
}

$settings['general']['settings'][] = array( 'type' => 'close' );

return apply_filters( 'yith_ywdpd_panel_settings_options', $settings );
