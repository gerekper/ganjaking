<?php
    class WC_pdf_export {

        public function __construct() {

        	global $woocommerce;

        	// Get PDF Invoice Options
        	$woocommerce_pdf_invoice_settings = get_option('woocommerce_pdf_invoice_settings');
        	$this->id 	 = 'woocommerce_pdf_invoice';
        	$this->debug = false;

        	if( isset( $woocommerce_pdf_invoice_settings["pdf_debug"] ) && $woocommerce_pdf_invoice_settings["pdf_debug"] == true ) {
        		$this->debug = true;
        	}

        	// Add Debugging Log
			if ( $this->debug == true ) {
				$this->pdf_debug( class_exists("ZipArchive"), $this->id, __('ZipArchive exists : ', 'woocommerce-pdf-invoice'), FALSE );
				$this->pdf_debug( function_exists("gzcompress"), $this->id, __('gzcompress exists : ', 'woocommerce-pdf-invoice'), FALSE );
			}

        	if( class_exists("ZipArchive") || function_exists("gzcompress") ) {

	        	add_filter( 'bulk_actions-edit-shop_order', array( $this, 'shop_order_bulk_actions' ) );
	        	add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_shop_order_bulk_actions' ), 10, 3 );

	        	add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );

	        }

        }

        function shop_order_bulk_actions( $actions ) {

			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			$actions['pdf_bulk_export'] = __( 'Bulk Export PDFs', 'woocommerce' );

			return $actions;

        }

		public function handle_shop_order_bulk_actions( $redirect_to, $action, $ids ) {

			// Add Debugging Log
			if ( $this->debug == true ) {
				$this->pdf_debug( NULL, $this->id, __('redirect_to : ', 'woocommerce-pdf-invoice') . $redirect_to, FALSE );
				$this->pdf_debug( NULL, $this->id, __('action : ', 'woocommerce-pdf-invoice') . $action, FALSE );
				$this->pdf_debug( NULL, $this->id, __('Order IDs : ', 'woocommerce-pdf-invoice') . print_r( $ids, TRUE ), FALSE );
			}

			// Bail out if this is not the pdf_bulk_export.
			if ( $action === 'pdf_bulk_export' && class_exists("ZipArchive") ) {

				require_once( 'class-pdf-send-pdf-class.php' );

				// Set the temp directory
				$pdftemp 	= ( null != ini_get('upload_tmp_dir') || '' != ini_get('upload_tmp_dir') ) ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
				$upload_dir =  wp_upload_dir();
                if ( file_exists( $upload_dir['basedir'] . '/woocommerce_pdf_invoice/index.html' ) ) {
    				$pdftemp = $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
    			}

    			// Set the zip file name
    			$filename_salt = time();
    			if( null !== NONCE_SALT ) {
    				$filename_salt = NONCE_SALT;
    			}
    			$zip_file = "pdfExport-" . date("Y-m-d-H-i-s") . '-' . MD5("pdfExport-" . date("Y-m-d-H-i-s") . $filename_salt);

    			// Add Debugging Log
				if ( $this->debug == true ) {
    				$this->pdf_debug( NULL, $this->id, __('$zip_file : ', 'woocommerce-pdf-invoice') . $zip_file, FALSE );
    			}

    			// Set the zip file extension
    			$extension = ".zip";

				$changed = 0;
				$files   = array();
				$ids 	 = array_map( 'absint', $ids );

				foreach ( $ids as $id ) {

					$order   = wc_get_order( $id );
					$files[] =  WC_send_pdf::get_woocommerce_invoice( $order );

					$changed++;

				}

				// Let's zip the $files
				$zip 		= new ZipArchive();
				$filename	= $pdftemp . "/" . $zip_file . $extension;

				// Zip creation failed
				if ( $zip->open($filename, ZipArchive::CREATE)!==TRUE ) {
					
				    $this->pdf_debug( NULL, $this->id, __('Export failed, cannot open : ', 'woocommerce-pdf-invoice') . $filename, FALSE );

				    $redirect_to = add_query_arg( array(
						'post_type'    	=> 'shop_order',
						'pdf_export'   	=> '1',
					), $redirect_to );

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
					'zip_file'		=> $zip_file,
					'changed'      	=> $changed,
					'ids'          	=> join( ',', $ids ),
				), $redirect_to );

				$zip->close();

			}

			return esc_url_raw( $redirect_to );

		}

		/**
		 * Show confirmation message that order status changed for number of orders.
		 */
		public function bulk_admin_notices() {
			global $post_type, $pagenow;

			// Bail out if not on shop order list page
			if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type ) {
				return;
			}

			// Set the temp directory
			$pdftemp 	= sys_get_temp_dir();
			$upload_dir =  wp_upload_dir();
            if ( file_exists( $upload_dir['basedir'] . '/woocommerce_pdf_invoice/index.html' ) ) {
				$pdftemp = $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
			}

			// Bulk Download
			if( isset( $_REQUEST['pdf_export'] ) ) {

				if( $_REQUEST['pdf_export'] == 0 ) {

					$number = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;
					/* translators: %s: orders count */
					echo '<div class="updated"><p>' . sprintf( _n( '%s invoice added to zip file.', '%s invoices added to zip file.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) ) . '</p><p>' . sprintf( __( 'Download the zipfile from <a href="%1$s/%2$s.zip">%3$s.zip</a>', 'woocommerce-pdf-invoice' ), $upload_dir['baseurl'].'/woocommerce_pdf_invoice', $_REQUEST['zip_file'], $_REQUEST['zip_file'] ) . '</p></div>';
							
				} else {
					echo '<div class="error"><p>' . __('Zip file creation failed. Check the log in the WooCommerce System Status logs tab.', 'woocommerce-pdf-invoice') . '</p></div>';
				}

			}

		}

        /**
         * [pdf_debug description]
         * @param  Array   $tolog   contents for log
         * @param  String  $id      payment gateway ID
         * @param  String  $message additional message for log
         * @param  boolean $start   is this the first log entry for this transaction
         */
        public static function pdf_debug( $tolog = NULL, $id, $message = NULL, $start = FALSE ) {

        	if( !class_exists('WC_Logger') ) {
        		return;
        	}

        	$logger 	 = new stdClass();
          	$logger->log = new WC_Logger();

            /**
             * If this is the start of the logging for this transaction add the header
             */
            if( $start ) {

                $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, ' ' );
                $logger->log->add( $id, __('PDF Invoice Log', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('' .date('d M Y, H:i:s'), 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, ' ' );

            }

            $logger->log->add( $id, $message );
            if( $tolog !== NULL ) {
            	$logger->log->add( $id, print_r( $tolog, TRUE ) );
            }

        }

    }

    $GLOBALS['WC_pdf_export'] = new WC_pdf_export();