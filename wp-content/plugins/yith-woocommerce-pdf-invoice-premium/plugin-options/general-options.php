<?php
/**
 * General options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$current_date = getdate();

$yith_ywcdd_installed = ( defined( 'YITH_DELIVERY_DATE_PREMIUM' ) && YITH_DELIVERY_DATE_PREMIUM );
$yith_ywcdd_landing   = 'https://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/';

$general_options = array(

	'general' => array(
		// Invoice options.
		'invoice-options'               => array(
			'title' => __( 'Invoice options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'invoice_generation'            => array(
			'title'     => __( 'Invoices creation', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_invoice_generation',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'manual' => __( 'Manually - Create the invoices only for the specific orders you select', 'yith-woocommerce-pdf-invoice' ),
				'auto'   => __( 'Automatically - Create the invoices for all orders made in your store', 'yith-woocommerce-pdf-invoice' ),
			),
			'default'   => 'manual',
			'std'       => 'manual',
			'desc'      => __(
				'Choose if you want to use the plugin to automatically generate invoices for all orders, or if you want to manually create invoices
		    only for the orders you select.',
				'yith-woocommerce-pdf-invoice'
			),
		),
		'create_invoice_on'             => array(
			'title'     => __( 'Date of invoice creation', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_create_invoice_on',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'new'        => __( 'Date of order creation', 'yith-woocommerce-pdf-invoice' ),
				'processing' => __( 'Date when order changes to processing status', 'yith-woocommerce-pdf-invoice' ),
				'completed'  => __( 'Date when order changes to completed status', 'yith-woocommerce-pdf-invoice' ),
			),
			'std'       => 'completed',
			'desc'      => __( 'Choose on which date the invoices will be automatically generated.', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_invoice_generation',
				'value' => 'auto',
				'type'  => 'fadeIn',
			),
		),
		'date_to_show_in_invoice'       => array(
			'title'     => __( 'Date to show in the invoice', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_date_to_show_in_invoice',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'new'              => __( 'Date of order creation', 'yith-woocommerce-pdf-invoice' ),
				'completed'        => __( 'Date the order is completed', 'yith-woocommerce-pdf-invoice' ),
				'invoice_creation' => __( 'Date of invoice creation', 'yith-woocommerce-pdf-invoice' ),
			),
			'std'       => 'completed',
			'desc'      => __( 'Choose which date to show in the invoices', 'yith-woocommerce-pdf-invoice' ),
		),
		'invoices_numbers'              => array(
			'title'     => __( 'Invoice numbers', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_invoices_numbers',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'order_number' => __( 'Use Order number', 'yith-woocommerce-pdf-invoice' ),
				'order_id'     => __( 'Use Order ID', 'yith-woocommerce-pdf-invoice' ),
				'sequential'   => __( 'Start sequential numbering from a specific number', 'yith-woocommerce-pdf-invoice' ),
			),
			'std'       => 'completed',
			'desc'      => __( 'Choose how to generate Invoice numbers.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'sequential',
		),
		'next_invoice_number'           => array(
			'name'      => __( 'Start invoice numbers from', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'id'        => 'ywpi_invoice_number',
			'desc'      => sprintf(
				__(
					'Enter the number for the next invoice (if empty, the first invoice will be the number "1"). 
		    %1$s Sequential numbering will start from this number.',
					'yith-woocommerce-pdf-invoice'
				),
				'<br>'
			),
			'default'   => 1,
			'std'       => 1,
			'deps'      => array(
				'id'    => 'ywpi_invoices_numbers',
				'value' => 'sequential',
				'type'  => 'fadeIn',
			),
		),
		'next_invoice_year'             => array(
			'name'    => __( 'Billing year', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'hidden',
			'id'      => 'ywpi_invoice_year_billing',
			'default' => $current_date['year'],
		),
		'ywpi_enable_number_of_digits'  => array(
			'name'      => __( 'Enforce minimum invoice number length', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_number_of_digits',
			'desc'      => __( 'Enable this option to set a minimum length for your invoice numbers. If the invoice number does not meet this length, it will get prefixed with zeros.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'ywpi_number_of_digits_invoice' => array(
			'name'      => __( 'Number of digits', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'id'        => 'ywpi_number_of_digits_invoice',
			'desc'      => __( 'Enter the minimum number of digits for your invoice number.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => '0',
			'deps'      => array(
				'id'    => 'ywpi_enable_number_of_digits',
				'value' => 'yes',
			),
		),
		'invoice_reset'                 => array(
			'name'      => __( 'Reset on 1st January', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_invoice_reset',
			'desc'      => __( 'Enable to automatically reset and restart sequential numbering from number "1" each January 1st.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => false,
			'deps'      => array(
				'id'    => 'ywpi_invoices_numbers',
				'value' => 'sequential',
				'type'  => 'fadeIn',
			),
		),
		'invoice_check_duplicated'      => array(
			'name'      => __( 'Avoid duplicate invoices', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_invoice_check_duplicated',
			'desc'      => __(
				'Enable this option if you want to check old invoice numbers before creating a new one, to avoid duplication.<br><b>Note: </b> this option may affect the performance of your site, if you have many orders or a poorly performing server.',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => false,
			'deps'      => array(
				'id'    => 'ywpi_invoices_numbers',
				'value' => 'sequential',
				'type'  => 'fadeIn',
			),
		),
		'electronic-invoice'            => array(
			'name'      => __( 'Enable eletronic invoice (Italian Customers)', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_electronic_invoice_enable',
			'desc'      => __(
				'Enable the electronic invoice system and save the options. Reload the page and you\'ll be able to see a new tab called  "Electronic Invoice" and download an XML to forward to "Agenzia delle Entrate".',
				'yith-woocommerce-pdf-invoice'
			),
			'default'   => 'no',
		),
		'view_generated_documents'      => array(
			'title'     => __( 'Viewing Generated Documents', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_pdf_invoice_behaviour',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'download' => __( 'Download files as PDF', 'yith-woocommerce-pdf-invoice' ),
				'open'     => __( 'Open file in the same browser tab', 'yith-woocommerce-pdf-invoice' ),
				'open_tab' => __( 'Open file in a new browser tab', 'yith-woocommerce-pdf-invoice' ),
			),
			'default'   => 'download',
			'std'       => 'download',
			'desc'      => __( 'Choose how to show generated documents to your users.', 'yith-woocommerce-pdf-invoice' ),
		),
		'invoice-options-options-end'   => array(
			'type' => 'sectionend',
			'id'   => 'yith-ywpi-invoice-options',
		),

		// Pro-forma options.
		'pro-forma-options'             => array(
			'title' => __( 'Pro-forma options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
		),
		'pro-forma'                     => array(
			'name'      => __( 'Allow users to download pro-forma document in My Account', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_pro_forma',
			'desc'      => __( 'If enabled, customers can download the "Pro-Forma" document from their "My Account -> Orders" page.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'send-pro-forma'                => array(
			'name'      => __( 'Attach pro-forma in new orders emails', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_send_pro_forma',
			'desc'      => __( 'If enabled, customers will receive a pro-forma document as an attachment of their order\'s emails', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'pro-forma-options-end'         => array(
			'type' => 'sectionend',
		),

		// Receipt options.
		'receipts-options'              => array(
			'title' => __( 'Receipts options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
		),
		'receipts'                      => array(
			'name'      => __( 'Enable receipts', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_receipts',
			'desc'      => __( 'Check this option to allow your customers to select at checkout if they prefer to receive a receipt instead of an invoice.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'receipts-options-end'          => array(
			'type' => 'sectionend',
		),

		// Credit notes options.
		'credit-notes-options'          => array(
			'title' => __( 'Credit notes options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
		),
		'credit-notes'                  => array(
			'name'      => __( 'Enable credit notes for refunded orders', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_credit_notes',
			'desc'      => sprintf(
				__(
					'Check this option to enable credit note management for refunded orders.%1$s
		    Customers can download the "Credit note" document from their "My Account -> Orders" page ',
					'yith-woocommerce-pdf-invoice'
				),
				'<br>'
			),
			'default'   => 'no',
		),
		'credit_note_next_number'       => array(
			'name'      => __( 'Credit note sequential numbers start from', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'id'        => 'ywpi_credit_note_next_number',
			'desc'      => sprintf(
				__(
					'Enter the number for the next credit note (if empty, the first credit note will be with number "1")%1$s
		    Sequential numbering will start from this number',
					'yith-woocommerce-pdf-invoice'
				),
				'<br>'
			),
			'default'   => 1,
			'std'       => 1,
			'deps'      => array(
				'id'    => 'ywpi_enable_credit_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'credit_note_reset'             => array(
			'name'      => __( 'Reset on 1st January', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_credit_note_reset',
			'desc'      => __( 'Enable to automatically reset and restart sequential numbering from "1" each 1st January.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => false,
			'deps'      => array(
				'id'    => 'ywpi_enable_credit_notes',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'credit-notes-options-end'      => array(
			'type' => 'sectionend',
		),

		// Packing slip options.
		'packing-slip-options'          => array(
			'title' => __( 'Packing slip options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
		),

		'packing_slip'                  => array(
			'name'      => __( 'Enable packing slip (Shipping document)', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_enable_packing_slip',
			'desc'      => __( 'Check this option to enable packing slip management for products that will be shipped.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),

		'packing_slip_generation'       => array(
			'title'     => __( 'Packing slip generation', 'yith-woocommerce-pdf-invoice' ),
			'id'        => 'ywpi_packing_slip_generation',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'manual' => __( 'Manually - Create the packing slip only for the specific orders you select', 'yith-woocommerce-pdf-invoice' ),
				'auto'   => __( 'Automatically - Create the packing slip for all orders made in your store', 'yith-woocommerce-pdf-invoice' ),
			),
			'default'   => 'manual',
			'std'       => 'manual',
			'desc'      => __(
				'Choose if you want to use the plugin to automatically generate packing slips for all orders, or if you want to manually create packing slips
				only for the orders you select.',
				'yith-woocommerce-pdf-invoice'
			),
			'deps'      => array(
				'id'    => 'ywpi_enable_packing_slip',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'packing-slip-options-end'      => array(
			'type' => 'sectionend',
		),

		// Checkout options.
		'checkout-options'              => array(
			'title' => __( 'Checkout options', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
		),
		'ask_ssn_number'                => array(
			'name'      => __( 'Add SSN field in Checkout page', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_ask_ssn_number',
			'desc'      => __( 'Enable to add a SSN input field in form of Checkout page', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'ask_ssn_number_required'       => array(
			'name'      => __( 'Make SSN field mandatory', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ask_ssn_number_required',
			'desc'      => __( 'Enable to make the SSN field mandatory to complete checkout process', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywpi_ask_ssn_number',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'ask_vat_number'                => array(
			'name'      => __( 'Add VAT field in Checkout page', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_ask_vat_number',
			'desc'      => __( 'Enable to add a VAT input field in form of Checkout page', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'yes',
		),
		'ask_vat_number_required'       => array(
			'name'      => __( 'Make VAT field mandatory', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ask_vat_number_required',
			'desc'      => __( 'Enable to make the VAT field mandatory to complete checkout process', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywpi_ask_vat_number',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'checkout-options-end'          => array(
			'type' => 'sectionend',
		),

		// Delivery Date integration.
		'delivery-date-start'           => array(
			'title' => __( 'YITH WooCommerce Delivery Date integration', 'yith-woocommerce-pdf-invoice' ),
			'type'  => 'title',
			'desc'  => ! $yith_ywcdd_installed ? sprintf( __( 'In order to use this integration you have to install and activate YITH WooCommerce Delivery Date. <a href="%s">Learn more</a>', 'yith-woocommerce-pdf-invoice' ), $yith_ywcdd_landing ) : '',
			'id'    => 'yith_ywpi_yith_ywcdd',
		),
		'ywpi_show_delivery_info'       => array(
			'name'      => __( 'Show Delivery info', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_show_delivery_info',
			'desc'      => __( 'Enable to show the delivery details when using the <a target="_blank" href="https://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/">YITH WooCommerce Delivery Date</a> plugin.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'delivery-date-end'             => array(
			'type' => 'sectionend',
			'id'   => 'yith_ywpi_yith_ywcdd',
		),
	),
);


return apply_filters( 'ywpi_general_options', $general_options );
