<?php

		// include DomPDF autoloader
        require_once ( PDFPLUGINPATH . "lib/dompdf/autoload.inc.php" );

        // reference the Dompdf namespaces
		use WooCommercePDFInvoice\Dompdf;
		use WooCommercePDFInvoice\Options;

        class WC_send_pdf {

            public function __construct() {
            	$this->wc_version = get_option( 'woocommerce_version' );
				add_action( 'init', array( $this, 'init' ) );
            }

            function init() {
            	// Check the email being sent and attach a PDF if it's the right one
				add_filter( 'woocommerce_email_attachments' , array( $this, 'pdf_attachment' ) ,10, 3 );
            }

            /**
			 * Check the email being sent and attach a PDF if it's the right one
             */
		 	public static function pdf_attachment( $attachment = NULL, $id = NULL, $order = NULL ) {

		 		// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
            	if ( ! extension_loaded( 'iconv' ) || ! extension_loaded( 'mbstring' ) || ! $id || ! $order ) {
            		return $attachment;
            	}

            	$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

            	// Make the array for email ids
            	$email_ids = array();
            	if( isset($woocommerce_pdf_invoice_options['attach_multiple']) && $woocommerce_pdf_invoice_options['attach_multiple'] !='' ) {
            		$email_ids = $woocommerce_pdf_invoice_options['attach_multiple'];
            	}

            	// Make sure the completed order IDs are in there
            	$email_ids[] = 'customer_completed_order';
            	$email_ids[] = 'customer_completed_renewal_order';

            	// Make sure it's a unique array
            	$email_ids = array_unique( $email_ids );

            	// Add a filter for the array
apply_filters( 'pdf_invoice_email_ids', $email_ids, $order );

            	if ( !empty( $email_ids ) && in_array( $id, $email_ids ) ) {
            		// Create the PDF
            		$pdf = WC_send_pdf::get_woocommerce_invoice( $order );

            		// Apply a filter to modify the PDF if required
apply_filters( 'pdf_invoice_modify_attachment', $pdf, $id, $order );

            		// Add the PDF to the attachments array
            		$attachment[] = $pdf;	
				}

				return array_unique( $attachment );
				
		 	} // pdf_attachment

			Public Static function get_woocommerce_invoice( $order = NULL, $stream = NULL ) {
				
				// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
				if ( ! extension_loaded( 'iconv' ) || ! extension_loaded( 'mbstring' ) || ! $order ) {
 					return array();
 				}
 
        		$order_id   = $order->get_id();

        		// Set the temp directory
        		$pdftemp = WC_send_pdf::get_pdf_temp();

				$pdf = new WC_send_pdf();
				
				$woocommerce_pdf_invoice_options = get_option('woocommerce_pdf_invoice_settings');

				// And now for the user variables, paper size and the like.
				$papersize 			= $woocommerce_pdf_invoice_options['paper_size']; 			// Currently A4 or Letter
				$paperorientation 	= $woocommerce_pdf_invoice_options['paper_orientation']; 	// Portrait or Landscape
				$customlogo			= '';														// No logo? No problem, we'll just use get_bloginfo('name')
				$footertext			= '';														// This is the legal stuff that you should be including everywhere!

				if( !isset($woocommerce_pdf_invoice_options['enable_remote']) || $woocommerce_pdf_invoice_options['enable_remote'] == 'false' ) {
					$pdfremoteimages = false;
				} else {
					$pdfremoteimages = true;
				}

				if( !isset($woocommerce_pdf_invoice_options['enable_subsetting']) || $woocommerce_pdf_invoice_options['enable_subsetting'] == 'true' ) {
					$fontsubsetting	= true;
				} else {
					$fontsubsetting	= true;
				}

				// Get the filename
				$filename 	= WC_send_pdf::create_filename( $order_id, $woocommerce_pdf_invoice_options );

				$messagetext  = '';
				$messagetext .= $pdf->get_woocommerce_invoice_content( $order_id );

				/**
				 * Debugging
				 */
		  		if( isset( $woocommerce_pdf_invoice_options["pdf_debug"] ) && $woocommerce_pdf_invoice_options["pdf_debug"] == "true" ) {
		  			// Load PDF Dbugging
		  			if( !class_exists( 'WC_pdf_debug') ) {
		  				include( 'class-pdf-debug.php' );
		  			}
		  			// WC_pdf_debug::pdf_debug( $messagetext, 'WC_PDF_Invoice', __('PDF Invoice Body : ', 'woocommerce-pdf-invoice'), TRUE );
				} 
					
				if ( $stream && 
					( !isset($woocommerce_pdf_invoice_options['pdf_termsid']) || $woocommerce_pdf_invoice_options['pdf_termsid'] == 0 ) && 
					( !isset($woocommerce_pdf_invoice_options['pdf_creation']) || $woocommerce_pdf_invoice_options['pdf_creation'] == 'standard' )
				) {
					// Start the PDF Generator for the invoice

					// Set Option for remote images
					$options = new Options();
					$options->set([
							'isRemoteEnabled' 			=> $pdfremoteimages,
							'isHtml5ParserEnabled' 		=> true,
							'enable_font_subsetting'	=> $fontsubsetting,
							'tempDir'					=> $pdftemp,
							'logOutputFile'				=> $pdftemp . DIRECTORY_SEPARATOR . "log.htm",
							'defaultPaperSize'			=> $papersize,
							'defaultPaperOrientation'	=> $paperorientation 
					]);

					ob_start();
					ob_clean();

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
						
					// Output the PDF for download
					return $dompdf->stream( $filename );
						
				} elseif ( 
					( isset($woocommerce_pdf_invoice_options['pdf_termsid']) && $woocommerce_pdf_invoice_options['pdf_termsid'] != 0 ) || 
					( isset($woocommerce_pdf_invoice_options['pdf_creation']) && $woocommerce_pdf_invoice_options['pdf_creation'] == 'file' )
				) {
					/**
					 * This section deals with sending / generating a PDF Invoice that will include a Terms and Conditions page
					 * Uses PDF Merge library
					 *
					 * REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
					 * You do not need to give a file path for browser, string, or download - just the name.
					 */
					
					// Add PDF extension 
					if (strpos($filename, '.pdf') === false) {
						$filename =  $filename . '.pdf';
					}
						 
					// Set Option for remote images
					$options = new Options();
					$options->set([
						'isRemoteEnabled' 			=> $pdfremoteimages,
						'isHtml5ParserEnabled' 		=> true,
						'enable_font_subsetting'	=> $fontsubsetting,
						'tempDir'					=> $pdftemp,
						'logOutputFile'				=> $pdftemp . DIRECTORY_SEPARATOR . "log.htm",
						'defaultPaperSize'			=> $papersize,
						'defaultPaperOrientation'	=> $paperorientation 
					]);

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
						
					$invattachments = $pdftemp . '/inv' . $filename;
						
					// Write the PDF to the TMP directory		
					file_put_contents( $invattachments, $dompdf->output() );
						
					ob_start();
					ob_clean();

					if ( !class_exists('PDFMerger') ) {
						include ( PDFPLUGINPATH . 'lib/PDFMerger/PDFMerger.php' );
					}

					if ( isset($woocommerce_pdf_invoice_options['pdf_termsid']) && $woocommerce_pdf_invoice_options['pdf_termsid'] != 0 ) {

						$options = new Options();
						$options->set([
							'isRemoteEnabled' 			=> $pdfremoteimages,
							'isHtml5ParserEnabled' 		=> true,
							'enable_font_subsetting'	=> $fontsubsetting,
							'tempDir'					=> $pdftemp,
							'logOutputFile'				=> $pdftemp . DIRECTORY_SEPARATOR . "log.htm" ,
							'defaultPaperSize'			=> $papersize,
							'defaultPaperOrientation'	=> $paperorientation 
						]);

						// Start the PDF Generator for the terms
						$dompdf = new Dompdf();
						$dompdf->setOptions($options);
						$dompdf->load_html( $pdf->get_woocommerce_invoice_terms( $woocommerce_pdf_invoice_options['pdf_termsid'], $order_id ) );
						$dompdf->set_paper( $papersize, $paperorientation );
						$dompdf->render();
						
						$termsattachments = $pdftemp . '/terms-' . $filename;
						
						// Write the PDF to the TMP directory		
						file_put_contents( $termsattachments, $dompdf->output() );
					
						$pdf = new PDFMerger;
						
						if ( $stream ) {
							$pdf->addPDF( $invattachments, 'all' )
								->addPDF( $termsattachments, 'all' )
								->merge( 'download', $filename );
								exit;
						} else {
							$pdf->addPDF( $invattachments, 'all' )
								->addPDF( $termsattachments, 'all' )
								->merge( 'file', $pdftemp . '/' . $filename );
						}

					} else {
					
						$pdf = new PDFMerger;

						if ( $stream ) {
							$pdf->addPDF( $invattachments, 'all' )
								->merge( 'download', $filename );
								exit;
						} else {
							$pdf->addPDF( $invattachments, 'all' )
								->merge( 'file', $pdftemp . '/' . $filename );
						}

					}
						
					// Send the file name and location to the Email
					// return 	array( $invattachments, $termsattachments );
					return ( $pdftemp . '/' . $filename );
											
				} else {
					// Add PDF extension 
					if (strpos($filename, '.pdf') === false) {
						$filename =  $filename . '.pdf';
					}

					ob_start();
					ob_clean();
					
					// Set Option for remoate images
					$options = new Options();
					$options->set([
						'isRemoteEnabled' 		=> $pdfremoteimages,
						'isHtml5ParserEnabled' 	=> true,
						'enable_font_subsetting'=> $fontsubsetting,
						'tempDir'				=> $pdftemp,
						'logOutputFile'			=> $pdftemp . DIRECTORY_SEPARATOR . "log.htm" 
					]);

					$dompdf = new DOMPDF();
					$dompdf->setOptions($options);
					$dompdf->load_html( $messagetext );
					$dompdf->set_paper( $papersize, $paperorientation );
					$dompdf->render();
					
					$attachments = $pdftemp . '/' . $filename;
					
					// Write the PDF to the TMP directory		
					file_put_contents( $attachments, $dompdf->output() );
		
					// Send the file name and location to the Email
					return 	$attachments;
						
				}

			}

			/**
			 * Create the file name based on the settings
			 *
			 * Allowed variables
			 *
			 * companyname
			 * invoicedate
			 * invoicenumber
			 * month
			 * mon
			 * year
			 */
			private static function create_filename( $order_id, $woocommerce_pdf_invoice_settings ) {

				$pdf = new WC_send_pdf();

				$replace 	= array( ' ', "/", "'",'"', "--" );
				$clean_up	= array( ',' );
				$filename	= $woocommerce_pdf_invoice_settings['pdf_filename'];

				if ( $filename == '' ) {

					$filename	= get_bloginfo('name') . '-' . $order_id;

				} else {

					$invoice_date = $pdf->get_woocommerce_pdf_date( $order_id,'completed', true );

					$filename	= str_replace( '{{company}}',	$woocommerce_pdf_invoice_settings['pdf_company_name'] , $filename );
					$filename	= str_replace( '{{invoicedate}}', $invoice_date, $filename );
					$filename	= str_replace( '{{invoicenumber}}',	( $pdf->get_woocommerce_pdf_invoice_num( $order_id ) ? $pdf->get_woocommerce_pdf_invoice_num( $order_id ) : $order_id ) , $filename );
					$filename	= str_replace( '{{month}}',	date( 'F', strtotime( $invoice_date ) ) , $filename );
					$filename	= str_replace( '{{mon}}',	date( 'M', strtotime( $invoice_date ) ) , $filename );
					$filename	= str_replace( '{{year}}',	date( 'Y', strtotime( $invoice_date ) ) , $filename );
					
				}

				// Clean up the filename
				$filename	= str_replace( $replace, '-' , $filename );
				$filename	= str_replace( $clean_up, '' , $filename );

// Filter the filename
apply_filters( 'pdf_output_filename', $filename, $order_id );

				return $filename;

			}

			/**
			 * Get the PDF order details in a table
			 * @param  [type] $order_id 
			 * @return [type]           
			 */
			function get_woocommerce_pdf_order_details( $order_id ) {
				global $woocommerce;
				$order 	 = new WC_Order( $order_id );

				$order_currency = $order->get_currency();
							
				$pdflines  = '<table width="100%" class="shop_table ordercontent">';
				$pdflines .= '<tbody>';

				if ( sizeof( $order->get_items() ) > 0 ) : 

					foreach ( $order->get_items() as $item ) {

						if ( $item['quantity'] ) {
							
							$line = '';
							// $item_loop++;

							$_product 	= $order->get_product_from_item( $item );
							$item_name 	= $item['name'];
							$item_id 	= $item->get_id();

							$meta_display = '';
							foreach ( $item->get_formatted_meta_data() as $meta_key => $meta ) {
								$meta_display .= '<br /><small>(' . $meta->display_key . ':' . wp_kses_post( strip_tags( $meta->display_value ) ) . ')</small>';
			 				}

			 				// Add Booking details
			 				if ( class_exists( 'WC_Booking_Data_Store' ) ) {
								$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );

								if ( $booking_ids ) {
									foreach ( $booking_ids as $booking_id ) {

										$booking = new WC_Booking( $booking_id );

										$product  = $booking->get_product();
										$resource = $booking->get_resource();
										$label    = $product && is_callable( array( $product, 'get_resource_label' ) ) && $product->get_resource_label() ? $product->get_resource_label() : __( 'Type', 'woocommerce-bookings' );

										if ( strtotime( 'midnight', $booking->get_start() ) === strtotime( 'midnight', $booking->get_end() ) ) {
											$booking_date = sprintf( '%1$s', $booking->get_start_date() );
										} else {
											$booking_date = sprintf( '%1$s / %2$s', $booking->get_start_date(), $booking->get_end_date() );
										}

										$meta_display .= '<br /><small>' . esc_html( sprintf( __( 'Booking ID : %d', 'woocommerce-pdf-invoice' ), $booking_id ) ) . '</small>';
apply_filters( 'wc_bookings_summary_list_date', $booking_date, $booking->get_start(), $booking->get_end() ) ) ) . '</small>';
apply_filters( 'pdf_invoice_meta_output', $meta_display );

/**
 * Allow additional info to be added to the $item_name
 *
 * add_filter( 'pdf_invoice_item_name', 'add_product_description_pdf_invoice_item_name', 10, 4 );
 * 
 * function add_product_description_pdf_invoice_item_name( $item_name, $item, $product, $order ) {
 * 	
 *	// Use $product->get_id() if you want to get the post id for the product.
 * 	$item_name .= '<p>' . $product->get_description() . '</p>';
 * 	return $item_name;
 * 
 * }
 */
apply_filters( 'pdf_invoice_item_name', $item_name, $item, $_product, $order );
				
apply_filters( 'pdf_template_line_output', $pdflines, $order_id );
				return $pdf;
			}

			/**
			 * Get the Invoice Number
			 * @param  [type] $order_id [description]
			 * @return [type]           [description]
			 */
			function get_woocommerce_pdf_invoice_num( $order_id ) {
				global $woocommerce;
		
				if ( $order_id ) :
					$invnum = esc_html( get_post_meta( $order_id, '_invoice_number_display', true ) );
				else :
					$invnum = ''; 
				endif;

				return $invnum;
			}
	
			/** 
			 * Get the invoice date
			 * @param  [type] $order_id [description]
			 * @param  [type] $usedate  [description]
			 * @return [type]           [description]
			 */
			public static function get_woocommerce_pdf_date( $order_id, $usedate, $sendsomething = false ) {
				global $woocommerce;
				
				if( get_post_meta( $order_id, '_invoice_date', TRUE ) ) {
					return get_post_meta( $order_id, '_invoice_date', TRUE );
				}

				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
				$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];

				// Force a $date_format if one is not set
				if ( !isset( $date_format ) || $date_format == '' ) {
					$date_format = "j F, Y";
				}

				$order 	 = new WC_Order( $order_id );
		
				if ( $usedate == 'completed' ) {
					$date = esc_html( get_post_meta( $order_id, '_invoice_date', TRUE ) );

					// Double check $date is set if the order is completed
					$order_status	= is_callable( array( $order, 'get_status' ) ) ? $order->get_status() : $order->order_status;
					if( $order_status == 'completed' && $date == '' ) {
						$date = WC_send_pdf::get_completed_date( $order_id );
					}

				} else {
					// WooCommerce 3.0 compatibility
					$date = is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->order_date;
				}

				// In some cases $date will be empty so we might want to send the order date
				if ( $sendsomething && !$date ) {
					// WooCommerce 3.0 compatibility
					$date = is_callable( array( $order, 'get_date_created' ) ) ? $order->get_date_created() : $order->order_date;
				}
				
				if ( $date ) {

					// Make sure the date is formated correctly
					$date_check = DateTime::createFromFormat( get_option( 'date_format' ), $date );

					if( $date_check ) {
						$date = $date_check->format( $date_format );
					}

					if( strtotime( $date ) ) {
						$date = date_i18n( $date_format, strtotime( $date ) );
					}

					// Return a date in the format that matches the PDF Ivoice settings.
					return $date;

				} else {
					return '';
				}
		
			}

			// Get the date the order was completed if _invoice_date was not set at the time the invoice number was created
			public static function get_completed_date( $order_id ) {

				$date = '';

				// Use _date_completed from order meta
				$date = get_post_meta( $order_id, '_completed_date', true );

				// if _date_completed is empty then use this as a backup
				if( !isset( $date ) || $date == '' ) {

					if( get_post_meta($order_id, '_invoice_meta', TRUE) && get_post_meta($order_id, '_invoice_meta', TRUE) != '' ) {

						$invoice_meta = get_post_meta($order_id, '_invoice_meta', TRUE);
						$date 		  = $invoice_meta['invoice_created'];

					} else {
						global $wpdb;

						$invoice_number = get_post_meta( $order_id, '_invoice_number_display', TRUE );

						$invoice = $wpdb->get_row( "SELECT * FROM $wpdb->comments 
													WHERE comment_post_id = $order_id 
													AND comment_content LIKE '% $invoice_number %' 
													AND comment_type = 'order_note'
													LIMIT 1;"
												);
									

						$date  = $invoice->comment_date;
					}

				}

				return $date;
			}
			

apply_filters( 'pdf_template_order_notes' , $output, $order_id );

apply_filters( 'pdf_template_order_subtotal' , $output, $order_id );
				
apply_filters( 'pdf_template_order_shipping' , $output, $order_id );

apply_filters( 'pdf_template_order_coupons' , $output, $order_id );
				
apply_filters( 'pdf_template_order_discount' , $output, $order_id );

apply_filters( 'pdf_template_order_tax' , $output, $order_id );
apply_filters( 'pdf_template_order_total' , $output, $order_id );
apply_filters( 'pdf_template_order_totals' , $output, $order_id );

apply_filters( 'before_pdf_content', $content , $order );
				
apply_filters( 'pdf_template_table_headings', $headers, $order_id );

apply_filters( 'pdf_template_show_barcode', true );
				
apply_filters( 'pdf_content_additional_content' , $content , $order_id );

apply_filters( 'pdf_invoice_logo', $logo, $order_id, $woocommerce_pdf_invoice_settings );

apply_filters( 'pdf_invoice_companyname', $pdfcompanyname, $order_id );

apply_filters( 'pdf_invoice_companydetails', $pdf_company_details, $order_id );

apply_filters( 'pdf_invoice_registeredname', $pdf_registered_name, $order_id );

apply_filters( 'pdf_invoice_registered_address', $pdf_registered_address, $order_id );

apply_filters( 'pdf_invoice_company_number', $pdf_company_number, $order_id );

apply_filters( 'pdf_invoice_tax_number', $pdf_tax_number, $order_id );

apply_filters( 'pdf_invoice_display_order_number', $output_order_num, $order ); 

apply_filters( 'pdf_invoice_payment_method_title', $payment_method_title, $order_id );

apply_filters( 'pdf_invoice_shipping_method_title', ucwords( $order->get_shipping_method() ), $order );

apply_filters( 'pdf_invoice_billing_address', $order->get_formatted_billing_address(), $order );

apply_filters( 'pdf_invoice_shipping_address', $order->get_formatted_shipping_address(), $order );

/**
 * Filter the $page_id for reasons
 */
apply_filters( 'pdf_invoice_terms_page_id', $page_id, $order_id );
								
// Allow the filename to be modified
apply_filters( 'woocommerce_pdf_invoice_filename', $filename, $order_id );

apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

apply_filters( 'pdf_display_invoice_date', $invoice_date, $order_id, $usedate, $sendsomething );




