<?php
/**
 * General options tab
 *
 * @package YITH\MinimumMaximumQuantity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$calculate_gift_card = '';
$integration_options = array();

if ( ywmmq_is_wcpb_active() || ywmmq_is_wraq_active() ) {
	$integration_options['ywmmq_integrations_title'] = array(
		'name' => esc_html__( 'Plugin Integrations', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type' => 'title',
	);
	if ( ywmmq_is_wcpb_active() ) {
		$integration_options['ywmmq_bundle_quantity'] = array(
			'name'      => esc_html__( 'Product Bundles', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'desc'      => esc_html__( 'Cart, category and tag quantity restrictions for product bundles', 'yith-woocommerce-minimum-maximum-quantity' ),
			'options'   => array(
				'bundle'   => esc_html__( 'Apply only to the bundle', 'yith-woocommerce-minimum-maximum-quantity' ),
				'elements' => esc_html__( 'Apply only to the bundle items', 'yith-woocommerce-minimum-maximum-quantity' ),
			),
			'id'        => 'ywmmq_bundle_quantity',
			'default'   => 'bundle',
		);
	}
	if ( ywmmq_is_wraq_active() ) {
		$integration_options['ywmmq_enable_rules_on_quotes'] = array(
			'name'      => esc_html__( 'Apply rules also to quote requests', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_enable_rules_on_quotes',
			'default'   => 'no',
		);
	}
	$integration_options['ywmmq_integrations_end'] = array(
		'type' => 'sectionend',
	);
}

if ( ywmmq_is_ywgc_active() ) {
	$calculate_gift_card = array(
		'name'      => esc_html__( 'Prevent Gift Cards calculation', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => esc_html__( 'By enabling this option Gift Cards won\'t be calculated on the amount limits', 'yith-woocommerce-minimum-maximum-quantity' ),
		'id'        => 'ywmmq_cart_value_calculate_giftcard',
		'default'   => 'no',
		'deps'      => array(
			'id'    => 'ywmmq_cart_value_limit',
			'value' => 'yes',
			'type'  => 'hide-disable',
		),
	);
}

return array(
	'general' => array_merge(
		$integration_options,
		array(
			'ywmmq_cart_section_title'            => array(
				'name' => esc_html__( 'Cart restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type' => 'title',
			),
			'ywmmq_cart_quantity_limit'           => array(
				'name'      => esc_html__( 'Enable cart quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_cart_quantity_limit',
				'default'   => 'yes',
			),
			'ywmmq_cart_minimum_quantity'         => array(
				'name'              => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_cart_minimum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_cart_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_cart_maximum_quantity'         => array(
				'name'              => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_cart_maximum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_cart_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_cart_step_quantity'            => array(
				'name'              => esc_html__( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Force users to purchase products only in groups of x items (Ex. groups of 6 units)', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_cart_step_quantity',
				'default'           => '1',
				'deps'              => array(
					'id'    => 'ywmmq_cart_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_cart_value_limit'              => array(
				'name'      => esc_html__( 'Enable cart spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_cart_value_limit',
				'default'   => 'yes',
			),
			'ywmmq_cart_minimum_value'            => array(
				'name'              => esc_html__( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_cart_minimum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_cart_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_cart_maximum_value'            => array(
				'name'              => esc_html__( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Maximum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_cart_maximum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_cart_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_cart_value_shipping'           => array(
				'name'      => esc_html__( 'Include shipping rates and relative fees.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_cart_value_shipping',
				'default'   => 'no',
				'deps'      => array(
					'id'    => 'ywmmq_cart_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
			),
			'ywmmq_cart_value_calculate_coupons'  => array(
				'name'      => esc_html__( 'Prevent coupons calculation', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html__( 'By enabling this option coupons won\'t be calculated on the amount limits', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'        => 'ywmmq_cart_value_calculate_coupons',
				'default'   => 'no',
				'deps'      => array(
					'id'    => 'ywmmq_cart_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
			),
			'ywmmq_cart_value_calculate_giftcard' => $calculate_gift_card,
			'ywmmq_cart_section_end'              => array(
				'type' => 'sectionend',
			),
			'ywmmq_product_section_title'         => array(
				'name' => esc_html__( 'Product restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type' => 'title',
			),
			'ywmmq_product_quantity_limit'        => array(
				'name'      => esc_html__( 'Enable product quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_product_quantity_limit',
				'default'   => 'no',
			),
			'ywmmq_product_minimum_quantity'      => array(
				'name'              => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Minimum number of items required for each product. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_product_minimum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_product_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_product_maximum_quantity'      => array(
				'name'              => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Maximum quantity allowed for each single product. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_product_maximum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_product_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_product_step_quantity'         => array(
				'name'              => esc_html__( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Allow your users to select products only in groups of x items (e.g. The product YITH Pen can be bought only in groups of 6)', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_product_step_quantity',
				'default'           => '1',
				'deps'              => array(
					'id'    => 'ywmmq_product_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="1" required',
			),
			'ywmmq_product_variations_unlocked'   => array(
				'name'      => esc_html__( 'Unlock variation quantities', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html__(
					'Enable to calculate the quantity of a variable product as the sum of all variations (e.g. minimum quantity 10 out of all variations). If disabled, the quantity restrictions will apply to each variation (e.g. minimum quantity 10 for every variation). 
This option can be overridden on a product level so you can set up a custom quantity value for every single single variation (e.g. minimum quantity 7 for "Blue" variation, and 3 for "Yellow" variation).',
					'yith-woocommerce-minimum-maximum-quantity'
				),
				'id'        => 'ywmmq_product_variations_unlocked',
				'default'   => 'no',
				'deps'      => array(
					'id'    => 'ywmmq_product_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
			),
			'ywmmq_product_section_end'           => array(
				'type' => 'sectionend',
			),
			'ywmmq_category_section_title'        => array(
				'name' => esc_html__( 'Category restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type' => 'title',
			),
			'ywmmq_category_quantity_limit'       => array(
				'name'      => esc_html__( 'Enable category quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_category_quantity_limit',
				'default'   => 'no',
			),
			'ywmmq_category_minimum_quantity'     => array(
				'name'              => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_category_minimum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_category_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_category_maximum_quantity'     => array(
				'name'              => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_category_maximum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_category_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_category_step_quantity'        => array(
				'name'              => esc_html__( 'Category quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Allow users to select products belonging to a specific category only in groups of x items (e.g. all pens can be purchased only in groups of 6)', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_category_step_quantity',
				'default'           => '1',
				'deps'              => array(
					'id'    => 'ywmmq_category_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="1" required',
			),
			'ywmmq_category_value_limit'          => array(
				'name'      => esc_html__( 'Enable category spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_category_value_limit',
				'default'   => 'no',
			),
			'ywmmq_category_minimum_value'        => array(
				'name'              => esc_html__( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_category_minimum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_category_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_category_maximum_value'        => array(
				'name'              => esc_html__( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Maximum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_category_maximum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_category_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_category_section_end'          => array(
				'type' => 'sectionend',
			),
			'ywmmq_tag_section_title'             => array(
				'name' => esc_html__( 'Tag restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type' => 'title',
			),
			'ywmmq_tag_quantity_limit'            => array(
				'name'      => esc_html__( 'Enable tag quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_tag_quantity_limit',
				'default'   => 'no',
			),
			'ywmmq_tag_minimum_quantity'          => array(
				'name'              => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_tag_minimum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_tag_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_tag_maximum_quantity'          => array(
				'name'              => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_tag_maximum_quantity',
				'default'           => '0',
				'deps'              => array(
					'id'    => 'ywmmq_tag_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_tag_step_quantity'             => array(
				'name'              => esc_html__( 'Tag quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'number',
				'desc'              => esc_html__( 'Allow users to select products with a specific tag only in groups of x items (e.g. Products tagged "Small" can be purchased only in groups of 12)', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_tag_step_quantity',
				'default'           => '1',
				'deps'              => array(
					'id'    => 'ywmmq_tag_quantity_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="1" required',
			),
			'ywmmq_tag_value_limit'               => array(
				'name'      => esc_html__( 'Enable tag spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => '',
				'id'        => 'ywmmq_tag_value_limit',
				'default'   => 'no',
			),
			'ywmmq_tag_minimum_value'             => array(
				'name'              => esc_html__( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_tag_minimum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_tag_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_tag_maximum_value'             => array(
				'name'              => esc_html__( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'type'              => 'yith-field',
				'yith-type'         => 'text',
				'desc'              => esc_html__( 'Maximum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
				'id'                => 'ywmmq_tag_maximum_value',
				'default'           => '0',
				'class'             => 'wc_input_price',
				'deps'              => array(
					'id'    => 'ywmmq_tag_value_limit',
					'value' => 'yes',
					'type'  => 'hide-disable',
				),
				'custom_attributes' => 'min="0" required',
			),
			'ywmmq_tag_section_end'               => array(
				'type' => 'sectionend',
			),
		)
	),
);
