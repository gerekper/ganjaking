<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('WooCommerce_PDF_Invoice_Defaults')) :
	
	class WooCommerce_PDF_Invoice_Defaults {

    	Public static $defaults = array(
            'pdf_generator'         => 'DOMPDF', 
            'attach_neworder'       => '',
            'attach_multiple'       => array(),
            'create_invoice'        => 'completed',
            'link_thanks'           => 'true',
            'attachment_method'     => 0,
            'invoice_download_url'  => 'Download your PDF Invoice [[PDFINVOICEDOWNLOADURL]]', 
            'paper_size'            => 'a4',
            'paper_orientation'     => 'portrait',
            'logo_file'             => '',
            'store_logo_file'       => 'true',
            'enable_remote'         => 'false',
            'setchroot'             => 'false',
            'enable_subsetting'     => 'true',
            'pdf_company_name'      => '',
            'pdf_registered_name'   => '',
            'pdf_company_number'    => '',
            'pdf_tax_number'        => '',
            'pdf_company_details'   => '',
            'pdf_registered_address'=> '',
            'sequential'            => 'true',
            'annual_restart'        => 'false',
            'start_number'          => '',
            'padding'               => '',
            'pdf_prefix'            => '',
            'pdf_sufix'             => '',
            'pdf_filename'          => '{{company}}-{{invoicenumber}}',
            'pdf_date'              => 'invoice',
            'pdf_date_format'       => 'j F, Y',
            'pdf_termsid'           => '',
            'pdf_creation'          => 'file',
            'pdf_cache'             => 'false',
            'pdf_debug'             => 'false',
            'pdf_font'              => 'Default',
            'pdf_currency_font'     => 'false',
            'pdf_rtl'               => 'false',
            'pdf_display_tax'       => 'FALSE',
            'pdf_thank_you_text'    => 'Download your invoice : [[INVOICENUMBER]]',
            'thanks_style'          => 'link',
            'paid_in_full_show'     => 'no',
            'paid_in_full_text'     => ''
        );

        Public static $layout_defaults = array(
            'template_css'      => 'DOMPDF',
        );

		function __construct(){
	        
	    }

	}

	$GLOBALS['WooCommerce_PDF_Invoice_Defaults'] = new WooCommerce_PDF_Invoice_Defaults();

endif; // Class exists check
