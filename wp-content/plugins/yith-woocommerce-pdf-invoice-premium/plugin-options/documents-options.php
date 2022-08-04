<?php
/**
 * Documents options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$documents_options = array(

	'documents' => array(

		// Documents format options.
		'documents_format_settings'         => array(
			'name' => __( 'Documents format', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),

		'documents_date_format'             => array(
			'name'      => __( 'Date format for all documents', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_invoice_date_format',
			'default'   => 'd/m/Y',
			'std'       => 'd/m/Y',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'd/m/Y' => __( 'd/m/Y', 'yith-woocommerce-pdf-invoice' ),
				'd-m-Y' => __( 'd-m-Y', 'yith-woocommerce-pdf-invoice' ),
				'm/d/Y' => __( 'm/d/Y', 'yith-woocommerce-pdf-invoice' ),
				'm-d-Y' => __( 'm-d-Y', 'yith-woocommerce-pdf-invoice' ),
				'Y-M-D' => __( 'Y-M-D', 'yith-woocommerce-pdf-invoice' ),
			),
			'desc'      => sprintf(
				__(
					'Set date format as it should appear on documents (default is d/m/Y).%1$s%2$sLearn more about Formatting Date and Time%3$s',
					'yith-woocommerce-pdf-invoice'
				),
				'<br>',
				'<a target="_blank" href="https://wordpress.org/support/article/formatting-date-and-time/">',
				'</a>'
			),
		),
		'enable_invoice_prefix_sufix'       => array(
			'name'      => __( 'Add prefix/suffix to the invoice number', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_invoice_prefix_sufix',
			'desc'      => __( 'Enable to show a custom text before or after the invoice number.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'invoice_prefix'                    => array(
			'name'      => __( 'Invoice prefix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_invoice_prefix',
			'desc'      => sprintf(
				__(
					'Set a text to show before the invoice number. Example: with "YITH", invoice number will be "%1$sYITH%2$s-485940"',
					'yith-woocommerce-pdf-invoice'
				),
				'<b>',
				'</b>'
			),
			'deps'      => array(
				'id'    => 'ywpi_enable_invoice_prefix_sufix',
				'value' => 'yes',
			),
		),
		'invoice_suffix'                    => array(
			'name'      => __( 'Invoice suffix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_invoice_suffix',
			'desc'      => sprintf(
				__(
					'Set a text to show after the invoice number. Example: with "YITH", invoice number will be "485940-%1$sYITH%2$s"',
					'yith-woocommerce-pdf-invoice'
				),
				'<b>',
				'</b>'
			),
			'deps'      => array(
				'id'    => 'ywpi_enable_invoice_prefix_sufix',
				'value' => 'yes',
			),
		),
		'invoice_number_format'             => array(
			'name'      => __( 'Invoice number format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_invoice_number_format',
			'desc'      => __(
				'Set the format for the invoice number. Use [number], [prefix], [suffix], [year], [month] and [day] as placeholders. <b>The [number] placeholder is required</b>, if not
			specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => '[prefix]/[number]/[suffix]',
		),
		'invoice_filename_format'           => array(
			'name'      => __( 'Invoice file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_invoice_filename_format',
			'desc'      => '<br>' . __(
				'Set the format for the invoice file name. Use [number], [prefix], [suffix], [year], [month], and [day] as placeholders.
<b>The [number] placeholder is required</b>. If not specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'css'       => 'width:60%;',
			'default'   => 'Invoice_[number]',
		),
		'enable_credit_note_prefix_sufix'   => array(
			'name'      => __( 'Add prefix/suffix to the credit note number', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_credit_note_prefix_sufix',
			'desc'      => __( 'Enable to show a custom text before or after the credit note number.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'credit_note_prefix'                => array(
			'name'      => __( 'Credit note prefix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_credit_note_prefix',
			'desc'      => sprintf(
				__(
					'Set a text to show before the credit note number. Example: with "YITH", credit note number will be "%1$sYITH%2$s-485940"',
					'yith-woocommerce-pdf-invoice'
				),
				'<b>',
				'</b>'
			),
			'deps'      => array(
				'id'    => 'ywpi_enable_credit_note_prefix_sufix',
				'value' => 'yes',
			),
		),
		'credit_note_suffix'                => array(
			'name'      => __( 'Credit note suffix', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_credit_note_suffix',
			'desc'      => sprintf(
				__(
					'Set a text to show after the credit note number. Example: with "YITH", credit note number will be "485940-%1$sYITH%2$s"',
					'yith-woocommerce-pdf-invoice'
				),
				'<b>',
				'</b>'
			),
			'deps'      => array(
				'id'    => 'ywpi_enable_credit_note_prefix_sufix',
				'value' => 'yes',
			),
		),
		'credit_note_number_format'         => array(
			'name'      => __( 'Credit note number format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_credit_note_number_format',
			'desc'      => __(
				'Set the format for the credit note number. Use [number], [prefix], [suffix], [year], [month] and [day] as placeholders. <b>The [number] placeholder is required</b>, if not
specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => '[prefix]/[number]/[suffix]',
		),
		'credit_note_filename_format'       => array(
			'name'      => __( 'Credit note file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_credit_note_filename_format',
			'desc'      => '<br>' . __(
				'Set the format for the credit note file name. Use [number], [prefix], [suffix], [year], [month] and [day] as placeholders. <b>The [number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'css'       => 'width:60%;',
			'default'   => 'Credit_[number]',
		),
		'pro_forma_invoice_filename_format' => array(
			'name'      => __( 'Proforma invoice file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_pro_forma_invoice_filename_format',
			'desc'      => '<br>' . __(
				'Set the format for the pro-forma file name. Use [order_number], [year], [month] and [day] as placeholders. <b>The [order_number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'css'       => 'width:60%;',
			'default'   => 'Pro_Forma_[order_number]',
		),
		'packing_slip_filename_format'      => array(
			'name'      => __( 'Packing slip file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_packing_slip_filename_format',
			'desc'      => '<br>' . __(
				'Set the format for the packing slip file name. Use [order_number], [year], [month] and [day] as placeholders. <b>The [order_number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.',
				'yith-woocommerce-pdf-invoice'
			),
			'css'       => 'width:60%;',
			'default'   => 'Shipping_List_[order_number]',
		),

		'documents_format_settings_end'     => array(
			'type' => 'sectionend',
		),
	),
);


return apply_filters( 'ywpi_documents_options', $documents_options );
