<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WC_pdf_functions {

	    public function __construct() {
			
			global $wpdb,$woocommerce;

			// Add woocommerce-pdf_admin-css.css to admin
			add_action( 'admin_enqueue_scripts', array( $this, 'woocommerce_pdf_admin_css' ) );

			// Add woocommerce-pdf-frontend-css.css to frontend
			add_action( 'init', array( $this, 'woocommerce_pdf_frontend_css' ) );

	    	// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
	    	if ( extension_loaded('iconv') && extension_loaded('mbstring') ) {					
				
				// Add PDF Invoice Email
				add_action( 'init',array( $this, 'pdf_invoice_email_init' ) );
				add_action( 'admin_init', array( $this, 'pdf_invoice_email_admin_init' ) );
				
				// Create Invoice actions
				add_action( 'woocommerce_before_thankyou', array( $this,'pdf_invoice_payment_complete' ) );

				add_action( 'init', array( $this,'pdf_invoice_order_status_array' ) );

				// Create Invoice for manual subscriptions
				add_filter( 'wcs_new_order_created', array( $this, 'create_pdf_invoice_for_manual_subscriptions'), 10, 3 );
				add_filter( 'wcs_renewal_order_created', array( $this, 'create_pdf_invoice_for_manual_subscriptions' ), 10,2 );

				// Attach Invoice to Refund Emails
				// add_action( 'woocommerce_order_partially_refunded', array( $this, 'attach_invoice_refunds' ), 10, 2 );
				// add_action( 'woocommerce_order_fully_refunded', array( $this, 'attach_invoice_refunds' ), 10, 2 );

				add_action( 'woocommerce_order_fully_refunded_notification', array( $this, 'attach_invoice_refunds' ), 10, 2 );
				add_action( 'woocommerce_order_partially_refunded_notification', array( $this, 'attach_invoice_refunds' ), 10, 2 );

				// Add Send Invoice icon to actions on orders page in admin
				// add_filter( 'woocommerce_admin_order_actions', array( $this,'send_invoice_icon_admin_init' ) ,10 , 2 );
				
				// Add Download Invoice icon to actions on orders page in admin
				// add_filter( 'woocommerce_admin_order_actions', array( $this,'download_invoice_icon_admin_init' ) ,11 , 2 );

				add_action( 'admin_init', array( $this, 'pdf_invoice_order_action_icons' ) );

				// Send PDF when icon is clicked
				add_action( 'wp_ajax_pdfinvoice-admin-send-pdf', array( $this, 'pdfinvoice_admin_send_pdf') );
				
				// Add invoice action to My-order page
				add_filter( 'woocommerce_my_account_my_orders_actions', array( $this,'my_account_pdf' ), 10, 2 );
				
				// Keep an eye on the URL
				add_action( 'init' , array( $this,'pdf_url_check') );
				add_action( 'init' , array( $this,'pdf_link_download') );
				add_action( 'admin_init' , array( $this,'admin_pdf_url_check') );

				// Send test email with PDF attachment
				add_action( 'admin_init' , array( $this,'pdf_invoice_send_test') );

				// Delete Invoice information
				add_action( 'admin_init' , array( $this,'pdf_invoice_delete_invoices') );

				// Create invoices for past orders
				add_action( 'admin_init' , array( $this,'pdf_invoice_past_orders') );
				add_action( 'woocommerce_pdf_invoice_update_past_orders', array( __CLASS__, 'action_scheduler_update_past_orders' ), 10, 2 );

				add_action( 'admin_init' , array( $this,'pdf_invoice_past_orders_email') );
				add_action( 'woocommerce_pdf_invoice_update_email_past_orders', array( __CLASS__, 'action_scheduler_update_email_past_orders' ), 10, 2 );

				// Fix Invoice dates
				add_action( 'admin_init' , array( $this,'pdf_invoice_fix_dates') );
				add_action( 'woocommerce_pdf_invoice_fix_dates', array( __CLASS__, 'action_scheduler_fix_dates' ), 10, 2 );

				// Delete invoices and zip files from temp folder
				add_action( 'init' , array( $this,'pdf_invoice_delete_files') );
				add_action( 'woocommerce_pdf_invoice_delete_files', array( __CLASS__, 'action_scheduler_delete_files' ), 10, 2 );

				// Clean up Action Scheduler table
				add_action( 'init' , array( $this,'pdf_invoice_delete_logs') );
				add_action( 'woocommerce_pdf_invoice_delete_logs', array( __CLASS__, 'action_scheduler_delete_logs' ), 10, 2 );

				// Use Action Scheduler to update Order Meta with pdf_creation
				add_action( 'woocommerce_pdf_invoice_update_order_meta_invoice_date', array( __CLASS__, 'action_scheduler_update_order_meta_invoice_date' ), 10, 2 );

				// Add invoice link to Thank You page
				add_action( 'woocommerce_thankyou' , array( $this,'invoice_link_thanks' ), 10 );

				// Deprecated Subscriptions filters :/
				if ( class_exists( 'WC_Subscriptions' ) ) {
					if ( version_compare( WC_Subscriptions::$version, '4.6.0', '<' ) ) {
						add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this, 'subscriptions_remove_renewal_order_meta_3' ), 10, 3 );
					} else {
						add_filter( 'wcs_renewal_order_meta_query', array( $this, 'subscriptions_remove_renewal_order_meta_3' ), 10, 3 );
					}
				}

			}

		}

		/**
		 * { pdf_invoice_payment_complete description }
		 *
		 * @param      <type>  $order_id  The order identifier
		 */
		public static function pdf_invoice_payment_complete( $order_id ) {

			// Get order object
			$order = new WC_Order( $order_id );

			// Get the invoice options
			$settings 			= get_option( 'woocommerce_pdf_invoice_settings' );

			$create_invoice 	= isset( $settings['create_invoice'] ) ? $settings['create_invoice'] : NULL;
			
			$order_status_array = WC_pdf_functions::get_order_status_array( $create_invoice );

			if( in_array( $order->get_status(), $order_status_array ) ) {
				WC_pdf_functions::woocommerce_completed_order_create_invoice( $order_id );

				// Maybe send PDF_Invoice_Admin_PDF_Invoice when invoice is created
				// WC_pdf_functions::maybe_send_admin_email_on_creation( $order_id );
			}

		}

		/**
		 * [pdf_invoice_order_status_array description]
		 * @return [type] [description]
		 */
		public static function pdf_invoice_order_status_array() {

			// Get the invoice options
			$settings 			= get_option( 'woocommerce_pdf_invoice_settings' );

			$create_invoice 	= isset( $settings['create_invoice'] ) ? $settings['create_invoice'] : NULL;
			
			$order_status_array = WC_pdf_functions::get_order_status_array( $create_invoice );

			foreach( $order_status_array as $order_status ) {
				add_action( 'woocommerce_order_status_' . $order_status, array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
				add_action( 'woocommerce_order_status_pending_to_' . $order_status . '_notification', array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
			}

			// Completed Renewal Order
			add_action( 'woocommerce_order_status_completed_renewal_notification', array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
			
		}

		/**
		 * [pdf_invoice_order_action_icons description]
		 * @return [type] [description]
		 */
		public static function pdf_invoice_order_action_icons() {
			// Add Send Invoice icon to actions on orders page in admin
			add_filter( 'woocommerce_admin_order_actions', array( 'WC_pdf_functions','send_invoice_icon_admin_init' ) ,10 , 2 );
			
			// Add Download Invoice icon to actions on orders page in admin
			add_filter( 'woocommerce_admin_order_actions', array( 'WC_pdf_functions','download_invoice_icon_admin_init' ) ,11 , 2 );
		}

		/**
		 * [create_pdf_invoice_for_manual_subscriptions description]
		 * @param  [type] $renewal_order [description]
		 * @param  [type] $subscription  [description]
		 * @param  [type] $type          [description]
		 * @return [type]                [description]
		 */
		public static function create_pdf_invoice_for_manual_subscriptions( $renewal_order, $subscription, $type = NULL ) {

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$order_status_array = WC_pdf_functions::get_order_status_array( $settings['create_invoice'] );

			if( $subscription->is_manual() && in_array( $renewal_order->get_status(), $order_status_array ) ) {
		    	WC_pdf_functions::woocommerce_completed_order_create_invoice( $renewal_order->get_id() );
		    }

		    return $renewal_order;

		}

		/**
		 * [get_order_status_array description]
		 * @param  [type] $create_invoice [description]
		 * @return [type]                 [description]
		 */
		public static function get_order_status_array( $create_invoice ) {

			$order_status_array = array();

			// Create an array of acceptable order statuses based on $woocommerce_pdf_invoice_options['create_invoice']
			if ( $create_invoice == 'on-hold' ) {
				$order_status_array = array( 'on-hold','pending','processing','completed' );
			} elseif ( $create_invoice == 'pending' ) {
				$order_status_array = array( 'pending','processing','completed' );
			} elseif ( $create_invoice == 'processing' ) {
				$order_status_array = array( 'processing','completed' );
			} elseif ( $create_invoice == 'completed' ) {
				$order_status_array = array( 'completed' );
			}

			/**
			 * Modify the $order_status_array if required.
			 * add_filter ( 'pdf_invoice_order_status_array', 'add_custom_order_status_to_pdf_invoice_order_status_array', 99, 2 );
			 * 
			 * function add_custom_order_status_to_pdf_invoice_order_status_array ( $order_status_array ) {
			 * 	$order_status_array[] = 'dispensing';
			 * 	return $order_status_array;
			 * }
			 */
			$order_status_array = apply_filters( 'pdf_invoice_order_status_array', $order_status_array );

			return $order_status_array;

		}

		/** 
		 * If an order is marked complete add _invoice_number, _invoice_number_display and _invoice_date
		 * It's important to remember that once an invoice has been created you can not change
		 * the number or date and you shouldn't change any other details either!
		 */ 	 
		public static function woocommerce_completed_order_create_invoice( $order_id ) {
			global $woocommerce;

			// Only create invoices for the 'right' post types
			// add_filter( 'woocommerce_pdf_invoice_ignore_order_types', 'custom_woocommerce_pdf_invoice_ignore_order_types', 10, 2 );
			// function custom_woocommerce_pdf_invoice_ignore_order_types( $ignore, $order_id ) {
			// 	$ignore[] = 'wcdp_payment';
			// 	return $ignore;
			// }
			$ignore_order_types = apply_filters( 'woocommerce_pdf_invoice_ignore_order_types', array(), $order_id );

			$order_type 		= get_post_type( $order_id );

			if( in_array( $order_type, $ignore_order_types ) ) {
				return NULL;
			}

			$order = new WC_Order( $order_id );

			if( !class_exists('WC_send_pdf') ){
				include( 'class-pdf-send-pdf-class.php' );
			}

			$no_pdf = true;
			if( apply_filters( 'pdf_invoice_no_pdf', $no_pdf, $order_id ) === true ) {

				// Invoice Number
				WC_pdf_functions::set_invoice_number( $order_id );
				
				// Invoice Date
				WC_pdf_functions::set_invoice_date( $order_id );

				// Set Invoice Meta
				WC_pdf_functions::set_invoice_meta( $order_id );

				apply_filters( 'maybe_filter_woocommerce_completed_order_create_invoice', $order_id );

				// Create Attachments
				$attachement = add_filter( 'woocommerce_email_attachments', array( 'WC_send_pdf', 'pdf_attachment' ), 10, 3 );

				// Maybe send PDF_Invoice_Admin_PDF_Invoice when invoice is created
				WC_pdf_functions::maybe_send_admin_email_on_creation( $order_id );

				// Return Attachments
				return $attachement;

			}

			return NULL;

		} // woocommerce_completed_order_create_invoice

		/**
		 * [attach_invoice_refunds description]
		 * @param  [type] $order_id  [description]
		 * @param  [type] $refund_id [description]
		 * @return [type]            [description]
		 */
		public static function attach_invoice_refunds( $order_id, $refund_id ) {
			if( !class_exists('WC_send_pdf') ){
				include( 'class-pdf-send-pdf-class.php' );
			}
			// Return Attachments
			return add_filter( 'woocommerce_email_attachments' , array( 'WC_send_pdf' ,'pdf_attachment' ), 10, 3 );
		}

		/**
		 * [get_next_invoice_number description]
		 * @param [type] $order_id [description]
		 */
		public static function get_next_invoice_number() {
			global $wpdb,$woocommerce;

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$annual_restart = FALSE;
			if ( isset($settings['annual_restart']) && $settings['annual_restart'] == 'TRUE' ) {
				$annual_restart = TRUE;
			}

			// Make sure the variable are cleared
			$invoice 		 = NULL;
			$current_invoice = 0;
			$next_invoice 	 = 1; 

			$invoice_count 	 = 0;

			// Check if we have created an invoice before this order
            if ( $annual_restart ) {

                // Jan 1st to Dec 31st of current year
                $start_date = date('Y-01-01');
                $end_date   = date('Y-12-31');

                // Invoices from this year
                $invoice = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta
											WHERE meta_key = '_invoice_number' 
											AND post_id IN (
											    SELECT post_id FROM $wpdb->postmeta
											    WHERE meta_key = '_invoice_created_mysql'
											    AND meta_value >=  '$start_date'
											    AND meta_value <=  '$end_date'
											)
											ORDER BY CAST(meta_value AS SIGNED) DESC
											LIMIT 1;"
                                        );

            } else {

                // Check if we have created an invoice before this order
                $invoice = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta 
                                            WHERE meta_key = '_invoice_number' 
                                            ORDER BY CAST(meta_value AS SIGNED) DESC
                                            LIMIT 1;"
                                        );
            }
                    
			if ( null !== $invoice ) {
				$current_invoice  = $invoice->meta_value;
			}

			/**
			 * If !$current_invoice then we use the start_number or 1 if no start number is set
			 */
			if ( !$current_invoice ) {

				// Check if there are any invoices
                $invoice_count =   $wpdb->get_var( "SELECT count(*) FROM $wpdb->postmeta 
													WHERE meta_key = '_invoice_number' "
		                                        );

				$next_invoice = 1;

				// If there is a $start_number and $annual_restart is not set
				if ( isset($settings['start_number']) && !$annual_restart ) {
					$next_invoice = $settings['start_number'];
				}

				// If there is a $start_number and $annual_restart is set and there are no previous invoices.
				if ( isset($settings['start_number']) && $annual_restart && $invoice_count == 0 ) {
					$next_invoice = $settings['start_number'];
				}

			} else {
				$next_invoice = $current_invoice + 1;
			}

			// Check the pdf_next_number settings in case 
			if( isset( $settings['pdf_next_number'] ) && $settings['pdf_next_number'] > $next_invoice && !$annual_restart ) {
				$next_invoice =  $settings['pdf_next_number'];
			}

			return $next_invoice;

		}

		/**
		 * [set_invoice_number description]
		 * @param [type] $order_id [description]
		 */
		public static function set_invoice_number( $order_id ) {

			// Get Order
			$order   = new WC_Order( $order_id );

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			// Fix the Invoice date at the point we create the invoice number.
			if( !get_post_meta( $order_id, '_wc_pdf_invoice_created_date', TRUE ) ) {

				$invoice_meta 	= get_post_meta( $order_id, '_invoice_meta', TRUE );

				if( isset( $invoice_meta['invoice_created'] ) && strlen( $invoice_meta['invoice_created'] != 0 ) ) {
					update_post_meta( $order_id, '_wc_pdf_invoice_created_date', $invoice_meta['invoice_created'] );
				} else {
					$created_date = wc_string_to_datetime( current_time('mysql', true ) );
					update_post_meta( $order_id, '_wc_pdf_invoice_created_date', $created_date );
				}

				// Add additional _invoice_created_mysql for invoice number restart option
				add_post_meta( $order_id, '_invoice_created_mysql', current_time('mysql', true ), TRUE );

			}

			// Invoice number exists, just get it and leave.
			if( get_post_meta( $order_id, '_wc_pdf_invoice_number', TRUE ) ) {
				return get_post_meta( $order_id, '_wc_pdf_invoice_number', TRUE );
			}

			if( get_post_meta( $order_id, '_invoice_number', TRUE ) ) {

				$compatibility_invoice_number = get_post_meta( $order_id, '_invoice_number', TRUE );
				update_post_meta( $order_id, '_wc_pdf_invoice_number', $compatibility_invoice_number );

				return $compatibility_invoice_number;
				
			}

			if ( !isset( $settings['sequential'] ) || $settings['sequential'] != 'true' ) {
				// Sequential order numbering is not needed, just use the order_id.
				$next_invoice = apply_filters( 'woocommerce_pdf_invoices_get_order_number', $order->get_order_number(), $order );
			} else {
				$next_invoice = WC_pdf_functions::get_next_invoice_number();
			}

			// Set an option for the current invoice and year to avoid querying the DB everytime
			update_option( 'woocommerce_pdf_invoice_current_year', date('Y') );
			
			// Allow the invoice number to be modified if needed
			$next_invoice = apply_filters( 'woocommerce_pdf_invoices_set_invoice_number', $next_invoice, $order_id );

			// set the invoice number for the order
			update_post_meta( $order_id, '_wc_pdf_invoice_number', $next_invoice );
			update_post_meta( $order_id, '_invoice_number', $next_invoice );

			// Update PDF Invoice options 'pdf_next_number'
			$settings['pdf_next_number'] = $next_invoice;
			update_option( 'woocommerce_pdf_invoice_settings', $settings );
			
			return $next_invoice;
		
		}

		/**
		 * [set_invoice_date description]
		 * @param [type] $order_id [description]
		 */
		public static function set_invoice_date( $order_id ) {
			global $woocommerce;

			$order 			= new WC_Order( $order_id );
			$invoice_date 	= get_post_meta( $order_id, '_invoice_date', TRUE );

			if ( get_post_meta($order_id, '_invoice_date', TRUE) && strlen( get_post_meta($order_id, '_invoice_date', TRUE) ) != 0 ) {
				return get_post_meta( $order_id, '_invoice_date', TRUE );
			} else {

				// Get the invoice options
				$settings = get_option( 'woocommerce_pdf_invoice_settings' );

				$pdf_date = self::get_option( 'pdf_date' );

				if ( $pdf_date == 'invoice' ) {
					$invoice_date = get_post_meta( $order_id, '_wc_pdf_invoice_created_date', TRUE ) ? get_post_meta( $order_id, '_wc_pdf_invoice_created_date', TRUE ) : $order->get_date_created();
				} elseif ( $pdf_date == 'completed' ) {
					$invoice_date = $order->get_date_completed();
				} else {
					$invoice_date = $order->get_date_created();
				}

				$invoice_date = wc_format_datetime( $invoice_date, self::get_option( 'pdf_date_format' ) ); 

				return apply_filters( 'woocommerce_pdf_nvoices_set_invoice_date', $invoice_date, $order_id );

			}

		}

		/**
		 * [set_invoice_meta description]
		 * @param [type] $order_id [description]
		 * @param [type] $data     [description]
		 * @param [type] $field    [description]
		 */
		public static function set_invoice_meta( $order_id ) {
			global $woocommerce;

			$order 	  = new WC_Order( $order_id );

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$order_date 	= $order->get_date_created();
			$completed_date = $order->get_date_completed();
			$created_date 	= get_post_meta( $order_id, '_wc_pdf_invoice_created_date', TRUE );

			// Backup Invoice Meta
			$invoice_meta = array(
				'invoice_created' 			=> get_post_meta( $order_id, '_wc_pdf_invoice_created_date', TRUE ),
				'invoice_date' 				=> WC_pdf_functions::set_invoice_date( $order_id ),
				'invoice_number' 			=> WC_pdf_functions::set_invoice_number( $order_id ),
				'wc_pdf_invoice_number'		=> WC_pdf_functions::set_invoice_number( $order_id ),
				'invoice_number_display' 	=> WC_pdf_functions::create_display_invoice_number( $order_id ),
				'pdf_company_name' 			=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_name' ),
				'pdf_company_details' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_details' ),
				'pdf_registered_name' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_name' ),
				'pdf_registered_address' 	=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_address' ),
				'pdf_company_number' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_number' ),
				'pdf_tax_number' 			=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_tax_number' ),
				'pdf_logo_file' 			=> isset( $settings['logo_file'] ) ? $settings['logo_file'] : ''
			);

			// Create separate order_meta
			foreach( $invoice_meta as $meta_key => $meta_value ) {
				update_post_meta( $order_id, '_'.$meta_key, $meta_value );
			}

			// Set the _invoice_meta post_meta
			update_post_meta( $order_id, '_invoice_meta', $invoice_meta );

		}

		/**
		 * [create_display_invoice_number description]
		 * @param  [type] $next_invoice [Raw invoice number]
		 * @param  [type] $id           [Order ID]
		 * get_woocommerce_pdf_date( $order_id, $usedate, $sendsomething = false, $display_date = 'invoice', $date_format )
		 * @return [type]               [Formatted Invoice Number]
		 */
		static function create_display_invoice_number( $order_id ) { 

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$pdf_date = self::get_option( 'pdf_date' );

			if ( $pdf_date == 'invoice' ) {
				$date_required = 'invoice';
			} else if ( $pdf_date == 'completed' ) {
				$date_required = 'completed';
			} else {
				$date_required = 'order';
			}

			// Get the Invoice Number
			$invoice_number = get_post_meta( $order_id, '_wc_pdf_invoice_number', TRUE );
			// Get the Invoice Date
			$invoice_date = WC_send_pdf::get_woocommerce_pdf_date( $order_id, $date_required, false, 'invoice', self::get_option( 'pdf_date_format' ) );

			// pattern substitution
			$replacements = array(
				'{{D}}'    			=> date_i18n( 'j', strtotime( $invoice_date ) ),
				'{{DD}}'   			=> date_i18n( 'd', strtotime( $invoice_date ) ),
				'{{M}}'    			=> date_i18n( 'n', strtotime( $invoice_date ) ),
				'{{MM}}'   			=> date_i18n( 'm', strtotime( $invoice_date ) ),
				'{{YY}}'   			=> date_i18n( 'y', strtotime( $invoice_date ) ),
				'{{yy}}'   			=> date_i18n( 'y', strtotime( $invoice_date ) ),
				'{{YYYY}}' 			=> date_i18n( 'Y', strtotime( $invoice_date ) ),
				'{{H}}'    			=> date_i18n( 'G', strtotime( $invoice_date ) ),
				'{{HH}}'   			=> date_i18n( 'H', strtotime( $invoice_date ) ),
				'{{N}}'    			=> date_i18n( 'i', strtotime( $invoice_date ) ),
				'{{S}}'    			=> date_i18n( 's', strtotime( $invoice_date ) ),
				'{{year}}' 			=> date_i18n( 'Y', strtotime( $invoice_date ) ),
				'{{YEAR}}' 			=> date_i18n( 'Y', strtotime( $invoice_date )),
				'{{invoicedate}}' 	=> $invoice_date,
				'{{INVOICEDATE}}' 	=> $invoice_date,
				'{D}'    			=> date_i18n( 'j', strtotime( $invoice_date ) ),
				'{DD}'   			=> date_i18n( 'd', strtotime( $invoice_date ) ),
				'{M}'    			=> date_i18n( 'n', strtotime( $invoice_date ) ),
				'{MM}'   			=> date_i18n( 'm', strtotime( $invoice_date ) ),
				'{YY}'   			=> date_i18n( 'y', strtotime( $invoice_date ) ),
				'{yy}'   			=> date_i18n( 'y', strtotime( $invoice_date ) ),
				'{YYYY}' 			=> date_i18n( 'Y', strtotime( $invoice_date ) ),
				'{H}'    			=> date_i18n( 'G', strtotime( $invoice_date ) ),
				'{HH}'   			=> date_i18n( 'H', strtotime( $invoice_date ) ),
				'{N}'    			=> date_i18n( 'i', strtotime( $invoice_date ) ),
				'{S}'    			=> date_i18n( 's', strtotime( $invoice_date ) ),
				'{year}' 			=> date_i18n( 'Y', strtotime( $invoice_date ) ),
				'{YEAR}' 			=> date_i18n( 'Y', strtotime( $invoice_date ) ),
				'{invoicedate}' 	=> $invoice_date,
				'{INVOICEDATE}' 	=> $invoice_date,
			);

			$replacements = apply_filters( 'woocommerce_pdf_invoice_create_display_invoice_number_replacements', $replacements, $order_id, $invoice_number, $invoice_date );
			
			$invoice_prefix = esc_html( self::get_option( 'pdf_prefix' ) );
			$invoice_prefix = str_replace( array_keys( $replacements ), $replacements, $invoice_prefix );

			$invoice_suffix = esc_html( self::get_option( 'pdf_sufix' ) );
			$invoice_suffix = str_replace( array_keys( $replacements ), $replacements, $invoice_suffix );

			$padding = self::get_option( 'padding' );
				
			// Add number padding if necessary
			if ( '' != $padding ) {
				$invnum 	= $invoice_prefix . str_pad($invoice_number, strlen($settings['padding']), "0", STR_PAD_LEFT) . $invoice_suffix;
			} else {
				$invnum 	= $invoice_prefix . $invoice_number . $invoice_suffix;
			}

			// set the display invoice number for the order
			// update_post_meta( $order_id, '_invoice_number_display', $invnum );
			
			return $invnum;

		}

		/**
		 * [get_pdf_company_field description]
		 * @param  [type] $order_id [description]
		 * @param  [type] $field    [description]
		 * @return [type]           [description]
		 */
		public static function get_pdf_company_field( $order_id, $field ) {
			global $woocommerce;

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$return = isset( $settings[$field] ) ? $settings[$field] : '';

			// set the field for the order
			// update_post_meta( $order_id, '_'.$field, $return );

			return apply_filters( 'pdf_invoice_set_'.$field, $return, $order_id );
		}

		/**
		 * Add woocommerce-pdf-admin-css.css to admin
		 */
		function woocommerce_pdf_admin_css() {
			wp_register_style( 
				'woocommerce-pdf-admin-css', 
				str_replace( 'classes/', '', plugins_url( 'assets/css/woocommerce-pdf-admin-css.css', __FILE__ ) ),
				NULL,
				PDFVERSION 
				);

			wp_enqueue_style( 'woocommerce-pdf-admin-css' );
		}

		function woocommerce_pdf_frontend_css() {
			wp_register_style( 
                'woocommerce-pdf-frontend-css', 
                str_replace( 'classes/', '', plugins_url( 'assets/css/woocommerce-pdf-frontend-css.css', __FILE__ ) ),
                NULL,
                PDFVERSION 
                );

            wp_enqueue_style( 'woocommerce-pdf-frontend-css' );
		}

		/**
		 * Add Send Invoice icon to actions on orders page in admin
		 */
		public static function send_invoice_icon_admin_init( $actions, $order ) {
			global $post, $column, $woocommerce;

			if ( get_post_meta( $post->ID, '_invoice_number', TRUE ) ) {

				$actions['sendpdf'] = array(
					'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=pdfinvoice-admin-send-pdf&order_id=' . $post->ID ), 'pdfinvoice-admin-send-pdf' ),
					'name' 		=> __( 'Send PDF', 'woocommerce-pdf-invoice' ),
					'action' 	=> "icon-sendpdf"
				);
			
			}

			return $actions;

		}
		
		/**
		 * Add Download Invoice icon to actions on orders page in admin
		 */
		public static function download_invoice_icon_admin_init( $actions, $order ) {
			global $post, $column, $woocommerce;

			// Get the PDF invoice settings
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$show_download_link = true;

			if( isset( $settings['create_invoice'] ) && $settings['create_invoice'] == 'manual' && !get_post_meta( $post->ID, '_invoice_number', TRUE ) ) {
				$show_download_link = false;
			}

			if( $show_download_link ) {
				$actions['downloadpdf'] = array(
					'url' 		=> ( $_SERVER['REQUEST_URI'] . '&pdfid=' .$post->ID ),
					'name' 		=> __( 'Download PDF', 'woocommerce-pdf-invoice' ),
					'action' 	=> "icon-downloadpdf"
				);
			}

			return $actions;

		}

		/**
		 * Send a PDF invoice from Admin order list
		 */
		function pdfinvoice_admin_send_pdf() {

			if ( !is_admin() ) die;
			if ( !current_user_can('edit_posts') ) wp_die( __('You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoice') );
			if ( !check_admin_referer('pdfinvoice-admin-send-pdf')) wp_die( __('You have taken too long. Please go back and retry.', 'woocommerce-pdf-invoice') );
			
			$order_id = isset($_GET['order_id']) && (int) $_GET['order_id'] ? (int) $_GET['order_id'] : '';

			if (!$order_id) die;

			$order   = new WC_Order( $order_id );

			// Send the 'Resend Invoice', complete with PDF invoice!
			WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $order_id, $order );

			wp_safe_redirect( wp_get_referer() );
			exit;

		}
		
		/**
		 * Add a PDF link to the My Account orders table
		 */
		 function my_account_pdf( $actions = NULL, $order = NULL ) {
			global $woocommerce;

			// WooCommerce 3.0 compatibility 
			$order_id   = $order->get_id();

			$page_id 	= version_compare( WC_VERSION, '3.0', '<' ) ? woocommerce_get_page_id( 'view_order' ) : wc_get_page_id( 'view_order' );
			 
			if ( get_post_meta( $order_id, '_invoice_number', TRUE ) ) {

				$url = add_query_arg( array(
						'pdfid'    	=> $order_id,
						'key'   	=> $order->get_order_key(),
					), get_permalink( $page_id ) );
			 
			 	$actions['pdf'] = array(
					'url'  => $url,
					'name' => __( apply_filters('woocommerce_pdf_my_account_button_label', __( 'PDF Invoice', 'woocommerce-pdf-invoice' ) ) )
				);
			 
			}
			
			return $actions;
			 
		 }
		 
		 /**
		  * Check URL for pdfaction
		  */
		 function pdf_url_check() {
			 global $woocommerce;
			 
			 if ( isset( $_GET['pdfid'] ) && !is_admin() && isset( $_GET['key'] ) ) {
				
				$orderid = stripslashes( $_GET['pdfid'] );
				$order   = new WC_Order( $orderid );

				// Get the current user
				$current_user = wp_get_current_user();

				// Get Order Key from order object
				$order_key = $order->get_order_key();

				// Get Order Key from URL
				$order_key_url = stripslashes( $_GET['key'] );

				// Get the user id from the order
				$user_id = $order->get_user_id();
			
				// Allow $user_id to be filtered
				$user_id = apply_filters( 'pdf_invoice_download_user_id', $user_id, $current_user, $orderid );

				// Check the current user ID matches the ID of the user who placed the order and the keys match
				if ( $user_id == $current_user->ID && $order_key == $order_key_url ) {

					if( !class_exists('WC_send_pdf') ){
						include( 'class-pdf-send-pdf-class.php' );
					}

					echo WC_send_pdf::get_woocommerce_pdf_invoice( $order, NULL, 'false' );
				}
			 
			}

		 }
		 
		 /**
		  * Download PDF from link
		  * Link contains salted and hashed order_key to prevent the link from being highjacked.
		  * Example download URL : https://yourwebsite.com/?pdfid=100&pdfnonce=qwertyuiopasdfghjklzxcvbnm
		  */
		 function pdf_link_download() {
			 global $woocommerce;
			 
			 if ( isset( $_GET['pdfid'] ) && isset( $_GET['pdfnonce'] ) ) {

			 	if( !class_exists('WC_send_pdf') ){
					include( 'class-pdf-send-pdf-class.php' );
				}
				
				$orderid 	= stripslashes( $_GET['pdfid'] );
				$order   	= new WC_Order( $orderid );

				$pdfnonce 	= strlen( stripslashes( $_GET['pdfnonce'] ) ) === 0 ? false : stripslashes( $_GET['pdfnonce'] );
				$ordernonce = wp_hash( $order->get_order_key(), 'nonce' );
				
				// Check ordernonce and pdfnonce match
				if ( $ordernonce == $pdfnonce ) {
					echo WC_send_pdf::get_woocommerce_pdf_invoice( $order, NULL, 'false' );
				}
			 
			}

		 }

		 /**
		  * Check Admin URL for pdfaction
		  */
		 function admin_pdf_url_check() {
			 global $woocommerce;

			if ( is_admin() && isset( $_GET['pdfid'] ) ) {
				
				$order_id = stripslashes( $_GET['pdfid'] );
				$order    = new WC_Order($order_id);

				echo WC_send_pdf::get_woocommerce_pdf_invoice( $order, NULL, 'false' );
			 
			}

		 }

		/**
		 * [pdf_invoice_email_init description]
		 * @return [type] [description]
		 */
		 function pdf_invoice_email_init() {
			// Add PDF Invoice Email for resending invoice
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_class' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email' ) );
		}

		/**
		 * [pdf_invoice_email_admin_init description]
		 * @return [type] [description]
		 */
		function pdf_invoice_email_admin_init() {
			// Add PDF Invoice Email for resending invoice
			add_filter( 'woocommerce_email_classes', array( $this, 'add_admin_email_class' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_admin_email' ) );
			add_action( 'pdf_invoice_send_emails', array( $this, 'trigger_admin_email_action' ) );
		}

	    /**
	     * add_pdf_invoices_email
	     */
	    function add_email_class( $emails ) {
	    	$emails['PDF_Invoice_Admin_PDF_Invoice'] = include 'class-pdf-email-admin-invoice.php';
	    	return $emails;
	    }

	    /**
	     * add_pdf_invoices_email
	     */
	    function add_admin_email_class( $emails ) {
	    	$emails['PDF_Invoice_Customer_PDF_Invoice'] = include 'class-pdf-email-customer-invoice.php';
	    	return $emails;
	    }

	    /**
	     * [add_email description]
	     * @param [type] $emails [description]
	     */
	    function add_email( $emails ) {
	    	$emails[] = 'woocommerce_admin_pdf_invoice';

	    	return $emails;
	    }

	    /**
	     * [add_email description]
	     * @param [type] $emails [description]
	     */
	    function add_admin_email( $emails ) {
	    	$emails[] = 'woocommerce_customer_pdf_invoice';

	    	return $emails;
	    }

	    /**
	     * [trigger_admin_email_action description]
	     * @param  [type] $order_id [description]
	     * @return [type]           [description]
	     */
	    function trigger_admin_email_action( $order_id ) {

	    	if ( isset( $order_id ) && !empty( $order_id ) ) {
	            WC_Emails::instance();
	            do_action( 'pdf_invoice_resend_invoice', $order_id );
	        }
	    }

		 /**
		  * Add an invoice link to the thank you page
		  */
		 function invoice_link_thanks( $order_id ) {

		 	$settings = get_option( 'woocommerce_pdf_invoice_settings' );

		 	if ( isset($settings['link_thanks']) && $settings['link_thanks'] == 'true' && get_post_meta( $order_id, '_invoice_number_display', TRUE ) ) {

		 		// Invoice number 
		 		$display_invoice_number = get_post_meta( $order_id, '_invoice_number_display', TRUE );

		 		// URL
		 		$download_url = add_query_arg( 'pdfid', $order_id, $_SERVER['REQUEST_URI'] );

		 		// Button or link?
		 		$button_or_link = isset( $settings['thanks_style'] ) ? $settings['thanks_style'] : 'link';

		 		// Output
		 		$output = isset( $settings['pdf_thank_you_text'] ) ? $settings['pdf_thank_you_text'] : 'Download your invoice : [[INVOICENUMBER]]';
		 		
		 		if( $button_or_link === 'button' ) {

		 			$button_output          = str_replace( '[[INVOICENUMBER]]', $display_invoice_number, $output );
                    $button_url             = '<a href="'. $download_url .'" class="pdf_invoice_download_button">' . $button_output . '</a>';
                    $invoice_link_thanks  = sprintf( __('<p class="pdf-download">%s</p>', 'woocommerce-pdf-invoice' ), $button_url );
                } else {
		 			$download_url 		 = '<a href="'. $download_url .'">' . $display_invoice_number . '</a>';
		 			$download_output 	 = str_replace( '[[INVOICENUMBER]]', $download_url, $output );
		 			$invoice_link_thanks = sprintf( __('<p class="pdf-download">%s</p>', 'woocommerce-pdf-invoice' ), $download_output );
		 		}

				echo apply_filters( 'pdf_invoice_invoice_link_thanks', $invoice_link_thanks, $order_id );
				
			}
					 
		 }
		 
		 /**
		  * Send a test PDF from the PDF Debugging settings
		  */
		function pdf_invoice_send_test() {
			 
			 if ( isset( $_POST['pdfemailtest'] ) && $_POST['pdfemailtest'] == '1' ) {

			 	if( !class_exists('WC_send_pdf') ){
					include( 'class-pdf-send-pdf-class.php' );
				}
				
				if ( !isset($_POST['pdf_test_nonce']) || !wp_verify_nonce($_POST['pdf_test_nonce'],'pdf_test_nonce_action') ) {
					die( 'Security check' );
				}

				WC_send_pdf::send_test_pdf();

			}
			 
		}

		static function get_invoice_meta() {
								
			$invoice_meta = array( 
					'_invoice_created',
					'_invoice_date',
					'_invoice_number',
					'_wc_pdf_invoice_number',
					'_invoice_number_display',
					'_pdf_company_name',
					'_pdf_company_details',
					'_pdf_registered_name',
					'_pdf_registered_address',
					'_pdf_company_number',
					'_pdf_tax_number',
					'_pdf_logo_file',
					'_invoice_meta'
			);

			return $invoice_meta;
		}

		static function get_invoice_meta_fields() {
								
			$invoice_meta_fields = array( 
					'invoice_created',
					'invoice_date',
					'invoice_number',
					'wc_pdf_invoice_number',
					'invoice_number_display',
					'pdf_company_name',
					'pdf_company_details',
					'pdf_registered_name',
					'pdf_registered_address',
					'pdf_company_number',
					'pdf_tax_number',
					'pdf_logo_file',
			);

			return $invoice_meta_fields;
		}

		/**
		 * [pdf_invoice_fix_dates description]
		 * @return [type] [description]
		 */
		function pdf_invoice_fix_dates() {

	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_fix_dates', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
			
				if ( isset( $_POST['pdffixdates'] ) && $_POST['pdffixdates'] == '1' && isset( $_POST['pdffix-dates-confirmation'] ) && $_POST['pdffix-dates-confirmation'] === "confirm" ) {
					
					if ( !isset($_POST['pdf_fix_dates_nonce']) || !wp_verify_nonce($_POST['pdf_fix_dates_nonce'],'pdf_fix_dates_nonce_action') ) {
						die( 'Security check' );
					}

					update_option( 'woocommerce_pdf_invoice_fix_date_offset', 0 );

					WC()->queue()->schedule_single( time()+1, 'woocommerce_pdf_invoice_fix_dates' );
	            }

			}
			 
		}

		/**
		 * [pdf_invoice_fix_dates description]
		 * @return [type] [description]
		 */
		public static function action_scheduler_fix_dates() {
			global $wpdb;

			$offset 	= get_option( 'woocommerce_pdf_invoice_fix_date_offset' );
			$settings 	= get_option( 'woocommerce_pdf_invoice_settings' );

			$query =   "SELECT p.ID 
						FROM {$wpdb->prefix}posts AS p 
						INNER JOIN {$wpdb->prefix}postmeta pm on p.id = pm.post_id 
						WHERE p.post_type = 'shop_order' AND pm.meta_key = '_invoice_number'
                      	ORDER BY p.id ASC LIMIT 100 OFFSET " . $offset;

            $results = $wpdb->get_results( $query );
			
			if( isset($results) && !empty( $results ) ) {

				$offset = $offset+100;
				update_option( 'woocommerce_pdf_invoice_fix_date_offset', $offset );

				require_once( 'class-pdf-admin-functions.php' );

				foreach ( $results as $result ) {

					$tolog 		= '';
					$order_id 	= $result->ID;
                	$order 		= new WC_Order( $order_id );

                	$tolog .= ' Order ID :' . $order_id;

					$current_date = get_post_meta( $order_id, '_invoice_date', TRUE );

					$tolog .= ' Current Date : ' . $current_date;

					if ( $settings['pdf_date'] == 'completed' ) {
						$invoice_date = $order->get_date_completed();
					} else {
						$invoice_date = $order->get_date_created();
					}

					$new_date = wc_format_datetime( $invoice_date, $settings['pdf_date_format'] );

					// Update Invoice meta
					update_post_meta( $order_id, '_invoice_date', $new_date );

					$invoice_meta = get_post_meta( $order_id, '_invoice_meta', TRUE );
					$invoice_meta['invoice_date'] = $new_date;
					update_post_meta( $order_id, '_invoice_meta', $invoice_meta );

                }

                WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_fix_dates' );
				WC()->queue()->schedule_single( time()+5, 'woocommerce_pdf_invoice_fix_dates' );
			} else {
				// No orders left to update, cancel the task.
				WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_fix_dates' );
			}
			 
		}

		/**
		 * [pdf_invoice_delete_invoices description]
		 * Delete all PDF Invoice data from WordPress
		 * @return NULL
		 */
		public static function pdf_invoice_delete_invoices() {
	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_delete_invoices', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
				// Delete the invoice meta from the order
				if ( isset( $_POST['pdfdelete'] ) && $_POST['pdfdelete'] == '1' && isset( $_POST['pdfdelete-confirmation'] ) && $_POST['pdfdelete-confirmation'] === "confirm" ) {
					
					if ( !isset($_POST['pdf_delete_nonce']) || !wp_verify_nonce($_POST['pdf_delete_nonce'],'pdf_delete_nonce_action') ) {
						die( 'Security check' );
					}

					$invoice_meta = self::get_invoice_meta();
					foreach( $invoice_meta as $meta ) {
						delete_post_meta_by_key( $meta );
					}

					// Delete other postmeta
					delete_post_meta_by_key( '_invoice_created_mysql' );
					delete_post_meta_by_key( '_wc_pdf_invoice_created_date' );

					// Delete invoice number option
					delete_option( 'woocommerce_pdf_invoice_current_invoice' );

				}

			}
			 
		}

		/**
		 * [pdf_invoice_past_orders description]
		 * Create invoices for orders placed before PDF Invoices was installed.
		 * @return NULL
		 */
		public static function pdf_invoice_past_orders() {

	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_past_orders', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
			
				if ( (isset( $_POST['pdf_past_orders'] ) && $_POST['pdf_past_orders'] == '1' && isset( $_POST['pdf_past_orders-confirmation'] ) && $_POST['pdf_past_orders-confirmation'] === "confirm") ) {
					WC()->queue()->schedule_single( time()+1, 'woocommerce_pdf_invoice_update_past_orders' );
	            }

			}
			 
		}

		/**
		 * [action_scheduler_update_past_orders description]
		 * Setup schedule to create invoices for orders placed before PDF Invoices was installed.
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return [type]        [description]
		 */
		public static function action_scheduler_update_past_orders( $args = NULL, $group = '' ) {
            global $wpdb;

        	$query = "SELECT *
                      FROM {$wpdb->prefix}posts AS p
                      WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND p.id NOT IN (
                            SELECT p.ID FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta pm on p.id = pm.post_id WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND pm.meta_key = '_invoice_number'
                      )
                      ORDER BY p.id ASC";

            $results = $wpdb->get_results( $query );

            if( isset($results) && !empty( $results ) ) {

	        	$query = "SELECT *
	                      FROM {$wpdb->prefix}posts AS p
	                      WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND p.id NOT IN (
	                            SELECT p.ID FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta pm on p.id = pm.post_id WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND pm.meta_key = '_invoice_number'
	                      )
	                      ORDER BY p.id ASC
	                      LIMIT 100";

	            $res = $wpdb->get_results( $query );

	            if( isset( $res ) ) {

	                foreach ( $res as $result ) {

	                    // Make the invoice if we need one.
	                    $order = new WC_Order( $result->ID );

	                    // WooCommerce 3.0 compatibility
	                    $order_status = $order->get_status();
	            
	                    if ( sanitize_title( $order_status ) == 'completed' && get_post_meta($result->ID, '_invoice_number', TRUE) == '' ) {
	                        WC_pdf_functions::woocommerce_completed_order_create_invoice( $result->ID );
	                    }

	                }

	                WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_update_past_orders' );
					WC()->queue()->schedule_single( time()+5, 'woocommerce_pdf_invoice_update_past_orders' );

				}

            } else {
            	WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_update_past_orders' );
            }

		}

		/**
		 * [pdf_invoice_past_orders description]
		 * Create invoices for orders placed before PDF Invoices was installed.
		 * @return NULL
		 */
		function pdf_invoice_past_orders_email() {

	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_past_orders', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
			
				if ( (isset( $_POST['pdf_past_orders_email'] ) && $_POST['pdf_past_orders_email'] == '1' && isset( $_POST['pdf_past_orders_email-confirmation'] ) && $_POST['pdf_past_orders_email-confirmation'] === "confirm") ) {
					WC()->queue()->schedule_single( time()+5, 'woocommerce_pdf_invoice_update_email_past_orders' );
	            }

			}
			 
		}

		/**
		 * [action_scheduler_update_past_orders description]
		 * Setup schedule to create invoices for orders placed before PDF Invoices was installed.
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return [type]        [description]
		 */
		public static function action_scheduler_update_email_past_orders( $args = NULL, $group = '' ) {
            global $wpdb, $woocommerce;

        	$query = "SELECT *
                      FROM {$wpdb->prefix}posts AS p
                      WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND p.id NOT IN (
                            SELECT p.ID FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta pm on p.id = pm.post_id WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed' AND pm.meta_key = '_invoice_number'
                      )
                      ORDER BY p.id ASC
                      LIMIT 50";

            $results = $wpdb->get_results( $query );
			
			if( isset($results) && !empty( $results ) ) {

                foreach ( $results as $result ) {

                    // Make the invoice if we need one.
                    $order 		= new WC_Order( $result->ID );
                    $order_id 	= $result->ID;

                    $order_status = $order->get_status();
            
                    if ( sanitize_title( $order_status ) == 'completed' && get_post_meta( $order_id, '_invoice_number', TRUE ) == '' ) {
                        WC_pdf_functions::woocommerce_completed_order_create_invoice( $order_id );

						WC()->mailer()->emails['PDF_Invoice_Customer_PDF_Invoice']->trigger( $order_id, $order );
                    }

                    // Pause for 5 seconds before going again.
                    sleep(5);

                }

                WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_update_email_past_orders' );
				WC()->queue()->schedule_single( time()+5, 'woocommerce_pdf_invoice_update_email_past_orders' );

            } else {
            	WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_update_email_past_orders' );
            }

		}

		/**
		 * [pdf_invoice_delete_files description]
		 * Setup schedule to delete temporary files.
		 * @return NULL
		 * 
		 * as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
		 */
		function pdf_invoice_delete_files() {

			$next = WC()->queue()->get_next( 'woocommerce_pdf_invoice_delete_files' );
			if ( ! $next ) {
				WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_delete_files' );
				WC()->queue()->schedule_single( time()+3600, 'woocommerce_pdf_invoice_delete_files' );
			}
			 
		}

		/**
		 * [action_scheduler_delete_files description]
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return NULL
		 */
		public static function action_scheduler_delete_files( $args = NULL, $group = '' ) {

	        $upload_dir =  wp_upload_dir();
	        $upload_dir =  $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
	        
	        // Filter to allow changing the location for PDF storeage
	        $upload_dir =  apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

	        // Set this to false to prevent PDFs from being deleted.
	        $delete_pdf = apply_filters( 'woocommerce_pdf_invoice_empty_temp_folder_filter', TRUE );

	        if ( file_exists( $upload_dir . '/index.html' ) && $delete_pdf === TRUE ) {

	            // Delete PDFs
	            $pdfs = glob( $upload_dir . '/*.pdf' ); // get all file names
	            foreach( $pdfs as $pdf ) {
	                if( is_file($pdf) ) {
	                    unlink( $pdf ); // delete file
	                }
	            }

	            // Delete Zips
	            $zips = glob( $upload_dir . '/*.zip' ); // get all file names
	            foreach( $zips as $zip ) {
	                if( is_file($zip) ) {
	                    unlink( $zip ); // delete file
	                }
	            }

	        }

		}

		/**
		 * [pdf_invoice_delete_logs description]
		 * Setup schedule to delete temporary files.
		 * @return NULL
		 * 
		 * as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
		 */
		function pdf_invoice_delete_logs() {

			$next = WC()->queue()->get_next( 'woocommerce_pdf_invoice_delete_logs' );
			if ( ! $next ) {
				WC()->queue()->cancel_all( 'woocommerce_pdf_invoice_delete_logs' );
				WC()->queue()->schedule_single( time()+86400, 'woocommerce_pdf_invoice_delete_logs' );
			}
			 
		}

		/**
		 * [action_scheduler_delete_logs description]
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return NULL
		 */
		public static function action_scheduler_delete_logs( $args = NULL, $group = '' ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( 
								"DELETE FROM {$wpdb->prefix}actionscheduler_actions 
							   	WHERE hook = '%s' 
							   	AND status ='%s'", 
							   	'woocommerce_pdf_invoice_delete_files', 'complete' 
							) 
						);

			$wpdb->query( $wpdb->prepare( 
								"DELETE FROM {$wpdb->prefix}actionscheduler_actions 
							   	WHERE hook = '%s' 
							   	AND status ='%s'", 
							   	'woocommerce_pdf_invoice_update_order_meta_invoice_date', 'complete' 
							) 
						);

			$wpdb->query( $wpdb->prepare( 
								"DELETE FROM {$wpdb->prefix}actionscheduler_actions 
							   	WHERE hook = '%s' 
							   	AND status ='%s'", 
							   	'woocommerce_pdf_invoice_update_email_past_orders', 'complete' 
							) 
						);

			$wpdb->query( $wpdb->prepare( 
								"DELETE FROM {$wpdb->prefix}actionscheduler_actions 
							   	WHERE hook = '%s' 
							   	AND status ='%s'", 
							   	'woocommerce_pdf_invoice_upgrade_order_meta_invoice_creation_date', 'complete' 
							) 
						);

			
		
		}

		/**
		 * subscriptions_remove_renewal_order_meta description Subs 1.5
		 * @param  [type] $order_meta_query  [description]
		 * @param  [type] $original_order_id [description]
		 * @param  [type] $renewal_order_id  [description]
		 * @param  [type] $new_order_role    [description]
		 * @return [type]                    [description]
		 *
		 * Remove the Invoice meta keys from the list when creating a renewal order
		 * This information will be added when the invoice is created
		 */
		function subscriptions_remove_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {
			$order_meta_query .= " AND meta_key NOT IN ( " . implode( "','", $this->get_invoice_meta() ) . " )";
			return $order_meta_query;
		}

		/**
		 * Remove invoice meta when creating a subscription object from an order at checkout.
		 * Subscriptions aren't true orders so they shouldn't have an invoice
		 *
		 * @return array
		 */
		function subscriptions_remove_subscription_order_meta( $order_meta, $to_order, $from_order ) {

			// only when copying from an order to a subscription
			if ( $to_order instanceof WC_Subscription && $from_order instanceof WC_Order ) {

				foreach ( $order_meta as $index => $meta ) {

					if ( in_array( $meta['meta_key'], $this->get_invoice_meta() ) ) {
						unset( $order_meta[ $index ] );
					}

				}
			}

			return $order_meta;
		}

		/**
		 * subscriptions_remove_renewal_order_meta_2 description Subs 2.0
		 * @param  [type] $order_meta
		 *
		 * Remove the Invoice meta keys from the list when creating a renewal order
		 * This information will be added when the invoice is created
		 */
		function subscriptions_remove_renewal_order_meta_2( $order_meta ) {

			foreach ( $order_meta as $index => $meta ) {

				if ( in_array( $meta['meta_key'], $this->get_invoice_meta() ) ) {
					unset( $order_meta[ $index ] );
				}

			}

			return $order_meta;
		}

		/**
		 * [subscriptions_remove_renewal_order_meta_3 description]
		 * @param  [type] $order_meta_query [description]
		 * @param  [type] $to_order         [description]
		 * @param  [type] $from_order       [description]
		 * @return [type]                   [description]
		 */
		function subscriptions_remove_renewal_order_meta_3( $order_meta_query, $to_order, $from_order ) {

			$order_meta_query .= " AND meta_key NOT IN ( '" . implode( "','", $this->get_invoice_meta() ) . "' )";
			return $order_meta_query;

		}

		// Clean Invoice Meta - replace pdf_registered_office with pdf_registered_address
		static function clean_invoice_meta( $invoice_meta ) {

			if( is_array( $invoice_meta ) ) {

				$keys = array(
						'pdf_company_information' => 'pdf_company_details',
						'pdf_registered_office' => 'pdf_registered_address'
					);

				foreach( $keys as $old => $new ) {

					$invoice_meta = WC_pdf_functions::change_invoice_meta( $invoice_meta, $old, $new );
					$invoice_meta = WC_pdf_functions::change_invoice_meta( $invoice_meta, '_'.$old, '_'.$new );

				}

			}

			return $invoice_meta;
		}

		/**
		 * [change_invoice_meta description]
		 * @param  [type] $array   [description]
		 * @param  [type] $old_key [description]
		 * @param  [type] $new_key [description]
		 * @return [type]          [description]
		 */
		static function change_invoice_meta( $array, $old_key, $new_key ) {

		    if( ! array_key_exists( $old_key, $array ) )
		        return $array;

		    $keys = array_keys( $array );
		    $keys[ array_search( $old_key, $keys ) ] = $new_key;

		    return array_combine( $keys, $array );

		}

		/**
		 * [pdf_invoice_update_order_meta_invoice_date description]
		 * @return NULL
		 */
		public static function pdf_invoice_update_order_meta_invoice_date() {
			global $wpdb;

			$query = "SELECT COUNT(post_id) FROM {$wpdb->prefix}postmeta 
						WHERE post_id IN (
							SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_invoice_number'
						) AND post_id NOT IN (
							SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_invoice_created'
						)
						GROUP BY (post_id)
					 ";

            $results = $wpdb->get_var( $query );

			if ( $results && $results > 0 ) {
				WC()->queue()->add( 'woocommerce_pdf_invoice_update_order_meta_invoice_date', array(), 'pdf_invoice' );
	        }
			 
		}

		/**
		 * [action_scheduler_update_past_orders description]
		 * Setup schedule to create invoices for orders placed before PDF Invoices was installed.
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return [type]        [description]
		 */
		public static function action_scheduler_update_order_meta_invoice_date( $args = NULL, $group = '' ) {
            global $wpdb;

            $query = "SELECT post_id FROM {$wpdb->prefix}postmeta 
						WHERE post_id IN (
							SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_invoice_number'
						) AND post_id NOT IN (
							SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_invoice_created'
						)
						GROUP BY (post_id)
						ORDER BY post_id ASC
					 ";

            $results = $wpdb->get_results( $query );

            if( $results ) {

            	foreach( $results AS $result ) {

            		$invoice_meta = get_post_meta( $result->post_id, '_invoice_meta', TRUE );

            		if( is_array( $invoice_meta ) ) {
            			update_post_meta( $result->post_id, '_invoice_created', $invoice_meta['invoice_created'] );
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
        public static function debuger( $tolog = NULL, $id = NULL, $message = NULL, $start = FALSE ) {

        	if( !class_exists('WC_Logger') ) {
        		return;
        	}

            if( !isset( $logger ) ) {
                $logger      = new stdClass();
                $logger->log = new WC_Logger();
            }

            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );
            $logger->log->add( $id, print_r( $tolog, TRUE ) );
            $logger->log->add( $id, __('=============================================', 'woocommerce-pdf-invoice') );

        }

        /**
         * { item_description }
         */
        public static function maybe_send_admin_email_on_creation( $order_id ) {
        	
        	if( isset( $order_id) && !is_null( $order_id ) ) {

        		// Test PDF_Invoice_Admin_PDF_Invoice is loaded
        		$mailer = WC()->mailer();
				$emails = $mailer->get_emails();

				$email_classes = array_keys($emails);

				if( in_array( 'PDF_Invoice_Admin_PDF_Invoice', $email_classes ) ) {
					$order = wc_get_order( $order_id );
        			WC()->mailer()->emails['PDF_Invoice_Admin_PDF_Invoice']->trigger( $order_id, $order );
				}
       		
        	}

        }

        public static function get_option( $option ) {

            $settings 	= get_option('woocommerce_pdf_invoice_settings');
            $defaults 	= WooCommerce_PDF_Invoice_Defaults::$defaults;

            $option 	= isset( $settings[$option] ) ? $settings[$option] : $defaults[$option];

            return $option;

        }

	} // EOF WC_pdf_functions

	$GLOBALS['WC_pdf_functions'] = new WC_pdf_functions();

	function invoice_column_admin_init( $columns ) {
		global $woocommerce;
			
		$columns = 	array_slice( $columns, 0, 2, true ) +
					array( "pdf_invoice_num" => "Invoice" ) +
					array_slice($columns, 2, count($columns) - 1, true) ;
			
		return $columns;

	}