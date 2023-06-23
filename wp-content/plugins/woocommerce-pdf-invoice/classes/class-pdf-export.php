<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

    class WC_pdf_export {

        public function __construct() {

        	global $woocommerce;

        	// Get PDF Invoice Options
        	$woocommerce_pdf_invoice_settings = get_option('woocommerce_pdf_invoice_settings');

        }

		public function handle_shop_order_bulk_actions( $redirect_to, $action, $ids ) {

			// Start some logging :/
			$export_log = array();

			// PDF Invoices settings
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			// Logging
			$export_log['Start'] = "New Export";

			// Bail out if this is not the pdf_bulk_export.
			if ( $action === 'pdf_bulk_export' && class_exists("ZipArchive") ) {
				
				// Clear existing transients
				delete_transient( '_pdf_export_status' );
    			delete_transient( '_pdf_export_zip_file' );
    			delete_transient( '_pdf_export_changed' );

				// Logging
				$export_log['Action'] 	= $action;
				$export_log['Class'] 	= "ZipArchive";

				require_once( 'class-pdf-send-pdf-class.php' );

				// Set the temp directory
				$pdftemp 	= ( null != ini_get('upload_tmp_dir') || '' != ini_get('upload_tmp_dir') ) ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
				$upload_dir =  wp_upload_dir();
                if ( file_exists( $upload_dir['basedir'] . '/woocommerce_pdf_invoice/index.html' ) ) {
    				$pdftemp = $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
    			}

    			// Logging
    			$export_log['Temp'] 	= $pdftemp;

    			// Set the zip file name
    			$filename_salt = time();
    			if( null !== NONCE_SALT ) {
    				$filename_salt = NONCE_SALT;
    			}
    			$zip_file = "pdfExport-" . date("Y-m-d-H-i-s") . '-' . MD5("pdfExport-" . date("Y-m-d-H-i-s") . $filename_salt);

    			// Logging
    			$export_log['Zip File'] 	= $zip_file;

    			// Set the zip file extension
    			$extension = ".zip";

				$changed = 0;
				$files   = array();
				$ids 	 = array_map( 'absint', $ids );

				$temporary   = array();

				foreach ( $ids as $id ) {

					// Get the order
					$order   = wc_get_order( $id );

					// Create the PDF
					if( isset( $settings["pdf_generator"] ) && $settings["pdf_generator"] == 'MPDF' ) {
						$file 	 = WC_send_pdf::get_woocommerce_pdf_invoice( $order, NULL, FALSE );
					} else {
						$file 	 = WC_send_pdf::get_woocommerce_pdf_invoice( $order );
					}

					$temporary[] = $file;

					// Check if the returned fileame contains the full path, add it if necessary (mPDF)
					if (strpos( $file, $pdftemp  ) !== false ) {
						$files[] = $file;
					} else {
						$files[] = $pdftemp  . '/' . $file;
					}

					$changed++;

				}

				// Let's zip the $files
				$zip 		= new ZipArchive();
				$filename	= $pdftemp . "/" . $zip_file . $extension;

				// Zip creation failed
				if ( $zip->open($filename, ZipArchive::CREATE)!==TRUE ) {
					
				    $redirect_to = add_query_arg( array(
						'post_type'    	=> 'shop_order',
						'pdf_export'   	=> '1',
					), $redirect_to );

					// Logging
    				$export_log['Redirect To'] 	= $redirect_to;
    				$export_log['Zip File'] 	= "No Zip File!";

    				return esc_url_raw( $redirect_to );

				}

				foreach ( $files as $file ) {
					$pdf = str_replace( $pdftemp, '', $file );
					$pdf = str_replace( '/', '', $pdf );

					$zip->addFile( $file, $pdf );
				}

				$redirect_to = add_query_arg( array(
					'post_type'    	=> 'shop_order',
					'pdf_export'   	=> $zip->status,
				), $redirect_to );

				$zip->close();

			}

			// Logging
    		$export_log['Redirect To'] 	= $redirect_to;
    		$export_log['Zip File'] 	= "Zip File Created!";

    		set_transient( '_pdf_export_status', $zip->status, DAY_IN_SECONDS );
    		set_transient( '_pdf_export_zip_file', $zip_file, DAY_IN_SECONDS );
    		set_transient( '_pdf_export_changed', $changed, DAY_IN_SECONDS );

    		return esc_url_raw( $redirect_to );

		}

    }