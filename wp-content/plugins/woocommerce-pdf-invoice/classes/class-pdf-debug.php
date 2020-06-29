<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

    class WC_pdf_debug {

        public function __construct() {

        	global $woocommerce;

        	// Get PDF Invoice Options
        	$woocommerce_pdf_invoice_settings = get_option('woocommerce_pdf_invoice_settings');
        	$this->id 	 = 'woocommerce_pdf_invoice';
        	$this->debug = false;

        	if( isset( $woocommerce_pdf_invoice_settings["pdf_debug"] ) && $woocommerce_pdf_invoice_settings["pdf_debug"] == "true" ) {
        		$this->debug = true;
        	}

        	// Add Debugging Log
			if ( $this->debug == true ) {
				// Add Invoice meta box
				add_action( 'add_meta_boxes', array( $this,'invoice_meta' ), 10, 2 );
				add_action( 'woocommerce_update_order', array( $this,'save_invoice_meta' ) );

			}

        }

        /**
         * [invoice_meta description]
         * @param  [type] $post_type [description]
         * @param  [type] $post      [description]
         * @return [type]            [description]
         */
		function invoice_meta( $post_type,$post ) {
			if ( get_post_meta( $post->ID, '_invoice_meta', TRUE ) ) {
				add_meta_box( 'woocommerce-invoice-meta', __('Invoice Meta', 'woocommerce-pdf-invoice'), array($this,'woocommerce_invoice_meta_box'), 'shop_order', 'advanced', 'low');
			}
		}
		
		/**
		 * [woocommerce_invoice_meta_box description]
		 * @param  [type] $post [description]
		 * @return [type]       [description]
		 */
		function woocommerce_invoice_meta_box( $post ) {
			global $woocommerce;

			$data 							  = get_post_custom( $post->id );
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );
			$pdf_invoice_meta_items			  = WC_pdf_functions::clean_invoice_meta( get_post_meta( $post->ID, '_invoice_meta', TRUE ) );
			
?>
			<div class="invoice_meta_group">
				<ul>
<?php 
			foreach( $pdf_invoice_meta_items as $key => $value ) {
				echo '<li><span>' . ucwords( str_replace( '_', ' ', $key) ) . ' : </span><input name="' . $key . '" type="text" value="' . $value . '" /></li>';
			}
?>
				</ul>
				<p><?php _e('Please ensure you are aware of any potential legal issues before changing this information.<br />Changing the "Invoice Number" field IS NOT RECOMMENDED, changing this could cause duplicate invoice numbers.', 'woocommerce-pdf-invoice'); ?></p>
				<div class="clear"></div>
			</div><?php
			
		}

		/**
		 * [save_invoice_meta description]
		 * @param  [type] $order [description]
		 * @return [type]        [description]
		 */
		function save_invoice_meta( $order ) {
			global $woocommerce;

			if( !is_object( $order ) ) {
				$order 	 = new WC_Order( $order );
			}

			$id                				  = $order->get_id();
			$woocommerce_pdf_invoice_settings = get_option( 'woocommerce_pdf_invoice_settings' );
			$old_pdf_invoice_meta_items		  = WC_pdf_functions::clean_invoice_meta( get_post_meta( $id, '_invoice_meta', TRUE ) );
			$ordernote 						  = '';
			$new_invoice_meta 				  = array();

			if( isset( $old_pdf_invoice_meta_items['invoice_created'] ) ) {

				$invoice_meta_fields = WC_pdf_functions::get_invoice_meta_fields();	
					
				foreach( $invoice_meta_fields as $invoice_meta_field ) {

					// Clean up empty values
					$old_pdf_invoice_meta_items[$invoice_meta_field] = json_encode( $old_pdf_invoice_meta_items[$invoice_meta_field] ) == 'null' ? '' : $old_pdf_invoice_meta_items[$invoice_meta_field];

					// Build new values array
					$new_invoice_meta[$invoice_meta_field] = isset( $_POST[$invoice_meta_field] ) ? wc_clean( $_POST[$invoice_meta_field] ) : wc_clean( $old_pdf_invoice_meta_items[$invoice_meta_field] );

				}

				// Only update if the invoice meta has changed.
				if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) ) {

					// Update the invoice_meta
					update_post_meta( $id, '_invoice_meta', $new_invoice_meta );

					// Update the individual invoice meta
					foreach( $new_invoice_meta as $key => $value ) {
						update_post_meta( $id, '_'.$key, $value );
					}

					// Add an order note with the original infomation
					foreach( $old_pdf_invoice_meta_items as $key => $value ) {
						$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
					}

					// Add order note
					$order->add_order_note( __("Invoice information changed. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

					// Let's check the "next invoice number" setting
					if ( isset($_POST['invoice_number']) && wc_clean( $_POST['invoice_number'] ) > get_option( 'woocommerce_pdf_invoice_current_invoice' ) ) {
						update_option( 'woocommerce_pdf_invoice_current_invoice', wc_clean( $_POST['invoice_number'] ) );
					}
					

				}

			}
			
		}

        /**
         * [sagepay_debug description]
         * @param  Array   $tolog   contents for log
         * @param  String  $id      payment gateway ID
         * @param  String  $message additional message for log
         * @param  boolean $start   is this the first log entry for this transaction
         */
        public static function pdf_debug( $tolog = NULL, $id, $message = NULL, $start = FALSE ) {

        	if( !class_exists('WC_Logger') ) {
        		return;
        	}

            if( !isset( $logger ) ) {
                $logger      = new stdClass();
                $logger->log = new WC_Logger();
            }

            /**
             * If this is the start of the logging for this transaction add the header
             */
            if( $start ) {

                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('PDF Invoice Log', 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('' .date('d M Y, H:i:s'), 'woocommerce-pdf-invoice') );
                $logger->log->add( $id, __('', 'woocommerce-pdf-invoice') );

            }

            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
            $logger->log->add( $id, $message );
            $logger->log->add( $id, print_r( $tolog, TRUE ) );
            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );

        }
    }

    $GLOBALS['WC_pdf_debug'] = new WC_pdf_debug();