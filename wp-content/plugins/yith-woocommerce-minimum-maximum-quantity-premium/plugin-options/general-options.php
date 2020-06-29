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
} // Exit if accessed directly

$bundles_title = $bundles_option = $bundles_end = $calculate_gift_card = $apply_to_quotes = '';

if ( YITH_WMMQ()->is_wcpb_active() ) {

	$bundles_title  = array(
		'name' => __( 'Product Bundles', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type' => 'title',
	);
	$bundles_option = array(
		'name'      => __( 'Cart, category and tag quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'desc'      => '',
		'options'   => array(
			'bundle'   => __( 'Apply only to the bundle', 'yith-woocommerce-minimum-maximum-quantity' ),
			'elements' => __( 'Apply only to the bundle items', 'yith-woocommerce-minimum-maximum-quantity' ),
		),
		'id'        => 'ywmmq_bundle_quantity',
		'default'   => 'bundle',
	);
	$bundles_end    = array(
		'type' => 'sectionend',
	);

}

if ( YITH_WMMQ()->is_ywgc_active() ) {
	$calculate_gift_card = array(
		'name'      => __( 'Prevent Gift Cards calculation', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => __( 'By enabling this option Gift Cards won\'t be calculated on the amount limits', 'yith-woocommerce-minimum-maximum-quantity' ),
		'id'        => 'ywmmq_cart_value_calculate_giftcard',
		'default'   => 'no',
		'deps'      => array(
			'id'    => 'ywmmq_cart_value_limit',
			'value' => 'yes',
			'type'  => 'hide-disable'
		),
	);
}

if ( YITH_WMMQ()->is_wraq_active() ) {

	$apply_to_quotes = array(
		'name'      => __( 'Apply rules also to quote requests', 'yith-woocommerce-minimum-maximum-quantity' ),
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'desc'      => '',
		'id'        => 'ywmmq_enable_rules_on_quotes',
		'default'   => 'no',
	);
}

return array(

	'general' => array(

		'ywmmq_main_section_title'     => array(
			'name' => __( 'Minimum Maximum Quantity settings', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_enable_plugin'          => array(
			'name'      => __( 'Enable YITH WooCommerce Minimum Maximum Quantity', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_enable_plugin',
			'default'   => 'yes',
		),
		'ywmmq_enable_rules_on_quotes' => $apply_to_quotes,
		'ywmmq_main_section_end'       => array(
			'type' => 'sectionend',
		),

		'ywmmq_bundles_title'   => $bundles_title,
		'ywmmq_bundle_quantity' => $bundles_option,
		'ywmmq_bundles_end'     => $bundles_end,

		'ywmmq_cart_section_title'            => array(
			'name' => __( 'Cart restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_cart_quantity_limit'           => array(
			'name'      => __( 'Enable cart quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_cart_quantity_limit',
			'default'   => 'yes',
		),
		'ywmmq_cart_minimum_quantity'         => array(
			'name'              => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_cart_minimum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_cart_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_cart_maximum_quantity'         => array(
			'name'              => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_cart_maximum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_cart_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_cart_step_quantity'            => array(
			'name'              => __( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Force users to purchase products only in groups of x items (Ex. groups of 6 units)', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_cart_step_quantity',
			'default'           => '1',
			'deps'              => array(
				'id'    => 'ywmmq_cart_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_cart_value_limit'              => array(
			'name'      => __( 'Enable cart spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_cart_value_limit',
			'default'   => 'yes',
		),
		'ywmmq_cart_minimum_value'            => array(
			'name'              => __( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_cart_minimum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_cart_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_cart_maximum_value'            => array(
			'name'              => __( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Allow your users to purchase products only in groups of x items (e.g. The cart can contains items only in groups of 6)', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_cart_maximum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_cart_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_cart_value_shipping'           => array(
			'name'      => __( 'Include shipping rates and relative fees.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_cart_value_shipping',
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywmmq_cart_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywmmq_cart_value_calculate_coupons'  => array(
			'name'      => __( 'Prevent coupons calculation', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'By enabling this option coupons won\'t be calculated on the amount limits', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'        => 'ywmmq_cart_value_calculate_coupons',
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywmmq_cart_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywmmq_cart_value_calculate_giftcard' => $calculate_gift_card,
		'ywmmq_cart_section_end'              => array(
			'type' => 'sectionend',
		),

		'ywmmq_product_section_title'    => array(
			'name' => __( 'Product restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_product_quantity_limit'   => array(
			'name'      => __( 'Enable product quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_product_quantity_limit',
			'default'   => 'no',
		),
		'ywmmq_product_minimum_quantity' => array(
			'name'              => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Minimum number of items required for each product. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_product_minimum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_product_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_product_maximum_quantity' => array(
			'name'              => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Maximum quantity allowed for each single product. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_product_maximum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_product_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_product_step_quantity'    => array(
			'name'              => __( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Allow your users to select products only in groups of x items (e.g. The product YITH Pen can be bought only in groups of 6)', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_product_step_quantity',
			'default'           => '1',
			'deps'              => array(
				'id'    => 'ywmmq_product_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="1" required'
		),
		'ywmmq_product_section_end'      => array(
			'type' => 'sectionend',
		),

		'ywmmq_category_section_title'    => array(
			'name' => __( 'Category restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_category_quantity_limit'   => array(
			'name'      => __( 'Enable category quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_category_quantity_limit',
			'default'   => 'no',
		),
		'ywmmq_category_minimum_quantity' => array(
			'name'              => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_category_minimum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_category_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_category_maximum_quantity' => array(
			'name'              => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_category_maximum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_category_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_category_step_quantity'    => array(
			'name'              => __( 'Category quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Allow users to select products belonging to a specific category only in groups of x items (e.g. all pens can be purchased only in groups of 6)', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_category_step_quantity',
			'default'           => '1',
			'deps'              => array(
				'id'    => 'ywmmq_category_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="1" required'
		),
		'ywmmq_category_value_limit'      => array(
			'name'      => __( 'Enable category spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_category_value_limit',
			'default'   => 'no',
		),
		'ywmmq_category_minimum_value'    => array(
			'name'              => __( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_category_minimum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_category_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_category_maximum_value'    => array(
			'name'              => __( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Maximum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_category_maximum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_category_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_category_section_end'      => array(
			'type' => 'sectionend',
		),

		'ywmmq_tag_section_title'    => array(
			'name' => __( 'Tag restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_tag_quantity_limit'   => array(
			'name'      => __( 'Enable tag quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_tag_quantity_limit',
			'default'   => 'no',
		),
		'ywmmq_tag_minimum_quantity' => array(
			'name'              => __( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Minimum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_tag_minimum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_tag_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_tag_maximum_quantity' => array(
			'name'              => __( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Maximum number of items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_tag_maximum_quantity',
			'default'           => '0',
			'deps'              => array(
				'id'    => 'ywmmq_tag_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_tag_step_quantity'    => array(
			'name'              => __( 'Tag quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => __( 'Allow users to select products with a specific tag only in groups of x items (e.g. Products tagged "Small" can be purchased only in groups of 12)', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_tag_step_quantity',
			'default'           => '1',
			'deps'              => array(
				'id'    => 'ywmmq_tag_quantity_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="1" required'
		),
		'ywmmq_tag_value_limit'      => array(
			'name'      => __( 'Enable tag spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_tag_value_limit',
			'default'   => 'no',
		),
		'ywmmq_tag_minimum_value'    => array(
			'name'              => __( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Minimum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_tag_minimum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_tag_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_tag_maximum_value'    => array(
			'name'              => __( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Maximum spend for items in cart. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'                => 'ywmmq_tag_maximum_value',
			'default'           => '0',
			'class'             => 'wc_input_price',
			'deps'              => array(
				'id'    => 'ywmmq_tag_value_limit',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'min="0" required'
		),
		'ywmmq_tag_section_end'      => array(
			'type' => 'sectionend',
		),

	)

);