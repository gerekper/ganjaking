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

/**
 * Show and save option for the specific vendor
 */
$current_date = getdate();
$vendor = yith_get_vendor(get_current_user_id(), 'user');

$vendor_options = array(

    'vendor' => array(
        'section_vendor_settings' => array(
            'name' => __('Vendor invoice settings', 'yith-woocommerce-pdf-invoice'),
            'type' => 'title',
        ),
        'next_invoice_number' => array(
            'name' => __('Next invoice number', 'yith-woocommerce-pdf-invoice'),
            'type' => 'number',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_number'),
            'desc' => __('Invoice number for the next invoice.', 'yith-woocommerce-pdf-invoice'),
            'default' => 1,
            'std' => 1,
            'custom_attributes' => array(
                'min' => 1,
                'step' => 1,
                'required' => 'required',
            ),
        ),
        'next_invoice_year' => array(
            'name' => __('billing year', 'yith-woocommerce-pdf-invoice'),
            'type' => 'hidden',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_year_billing'),
            'default' => $current_date['year'],
        ),
        'invoice_prefix' => array(
            'name' => __('Invoice prefix', 'yith-woocommerce-pdf-invoice'),
            'type' => 'text',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_prefix'),
            'desc' => __('Type a text to add as a prefix to the invoice number. Leave it blank if no prefix has to be used',
                'yith-woocommerce-pdf-invoice'),
        ),
        'invoice_suffix' => array(
            'name' => __('Invoice suffix', 'yith-woocommerce-pdf-invoice'),
            'type' => 'text',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_suffix'),
            'desc' => __('Type a text to add as a suffix to the invoice number. Leave it blank if no suffix has to be used',
                'yith-woocommerce-pdf-invoice'),
        ),
        'invoice_number_format' => array(
            'name' => __('Invoice number format', 'yith-woocommerce-pdf-invoice'),
            'type' => 'text',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_number_format'),
            'desc' => __('Set the format for the invoice number. Use [number], [prefix] and [suffix] as placeholders. <b>The [number] placeholder is required</b>. If not
specified,
it will be queued to the corresponding text.</b>', 'yith-woocommerce-pdf-invoice'),
            'default' => '[prefix]/[number]/[suffix]',
            'custom_attributes' => array(
                'required' => 'required',
            ),
        ),
        'invoice_reset' => array(
            'name' => __('Reset on 1st January', 'yith-woocommerce-pdf-invoice'),
            'type' => 'checkbox',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_reset'),
            'desc' => __('Set restart from 1 on 1st January.', 'yith-woocommerce-pdf-invoice'),
            'default' => false,
        ),
        'section_vendor_settings_end' => array(
            'type' => 'sectionend',
        ),
        'section_template' => array(
            'name' => __('Template settings', 'yith-woocommerce-pdf-invoice'),
            'type' => 'title',
        ),
        'company_name' => array(
            'name' => __('Company name', 'yith-woocommerce-pdf-invoice'),
            'type' => 'text',
            'id' => apply_filters('ywpi_option_name', 'ywpi_company_name'),
            'desc' => __('Set company name to be shown on invoices', 'yith-woocommerce-pdf-invoice'),
            'default' => __('Your company name', 'yith-woocommerce-pdf-invoice'),
        ),
        'company_logo' => array(
            'name' => __('Your company logo', 'yith-woocommerce-pdf-invoice'),
            'type' => 'ywpi_logo',
            'id' => apply_filters('ywpi_option_name', 'ywpi_company_logo'),
            'desc' => __('Set a default logo to be shown', 'yith-woocommerce-pdf-invoice'),
        ),
        'company_details' => array(
            'name' => __('Company details', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_company_details'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Set company details to be used on the invoice', 'yith-woocommerce-pdf-invoice'),
            'default' => __('Your company details
Address
City, State'),
        ),
        'invoice_notes' => array(
            'name' => __('Invoice notes', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_notes'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as notes on invoices.', 'yith-woocommerce-pdf-invoice'),
        ),
        'invoice_footer' => array(
            'name' => __('Invoice footer', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_invoice_footer'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as footer on invoices.', 'yith-woocommerce-pdf-invoice'),
        ),
        'ywpi_credit_note_notes' => array(
	        'name' => __('Notes on credit note', 'yith-woocommerce-pdf-invoice'),
	        'type' => 'textarea',
	        'id' => apply_filters('ywpi_option_name', 'ywpi_credit_note_notes'),
	        'css' => 'width:80%; height: 90px;',
	        'desc' => __('Type the text to show as notes on credit notes.', 'yith-woocommerce-pdf-invoice'),
        ),
        'ywpi_credit_note_footer' => array(
	        'name' => __('Credit note footer', 'yith-woocommerce-pdf-invoice'),
	        'type' => 'textarea',
	        'id' => apply_filters('ywpi_option_name', 'ywpi_credit_note_footer'),
	        'css' => 'width:80%; height: 90px;',
	        'desc' => __('Type the text to show as footer on credit notes.', 'yith-woocommerce-pdf-invoice'),
        ),
        'pro_forma_notes' => array(
            'name' => __('Proforma Invoice notes', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_pro_forma_notes'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as notes on Proforma invoices.', 'yith-woocommerce-pdf-invoice'),
        ),
        'pro_forma_footer' => array(
            'name' => __('Proforma invoice footer', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_pro_forma_footer'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as footer on proforma invoices.', 'yith-woocommerce-pdf-invoice'),
        ),
        'packing_slip_notes' => array(
            'name' => __('Packing slip notes', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_packing_slip_notes'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as notes on packing slip.', 'yith-woocommerce-pdf-invoice'),
        ),
        'packing_slip_footer' => array(
            'name' => __('Packing slip footer', 'yith-woocommerce-pdf-invoice'),
            'type' => 'textarea',
            'id' => apply_filters('ywpi_option_name', 'ywpi_packing_slip_footer'),
            'css' => 'width:80%; height: 90px;',
            'desc' => __('Type the text to show as footer on packing slip.', 'yith-woocommerce-pdf-invoice'),
        ),
        'general-description' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'html' => __('We recommend verifying carefully the correct data provided to generate the invoice. The plugin\'s authors refuse any responsibility about possible mistakes or shortcomings when generating invoices.','yith-woocommerce-pdf-invoice'),
        ),
        'section_template_end' => array(
            'type' => 'sectionend',
        ),
    ),
);

return apply_filters('ywpi_vendor_options',$vendor_options);
