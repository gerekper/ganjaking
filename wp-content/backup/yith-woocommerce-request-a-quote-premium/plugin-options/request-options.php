<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$section1 = array(
	'request_settings'     => array(
		'name' => esc_html__( 'Request a Quote List Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'request_settings',
	),
	'page_id'              => array(
		'name'     => esc_html__( 'Choose what page will include the list.', 'yith-woocommerce-request-a-quote' ),
		'desc'     => sprintf(
			'%s<br><span class="ywraq-attention">%s</span>%s',
			esc_html__( 'You can choose from this list the page on which users will see their quote requests.', 'yith-woocommerce-request-a-quote' ),
			esc_html__( 'Please note', 'yith-woocommerce-request-a-quote' ),
			esc_html__( ': by choosing a page different from the default one (request a quote) and allow users to view their requests list, you will need to insert the following shortcode [yith_ywraq_request_quote] ', 'yith-woocommerce-request-a-quote' )
		),
		'id'       => 'ywraq_page_id',
		'type'     => 'single_select_page',
		'class'    => 'wc-enhanced-select',
		'css'      => 'min-width:300px',
		'desc_tip' => false,
	),


	'page_list_layout_template' => array(
		'name'      => esc_html__( 'Page Layout', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Choose the layout for the quote page.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_page_list_layout_template',
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'options'   => array(
			'vertical' => esc_html__( 'Show the form under the quote list', 'yith-woocommerce-request-a-quote' ),
			'wide'     => esc_html__( 'Show the form next to the quote list', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'vertical',
	),

	'show_sku'             => array(
		'name'      => esc_html__( 'Show SKU on list table', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the sku will be added near the title of product in the request list', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_sku',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'show_preview'         => array(
		'name'      => esc_html__( 'Show preview thumbnail on email list table', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the thumbnail will be added in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_preview',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'show_old_price'       => array(
		'name'      => esc_html__( 'Show old price on list table', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the old price will be showed in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_old_price',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'show_return_to_shop'  => array(
		'name'      => esc_html__( 'Show "Return to Shop" button', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the "Return to Shop" button will be showed in the request list', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_return_to_shop',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'return_to_shop_label' => array(
		'name'      => esc_html__( '"Return to Shop" button label', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Edit the text of the button that will allow users to go back to the shop page.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_return_to_shop_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_show_return_to_shop',
			'value' => 'yes',
		),
		'default'   => esc_html__( 'Return to Shop', 'yith-woocommerce-request-a-quote' ),
	),

	'return_to_shop_url'   => array(
		'name'      => esc_html__( '"Return to Shop" URL', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Define the URL to assign to the button.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_return_to_shop_url',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_show_return_to_shop',
			'value' => 'yes',
		),
		'default'   => get_permalink( wc_get_page_id( 'shop' ) ),
	),

	'show_update_list'     => array(
		'name'      => esc_html__( 'Show "Update List" button', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the "Update List" button will be showed in the request list', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_update_list',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),

	'update_list_label'    => array(
		'name'      => esc_html__( '"Update List" button label', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Edit the text of the button that will allow users to update the list.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_update_list_label',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'deps'      => array(
			'id'    => 'ywraq_show_update_list',
			'value' => 'yes',
		),
		'default'   => esc_html__( 'Update List', 'yith-woocommerce-request-a-quote' ),
	),

	'hide_column_total'    => array(
		'name'      => esc_html__( 'Hide column Total', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Hide the column Total of single product.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_hide_total_column',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'show_total_in_list'   => array(
		'name'      => esc_html__( 'Show total in quote list', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Show the total amount of selected products', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_total_in_list',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'clear_list_button'    => array(
		'name'      => esc_html__( 'Show Button to clear the quote list', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Show the Button to remove all the items in the quote list with one click', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_clear_list_button',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'request_settings_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_request_settings_end',
	),
);


return array( 'request' => apply_filters( 'ywraq_request_settings_options', $section1 ) );
