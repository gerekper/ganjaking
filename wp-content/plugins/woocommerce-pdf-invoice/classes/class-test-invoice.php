<?php

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

if (!class_exists('PDF_Invoice_Test_Invoice')) :
	
	class PDF_Invoice_Test_Invoice {
		
		function __construct(){
	        
	    }

	    function get_pdf_template() {

	    }

	    function build_invoice_number() {

	    }

		function get_woocommerce_invoice_content() {
			global $woocommerce;

			// WPML
			do_action( 'before_invoice_content', $order_id );

			$settings = get_option( 'woocommerce_pdf_invoice_settings' );
			
			if (!$order_id) return;
			
			$order = new WC_Order( $order_id );

			// Check if the order has an invoice
			$invoice_number_display = get_post_meta( $order_id, '_invoice_number_display', true );

			// Get the Invoice template ID
			$template_id = isset( $settings['pdf_invoice_template_id'] ) ? $settings['pdf_invoice_template_id'] : 0;

			// Just temporarily
			$template_id = 0;

            // Get the default invoice template ID
            // $default_invoice_template_id = (null !== get_option('woocommerce_pdf_invoice_main_template_id') ) ? get_option('woocommerce_pdf_invoice_main_template_id') : 0;
            // $template_id = (null !== $settings['pdf_invoice_template_id']) ? $settings['pdf_invoice_template_id'] : $default_invoice_template_id;

            // Allow the template to be filtered if required.
			$template_id = apply_filters( 'woocommerce_pdf_invoice_template_id', $template_id, $order );

			if( $template_id !== 0 ) {
				$post 	 = get_post( $template_id );
				$content = $post->post_content;
			} else {
				// Buffer
				ob_start();
	
				// load_template( $pdftemplate, false );
				require( WC_send_pdf::get_pdf_template( 'template.php', $order_id ) );
					
				// Get contents
				$content = ob_get_clean();
			}
			
			/**
			 * Notify when the PDF is about to be generated
			 * Added for Currency Switcher for WooCommerce
			 */
			do_action( 'woocommerce_pdf_invoice_before_pdf_content', $order );
	
			// REPLACE ALL TEMPLATE TAGS WITH REAL CONTENT
			$content = str_replace(	'[[PDFFONTFAMILY]]', 						self::get_fontfamily( $order_id, $settings ),								$content );
			$content = str_replace( '[[PDFCURRENCYSYMBOLFONT]]', 				self::get_currency_fontfamily( $order_id, $settings ),						$content );
			$content = str_replace(	'[[PDFLOGO]]', 								self::get_pdf_logo( $order_id, $settings ), 			 					$content );

			$content = str_replace(	'[[PDFCOMPANYNAME]]', 						self::get_invoice_companyname( $order_id, $settings ),						$content );
			$content = str_replace(	'[[PDFCOMPANYDETAILS]]', 					self::get_invoice_companydetails( $order_id, $settings ), 					$content );
			$content = str_replace(	'[[PDFREGISTEREDNAME]]', 					self::get_invoice_registeredname( $order_id, $settings ), 					$content );
			$content = str_replace(	'[[PDFREGISTEREDADDRESS]]', 				self::get_invoice_registeredaddress( $order_id, $settings ),				$content );
			$content = str_replace(	'[[PDFCOMPANYNUMBER]]', 					self::get_invoice_companynumber( $order_id, $settings ), 					$content );
			$content = str_replace(	'[[PDFTAXNUMBER]]', 						self::get_invoice_taxnumber( $order_id, $settings ), 						$content );

			$content = str_replace(	'[[PDFINVOICENUMHEADING]]', 				self::get_pdf_template_invoice_number_text( $order ), 	 					$content );
			$content = str_replace(	'[[PDFINVOICENUM]]', 						self::get_invoice_display_invoice_num( $order_id ),							$content );

			$content = str_replace(	'[[PDFORDERENUMHEADING]]', 					self::get_pdf_template_order_number_text( $order ), 	 					$content );
			$content = str_replace(	'[[PDFORDERENUM]]', 						self::get_invoice_display_order_number( $order ), 							$content );

			$content = str_replace(	'[[PDFINVOICEDATEHEADING]]', 				self::get_pdf_template_invoice_date_text( $order ), 	 					$content );
			$content = str_replace(	'[[PDFINVOICEDATE]]', 						self::get_invoice_display_date( $order_id,'completed', false, 'invoice' ), 	$content );

			$content = str_replace(	'[[PDFORDERDATEHEADING]]', 					self::get_pdf_template_order_date_text( $order ), 	 						$content );
			$content = str_replace(	'[[PDFORDERDATE]]', 						self::get_invoice_display_date( $order_id,'ordered', false, 'order' ), 		$content );
			
			$content = str_replace(	'[[PDFINVOICE_BILLINGDETAILS_HEADING]]',	self::get_pdf_billing_details_heading( $order ), 	 						$content );
			$content = str_replace(	'[[PDFBILLINGADDRESS]]', 					self::get_invoice_billing_address( $order ),  								$content );
			$content = str_replace(	'[[PDFBILLINGTEL]]', 						self::get_invoice_billing_phone( $order_id ), 	  							$content );
			$content = str_replace(	'[[PDFBILLINGEMAIL]]', 						self::get_invoice_billing_email( $order_id ), 								$content );
			$content = str_replace(	'[[PDFBILLINGVATNUMBER]]', 					self::get_invoice_billing_vat_number( $order_id ), 							$content );

			$content = str_replace(	'[[PDFINVOICE_SHIPPINGDETAILS_HEADING]]',	self::get_pdf_shipping_details_heading( $order ), 	 						$content );
			$content = str_replace(	'[[PDFSHIPPINGADDRESS]]', 					self::get_invoice_shipping_address( $order ), 								$content );

			$content = str_replace(	'[[PDFINVOICE_PAYMETHOD_HEADING]]', 		self::get_template_payment_method_text( $order ), 	 						$content );
			$content = str_replace(	'[[PDFINVOICEPAYMENTMETHOD]]',				self::get_invoice_payment_method_title( $order_id ), 						$content );

			$content = str_replace(	'[[PDFINVOICE_SHIPMETHOD_HEADING]]', 		self::get_pdf_template_shipping_method_text( $order ), 	 					$content );
			$content = str_replace(	'[[PDFSHIPPINGMETHOD]]',					self::get_invoice_shipping_method_title( $order ), 							$content );

			$content = str_replace(	'[[ORDERINFOHEADER]]',						self::get_pdf_headers( $order_id ), 										$content );
			$content = str_replace(	'[[ORDERINFO]]', 							self::get_pdf_order_details( $order_id ), 	  								$content );
			$content = str_replace(	'[[PDFORDERNOTES]]', 						self::get_pdf_order_note( $order_id ), 	  									$content );

			$content = str_replace(	'[[PDFORDERSUBTOTAL]]', 					self::get_pdf_order_subtotal( $order_id ), 	  								$content );
			$content = str_replace(	'[[PDFORDERSHIPPING]]', 					self::get_pdf_order_shipping( $order_id ), 	  								$content );
			$content = str_replace(	'[[PDFORDERDISCOUNT]]', 					self::get_pdf_order_discount( $order_id ), 	  								$content );
			$content = str_replace(	'[[PDFORDERTAX]]', 							self::get_pdf_order_tax( $order_id ), 	  									$content );
			$content = str_replace(	'[[PDFORDERTOTAL]]', 						self::get_pdf_order_total( $order_id ), 	  								$content );
			$content = str_replace(	'[[PDFORDERTOTALS]]', 						self::get_pdf_order_totals( $order_id ), 	  								$content );

			$content = str_replace(	'[[PDFINVOICE_ORDERDETAILS_HEADING]]', 		self::get_pdf_order_details_heading( $order ), 	 							$content );

			$content = str_replace(	'[[PDFINVOICE_QTY_HEADING]]', 				self::get_pdf_qty_heading( $order ), 	 									$content );
			$content = str_replace(	'[[PDFINVOICE_PRODUCT_HEADING]]', 			self::get_pdf_product_heading( $order ),  									$content );
			$content = str_replace(	'[[PDFINVOICE_PRICEEX_HEADING]]', 			self::get_pdf_priceex_heading( $order ),  									$content );
			$content = str_replace(	'[[PDFINVOICE_TOTALEX_HEADING]]', 			self::get_pdf_totalex_heading( $order ),  									$content );
			$content = str_replace(	'[[PDFINVOICE_TAX_HEADING]]', 				self::get_pdf_tax_heading( $order ), 	 									$content );
			$content = str_replace(	'[[PDFINVOICE_PRICEINC_HEADING]]', 			self::get_pdf_priceinc_heading( $order ), 									$content );
			$content = str_replace(	'[[PDFINVOICE_TOTALINC_HEADING]]', 			self::get_pdf_totalinc_heading( $order ), 									$content );

			$content = str_replace(	'[[PDFINVOICE_REGISTEREDNAME_HEADING]]', 	self::get_pdf_template_registered_name_text( $order, $settings ), 			$content );
			$content = str_replace(	'[[PDFINVOICE_REGISTEREDOFFICE_HEADING]]', 	self::get_pdf_template_registered_office_text( $order, $settings ), 		$content );
			$content = str_replace(	'[[PDFINVOICE_COMPANYNUMBER_HEADING]]', 	self::get_pdf_template_company_number_text( $order, $settings ), 			$content );
			$content = str_replace(	'[[PDFINVOICE_VATNUMBER_HEADING]]', 		self::get_pdf_template_vat_number_text( $order, $settings ), 				$content );

			$content = str_replace(	'[[PDFBARCODES]]', 							self::get_barcode( $order_id ), 			 								$content );
			$content = str_replace(	'[[PDFBILLINGVATNUMBER]]', 					self::get_vat_number( $order_id ), 		 									$content );

			if( preg_match('/ORDERDETAILS(.*?)ENDORDERDETAILS/', $content, $match) == 1 ) {
				$template_order_details = WC_send_pdf::get_pdf_template_invoice_order_details( $order, $match );
				$content = preg_replace( '/ORDERDETAILS(.*?)ENDORDERDETAILS/', WC_send_pdf::get_pdf_template_invoice_order_details( $order, $match ), $content );
			} 

			// Allow the content to be filtered
			$content = apply_filters( 'pdf_content_additional_content' , $content , $order_id );

			// WPML
			global $current_language;

			do_action( 'after_invoice_content', $current_language ); 
	
			return mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
		}	    
	    

	}

endif; // Class exists check
