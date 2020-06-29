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
	'pdf_settings'                 => array(
		'name' => esc_html__( 'PDF Quote Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_pdf_settings',
	),
	'pdf_in_myaccount'             => array(
		'name'      => esc_html__( 'Allow creating PDF document download in My Account page', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, a button "Download PDF" will be added in the quote detail page', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_in_myaccount',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'pdf_pagination'               => array(
		'name'      => esc_html__( 'Enable pagination.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Enable pagination.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'id'        => 'ywraq_pdf_pagination',
		'yith-type' => 'onoff',
		'default'   => 'yes',
	),
	'pdf_attachment'               => array(
		'name'      => esc_html__( 'Attach PDF quote to the email', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the quote will be sent as PDF attachment', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_attachment',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'pdf_settings_end'             => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_pdf_settings_end',
	),
	'pdf_layout'                   => array(
		'name' => esc_html__( 'PDF Quote Layout', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_pdf_layout',
	),
	'pdf_template'                 => array(
		'name'      => esc_html__( 'Template', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Select the template: Table allows adding the quote content to an HTML table; Div replaces the HTML table with Divs (this can help to avoid some issues with pagination)', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_template',
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'options'   => array(
			'table' => esc_html__( 'Table', 'yith-woocommerce-request-a-quote' ),
			'div'   => esc_html__( 'Div', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'table',
	),
	'pdf_logo'                     => array(
		'name'      => esc_html__( 'Logo', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Upload the logo you want to show in the PDF document. Only .png and .jpeg extensions are allowed.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_logo',
		'type'      => 'yith-field',
		'yith-type' => 'upload',
		'default'   => YITH_YWRAQ_DIR . 'assets/images/logo.jpg',
	),
	'pdf_info'                     => array(
		'name'      => esc_html__( 'Sender Info in PDF file', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Add sender information that have to be shown in the PDF document', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_info',
		'type'      => 'yith-field',
		'yith-type' => 'textarea',
		'default'   => get_bloginfo( 'name' ),
	),
	'show_author_quote'            => array(
		'name'      => esc_html__( 'Show quote author', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, the quote will show information of the user who sent it.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_author_quote',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),

	'pdf_columns'                  => array(
		'name'      => esc_html__( 'Table Columns', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Select the columns you want to show in the List', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_columns',
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'multiple'  => true,
		'options'   => array_merge(
			array(
				'all' => esc_html__( 'All', 'yith-woocommerce-request-a-quote' ),
			),
			apply_filters(
				'ywpar_pdf_columns',
				array(
					'thumbnail'        => 'Product Thumbnail',
					'product_name'     => 'Product Name',
					'unit_price'       => 'Unit Price',
					'quantity'         => 'Quantity',
					'product_subtotal' => 'Product Subtotal',
				)
			)
		),
		'default'   => array( 'all' ),
	),
	'pdf_hide_total_row'           => array(
		'name'      => esc_html__( 'Hide Total Row', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Do not diasplay the Total Price Row in the Product List ', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_hide_total_row',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'hide_table_is_pdf_attachment' => array(
		'name'      => esc_html__( 'Remove the list with products from the email', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'Hide quote in the email if it has been sent as PDF attachment', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_hide_table_is_pdf_attachment',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'pdf_link'                     => array(
		'name'      => esc_html__( 'Show Link  Accept/Reject', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'By enabling this option, the link to accept or reject the quote will be inserted into the PDF. You can enable the link to accept or reject the quote from the Quote Settings tab.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_link',
		'type'      => 'yith-field',
		'yith-type' => 'onoff',
		'default'   => 'no',
	),
	'pdf_footer_content'           => array(
		'name'      => esc_html__( 'Add general text on the footer of pdf document.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => esc_html__( 'If checked, a button "Download PDF" will be added in the quote detail page.', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_pdf_footer_content',
		'type'      => 'yith-field',
		'yith-type' => 'textarea',
		'default'   => '',
	),


	'pdf_layout_end'               => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_pdf_layout_end',
	),


);


return array( 'pdf' => apply_filters( 'ywraq_pdf_settings_options', $section1 ) );
