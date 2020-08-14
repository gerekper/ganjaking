<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$current_date = getdate();

$general_options = array(

    'general' => array(
        array(
            'name' => __('Generating documents', 'yith-woocommerce-pdf-invoice'),
            'type' => 'title',
        ),
        'preview_mode' => array(
            'name' => __('Preview mode', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_preview_mode',
            'desc' => __('Flag this item if you want to preview the invoice. Useful to preview changes while customizing the invoice template.
			When this option is enabled, no counter will be incremented.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),
        'packing_slip' => array(
            'name' => __('Enable packing slip', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_enable_packing_slip',
            'desc' => __('Check this option to enable packing slip management.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'yes',
        ),
        'pro-forma' => array(
            'name' => __('Enable proforma', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_enable_pro_forma',
            'desc' => __('Check this option to enable proforma document management.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'yes',
        ),
        'credit-notes' => array(
            'name' => __('Enable credit notes', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_enable_credit_notes',
            'desc' => __('Check this option to enable credit note management.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),
        'electronic-invoice' => array(
            'name' => __('Enable eletronic invoice (Italian Customers)', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_electronic_invoice_enable',
            'desc' => sprintf( __('Check this option to enable electronic invoice system. You\' be able to download a XML to forward to "Agenzia delle Entrate".%3$s
                       %1$s Only for Italian Customers', 'yith-woocommerce-pdf-invoice'),'<b>','</b>','<br>' ),
            'default' => 'no',
        ),
        'receipts' => array(
	        'name' => __('Enable receipts', 'yith-woocommerce-pdf-invoice'),
	        'type'    => 'yith-field',
	        'yith-type' => 'onoff',
	        'id' => 'ywpi_enable_receipts',
	        'desc' => __('Enable this option to allow your customers to select at checkout if they prefer to receive a receipt instead of an invoice.', 'yith-woocommerce-pdf-invoice'),
	        'default' => 'no',
        ),
        array(
            'type' => 'sectionend',
        ),
        array(
            'name' => __('General settings', 'yith-woocommerce-pdf-invoice'),
            'type' => 'title',
        ),
        'invoice_folder_format' => array(
            'name' => __('Invoice folder format', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id' => 'ywpi_invoice_folder_format',
            'desc' => __('Set the folder where you want to store documents. Use [year], [month], [day] as placeholders. Example:
			"Invoices/[year]/[month]" for invoices stored by year and month; leave it blank to store documents in root folder.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'Invoices',
        ),
        'invoice_date_format' => array(
            'name' => __('Document date format', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_invoice_date_format',
            'desc' => __('Set date format as it should appear on documents (default is d/m/Y).', 'yith-woocommerce-pdf-invoice'),
            'default' => 'd/m/Y',
            'std' => 'd/m/Y',
            'type'    => 'yith-field',
            'yith-type' => 'select',
            'class'   => 'wc-enhanced-select',
            'options' => array(
                'd/m/Y' => __('d/m/Y', 'yith-woocommerce-pdf-invoice'),
                'd-m-Y' => __('d-m-Y', 'yith-woocommerce-pdf-invoice'),
                'm/d/Y' => __('m/d/Y', 'yith-woocommerce-pdf-invoice'),
                'm-d-Y' => __('m-d-Y', 'yith-woocommerce-pdf-invoice'),
                'Y-M-D' => __('Y-M-D', 'yith-woocommerce-pdf-invoice'),
            ),
        ),
        array(
            'title' => __('Generate invoices', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_invoice_generation',
            'type' => 'yith-field',
            'yith-type' => 'select',
            'class'   => 'wc-enhanced-select',
            'options' => array(
                'auto' => __("Automatically", 'yith-woocommerce-pdf-invoice'),
                'manual' => __("Manually", 'yith-woocommerce-pdf-invoice'),
            ),
            'default' => 'manual',
            'std' => 'manual',
            'desc'  =>  sprintf( __('In case invoices are generated manually, we kindly invite you to %1$spay attention%2$s to
                            their creation ordering since, according to every country, %1$sDate%2$s and %1$sInvoice number%2$s must be sequential.%3$s
                             Please create your invoices considering the date you decided to use in the following option.','yith-woocommerce-pdf-invoice'),'<b>','</b>','<br>')
        ),
        array(
            'title' => __('Generate packing slip', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_packing_slip_generation',
            'type' => 'yith-field',
            'yith-type' => 'select',
            'class'   => 'wc-enhanced-select',
            'options' => array(
                'auto' => __("Automatically", 'yith-woocommerce-pdf-invoice'),
                'manual' => __("Manually", 'yith-woocommerce-pdf-invoice'),
            ),
            'default' => 'manual',
            'std' => 'manual',
        ),
        array(
            'title' => __('Choose when generate the invoice', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_create_invoice_on',
            'type' => 'yith-field',
            'yith-type' => 'radio',
            'options' => array(
                'new' => __("Order creation", 'yith-woocommerce-pdf-invoice'),
                'processing' => __("When order turns into processing status", 'yith-woocommerce-pdf-invoice'),
                'completed' => __("When order turns into completed status", 'yith-woocommerce-pdf-invoice'),
            ),
            'std' => 'completed',
        ),
        array(
            'title' => __('Date to show in the invoice', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_date_to_show_in_invoice',
            'type' => 'yith-field',
            'yith-type' => 'radio',
            'options' => array(
                'new' => __("Date of order creation", 'yith-woocommerce-pdf-invoice'),
                'completed' => __("Date when order turns into completed status", 'yith-woocommerce-pdf-invoice'),
                'invoice_creation' => __("Date of invoice creation", 'yith-woocommerce-pdf-invoice'),
            ),
            'std' => 'completed',
        ),
        array(
            'title' => __('How to show the generated document?', 'yith-woocommerce-pdf-invoice'),
            'id' => 'ywpi_pdf_invoice_behaviour',
            'type' => 'yith-field',
            'yith-type' => 'radio',
            'options' => array(
                'download' => __("Download file", 'yith-woocommerce-pdf-invoice'),
                'open' => __("Open file in the browser", 'yith-woocommerce-pdf-invoice'),
            ),
            'default' => 'download',
            'std' => 'download',
        ),
        array(
            'type' => 'sectionend',
        ),
        array(
            'name' => __('Advanced settings', 'yith-woocommerce-pdf-invoice'),
            'type' => 'title',
        ),
        'send-pro-forma' => array(
            'name' => __('Send proforma', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_send_pro_forma',
            'desc' => __('Attach proforma invoice to new order email', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),
        'ask_ssn_number' => array(
            'name' => __('SSN number', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_ask_ssn_number',
            'desc' => __('Add a SSN number field on checkout page', 'yith-woocommerce-pdf-invoice'),
            'default' => 'yes',
        ),
        'ask_ssn_number_required' => array(
            'name' => __('Mandatory SSN number', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ask_ssn_number_required',
            'desc' => __('The SSN number is mandatory to complete the checkout process', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),
        'ask_vat_number' => array(
            'name' => __('VAT number', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_ask_vat_number',
            'desc' => __('Add a VAT number field on checkout page', 'yith-woocommerce-pdf-invoice'),
            'default' => 'yes',
        ),
        'ask_vat_number_required' => array(
            'name' => __('Mandatory VAT number', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ask_vat_number_required',
            'desc' => __('The VAT number is mandatory to complete the checkout process', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),
        'ywpi_show_delivery_info' => array(
            'name' => __('Show delivery date', 'yith-woocommerce-pdf-invoice'),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id' => 'ywpi_show_delivery_info',
            'desc' => __('Show the delivery details when using the <a target="_blank" href="https://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/">YITH WooCommerce Delivery Date</a> plugin.', 'yith-woocommerce-pdf-invoice'),
            'default' => 'no',
        ),

        'dropbox' => array(
            'name' => __('Authorize Dropbox', 'yith-woocommerce-pdf-invoice'),
            'desc' => __('Set automatic document backup to Dropbox.', 'yith-woocommerce-pdf-invoice'),
            'type' => 'ywpi_dropbox',
            'id' => 'ywpi_dropbox_key',
            'default' => 'yes',
        ),
        'dropbox_folder' => array(
            'name' => __('Dropbox folder', 'yith-woocommerce-pdf-invoice'),
            'desc' => __('Choose the name of the Dropbox folder where to save the files.', 'yith-woocommerce-pdf-invoice'),
            'type' => 'ywpi_dropbox_folder',
            'id' => 'ywpi_dropbox_folder',
            'default' => 'Invoices',
        ),
        'general-description' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'html' => __('We recommend verifying carefully the correct data provided to generate the invoice. The plugin\'s authors refuse any responsibility about possible mistakes or shortcomings when generating invoices.','yith-woocommerce-pdf-invoice'),
        ),
        array(
            'type' => 'sectionend',
        ),
    ),
);


return apply_filters('ywpi_general_options', $general_options);
