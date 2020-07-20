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

$current_date = getdate();

$general_options = array(

	'documents' => array(
		'document_settings'                 => array(
			'name' => __( 'Document settings', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'invoice_number_format'             => array(
			'name'              => __( 'Invoice number format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_invoice_number_format',
			'desc'              => __( 'Set the format for the invoice number. Use [number], [prefix], [suffix], [year], [month] and [day] as placeholders. <b>The [number] placeholder is required</b>, if not
specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'default'           => '[prefix]/[number]/[suffix]',
		),
		'credit_note_number_format'             => array(
			'name'              => __( 'Credit note number format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_credit_note_number_format',
			'desc'              => __( 'Set the format for the invoice number. Use [number], [prefix], [suffix], [year], [month] and [day] as placeholders. <b>The [number] placeholder is required</b>, if not
specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'default'           => '[prefix]/[number]/[suffix]',
		),
		'invoice_filename_format'           => array(
			'name'              => __( 'Invoice file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_invoice_filename_format',
			'desc'              => '<br>' . __( 'Set the format for the invoice file name. Use [number], [prefix], [suffix], [year], [month], [day] as placeholders.
<b>The [number] placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'css'               => 'width:60%;',
			'default'           => 'Invoice_[number]',
		),
		'credit_note_filename_format'           => array(
			'name'              => __( 'Credit note file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_credit_note_filename_format',
			'desc'              => '<br>' . __( 'Set the format for the credit note file name. Use [number], [prefix], [suffix], [year], [month], [day] as placeholders. <b>The [number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'css'               => 'width:60%;',
			'default'           => 'Credit_[number]',
		),
		'pro_forma_invoice_filename_format' => array(
			'name'              => __( 'Proforma invoice file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_pro_forma_invoice_filename_format',
			'desc'              => '<br>' . __( 'Set the format for the proforma file name. Use [order_number], [year], [month], [day] as placeholders. <b>The [order_number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'css'               => 'width:60%;',
			'default'           => 'Pro_Forma_[order_number]',
		),
		'packing_slip_filename_format'     => array(
			'name'              => __( 'Packing slip file name format', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'                => 'ywpi_packing_slip_filename_format',
			'desc'              => '<br>' . __( 'Set the format for the packing slip file name. Use [order_number], [year], [month], [day] as placeholders. <b>The [order_number]
placeholder is necessary</b>. If not specified, it will be queued to the corresponding text.', 'yith-woocommerce-pdf-invoice' ),
			'css'               => 'width:60%;',
			'default'           => 'Shipping_list_[order_number]',
		),

		'document_settings_end' => array(
			'type' => 'sectionend',
		),
		'invoice_settings'      => array(
			'name' => __( 'Invoice number', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'next_invoice_number'   => array(
			'name'              => __( 'Next invoice number', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'number',
			'id'                => 'ywpi_invoice_number',
			'desc'              => __( 'Choose the invoice number for the next invoice (use this option if you want to
			 move away from automatic numeration).',
                'yith-woocommerce-pdf-invoice' ),
			'default'           => 1,
			'std'               => 1,
		),
		'next_invoice_year'     => array(
			'name'    => __( 'Billing year', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'hidden',
			'id'      => 'ywpi_invoice_year_billing',
			'default' => $current_date['year'],
		),
		'invoice_prefix'        => array(
			'name' => __( 'Invoice prefix', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'   => 'ywpi_invoice_prefix',
			'desc' => __( 'Set a text to be added as a prefix to the invoice number. Leave it blank if no prefix has to be used',
                'yith-woocommerce-pdf-invoice' ),
		),
		'invoice_suffix'        => array(
			'name' => __( 'Invoice suffix', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'   => 'ywpi_invoice_suffix',
			'desc' => __( 'Set a text to be added as a suffix to the invoice number. Leave it blank if no suffix has to be used',
                'yith-woocommerce-pdf-invoice' ),
		),
		'invoice_reset'         => array(
			'name'    => __( 'Reset on 1st January', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'id'      => 'ywpi_invoice_reset',
			'desc'    => __( 'Set restart from 1 on 1st January.', 'yith-woocommerce-pdf-invoice' ),
			'default' => false,
		),
		'invoice_settings_end'  => array(
			'type' => 'sectionend',
		),
		'credit_note_settings'      => array(
			'name' => __( 'Credit note number', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'credit_note_next_number'   => array(
			'name'              => __( 'Next credit note number', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'number',
			'id'                => 'ywpi_credit_note_next_number',
			'desc'              => __( 'Choose the number for the next credit note (use this option if you want to
			 move away from automatic numeration)', 'yith-woocommerce-pdf-invoice' ),
			'default'           => 1,
			'std'               => 1,
		),
		'credit_note_year'     => array(
			'name'    => __( 'Billing year', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'hidden',
			'id'      => 'ywpi_credit_note_year_billing',
			'default' => $current_date['year'],
		),
		'credit_note_prefix'        => array(
			'name' => __( 'Credit note prefix', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'   => 'ywpi_credit_note_prefix',
			'desc' => __( 'Set a text to be used as a prefix to the credit note number. Leave it blank if no prefix has to be used',
                'yith-woocommerce-pdf-invoice' ),
		),
		'credit_note_suffix'        => array(
			'name' => __( 'Credit note suffix', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'text',
			'id'   => 'ywpi_credit_note_suffix',
			'desc' => __( 'Set a text to be used as a suffix to the credit note number. Leave it blank if no suffix has to be used',
                'yith-woocommerce-pdf-invoice' ),
		),
		'credit_note_reset'         => array(
			'name'    => __( 'Reset on 1st January', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'id'      => 'ywpi_credit_note_reset',
			'desc'    => __( 'Set restart from 1 on 1st January.', 'yith-woocommerce-pdf-invoice' ),
			'default' => false,
		),
        'general-description' => array(
            'type'             => 'yith-field',
            'yith-type'        => 'html',
            'html' => __('We recommend verifying carefully the correct data provided to generate the invoice. The plugin\'s authors refuse any responsibility about possible mistakes or shortcomings when generating invoices.','yith-woocommerce-pdf-invoice'),
        ),
		'credit_note_settings_end'  => array(
			'type' => 'sectionend',
		),
	),
);


return $general_options;
