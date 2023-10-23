<?php
/**
 * Vendor options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Show and save option for the specific vendor
 */
$current_date = getdate();
$vendor       = yith_get_vendor( get_current_user_id(), 'user' );

$vendor_options = array(
	'vendor' => array(
		'section_vendor_settings'                => array(
			'name' => __( 'Vendor invoice settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'next_invoice_number'                    => array(
			'name'              => __( 'Next invoice number', 'yith-woocommerce-pdf-invoice' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'id'                => apply_filters( 'ywpi_option_name', 'ywpi_invoice_number' ),
			'desc'              => __( 'Invoice number for the next invoice.', 'yith-woocommerce-pdf-invoice' ),
			'default'           => 1,
			'std'               => 1,
			'custom_attributes' => array(
				'min'      => 1,
				'step'     => 1,
				'required' => 'required',
			),
		),
		'next_invoice_year'                      => array(
			'name'      => __( 'Billing year', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'hidden',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_year_billing' ),
			'default'   => $current_date['year'],
			'value'     => $current_date['year'],
		),
		'invoice_prefix'                         => array(
			'name'      => __( 'Invoice prefix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_prefix' ),
			// translators: %1$s and %2$s are <b> and </b> tags.
			'desc'      => sprintf( __( 'Set a text to show before the invoice number. Example: with "YITH", invoice number will be "%1$sYITH%2$s-485940".', 'yith-woocommerce-pdf-invoice' ), '<b>', '</b>' ),
		),
		'invoice_suffix'                         => array(
			'name'      => __( 'Invoice suffix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_suffix' ),
			// translators: %1$s and %2$s are <b> and </b> tags.
			'desc'      => sprintf( __( 'Set a text to show after the invoice number. Example: with "YITH", invoice number will be "485940-%1$sYITH%2$s".', 'yith-woocommerce-pdf-invoice' ), '<b>', '</b>' ),
		),
		'invoice_number_format'                  => array(
			'name'              => __( 'Invoice number format', 'yith-woocommerce-pdf-invoice' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'id'                => apply_filters( 'ywpi_option_name', 'ywpi_invoice_number_format' ),
			'desc'              => __( 'Set the format for the invoice number. Use [number], [prefix] and [suffix] as placeholders. <b>The [number] placeholder is required</b>. If not specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'default'           => '[prefix]/[number]/[suffix]',
			'custom_attributes' => array(
				'required' => 'required',
			),
		),
		'invoice_reset'                          => array(
			'name'      => __( 'Reset on January 1st', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_reset' ),
			'desc'      => __( 'Enable to automatically reset and restart sequential numbering from number "1" each January 1st.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => false,
		),
		'section_vendor_settings_end'            => array(
			'type' => 'sectionend',
		),
		'section_template'                       => array(
			'name' => __( 'Template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'desc' => __( 'We recommend to carefully verify the data provided is correct, to generate the invoice. The plugin\'s authors refuse any responsibility for possible mistakes or shortcomings when generating invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'company_name'                           => array(
			'name'      => __( 'Company name', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_company_name' ),
			'desc'      => __( 'Set the company name to be shown in the invoices.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => __( 'Your company name', 'yith-woocommerce-pdf-invoice' ),
		),
		'company_logo'                           => array(
			'name'      => __( 'Your company logo', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'media',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_company_logo' ),
			'desc'      => __( 'Set a default logo to be shown.', 'yith-woocommerce-pdf-invoice' ),
		),
		'company_details'                        => array(
			'name'      => __( 'Company details', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_company_details' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Set the company details to be used in the invoice.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => __(
				'Your company details
Address
City, State'
			),
		),
		'section_template_end'                   => array(
			'type' => 'sectionend',
		),
		'section_template_invoice'               => array(
			'name' => __( 'Invoice and Pro-forma invoice template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_section_template_invoice',
		),
		'invoice_notes'                          => array(
			'name'      => __( 'Invoice notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_notes' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes in the invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'invoice_footer'                         => array(
			'name'      => __( 'Invoice footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_invoice_footer' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show in the footer of the invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'pro_forma_notes'                        => array(
			'name'      => __( 'Pro-forma invoice notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_pro_forma_notes' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes in the pro-forma invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'pro_forma_footer'                       => array(
			'name'      => __( 'Pro-forma invoice footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_pro_forma_footer' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show in the footer of the pro-forma invoices.', 'yith-woocommerce-pdf-invoice' ),
		),
		'section_section_template_invoice_end'   => array(
			'type' => 'sectionend',
			'id'   => 'ywpi_section_template_invoice_end',
		),
		'ywpi_credit_note_template_options'      => array(
			'name' => __( 'Credit note template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_section_credit_note',
		),
		'ywpi_credit_note_notes'                 => array(
			'name'      => __( 'Notes on credit note', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_credit_note_notes' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes in the credit notes.', 'yith-woocommerce-pdf-invoice' ),
		),
		'ywpi_credit_note_footer'                => array(
			'name'      => __( 'Credit note footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_credit_note_footer' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show in the footer of the credit notes.', 'yith-woocommerce-pdf-invoice' ),
		),
		'ywpi_credit_note_template_options_end'  => array(
			'type' => 'sectionend',
			'id'   => 'ywpi_section_credit_note_end',
		),
		'ywpi_packing_slip_template_options'     => array(
			'name' => __( 'Packing slip template settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
			'id'   => 'ywpi_section_packing_slip',
		),
		'packing_slip_notes'                     => array(
			'name'      => __( 'Packing slip notes', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_packing_slip_notes' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes in the packing slip.', 'yith-woocommerce-pdf-invoice' ),
		),
		'packing_slip_footer'                    => array(
			'name'      => __( 'Packing slip footer', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'id'        => apply_filters( 'ywpi_option_name', 'ywpi_packing_slip_footer' ),
			'css'       => 'width:80%; height: 90px;',
			'desc'      => __( 'Type the text to show as notes in the packing slip.', 'yith-woocommerce-pdf-invoice' ),
		),
		'ywpi_packing_slip_template_options_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywpi_section_packing_slip_end',
		),
	),
);

return apply_filters( 'ywpi_vendor_options', $vendor_options );
