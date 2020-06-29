<?php
/**
 * Cart Message Options for Metabox
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$woocommerce_pages = array();
if ( get_option( 'ywcm_show_in_cart' ) === 'yes' ) {
	$woocommerce_pages['cart'] = __( 'Cart', 'yith-woocommerce-cart-messages' );
}
if ( get_option( 'ywcm_show_in_checkout' ) === 'yes' ) {
	$woocommerce_pages['checkout'] = __( 'Checkout', 'yith-woocommerce-cart-messages' );
}
if ( get_option( 'ywcm_show_in_shop_page' ) === 'yes' ) {
	$woocommerce_pages['shop'] = __( 'Shop', 'yith-woocommerce-cart-messages' );
}
if ( get_option( 'ywcm_show_in_single_product' ) === 'yes' ) {
	$woocommerce_pages['single-product'] = __( 'Single Product', 'yith-woocommerce-cart-messages' );
}

return array(
	'label'    => __( 'Message Settings', 'yith-woocommerce-cart-messages' ),
	'pages'    => 'ywcm_message', // or array( 'post-type1', 'post-type2').
	'context'  => 'normal', // ('normal', 'advanced', or 'side').
	'priority' => 'default',
	'tabs'     => array(
		'settings' => array(
			'label'  => __( 'Settings', 'yith-woocommerce-cart-messages' ),
			'fields' => apply_filters(
				'ywcm_message_metabox',
				array(
					'ywcm_message_type'                    => array(
						'label'   => __( 'Message Type', 'yith-woocommerce-cart-messages' ),
						'desc'    => __( 'Choose the type of the message', 'yith-woocommerce-cart-messages' ),
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => YWCM_Cart_Message()->get_types(),
						'std'     => 'minimum_amount',
					),

					/* Minimum order amount _________________________________________________________________________*/
					'ywcm_sep_message'                     => array(
						'type' => 'sep',
					),
					'ywcm_message_minimum_amount_text'     => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Edit the message. You can use {remaining_amount} as remaining amount required to meet the minimum order amount', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   =>  __( 'Add <strong>{remaining_amount}</strong> to your cart in order to receive free shipping!', 'yith-woocommerce-cart-messages' ),
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'minimum_amount',
						),
					),


					'ywcm_message_minimum_amount'          => array(
						'label' => __( 'Minimum order amount', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Minimum order amount to encourage users to purchase more.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'minimum_amount',
						),
					),


					'ywcm_minimum_amount_threshold_amount' => array(
						'label' => __( 'Threshold amount', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Set here the minimum order amount to show the message.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'minimum_amount',
						),
					),
					// from 1.1.5.
					'ywcm_minimum_amount_exclude_coupons'  => array(
						'label' => __( 'Exclude coupons amount', 'yith-woocommerce-cart-messages' ),
						'desc'  => '',
						'type'  => 'checkbox',
						'std'   => 'no',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'minimum_amount',
						),
					),

					// from 1.1.9.
					'ywcm_minimum_amount_products_exclude' => array(
						'label'    => __( 'Hide the message if these products are in cart', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'If there are these products in cart the message is not showed', 'yith-woocommerce-cart-messages' ),
						'type'     => 'ajax-products',
						'multiple' => true,
						'options'  => array(),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'minimum_amount',
						),
					),

					/* Products in Cart ____________________________________________________________________________*/
					'ywcm_message_products_cart_text'      => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'You can edit the text using the following placeholder: <br>{remaining_quantity} indicates the remaining quantity;<br>{products} specifies which of the listed product is in the cart;<br>{quantity} indicates quantity in cart,{required_quantity} states the exact number of product to purchase.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   => __( 'To benefit from free shipping, add <strong>{remaining_quantity}</strong> quantity more of <strong>{products}</strong>!', 'yith-woocommerce-cart-messages' ),
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),

					'ywcm_message_products_cart_minimum'   => array(
						'label' => __( 'Required quantity', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'The minimum total quantity of above selected products.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),

					'ywcm_products_cart_threshold_quantity' => array(
						'label' => __( 'Threshold quantity', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Set here the minimum product quantity to show the message.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),

					'ywcm_products_cart_products'          => array(
						'label'    => __( 'Select products', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Leave empty to select all products of the shop', 'yith-woocommerce-cart-messages' ),
						'type'     => 'ajax-products',
						'multiple' => true,
						'options'  => array(),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),


					'ywcm_products_cart_products_exclude'  => array(
						'label'    => __( 'Select products to exclude', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Leave empty to select all products of the shop', 'yith-woocommerce-cart-messages' ),
						'type'     => 'ajax-products',
						'multiple' => true,
						'options'  => array(),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),


					/* Category in Cart  ___________________________________________________________________________*/
					'ywcm_message_categories_cart_text'    => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'You can edit the message using <br>{categories} to state the list of categories.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   => __('Do you like <strong>{categories}</strong>? Discovery our outlet!', 'yith-woocommerce-cart-messages') ,
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'categories_cart',
						),
					),


					'ywcm_message_category_cart_categories' => array(
						'label'    => __( 'Select categories', 'yith-woocommerce-cart-messages' ),
						'desc'     => '',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'multiple' => true,
						'options'  => ywcm_get_shop_categories( false ),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'categories_cart',
						),
					),


					/* Referer  __________________________________________________________________________________*/
					'ywcm_message_referer_text'            => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Edit the message', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'referer',
						),
					),

					'ywcm_message_referer'                 => array(
						'label' => __( 'Referrer url', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Show a custom message to users who reach your site through a specific URL', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'referer',
						),
					),



					/* Deadline  ________________________________________________________________________________*/
					'ywcm_message_deadline_text'           => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'You can edit the message with {time_remain} as a time variable.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   => 'Make your order within the next {time_remain} and your order will be shipped today!',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'deadline',
						),
					),

					'ywcm_message_start_hour'              => array(
						'label' => __( 'Start hour', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Start hour is in 24-hour format and it can be a number from 1 to 24.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'number',
						'std'   => '12',
						'min'   => '1',
						'max'   => '24',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'deadline',
						),
					),

					'ywcm_message_deadline_hour'           => array(
						'label' => __( 'Deadline hour', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Deadline hour is in 24-hour format and it can be a number from 1 to 24.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'number',
						'std'   => '12',
						'min'   => '1',
						'max'   => '24',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'deadline',
						),
					),

					'ywcm_message_deadline_days'           => array(
						'label'    => __( 'Deadline days', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Select the days in which you want to activate and show this notice.', 'yith-woocommerce-cart-messages' ),
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'multiple' => true,
						'options'  => array(
							'0' => __( 'Sunday', 'yith-woocommerce-cart-messages' ),
							'1' => __( 'Monday', 'yith-woocommerce-cart-messages' ),
							'2' => __( 'Tuesday', 'yith-woocommerce-cart-messages' ),
							'3' => __( 'Wednesday', 'yith-woocommerce-cart-messages' ),
							'4' => __( 'Thursday', 'yith-woocommerce-cart-messages' ),
							'5' => __( 'Friday', 'yith-woocommerce-cart-messages' ),
							'6' => __( 'Saturday', 'yith-woocommerce-cart-messages' ),
						),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'deadline',
						),
					),

					/* Simple message ____________________________________________________________________________*/
					'ywcm_message_simple_message_text'     => array(
						'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Edit the message', 'yith-woocommerce-cart-messages' ),
						'type'  => 'textarea',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'simple_message',
						),
					),


					'ywcm_sep_layout'                      => array(
						'type' => 'sep',
					),
					/* Common options  ____________________________________________________________________________*/
					'ywcm_message_button'                  => array(
						'label' => __( 'Text Button (optional)', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'The text of the button for the action call. Leave it empty if you do not want to show it.', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
					),

					'ywcm_message_button_url'              => array(
						'label' => __( 'Button URL (optional)', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'The URL of the button of the call to action', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
					),

					'ywcm_message_button_anchor'           => array(
						'label' => __( 'Open a link in another tab', 'yith-woocommerce-cart-messages' ),
						'type'  => 'checkbox',
						'desc'  => '',
						'std'   => '',
					),


					'ywcm_message_layout'                  => array(
						'label'   => __( 'Choose a custom layout', 'yith-woocommerce-cart-messages' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'layout'  => __( 'Layout-1 - Woocommerce Layout', 'yith-woocommerce-cart-messages' ),
							'layout2' => __( 'Layout-2', 'yith-woocommerce-cart-messages' ),
							'layout3' => __( 'Layout-3', 'yith-woocommerce-cart-messages' ),
							'layout4' => __( 'Layout-4', 'yith-woocommerce-cart-messages' ),
							'layout5' => __( 'Layout-5', 'yith-woocommerce-cart-messages' ),
							'layout6' => __( 'Layout-6', 'yith-woocommerce-cart-messages' ),
						),
						'std'     => 'all',
					),

					'ywcm_sep_user'                        => array(
						'type' => 'sep',
					),
					'ywcm_message_user'                    => array(
						'label'   => __( 'Show to:', 'yith-woocommerce-cart-messages' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'all'       => __( 'Guests and logged-in users', 'yith-woocommerce-cart-messages' ),
							'guests'    => __( 'Guests', 'yith-woocommerce-cart-messages' ),
							'customers' => __( 'Logged-in users', 'yith-woocommerce-cart-messages' ),
						),
						'std'     => 'all',
					),

					'ywcm_message_role_user'               => array(
						'label'    => __( 'Choose who you want to show the message to:', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Choose whether to show the message only to specific roles. Leave empty for all.', 'yith-woocommerce-cart-messages' ),
						'type'     => 'select',
						'multiple' => true,
						'class'    => 'wc-enhanced-select',
						'options'  => ywcm_get_roles(),
						'std'      => array( 'all' ),
					),

					'ywcm_message_country'                 => array(
						'label'    => __( 'Restrict to the following countries:', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Enter here the countries to which your messages will be visible. Leave it empty to show messages to everyone.', 'yith-woocommerce-cart-messages' ),
						'type'     => 'select',
						'multiple' => true,
						'class'    => 'wc-enhanced-select',
						'options'  => WC()->countries->get_countries(),
						'std'      => array(),
					),

					'ywcm_sep_user_end'                    => array(
						'type' => 'sep',
					),
					'ywcm_message_start'                   => array(
						'label' => __( 'Start date (optional)', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Choose a date when message will appear', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
					),


					'ywcm_message_expire'                  => array(
						'label' => __( 'Expiration date (optional)', 'yith-woocommerce-cart-messages' ),
						'desc'  => __( 'Choose a date until this message will appear', 'yith-woocommerce-cart-messages' ),
						'type'  => 'text',
						'std'   => '',
					),

					'ywcm_sep_position'                    => array(
						'type' => 'sep',
					),

					'ywcm_message_pages'                   => array(
						'label'    => __( 'Show in', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Choose the woocommerce pages on which you want this notice to be active.', 'yith-woocommerce-cart-messages' ),
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'multiple' => true,
						'options'  => $woocommerce_pages,
						'std'      => array(),
					),




					'ywcm_products_cart_show_only_in'      => array(
						'label' => __( 'Only in the product selected', 'yith-woocommerce-cart-messages' ),
						'type'  => 'checkbox',
						'desc'  => '',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'products_cart',
						),
					),


					'ywcm_simple_message_show_in_products' => array(
						'label'    => __( 'Select products where show the message', 'yith-woocommerce-cart-messages' ),
						'desc'     => __( 'Leave empty to select all products of the shop', 'yith-woocommerce-cart-messages' ),
						'type'     => 'ajax-products',
						'multiple' => true,
						'options'  => array(),
						'std'      => array(),
						'deps'     => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'simple_message',
						),
					),

					'ywcm_simple_message_hide_products_in_cart' => array(
						'label' => __( 'Hide the message when the products above are on cart', 'yith-woocommerce-cart-messages' ),
						'type'  => 'checkbox',
						'desc'  => '',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_ywcm_message_type',
							'values' => 'simple_message',
						),
					),

					'ywcm_message_cart_page_position'      => array(
						'label'   => __( 'Cart Page Position', 'yith-woocommerce-cart-messages' ),
						'desc'    => '',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'std'     => 'woocommerce_before_cart_contents',
						'options' => array(
							'woocommerce_before_cart'      => __( 'Before cart', 'yith-woocommerce-cart-messages' ),
							'woocommerce_before_cart_table' => __( 'Before cart table', 'yith-woocommerce-cart-messages' ),
							'woocommerce_before_cart_contents' => __( 'Before cart contents', 'yith-woocommerce-cart-messages' ),
							'woocommerce_after_cart_contents' => __( 'After cart contents', 'yith-woocommerce-cart-messages' ),
							'woocommerce_after_cart_table' => __( 'After cart table', 'yith-woocommerce-cart-messages' ),
							'woocommerce_after_cart'       => __( 'After cart', 'yith-woocommerce-cart-messages' ),
						),
					),


				)
			),
		),
	),
);
