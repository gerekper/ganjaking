<?php
/**
 * Template content options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPI_PREMIUM' ) ) {
	exit;
} // Exit if accessed directly.

$content_template_options = array(

	'template-content' => array(

		'section_template'                      => array(
			'name' => __( 'Template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),

		'company_name'                          => array(
			'name'      => __( 'Company name', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_company_name',
			'desc'      => __( 'Set the company name to be shown on invoices', 'yith-woocommerce-pdf-invoice' ),
			'default'   => __( 'Your company name', 'yith-woocommerce-pdf-invoice' ),
		),
		'company_logo'                          => array(
			'name'      => __( 'Your company logo', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'ywpi_company_logo',
			'desc'      => __( 'Set a default logo to be shown.', 'yith-woocommerce-pdf-invoice' ),
		),
		'company_details'                       => array(
			'name'      => __( 'Company details', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_company_details',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Set company details to use in the invoice.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => __(
				'Your company details
Address
City, State'
			),
		),
		'show_company_name'                     => array(
			'name'          => __( 'Visible sections', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'id'            => 'ywpi_show_company_name',
			'desc'          => __( 'Show company name', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_company_logo'                     => array(
			'name'          => __( 'Show company logo', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_show_company_logo',
			'desc'          => __( 'Show company logo', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_company_details'                  => array(
			'name'          => __( 'Show company details', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'id'            => 'ywpi_show_company_details',
			'desc'          => __( 'Show company details', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'customer_billing_details'              => array(
			'name'      => __( 'Customer invoice details', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_customer_billing_details',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Set the customer details to use in the invoice. <br>
			Use the postmeta metakeys as placeholders within double curly brackets. <br>Example: Use <b>{{_billing_first_name}}</b> to show the order billing first name. <br>
			You can read the documentation <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">here</a> for more information.
			',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => __(
				'{{_billing_first_name}} {{_billing_last_name}}
{{_billing_address_1}}
{{_billing_postcode}}{{_billing_city}}
{{_billing_country}}
SSN: {{_billing_vat_ssn}}
VAT: {{_billing_vat_number}}
{{_billing_phone}}
{{_billing_email}}',
				'yith-woocommerce-pdf-invoice'
			),
		),
		'customer_shipping_details'             => array(
			'name'      => __( 'Customer packing slip details', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_customer_shipping_details',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Set the customer details to use in the invoice. <br>
			 Use the postmeta metakeys as placeholders within double curly brackets. <br>Example: Use <b>{{_shipping_first_name}}</b> to show the order shipping first name. <br>
			 You can read the documentation <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">here</a> for more information.
			 ',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => '{{_shipping_first_name}} {{_shipping_last_name}}
{{_shipping_address_1}}
{{_shipping_postcode}}{{_shipping_city}}
{{_shipping_country}}',
		),
		'section_template_end'                  => array(
			'type' => 'sectionend',
			'id'   => 'ywpi_template_end',
		),
		'section_template_invoice'              => array(
			'name' => __( 'Invoice and Pro-forma invoice template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_section_template_invoice',
		),
		'show_invoice_notes'                    => array(
			'name'      => __( 'Show notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_show_invoice_notes',
			'desc'      => __( 'Show the notes in the invoice and pro-forma document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'invoice_notes'                         => array(
			'name'      => __( 'Invoice notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_invoice_notes',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes on invoices.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_show_invoice_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'pro_forma_notes'                       => array(
			'name'      => __( 'Pro-forma Invoice notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_pro_forma_notes',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes on Pro-forma invoices.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_show_invoice_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'show_invoice_footer'                   => array(
			'name'      => __( 'Show footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_show_invoice_footer',
			'desc'      => __( 'Show the footer in the invoice and pro-forma document', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'invoice_footer'                        => array(
			'name'      => __( 'Invoice footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_invoice_footer',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Type the text to show in the footer of the invoices. <br>
			You also can use the postmeta metakeys as placeholders. For more information read the <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">documentation.</a>
			',
				'yith-woocommerce-pdf-invoice'
			),
			'deps'      => array(
				'id'    => 'ywpi_show_invoice_footer',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'pro_forma_footer'                      => array(
			'name'      => __( 'Pro-forma invoice footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_pro_forma_footer',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Type the text to show in the footer of the pro-forma invoices. <br>
			You also can use the postmeta metakeys as placeholders. For more information read the <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">documentation.</a>
			',
				'yith-woocommerce-pdf-invoice'
			),
			'deps'      => array(
				'id'    => 'ywpi_show_invoice_footer',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'ywpi_invoice_footer_last_page'         => array(
			'name'      => __( 'Show footer only on the last page', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_invoice_footer_last_page',
			'desc'      => __( 'Show the footer only on the last page of the invoice, not on all the pages.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywpi_show_invoice_footer',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'ywpi_invoice_shipping_details'         => array(
			'name'      => __( 'Show shipping details', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_invoice_shipping_details',
			'desc'      => __( 'Show the shipping details in the invoice and pro-forma document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'ywpi_subtotal_inclusive_discount'      => array(
			'name'          => __( 'Total section', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'id'            => 'ywpi_subtotal_inclusive_discount',
			'desc'          => __( 'Show order subtotal inclusive of order discount', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_show_discount'                    => array(
			'name'          => __( 'Show discount', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_show_discount',
			'desc'          => __( 'Show the order discount in the invoice summary amounts', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_broken_down_taxes'                => array(
			'name'          => __( 'Show broken down taxes', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'id'            => 'ywpi_broken_down_taxes',
			'desc'          => __( 'Show broken down taxes in the invoice summary', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_picture'           => array(
			'name'            => __( 'Visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'            => 'checkbox',
			'checkboxgroup'   => 'start',
			'show_if_checked' => 'option',
			'id'              => 'ywpi_invoice_column_picture',
			'css'             => 'width:80%; height: 90px;',
			'desc'            => __( 'Product picture', 'yith-woocommerce-pdf-invoice' ),
			'default'         => 'yes',
		),
		'show_invoice_column_SKU'               => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_SKU',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product SKU', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_short_description' => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_short_description',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Short description', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'show_invoice_column_variation'         => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_variation',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product variation', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'show_invoice_column_quantity'          => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_quantity',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Quantity', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_regular_price'     => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_regular_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Regular price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'show_invoice_column_sale_price'        => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_sale_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'On sale price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_product_price'     => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_product_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_tax'               => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_tax',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Tax', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_percentage_tax'    => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_percentage_tax',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Tax percentage', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'show_invoice_column_line_total'        => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_line_total',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Line total', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_invoice_column_total_taxed'       => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_invoice_column_total_taxed',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_invoice_column_percentage'        => array(
			'name'          => __( 'Invoice visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'id'            => 'ywpi_invoice_column_percentage',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Show discount percentage', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'section_section_template_invoice_end'  => array(
			'type' => 'sectionend',
			'id'   => 'ywpi_section_template_invoice_end',
		),

		'ywpi_credit_note_template_options'     => array(
			'name' => __( 'Credit note template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'ywpi_show_credit_note_notes'           => array(
			'name'      => __( 'Show notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_show_credit_note_notes',
			'desc'      => __( 'Show the notes in the credit note document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'ywpi_credit_note_notes'                => array(
			'name'      => __( 'Credit note notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_credit_note_notes',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes on credit notes.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_show_credit_note_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'ywpi_show_credit_note_footer'          => array(
			'name'      => __( 'Show footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_show_credit_note_footer',
			'desc'      => __( 'Show the footer in the credit note document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'ywpi_credit_note_footer'               => array(
			'name'      => __( 'Credit note footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_credit_note_footer',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Type the text to show in the footer of the credit notes. <br>
			You also can use the postmeta metakeys as placeholders. For more information read the <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">documentation.</a>
			',
				'yith-woocommerce-pdf-invoice'
			),
			'deps'      => array(
				'id'    => 'ywpi_show_credit_note_footer',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'ywpi_credit_note_product_name_column'  => array(
			'name'          => __( 'Info to show', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'id'            => 'ywpi_credit_note_product_name_column',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product name', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_credit_note_product_sku_column'   => array(
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_credit_note_product_sku_column',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product SKU', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_credit_note_product_image_column' => array(
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'id'            => 'ywpi_credit_note_product_image_column',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product image', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'ywpi_credit_note_subtotal_column'      => array(
			'name'      => __( 'Show subtotal', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_credit_note_subtotal_column',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Show subtotal amount in the credit note documents.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'ywpi_credit_note_total_tax_column'     => array(
			'name'      => __( 'Show total tax', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_credit_note_total_tax_column',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Show total tax amount in the credit notes documents.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'ywpi_credit_note_positive_values'      => array(
			'name'      => __( 'Show positive amounts', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_credit_note_positive_values',
			'desc'      => __( 'In some countries like Germany or Spain, it is necessary to show amounts on credit notes with positive values.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'ywpi_credit_note_template_options_end' => array(
			'type' => 'sectionend',
		),

		'section_template_packing_slip'         => array(
			'name' => __( 'Packing slip template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_section_template_packing_slip',
			'desc' => __( 'We recommend to carefully verify the correct data provided, to generate the invoice. The plugin\'s authors refuse any responsibility about possible mistakes or shortcomings when generating invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'packing_slip_show_notes'               => array(
			'name'      => __( 'Show the notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_packing_slip_show_notes',
			'desc'      => __( 'Show notes in the packing slip document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'packing_slip_notes'                    => array(
			'name'      => __( 'Packing slip notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_packing_slip_notes',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes on packing slip.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_packing_slip_show_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'packing_slip_show_footer'              => array(
			'name'      => __( 'Show footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_packing_slip_show_footer',
			'desc'      => __( 'Show footer in the packing slip document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'packing_slip_footer'                   => array(
			'name'      => __( 'Packing slip Footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => 'ywpi_packing_slip_footer',
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __(
				'Type the text to show in the footer of the packing slip. <br>
			You also can use the postmeta metakeys as placeholders. For more information read the <a href="https://docs.yithemes.com/yith-woocommerce-pdf-invoice/premium-version-settings/insert-new-user-details-documents/">documentation.</a>
			',
				'yith-woocommerce-pdf-invoice'
			),
			'deps'      => array(
				'id'    => 'ywpi_packing_slip_show_footer',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'packing_slip_show_order_totals'        => array(
			'name'      => __( 'Show order totals', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_packing_slip_show_order_totals',
			'desc'      => __( 'Show order totals in the packing slip document.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'packing_slip_column_picture'           => array(
			'name'            => __( 'Visible columns', 'yith-woocommerce-pdf-invoice' ),
			'type'            => 'checkbox',
			'checkboxgroup'   => 'start',
			'show_if_checked' => 'option',
			'id'              => 'ywpi_packing_slip_column_picture',
			'css'             => 'width:80%; height: 90px;',
			'desc'            => __( 'Product picture', 'yith-woocommerce-pdf-invoice' ),
			'default'         => 'yes',
		),
		'packing_slip_column_SKU'               => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_SKU',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product SKU', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_weight'            => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_weight',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Weight and dimension', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'packing_slip_column_short_description' => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_short_description',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Short description', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'packing_slip_column_variation'         => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_variation',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product variation', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'packing_slip_column_quantity'          => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_quantity',
			'css'           => 'width:80%; hywpi_invoice_column_regular_priceeight: 90px;',
			'desc'          => __( 'Quantity', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_regular_price'     => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_regular_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Regular price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'packing_slip_column_sale_price'        => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_sale_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Sale price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_product_price'     => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_product_price',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Product price', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_line_total'        => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_line_total',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Line total', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_tax'               => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_tax',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Tax', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'no',
		),
		'packing_slip_column_percentage_tax'    => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_percentage_tax',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Tax percentage', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_total_taxed'       => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => '',
			'id'            => 'ywpi_packing_slip_column_total_taxed',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Total (inc. tax)', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'packing_slip_column_percentage'        => array(
			'name'          => __( 'Visible columns in the packing slip', 'yith-woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'checkboxgroup' => 'end',
			'id'            => 'ywpi_packing_slip_column_percentage',
			'css'           => 'width:80%; height: 90px;',
			'desc'          => __( 'Show discount percentage', 'yith-woocommerce-pdf-invoice' ),
			'default'       => 'yes',
		),
		'section_template_packing_slip_end'     => array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'ywpi_template_content_options', $content_template_options );

