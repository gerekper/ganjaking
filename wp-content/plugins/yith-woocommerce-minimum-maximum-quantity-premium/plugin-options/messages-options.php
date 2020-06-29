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

$query_args = array(
	'page' => isset( $_GET['page'] ) ? $_GET['page'] : '',
	'tab'  => 'howto',
);

$howto_url         = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
$placeholders_text = __( 'Allowed placeholders:', 'yith-woocommerce-minimum-maximum-quantity' );
$ph_reference_link = ' - <a href="' . $howto_url . '" target="_blank">' . __( 'More info', 'yith-woocommerce-minimum-maximum-quantity' ) . '</a>';
$ph_limit          = ' <b>{limit}</b>';
$ph_cart_quantity  = ' <b>{cart_quantity}</b>';
$ph_cart_value     = ' <b>{cart_value}</b>';
$ph_product_name   = ' <b>{product_name}</b>';
$ph_category_name  = ' <b>{category_name}</b>';
$ph_tag_name       = ' <b>{tag_name}</b>';
$ph_rules          = ' <b>{rules}</b>';
$ph_cart_quote     = ' <b>{cart_quote}</b>';

return array(

	'messages' => array(
		'ywmmq_rules_section_title' => array(
			'name' => __( 'Purchase rules', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_rules_enable'        => array(
			'name'      => __( 'Show rules on product page', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywmmq_rules_enable',
			'default'   => 'no',
		),
		'ywmmq_rules_position'      => array(
			'name'      => __( 'Position in product page', 'yith-woocommerce-minimum-maximum-quantity' ),
			'id'        => 'ywmmq_rules_position',
			'default'   => '2',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => __( 'Set the position in product detail page where showing rules.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'options'   => array(
				'0' => __( 'Before title', 'yith-woocommerce-minimum-maximum-quantity' ),
				'1' => __( 'After price', 'yith-woocommerce-minimum-maximum-quantity' ),
				'2' => __( 'Before "Add to cart"', 'yith-woocommerce-minimum-maximum-quantity' ),
				'3' => __( 'Before tabs', 'yith-woocommerce-minimum-maximum-quantity' ),
			),
			'deps'      => array(
				'id'    => 'ywmmq_rules_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
		),
		'ywmmq_rules_before_text'   => array(
			'name'              => __( 'Text before rules', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => __( 'Explanatory text shown before purchase rules.', 'yith-woocommerce-minimum-maximum-quantity' ) . ' ' . $placeholders_text . $ph_rules . $ph_reference_link,
			'id'                => 'ywmmq_rules_before_text',
			'default'           => __( 'The following rules are working: {rules}', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_rules_enable',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_rules_section_end'   => array(
			'type' => 'sectionend',
		),

		'ywmmq_message_section_title_cart'          => array(
			'name' => __( 'Cart Page Error Messages', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_message_min_cart_quantity_cart'      => array(
			'name'              => __( 'Minimum cart quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_quantity . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_cart_quantity_cart',
			'default'           => __( 'Your cart must contain at least {limit} products.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_cart_quantity_cart'      => array(
			'name'              => __( 'Maximum cart quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_quantity . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_cart_quantity_cart',
			'default'           => __( 'Your cart cannot contain more than {limit} products.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_cart_quantity_cart'      => array(
			'name'              => __( 'Cart quantity not allowed', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_quantity . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_step_cart_quantity_cart',
			'default'           => __( 'Your cart must contain products in group of {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_cart_value_cart'         => array(
			'name'              => __( 'Minimum cart spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_value . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_cart_value_cart',
			'default'           => __( 'Your cart totals must be at least {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_cart_value_cart'         => array(
			'name'              => __( 'Maximum cart spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_value . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_cart_value_cart',
			'default'           => __( 'Your cart totals cannot exceed {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_product_quantity_cart'   => array(
			'name'              => __( 'Minimum product quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_product_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_product_quantity_cart',
			'default'           => __( 'You must purchase at least {limit} units of {product_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_product_quantity_cart'   => array(
			'name'              => __( 'Maximum product quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_product_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_product_quantity_cart',
			'default'           => __( 'You cannot purchase more than {limit} units of {product_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_product_quantity_cart'  => array(
			'name'              => __( 'Product quantity not allowed', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_product_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_step_product_quantity_cart',
			'default'           => __( 'The product {product_name} can be purchased only in groups of {limit}.',
			                           'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_category_quantity_cart'  => array(
			'name'              => __( 'Minimum category quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_category_quantity_cart',
			'default'           => __( 'Your cart must contain at least {limit} products belonging to category {category_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_category_quantity_cart'  => array(
			'name'              => __( 'Maximum category quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_category_quantity_cart',
			'default'           => __( 'Your cart cannot contain more than {limit} products belonging to category {category_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_category_quantity_cart' => array(
			'name'              => __( 'Product quantity not allowed for this category', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_step_category_quantity_cart',
			'default'           => __( 'Products belonging to {category_name} category can only be purchased in groups of {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_category_value_cart'     => array(
			'name'              => __( 'Minimum category spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_category_value_cart',
			'default'           => __( 'You must spend at least {limit} for products belonging to category {category_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_category_value_cart'     => array(
			'name'              => __( 'Maximum category spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_category_value_cart',
			'default'           => __( 'You cannot spend more than {limit} for products belonging to category {category_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_tag_quantity_cart'       => array(
			'name'              => __( 'Minimum tag quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_tag_quantity_cart',
			'default'           => __( 'Your cart must contain {limit} products with tagged {tag_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_tag_quantity_cart'       => array(
			'name'              => __( 'Maximum tag quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_tag_quantity_cart',
			'default'           => __( 'Your cart cannot contain more than {limit} products tagged {tag_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_tag_quantity_cart'      => array(
			'name'              => __( 'Product quantity not allowed for this tag', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_step_tag_quantity_cart',
			'default'           => __( 'Products tagged {tag_name} can only be purchased in groups of {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_tag_value_cart'          => array(
			'name'              => __( 'Minimum tag spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_min_tag_value_cart',
			'default'           => __( 'You must spend at least {limit} for products tagged {tag_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_tag_value_cart'          => array(
			'name'              => __( 'Maximum tag spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_cart_quote . $ph_reference_link,
			'id'                => 'ywmmq_message_max_tag_value_cart',
			'default'           => __( 'You cannot spend more than {limit} for products tagged {tag_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_section_end_cart'            => array(
			'type' => 'sectionend',
		),

		'ywmmq_message_section_title_atc'          => array(
			'name' => __( '"Add to Cart" Error Messages', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type' => 'title',
		),
		'ywmmq_message_enable_atc'                 => array(
			'name'      => __( 'Enable messages on Add to Cart', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => '',
			'id'        => 'ywmmq_message_enable_atc',
			'default'   => 'yes',
		),
		'ywmmq_message_min_cart_quantity_atc'      => array(
			'name'              => __( 'Minimum cart quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_quantity . $ph_reference_link,
			'id'                => 'ywmmq_message_min_cart_quantity_atc',
			'default'           => __( 'The product has been added to the cart, but minimum number of items required in cart ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_cart_quantity_atc'      => array(
			'name'              => __( 'Maximum cart quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_quantity . $ph_reference_link,
			'id'                => 'ywmmq_message_max_cart_quantity_atc',
			'default'           => __( 'You cannot add this product because your cart cannot contain more than {limit} products.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_cart_value_atc'         => array(
			'name'              => __( 'Minimum cart spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_value . $ph_reference_link,
			'id'                => 'ywmmq_message_min_cart_value_atc',
			'default'           => __( 'The product has been added to the cart but minimum spend required ({limit}) has not be reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_cart_value_atc'         => array(
			'name'              => __( 'Maximum cart spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_cart_value . $ph_reference_link,
			'id'                => 'ywmmq_message_max_cart_value_atc',
			'default'           => __( 'You cannot add this product, because maximum totals for your cart cannot exceed {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_product_quantity_atc'   => array(
			'name'              => __( 'Minimum product quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_product_name . $ph_reference_link,
			'id'                => 'ywmmq_message_min_product_quantity_atc',
			'default'           => __( 'The product has been added to the cart, but minimum quantity required for this product ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_product_quantity_atc'   => array(
			'name'              => __( 'Maximum product quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_product_name . $ph_reference_link,
			'id'                => 'ywmmq_message_max_product_quantity_atc',
			'default'           => __( 'You cannot add this product because maximum quantity for this product ({limit}) has been reached.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_category_quantity_atc'  => array(
			'name'              => __( 'Minimum category quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_reference_link,
			'id'                => 'ywmmq_message_min_category_quantity_atc',
			'default'           => __( 'The product has been added to the cart, but minimum quantity required for category {category_name} ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_category_quantity_atc'  => array(
			'name'              => __( 'Maximum category quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_reference_link,
			'id'                => 'ywmmq_message_max_category_quantity_atc',
			'default'           => __( 'You cannot add this product because your cart cannot contain more than {limit} products belonging to category {category_name}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_category_quantity_atc' => array(
			'name'              => __( 'Product quantity not allowed for this category', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_reference_link,
			'id'                => 'ywmmq_message_step_category_quantity_atc',
			'default'           => __( 'The product has been added to the cart, but products belonging to the {category_name} can only be bought in groups of {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_category_value_atc'     => array(
			'name'              => __( 'Minimum category spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_reference_link,
			'id'                => 'ywmmq_message_min_category_value_atc',
			'default'           => __( 'The product has been added to the cart, but minimum spend required for category {category_name} ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_category_value_atc'     => array(
			'name'              => __( 'Maximum category spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_category_name . $ph_reference_link,
			'id'                => 'ywmmq_message_max_category_value_atc',
			'default'           => __( 'You cannot add this product because total spend for products belonging to category {category_name} cannot exceed {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_tag_quantity_atc'       => array(
			'name'              => __( 'Minimum tag quantity not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_reference_link,
			'id'                => 'ywmmq_message_min_tag_quantity_atc',
			'default'           => __( 'The product has been added to the cart but minumum required quantity for products tagged {tag_name} ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_tag_quantity_atc'       => array(
			'name'              => __( 'Maximum tag quantity exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_reference_link,
			'id'                => 'ywmmq_message_max_tag_quantity_atc',
			'default'           => __( 'You cannot add this product because your cart cannot contain more than {limit} products tagged {tag_name}', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_step_tag_quantity_atc'      => array(
			'name'              => __( 'Product quantity not allowed for this tag', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_reference_link,
			'id'                => 'ywmmq_message_step_tag_quantity_atc',
			'default'           => __( 'The product has been added to the cart, but products belonging to the {tag_name} can only be bought in groups of {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_min_tag_value_atc'          => array(
			'name'              => __( 'Minimum tag spend not reached', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_reference_link,
			'id'                => 'ywmmq_message_min_tag_value_atc',
			'default'           => __( 'The product has been added to the cart but minimum spend required for products tagged {tag_name} ({limit}) has not been reached yet.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_max_tag_value_atc'          => array(
			'name'              => __( 'Maximum tag spend exceeded', 'yith-woocommerce-minimum-maximum-quantity' ),
			'type'              => 'yith-field',
			'yith-type'         => 'textarea',
			'desc'              => $placeholders_text . $ph_limit . $ph_tag_name . $ph_reference_link,
			'id'                => 'ywmmq_message_max_tag_value_atc',
			'default'           => __( 'You cannot add this product because total spend for products tagged {tag_name} cannot exceed {limit}.', 'yith-woocommerce-minimum-maximum-quantity' ),
			'deps'              => array(
				'id'    => 'ywmmq_message_enable_atc',
				'value' => 'yes',
				'type'  => 'hide-disable'
			),
			'custom_attributes' => 'required'
		),
		'ywmmq_message_section_end_atc'            => array(
			'type' => 'sectionend',
		),

	)

);