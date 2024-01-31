<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_pdf_admin_functions {

	private $debug;

    public function __construct() {

    	// Get PDF Invoice Options
    	$woocommerce_pdf_invoice_options = get_option('woocommerce_pdf_invoice_settings');
    	$this->debug = false;

    	if( isset( $woocommerce_pdf_invoice_options["pdf_debug"] ) && $woocommerce_pdf_invoice_options["pdf_debug"] == "true" ) {
        	$this->debug = true;
        }

		// Add Invoice Number column to orders page in admin
		add_action( 'admin_init' , array( $this, 'pdf_manage_edit_shop_order_columns' ), 10, 2 );

		// Add Invoice Number to column
		add_action( 'manage_shop_order_posts_custom_column' , array( $this, 'invoice_number_admin_init'), 10, 2 );

		// HPOS add Invoice Number to column 
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'invoice_number_admin_init'), 10 ,2 );

		// Bulk Actions Admin Notices
		add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );

		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_pdf_invoice_bulk_actions' ), 10, 3 );
		// HPOS
    	add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'handle_pdf_invoice_bulk_actions' ), 10, 3 );

    	// Update Invoice Meta, only available if debugging is on
		if ( $this->debug == true ) {
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_update_meta' ), 9 );
			// HPOS
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'bulk_edit_pdf_invoice_update_meta' ), 9 );

		}

		// Add PDF Invoice options to Bulk edit menu
    	add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_options' ), 9 );

    	// HPOS add PDF Invoice options to Bulk edit menu
    	add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'bulk_edit_pdf_invoice_options' ), 9 );

    	// Delete Invoice, only available if debugging is on
		if ( $this->debug == true ) {
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_edit_pdf_invoice_delete_invoice' ) );
			// HPOS
    		add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'bulk_edit_pdf_invoice_delete_invoice' ), 9 );
		}
		
		// Add invoice number to order search
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'add_pdf_invoice_number_to_order_search' ) );

		// Add "Attach PDF to this email" settings to WooCommerce emails settings
		add_action( 'woocommerce_email_settings_before', array( $this, 'pdf_invoice_save_woocommerce_email_settings_before' ), 9 );
		add_action( 'woocommerce_email_settings_before', array( $this, 'pdf_invoice_woocommerce_email_settings_before' ), 10 );

    }

    /**
     * [pdf_manage_edit_shop_order_columns description]
     * @param  [type] $columns [description]
     * @return [type]          [description]
     */
	function pdf_manage_edit_shop_order_columns( $columns ) {
		add_filter( 'manage_edit-shop_order_columns', 'invoice_column_admin_init' );

		// Add security check column to orders page in admin HPOS
        add_filter( 'woocommerce_shop_order_list_table_columns', 'invoice_column_admin_init' );
	}

	/**
	 * [invoice_number_admin_init description]
	 * @param  [type] $column [description]
	 * @return [type]         [description]
	 */
	function invoice_number_admin_init( $column_id, $order = NULL ) {

		if ( $column_id == 'pdf_invoice_num' ) {

			if( !is_object($order) ) {
				$order 	 = wc_get_order( $order );
			}

			if ( $order->get_meta( '_invoice_number_display', TRUE ) ) {

				$invoice_number = $order->get_meta( '_invoice_number_display', TRUE );
				$invoice_date 	= $order->get_meta( '_invoice_date', TRUE );
				$output 		=  '<a href="' . $_SERVER['REQUEST_URI'] . '&pdfid=' . $order->get_id() .'">' . $invoice_number . '<br />' . $invoice_date . '</a>';

				/**
				 * Filter the output
				 * $output: default HTML
				 * $invoice_number: stored '_invoice_number_display' from order meta
				 * $invoice_date: stored '_invoice_date' from order meta
				 * $post->ID: $order_id
				 */
				echo apply_filters( 'pdf_invoice_number_order_screen_output', $output, $invoice_number, $invoice_date, $order->get_id() );

			}

		}

	}

	/**
	 * [bulk_edit_pdf_invoice_update_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_update_meta( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_update_invoice_meta'] 	= __( 'Update PDF Meta', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

	/**
	 * [bulk_update_invoice_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_options( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_create_invoice'] 			= __( 'Create Invoice(s)', 'woocommerce-pdf-invoice' );
		$actions['pdf_create_email_invoice'] 	= __( 'Create and Email Invoice(s)', 'woocommerce-pdf-invoice' );
		$actions['pdf_email_invoice'] 			= __( 'Email Invoice(s)', 'woocommerce-pdf-invoice' );
		$actions['pdf_bulk_export'] 			= __( 'Bulk Export PDFs', 'woocommerce-pdf-invoice' );

		return $actions;

    }

	/**
	 * [bulk_update_invoice_meta description]
	 * @param  [type] $actions [description]
	 * @return [type]          [description]
	 */
    public function bulk_edit_pdf_invoice_delete_invoice( $actions ) {

		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}

		$actions['pdf_delete_invoice'] = __( 'Delete Invoice(s)', 'woocommerce-pdf-invoice' );

    	return $actions;

    }

    /**
     * [handle_pdf_invoice_bulk_actions description]
     * @param  [type] $redirect_to [description]
     * @param  [type] $action      [description]
     * @param  [type] $ids         [description]
     * @return [type]              [description]
     */
    public function handle_pdf_invoice_bulk_actions( $redirect_to, $action, $ids ) {

    	$supported_actions = array( 
    		'pdf_update_invoice_meta', 
    		'pdf_create_invoice', 
    		'pdf_create_email_invoice', 
    		'pdf_email_invoice', 
    		'pdf_delete_invoice', 
    		'pdf_bulk_export' 
    	);

    	if( in_array( $action, $supported_actions ) ) {

	    	switch( $action ) {
	    		case 'pdf_update_invoice_meta':
	    			$redirect_args = $this->handle_update_invoice_meta( $redirect_to, $action, $ids );
	    			break;
	    		case 'pdf_create_invoice':
	    			$redirect_args = $this->handle_create_invoice( $redirect_to, $action, $ids );
	    			break;
	    		case 'pdf_create_email_invoice':
	    			$redirect_args = $this->handle_create_email_invoice( $redirect_to, $action, $ids );
	    			break;
	    		case 'pdf_email_invoice':
	    			$redirect_args = $this->handle_email_invoice_meta( $redirect_to, $action, $ids );
	    			break;
	    		case 'pdf_delete_invoice':
	    			$redirect_args = $this->handle_delete_invoice( $redirect_to, $action, $ids );
	    			break;
	    		case 'pdf_bulk_export':
	    			$redirect_args = $this->handle_bulk_export( $redirect_to, $action, $ids );
	    			break;
	    	}

	    	$redirect_to = add_query_arg(
					array(
						'changed'     => $redirect_args['changed'],
						'bulk_action' => $redirect_args['bulk_action'],
					),
					wp_get_referer()
				);

	    	return esc_url_raw( $redirect_to );

    	}

    }

    /**
     * [handle_update_invoice_meta description]
     * @param  [type] $redirect_to [description]
     * @param  [type] $action      [description]
     * @param  [type] $ids         [description]
     * @return [type]              [description]
     */
	public function handle_update_invoice_meta( $redirect_to, $action, $ids ) {

		$changed 		= 0;

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_update_invoice_meta' && $ids != NULL && $this->debug == true ) {

			require_once( 'class-pdf-send-pdf-class.php' );
			require_once( 'class-pdf-functions-class.php' );

			// Get PDF Invoice Options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$ids 	 = array_map( 'absint', $ids );

			foreach ( $ids as $id ) {

				$order 	 = wc_get_order( $id );

				$old_pdf_invoice_meta_items		 = $order->get_meta( '_invoice_meta', TRUE );
				$ordernote 						 = '';

				$new_invoice_meta = array( 
					'invoice_created' 			=> isset( $old_pdf_invoice_meta_items['invoice_created'] ) ? $old_pdf_invoice_meta_items['invoice_created'] : '',
					'invoice_date' 				=> WC_pdf_admin_functions::handle_pdf_date( $id, $settings['pdf_date'], isset( $old_pdf_invoice_meta_items['invoice_date'] ) ? $old_pdf_invoice_meta_items['invoice_date'] : '' ),
					'invoice_number' 			=> isset( $old_pdf_invoice_meta_items['invoice_number'] ) ? $old_pdf_invoice_meta_items['invoice_number'] : '',
					'wc_pdf_invoice_number' 	=> isset( $old_pdf_invoice_meta_items['wc_pdf_invoice_number'] ) ? $old_pdf_invoice_meta_items['wc_pdf_invoice_number'] : '',
					'invoice_number_display' 	=> WC_pdf_functions::create_display_invoice_number( $id ),
					'pdf_company_name' 			=> $settings['pdf_company_name'],
					'pdf_company_information' 	=> nl2br( $settings['pdf_company_details'] ),
					'pdf_registered_name' 		=> $settings['pdf_registered_name'],
					'pdf_registered_address' 	=> $settings['pdf_registered_address'],
					'pdf_company_number' 		=> $settings['pdf_company_number'],
					'pdf_tax_number' 			=> $settings['pdf_tax_number'],
					'pdf_logo_file' 			=> $settings['pdf_logo'] 
				);

				// Only update if the invoice meta has changed.
				if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) ) {

					// Update the invoice_meta
					WC_pdf_functions::update_order_meta_data ( '_invoice_meta', $new_invoice_meta, $order, $id );

					// Update the individual invoice meta
					foreach( $new_invoice_meta as $key => $value ) {
						WC_pdf_functions::update_order_meta_data ( '_'.$key, $value, $order, $id );
					}

					// Add an order note with the original infomation
					if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) ) {

						foreach( $old_pdf_invoice_meta_items as $key => $value ) {
							$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
						}

						// Add order note
						$order->add_order_note( __("Invoice information changed. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

					} // if( isset( $old_pdf_invoice_meta_items ) && is_array( $old_pdf_invoice_meta_items ) )

				} // if( md5( json_encode($old_pdf_invoice_meta_items) ) !== md5( json_encode($new_invoice_meta) ) )

				$changed++;

			} // foreach ( $ids as $id ) {

		}

		return array(
					'changed'     => $changed,
					'bulk_action' => 'handle_update_invoice_meta',
				);

	}

	/**
	 * [handle_create_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_create_invoice( $redirect_to, $action, $ids ) {

		$changed 		= 0;

		// Bail out if this is not the pdf_create_invoice.
		if ( $action === 'pdf_create_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids = array_map( 'absint', $ids );
			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				$order 	 = wc_get_order( $id );

				if ( $order->get_meta( '_invoice_number', TRUE ) == '' ) {
					// Crreate the invoice
                    WC_pdf_functions::woocommerce_completed_order_create_invoice( $id );

                    $changed++;
                }

			}

		}

		return array(
					'changed'     => $changed,
					'bulk_action' => 'handle_create_invoice',
				);

	}

	/**
	 * [handle_create_email_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_create_email_invoice( $redirect_to, $action, $ids ) {

		// Bail out if this is not the pdf_update_invoice_meta.
		if ( $action === 'pdf_create_email_invoice' ) {
			$args = array(
				'ids' => $ids,
			);

			WC()->queue()->add( 'woocommerce_pdf_invoice_create_email_invoice', array( $args ) );

		}

		return array(
					'changed'     => count( $ids ),
					'bulk_action' => 'handle_create_email_invoice',
				);

	}

	/**
	 * [handle_email_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_email_invoice( $redirect_to, $action, $ids ) {

		$changed 		= 0;

		// Bail out if this is not the pdf_email_invoice.
		if ( $action === 'pdf_email_invoice' ) {

			require_once( 'class-pdf-send-pdf-class.php' );

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $id ) {

				$order 	 = wc_get_order( $id );

				if ( $order->get_meta( '_invoice_number', TRUE ) != '' ) {
					// Send the 'Resend Invoice', complete with PDF invoice!
					WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $id, $order );

					$changed++;
                }

			}

		}

		return array(
					'changed'     => $changed,
					'bulk_action' => 'handle_email_invoice',
				);

	}

	/**
	 * [handle_delete_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_delete_invoice( $redirect_to, $action, $ids ) {

		$changed 		= 0;

		// Bail out if this is not the pdf_delete_invoice.
		if ( $action === 'pdf_delete_invoice' && $this->debug == true) {

			$ids = array_map( 'absint', $ids );

			// Sort Order IDs lowest to highest
			sort( $ids );

			foreach ( $ids as $order_id ) {

				$ordernote 					= '';
				$order 						= wc_get_order( $order_id );
				$invoice_meta 				= WC_pdf_functions::get_invoice_meta();
				$old_pdf_invoice_meta_items	= $order->get_meta( '_invoice_meta', TRUE );

				// Add an order note with the original infomation
				foreach( $old_pdf_invoice_meta_items as $key => $value ) {
					$ordernote .= ucwords( str_replace( '_', ' ', $key) ) . ' : ' . $value . "\r\n";
				}

				// Delete the invoice meta
				foreach( $invoice_meta as $meta_key ) {
					// delete_post_meta( $id, $meta_key );
					WC_pdf_functions::delete_order_meta_data( $meta_key, $order, $order_id );
				}

				// Delete other postmeta
				WC_pdf_functions::delete_order_meta_data( '_invoice_created_mysql', $order, $order_id );
				WC_pdf_functions::delete_order_meta_data( '_wc_pdf_invoice_created_date', $order, $order_id );
				WC_pdf_functions::delete_order_meta_data( '_invoice_meta', $order, $order_id );

				WC_pdf_admin_functions::handle_next_invoice_number();

				// Add order note
				$order->add_order_note( __("Invoice deleted. <br/>Previous details : ", 'woocommerce-pdf-invoice' ) . '<br />' . $ordernote, false, true );

				$changed++;

			}

		}

		return array(
					'changed'     => $changed,
					'bulk_action' => 'handle_delete_invoice',
				);

	}

	/**
	 * [handle_delete_invoice description]
	 * @param  [type] $redirect_to [description]
	 * @param  [type] $action      [description]
	 * @param  [type] $ids         [description]
	 * @return [type]              [description]
	 */
	public function handle_bulk_export( $redirect_to, $action, $ids ) {

		$changed = 0;

		// Bail out if this is not the pdf_create_invoice.
		if ( $action === 'pdf_bulk_export' && class_exists("ZipArchive") ) {

			$pdf_export     = new WC_pdf_export();
            $changed    	= $pdf_export->handle_shop_order_bulk_actions( $redirect_to, $action, $ids );

		}

		return array(
					'changed'     => $changed,
					'bulk_action' => 'handle_bulk_export',
				); 

	}

	/**
	 * [handle_pdf_date description]
	 * @param  [type] $order_id     [description]
	 * @param  [type] $usedate      [description]
	 * @param  [type] $invoice_date [description]
	 * @return [type]               [description]
	 */
	public static function handle_pdf_date( $order_id, $usedate, $invoice_date ) {
		global $woocommerce;
		
		$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );
		$date_format = $woocommerce_pdf_invoice_options['pdf_date_format'];
		$date 		 = NULL;  

		require_once( 'class-pdf-send-pdf-class.php' );
		require_once( 'class-pdf-functions-class.php' );

		// Force a $date_format if one is not set
		if ( !isset( $date_format ) || $date_format == '' ) {
			$date_format = "j F, Y";
		}

		$order 	 = wc_get_order( $order_id );

		if ( $usedate == 'completed' ) {
			$order_status = $order->get_status();
			if( $order_status == 'completed' ) {
				$date = WC_send_pdf::get_completed_date( $order_id );
			}

		} elseif ( $usedate == 'order' ) {
			$date = $order->get_date_created();
		}
		
		if ( $date ) {

			// Return a date in the format that matches the PDF Ivoice settings.
			return WC_pdf_admin_functions::format_pdf_date( $date );

		} else {
			// No changes to the date are being made or the order status is not completed but the settings say use the completed date
			// return the date already being used on the invoice.
			return $invoice_date;
		}

	}

	/**
	 * [format_pdf_date description]
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public static function format_pdf_date( $date = NULL ) {
		global $woocommerce;

		if( $date ) {
			$settings 	= get_option( 'woocommerce_pdf_invoice_settings' );
			$date 		= wc_format_datetime( $date, $settings['pdf_date_format'] );
		}

		// Return a date in the format that matches the PDF Invoice settings.
		return $date;

	}

	/**
	 * [handle_next_invoice_number description]
	 * @return [type] [description]
	 */
	public static function handle_next_invoice_number() {
		global $wpdb;

		// Clear the cache
		$wpdb->flush();

		// Get PDF Invoice Options
		$woocommerce_pdf_invoice_settings= get_option('woocommerce_pdf_invoice_settings');

		// Starting Invoice Number stored in PDF Invoice settings
		$starting_invoice_number		 = isset( $woocommerce_pdf_invoice_settings['start_number'] ) ? $woocommerce_pdf_invoice_settings['start_number'] : 0;

		// Set the last invice number to 0 prior to checking the database for any invoice nmbers stored in orders.
		$last_invoice_number			 = 0;

		// Check if we have any invoice numbers stored in orders
		$_invoice_number_meta = $wpdb->get_row("SELECT * FROM $wpdb->postmeta 
								   WHERE meta_key = '_invoice_number' 
								   ORDER BY CAST(meta_value AS SIGNED) DESC
								   LIMIT 1;"
								);

		if ( $_invoice_number_meta != NULL ) {
			$last_invoice_number  = $_invoice_number_meta->meta_value;
		}

		$next_invoice_number = $last_invoice_number +1;

		if( $starting_invoice_number > $next_invoice_number ) {
			$next_invoice_number = $starting_invoice_number;
		}

		// Update WordPress Options table
		update_option( 'woocommerce_pdf_invoice_current_invoice', $next_invoice_number );

		// Update PDF Invoice Settings
		$woocommerce_pdf_invoice_settings['pdf_next_number'] = $next_invoice_number;
		update_option( 'woocommerce_pdf_invoice_settings', $woocommerce_pdf_invoice_settings );
	}

	/**
	 * [get_next_invoice_number description]
	 * @return [type] [description]
	 */
	public static function get_next_invoice_number() {
		// Get PDF Invoice Options
		$woocommerce_pdf_invoice_settings 	= get_option('woocommerce_pdf_invoice_settings');

		$start_number_settings 				= isset( $woocommerce_pdf_invoice_options['start_number'] ) ? $woocommerce_pdf_invoice_options['start_number'] : 0;
		$next_number_settings  				= isset( $woocommerce_pdf_invoice_options['pdf_next_number'] ) ? $woocommerce_pdf_invoice_options['pdf_next_number'] : 0;
		$current_invoice_number_options 	= ( null !== get_option( 'woocommerce_pdf_invoice_current_invoice' ) ) ? get_option( 'woocommerce_pdf_invoice_current_invoice' ) : 0;

		if( $next_number_settings > $current_invoice_number_options ) {
            $next_invoice = $next_number_settings;
        } else {
            $next_invoice = $current_invoice_number_options;
        }

        if( $start_number_settings > $next_invoice ) {
            $next_invoice = $start_number_settings;
        }

        return $next_invoice;	
	}

	/**
	 * Show confirmation message that order has been updated for number of orders.
	 */
	public function bulk_admin_notices() {

		// No point is going any further if the bulk_action is not set
		if ( !isset( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		$bulk_actions = array( 'handle_create_invoice', 'handle_create_email_invoice', 'handle_email_invoice', 'handle_delete_invoice','handle_update_invoice_meta', 'handle_bulk_export' );

		// Bail out if not on shop order list page.
		if ( !in_array( $_REQUEST['bulk_action'], $bulk_actions ) || !isset( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		$number         = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;
		$bulk_action    = wc_clean( wp_unslash( $_REQUEST['bulk_action'] ) );

		switch ( $bulk_action ) {
		    case 'handle_create_invoice':
		        $message = sprintf( _n( '%s invoice created.', '%s invoices created.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) );
						echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		        break;
		    case 'handle_create_email_invoice':
		        $message = sprintf( _n( '%s order has been queued for invoicing.', '%s orders have been queued for invoicing.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) );
						echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		        break;
		    case 'handle_email_invoice':
		        $message = sprintf( _n( '%s invoice has been emailed.', '%s invoices have been emailed.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) );
						echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		        break;
		    case 'handle_delete_invoice':
		    	$message = sprintf( _n( '%s invoice has been deleted.', '%s invoices have been deleted.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) );
						echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		        break;
		    case 'handle_update_invoice_meta':
		    	$message = sprintf( _n( '%s invoice has been updated.', '%s invoices have been updated.', $number, 'woocommerce-pdf-invoice' ), number_format_i18n( $number ) );
						echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		        break;
		    case 'handle_bulk_export':

		    	// Set the temp directory
                $pdftemp    = sys_get_temp_dir();
                $upload_dir = wp_upload_dir();

                if ( file_exists( $upload_dir['basedir'] . '/woocommerce_pdf_invoice/index.html' ) ) {
                    $pdftemp = $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
                }

                $zip_status = get_transient( '_pdf_export_status' );
                $zip_file   = get_transient( '_pdf_export_zip_file' );
                $changed    = get_transient( '_pdf_export_changed' );

                if( isset( $zip_status ) && $zip_status == 0 ) {

                    $exported = isset( $number ) ? absint( $number ) : 0;
                    /* translators: %s: orders count */
                    echo '<div class="updated"><p>' . sprintf( _n( '%s invoice added to zip file.', '%s invoices added to zip file.', $exported, 'woocommerce-pdf-invoice' ), number_format_i18n( $exported ) ) . '</p><p>' . sprintf( __( 'Download the zipfile from <a href="%1$s/%2$s.zip">%3$s.zip</a>', 'woocommerce-pdf-invoice' ), $upload_dir['baseurl'].'/woocommerce_pdf_invoice', $zip_file, $zip_file ) . '</p></div>';
                            
                } else {
                    echo '<div class="error"><p>' . __('Zip file creation failed. Check the log in the WooCommerce System Status logs tab.', 'woocommerce-pdf-invoice') . '</p></div>';
                }
				break;

		    default;
		}
	}

	/**
	 * Add admin dropdown for order types to Woocommerce -> Orders screen
	 */
	public static function restrict_manage_subscriptions() {
		global $typenow;

		if ( 'shop_order' != $typenow ) {
			return;
		}?>
		<select name='shop_order_subtype' id='dropdown_shop_order_subtype'>
			<option value=""><?php esc_html_e( 'All orders types', 'woocommerce-subscriptions' ); ?></option>
			<?php
			$order_types = apply_filters( 'woocommerce_subscriptions_order_type_dropdown', array(
				'original'    => _x( 'Original', 'An order type', 'woocommerce-subscriptions' ),
				'parent'      => _x( 'Subscription Parent', 'An order type', 'woocommerce-subscriptions' ),
				'renewal'     => _x( 'Subscription Renewal', 'An order type', 'woocommerce-subscriptions' ),
				'resubscribe' => _x( 'Subscription Resubscribe', 'An order type', 'woocommerce-subscriptions' ),
				'switch'      => _x( 'Subscription Switch', 'An order type', 'woocommerce-subscriptions' ),
				'regular'     => _x( 'Non-subscription', 'An order type', 'woocommerce-subscriptions' ),
			) );

			foreach ( $order_types as $order_type_key => $order_type_description ) {
				echo '<option value="' . esc_attr( $order_type_key ) . '"';

				if ( isset( $_GET['shop_order_subtype'] ) && $_GET['shop_order_subtype'] ) {
					selected( $order_type_key, $_GET['shop_order_subtype'] );
				}

				echo '>' . esc_html( $order_type_description ) . '</option>';
			}
			?>
			</select>
		<?php
	}

	/**
	 * Add request filter for order types to Woocommerce -> Orders screen
	 *
	 * Including or excluding posts with a '_subscription_renewal' meta value includes or excludes
	 * renewal orders, as required.
	 */
	public static function orders_by_type_query( $vars ) {
		global $typenow, $wpdb;

		if ( 'shop_order' == $typenow && ! empty( $_GET['shop_order_subtype'] ) ) {

			if ( 'original' == $_GET['shop_order_subtype'] || 'regular' == $_GET['shop_order_subtype'] ) {

				$vars['meta_query']['relation'] = 'AND';

				$vars['meta_query'][] = array(
					'key'     => '_subscription_renewal',
					'compare' => 'NOT EXISTS',
				);

				$vars['meta_query'][] = array(
					'key'     => '_subscription_switch',
					'compare' => 'NOT EXISTS',
				);

			} elseif ( 'parent' == $_GET['shop_order_subtype'] ) {

				$vars['post__in'] = wcs_get_subscription_orders();

			} else {

				switch ( $_GET['shop_order_subtype'] ) {
					case 'renewal' :
						$meta_key = '_subscription_renewal';
						break;
					case 'resubscribe' :
						$meta_key = '_subscription_resubscribe';
						break;
					case 'switch' :
						$meta_key = '_subscription_switch';
						break;
					default:
						$meta_key = '';
						break;
				}

				$meta_key = apply_filters( 'woocommerce_subscriptions_admin_order_type_filter_meta_key', $meta_key, $_GET['shop_order_subtype'] );

				if ( ! empty( $meta_key ) ) {
					$vars['meta_query'][] = array(
						'key'     => $meta_key,
						'compare' => 'EXISTS',
					);
				}
			}

			// Also exclude parent orders from non-subscription query
			if ( 'regular' == $_GET['shop_order_subtype'] ) {
				$vars['post__not_in'] = wcs_get_subscription_orders();
			}
		}

		return $vars;
	}

	/**
	 * [add_pdf_invoice_number_to_order_search description]
	 * @param [type] $search_items [description]
	 */
	public static function add_pdf_invoice_number_to_order_search( $search_items ) {

		$search_items[] = '_invoice_number';
		$search_items[] = '_invoice_number_display';

		return $search_items;
	}

	/**
	 * [pdf_invoice_save_woocommerce_email_settings_before description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	function pdf_invoice_save_woocommerce_email_settings_before( $object ) {

		$emails = WC_Emails::instance();

        // Build the array of available email IDs
        foreach ( $emails->get_emails() as $email ) {
            $email_ids[] = $email->id;
        }
		
		// Get the data when the form is saved
		$post_data = $object->get_post_data();
		
		if( in_array( $object->id, $email_ids ) && isset( $post_data ) && !empty( $post_data ) ) {
			
			// Get the settings for this email
			$options = get_option( 'woocommerce_' . $object->id . '_settings' );
			
			// set pdf_invoice_attach_pdf_invoice
			$attach = isset( $post_data['woocommerce_' . $object->id . '_pdf_invoice_attach_pdf_invoice'] ) ? 'yes' : 'no';

			// set pdf_invoice_template_pdf_invoice
			$template = isset( $post_data['woocommerce_' . $object->id . '_pdf_invoice_template_pdf_invoice'] ) ? $post_data['woocommerce_' . $object->id . '_pdf_invoice_template_pdf_invoice'] : 'template';

			// Update the 
			$options['pdf_invoice_attach_pdf_invoice'] 		= $attach;
			$options['pdf_invoice_template_pdf_invoice'] 	= $template;

			// Update the email settings
			update_option( 'woocommerce_' . $object->id . '_settings', $options );

			// Update the Email Object settings
			$object->settings['pdf_invoice_attach_pdf_invoice'] 	= $options['pdf_invoice_attach_pdf_invoice'];
			$object->settings['pdf_invoice_template_pdf_invoice'] 	= $options['pdf_invoice_template_pdf_invoice'];
				
		}
		
	}

	/**
	 * [pdf_invoice_woocommerce_email_settings_before description]
	 * @param  [type] $object [description]
	 * @return [type]         [description]
	 */
	function pdf_invoice_woocommerce_email_settings_before( $object ) {
		
		$emails = WC_Emails::instance();
        // Build the array of available email IDs
        foreach ( $emails->get_emails() as $email ) {
            $email_ids[] = $email->id;
        }

        $template_files = $this->get_template_files();

		$woocommerce_email_settings_attach = array(
			'title'         => __( 'Attach PDF Invoice to this email', 'woocommerce-pdf-invoice' ),
			'lable'   		=> __( 'Attach PDF Invoice to this email if an invoice is available?', 'woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'default'       => 'no',
		);

		$woocommerce_email_settings_attach_yes = array(
			'title'         => __( 'Attach PDF Invoice to this email', 'woocommerce-pdf-invoice' ),
			'lable'   		=> __( 'Attach PDF Invoice to this email if an invoice is available?', 'woocommerce-pdf-invoice' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
		);

		$woocommerce_email_settings_template = array(
			'title'         => __( 'Choose PDF Invoice tempate for this email.', 'woocommerce-pdf-invoice' ),
			'lable'   		=> __( 'Set the invoice template to use for the PDF attached to this email. Defaults to template', 'woocommerce-pdf-invoice' ),
			'type'          => 'select',
			'default'       => 'template',
			'options'  		=> $template_files,
		);
		
		if( in_array( $object->id, $email_ids ) ) {
			// Add the field to the settings

			if( $object->id == 'pdf_admin_invoice' || $object->id == 'pdf_customer_invoice' ) {
				$object->form_fields['pdf_invoice_attach_pdf_invoice'] = $woocommerce_email_settings_attach_yes;
				$object->form_fields['pdf_invoice_template_pdf_invoice'] = $woocommerce_email_settings_template;
			} else {
				$object->form_fields['pdf_invoice_attach_pdf_invoice'] = $woocommerce_email_settings_attach;
				$object->form_fields['pdf_invoice_template_pdf_invoice'] = $woocommerce_email_settings_template;				
			}

		}
		
	}
 
	/**
	 * Get list of template files for settings
	 * @return [type] [description]
	 */
	function get_template_files() {

		$default_template_folder = PDFPLUGINPATH . 'templates/';
		$theme_template_folder 	 = get_stylesheet_directory() . '/pdf_templates/';

		// Output array of template file names
		$templates = array();

		$default_templates 	= glob( $default_template_folder . '*.php' );
		$theme_templates   	= glob( $theme_template_folder . '*.php' );

		// Remove the paths
		foreach( $default_templates as $template ) {

            if( !is_file($template) ) {
                unset( $template ); 
            }

            $filename = str_replace( array( $default_template_folder, '.php' ), '', $template );

            $templates[$filename] = $filename;
        }

        foreach( $theme_templates as $template ) {

        	if( !is_file($template) ) {
                unset( $template ); 
            }

            $filename = str_replace( array( $theme_template_folder, '.php' ), '', $template );

            $templates[$filename] = $filename;
        }



/*

        $templates_array 	= array_unique( $templates_array );
*/
        return $templates;

	}

    /**
     * [sagepay_debug description]
     * @param  Array   $tolog   contents for log
     * @param  String  $id      payment gateway ID
     * @param  String  $message additional message for log
     * @param  boolean $start   is this the first log entry for this transaction
     */
    public static function debuger( $tolog = NULL, $id = NULL, $message = NULL, $start = FALSE ) {

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

$GLOBALS['WC_pdf_admin_functions'] = new WC_pdf_admin_functions();
