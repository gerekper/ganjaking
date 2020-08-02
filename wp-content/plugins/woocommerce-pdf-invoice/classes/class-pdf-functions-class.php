<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WC_pdf_functions {

	    public function __construct() {
			
			global $wpdb,$woocommerce;

			// Add woocommerce-pdf_admin-css.css to admin
			add_action( 'admin_enqueue_scripts', array( $this, 'woocommerce_pdf_admin_css' ) );

	    	// Stop everything if iconv or mbstring are not loaded, prevents fatal errors
	    	if ( extension_loaded('iconv') && extension_loaded('mbstring') ) {					
				
				// Add PDF Invoice Email
				add_action( 'admin_init', array( $this, 'pdf_invoice_email_admin_init' ) );
				
				// Create Invoice actions
				add_action( 'init', array( $this,'pdf_invoice_order_status_array' ) );

				add_action( 'admin_init', array( $this,'pdf_invoice_order_status_array' ) );

				// Create Invoice for manual subscriptions
				add_filter( 'wcs_new_order_created', array( $this, 'create_pdf_invoice_for_manual_subscriptions'), 10, 3 );
				add_filter( 'wcs_renewal_order_created', array( $this, 'create_pdf_invoice_for_manual_subscriptions' ), 10,2 );

				// Add Send Invoice icon to actions on orders page in admin
				add_filter( 'woocommerce_admin_order_actions', array( $this,'send_invoice_icon_admin_init' ) ,10 , 2 );
				
				// Add Download Invoice icon to actions on orders page in admin
				add_filter( 'woocommerce_admin_order_actions', array( $this,'download_invoice_icon_admin_init' ) ,11 , 2 );

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

				add_filter( 'wcs_renewal_order_meta_query', array( $this, 'subscriptions_remove_renewal_order_meta_3' ), 10, 3 );

			}

		}

		public static function pdf_invoice_order_status_array() {

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$create_invoice 	= isset( $settings['create_invoice'] ) ? $settings['create_invoice'] : NULL;
			
			$order_status_array = WC_pdf_functions::get_order_status_array( $create_invoice );

			foreach( $order_status_array as $order_status ) {
				add_action( 'woocommerce_order_status_' . $order_status, array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
				add_action( 'woocommerce_order_status_pending_to_' . $order_status . '_notification', array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
			}

			// Completed Renewal Order
			add_action( 'woocommerce_order_status_completed_renewal_notification', array( 'WC_pdf_functions','woocommerce_completed_order_create_invoice' ) );
			
			
		}

		public static function create_pdf_invoice_for_manual_subscriptions( $renewal_order, $subscription, $type = NULL ) {

			// Get the invoice options
			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

			$order_status_array = WC_pdf_functions::get_order_status_array( $settings['create_invoice'] );

			if( $subscription->is_manual() && in_array( $renewal_order->get_status(), $order_status_array ) ) {
		    	WC_pdf_functions::woocommerce_completed_order_create_invoice( $renewal_order->get_id() );
		    }

		    return $renewal_order;

		}

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

			if( !class_exists('WC_send_pdf') ){
				include( 'class-pdf-send-pdf-class.php' );
			}

			$order = new WC_Order( $order_id );

			$no_pdf = true;
			if( apply_filters( 'pdf_invoice_no_pdf', $no_pdf, $order_id ) === true ) {

				// Invoice Number
				WC_pdf_functions::set_invoice_number( $order_id );
				
				// Invoice Date
				WC_pdf_functions::set_invoice_date( $order_id );

				// Set Invoice Meta
				WC_pdf_functions::set_invoice_meta( $order_id );

				// Return Attachments
				return add_filter( 'woocommerce_email_attachments' , array( 'WC_send_pdf' ,'pdf_attachment' ), 10, 3 );

			}

			return NULL;

		} // woocommerce_completed_order_create_invoice

		/**
		 * [get_next_invoice_number description]
		 * @param [type] $order_id [description]
		 */
		public static function get_next_invoice_number() {
			global $wpdb,$woocommerce;

			// Get the invoice options
			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

			$annual_restart = FALSE;
			if ( isset($woocommerce_pdf_invoice_options['annual_restart']) && $woocommerce_pdf_invoice_options['annual_restart'] == 'TRUE' ) {
				$annual_restart = TRUE;
			}

			// Make sure the variable are cleared
			$invoice 		 = NULL;
			$current_invoice = NULL;
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
											    WHERE meta_key = '_invoice_created'
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
				if ( $woocommerce_pdf_invoice_options['start_number'] && !$annual_restart ) {
					$next_invoice = $woocommerce_pdf_invoice_options['start_number'];
				}

				// If there is a $start_number and $annual_restart is set and there are no previous invoices.
				if ( $woocommerce_pdf_invoice_options['start_number'] && $annual_restart && $invoice_count == 0 ) {
					$next_invoice = $woocommerce_pdf_invoice_options['start_number'];
				}

			} else {
				$next_invoice = $current_invoice + 1;
			}

			// Check the pdf_next_number settings in case 
			if( isset( $woocommerce_pdf_invoice_options['pdf_next_number'] ) && $woocommerce_pdf_invoice_options['pdf_next_number'] > $next_invoice && !$annual_restart ) {
				$next_invoice =  $woocommerce_pdf_invoice_options['pdf_next_number'];
			}

			return $next_invoice;

		}

		/**
		 * [set_invoice_number description]
		 * @param [type] $order_id [description]
		 */
		public static function set_invoice_number( $order_id ) {

			// Invoice number exists, just get it and leave.
			if( get_post_meta( $order_id, '_invoice_number', TRUE ) ) {
				return get_post_meta( $order_id, '_invoice_number', TRUE );
			}

			// Get the invoice options
			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

			if ( !isset( $woocommerce_pdf_invoice_options['sequential'] ) || $woocommerce_pdf_invoice_options['sequential'] != 'true' ) {
				// Sequential order numbering is not needed, just use the order_id.
				$next_invoice = $order_id;
			} else {
				$next_invoice = WC_pdf_functions::get_next_invoice_number();
			}

			// Set an option for the current invoice and year to avoid querying the DB everytime
			update_option( 'woocommerce_pdf_invoice_current_year', date('Y') );
			
			// Allow the invoice number to be modified if needed
			$next_invoice = apply_filters( 'woocommerce_pdf_invoices_set_invoice_number', $next_invoice, $order_id );

			// set the invoice number for the order
			update_post_meta( $order_id, '_invoice_number', $next_invoice );

			// Udate PDF Invoice options 'pdf_next_number'
			$woocommerce_pdf_invoice_options['pdf_next_number'] = $next_invoice;
			update_option( 'woocommerce_pdf_invoice_settings', $woocommerce_pdf_invoice_options );
			
			return $next_invoice;
		
		}

		public static function set_invoice_date( $order_id ) {
			global $woocommerce;
			
			require_once( 'class-pdf-admin-functions.php' );

			$order = new WC_Order( $order_id );

			if ( get_post_meta($order_id, '_invoice_date', TRUE) && get_post_meta($order_id, '_invoice_date', TRUE) != '' ) {

				return WC_pdf_admin_functions::format_pdf_date( get_post_meta( $order_id, '_invoice_date', TRUE ) );

			} else {

				// Get the invoice options
				$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

				if ( $woocommerce_pdf_invoice_options['pdf_date'] == 'completed' ) {
					$invoice_date = current_time('mysql');
				} else {
					$invoice_date = $order->get_date_created();
					$invoice_date = wc_format_datetime( $invoice_date );
				}

				return WC_pdf_admin_functions::format_pdf_date( $invoice_date );

				// update_post_meta( $order_id, '_invoice_date', $invoice_date );

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

			// Get the invoice options
			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

			// Backup Invoice Meta
			$invoice_meta = array( 
					'invoice_created' 			=> current_time('mysql'),
					'invoice_date' 				=> WC_pdf_functions::set_invoice_date( $order_id ),
					'invoice_number' 			=> WC_pdf_functions::set_invoice_number( $order_id ),
					'invoice_number_display' 	=> WC_pdf_functions::create_display_invoice_number( $order_id ),
					'pdf_company_name' 			=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_name' ),
					'pdf_company_details' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_details' ),
					'pdf_registered_name' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_name' ),
					'pdf_registered_address' 	=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_address' ),
					'pdf_company_number' 		=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_number' ),
					'pdf_tax_number' 			=> WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_tax_number' )
			);

			update_post_meta( $order_id, '_invoice_meta', $invoice_meta );

			// Create separate order_meta
			foreach( $invoice_meta as $meta_key => $meta_value ) {
				update_post_meta( $order_id, '_'.$meta_key, $meta_value );
			}

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

			$invoice_number = get_post_meta( $order_id, '_invoice_number', TRUE );

			// pattern substitution
			$replacements = array(
				'{{D}}'    			=> date_i18n( 'j' ),
				'{{DD}}'   			=> date_i18n( 'd' ),
				'{{M}}'    			=> date_i18n( 'n' ),
				'{{MM}}'   			=> date_i18n( 'm' ),
				'{{YY}}'   			=> date_i18n( 'y' ),
				'{{yy}}'   			=> date_i18n( 'y' ),
				'{{YYYY}}' 			=> date_i18n( 'Y' ),
				'{{H}}'    			=> date_i18n( 'G' ),
				'{{HH}}'   			=> date_i18n( 'H' ),
				'{{N}}'    			=> date_i18n( 'i' ),
				'{{S}}'    			=> date_i18n( 's' ),
				'{{year}}' 			=> date_i18n( 'Y' ),
				'{{YEAR}}' 			=> date_i18n( 'Y' ),
				'{{invoicedate}}' 	=> WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', false, 'invoice', $settings['pdf_date_format'] ),
				'{{INVOICEDATE}}' 	=> WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', false, 'invoice', $settings['pdf_date_format'] ),
				'{D}'    			=> date_i18n( 'j' ),
				'{DD}'   			=> date_i18n( 'd' ),
				'{M}'    			=> date_i18n( 'n' ),
				'{MM}'   			=> date_i18n( 'm' ),
				'{YY}'   			=> date_i18n( 'y' ),
				'{yy}'   			=> date_i18n( 'y' ),
				'{YYYY}' 			=> date_i18n( 'Y' ),
				'{H}'    			=> date_i18n( 'G' ),
				'{HH}'   			=> date_i18n( 'H' ),
				'{N}'    			=> date_i18n( 'i' ),
				'{S}'    			=> date_i18n( 's' ),
				'{year}' 			=> date_i18n( 'Y' ),
				'{YEAR}' 			=> date_i18n( 'Y' ),
				'{invoicedate}' 	=> WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', false, 'invoice', $settings['pdf_date_format'] ),
				'{INVOICEDATE}' 	=> WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', false, 'invoice', $settings['pdf_date_format'] ),
			);
			
			$invoice_prefix = esc_html( $settings['pdf_prefix'] );
			$invoice_prefix = str_replace( array_keys( $replacements ), $replacements, $invoice_prefix );

			$invoice_suffix = esc_html( $settings['pdf_sufix'] );
			$invoice_suffix = str_replace( array_keys( $replacements ), $replacements, $invoice_suffix );
				
			// Add number padding if necessary
			if ( '' != $settings['padding'] ) {
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
			$woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

			$return = isset( $woocommerce_pdf_invoice_options[$field] ) ? $woocommerce_pdf_invoice_options[$field] : '';

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

		/**
		 * Add Send Invoice icon to actions on orders page in admin
		 */
		function send_invoice_icon_admin_init( $actions, $order ) {
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
		function download_invoice_icon_admin_init( $actions, $order ) {
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
			 
			 	$actions['pdf'] = array(
					'url'  => add_query_arg( 'pdfid', $order_id, get_permalink( $page_id ) ),
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
			 
			 if ( isset( $_GET['pdfid'] ) && !is_admin() ) {

			 	if( !class_exists('WC_send_pdf') ){
					include( 'class-pdf-send-pdf-class.php' );
				}
				
				$orderid = stripslashes( $_GET['pdfid'] );
				$order   = new WC_Order( $orderid );

				// Get the current user
				$current_user = wp_get_current_user();

				// Get the user id from the order
				$user_id = is_callable( array( $order, 'get_user_id' ) ) ? $order->get_user_id() : $order->user_id;
			
				// Allow $user_id to be filtered
				$user_id = apply_filters( 'pdf_invoice_download_user_id', $user_id, $current_user, $orderid );
				
				// Check the current user ID matches the ID of the user who placed the order
				if ( $user_id == $current_user->ID ) {
					echo WC_send_pdf::get_woocommerce_invoice( $order , 'false' );
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

				$pdfnonce 	= stripslashes( $_GET['pdfnonce'] );
				$ordernonce = wp_hash( $order->get_order_key(), 'nonce' );
				
				// Check ordernonce and pdfnonce match
				if ( $ordernonce == $pdfnonce ) {
					echo WC_send_pdf::get_woocommerce_invoice( $order , 'false' );
				}
			 
			}

		 }

		 /**
		  * Check Admin URL for pdfaction
		  */
		 function admin_pdf_url_check() {
			 global $woocommerce;
			 
			 if ( is_admin() && isset( $_GET['pdfid'] ) ) {
				
				$orderid = stripslashes( $_GET['pdfid'] );
				$order   = new WC_Order($orderid);

				echo WC_send_pdf::get_woocommerce_invoice( $order , 'false' );
			 
			}

		 }

		function pdf_invoice_email_admin_init() {
			// Add PDF Invoice Email for resending invoice
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_class' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email' ) );
			add_action( 'pdf_invoice_send_emails', array( $this, 'trigger_email_action' ) );
		}

	    /**
	     * add_pdf_invoices_email
	     */
	    function add_email_class( $emails ) {
	    	$emails['PDF_Invoice_Customer_PDF_Invoice'] = include 'class-pdf-email-customer-invoice.php';
	    	return $emails;
	    }

	    function add_email( $emails ) {
	    	$emails[] = 'woocommerce_customer_pdf_invoice';

	    	return $emails;
	    }

	    function trigger_email_action( $order_id ) {

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
				
				$invoice_link_thanks  = __('<p class="pdf-download">Download your invoice : ', 'woocommerce-pdf-invoice' );
				$invoice_link_thanks .= '<a href="'. add_query_arg( 'pdfid', $order_id ) .'">' . get_post_meta( $order_id, '_invoice_number_display', TRUE ) .'</a>';
				$invoice_link_thanks .= __('</p>', 'woocommerce-pdf-invoice');

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
					'_invoice_number_display',
					'_pdf_company_name',
					'_pdf_company_details',
					'_pdf_registered_name',
					'_pdf_registered_address',
					'_pdf_company_number',
					'_pdf_tax_number',
					'_invoice_meta'
			);

			return $invoice_meta;
		}

		static function get_invoice_meta_fields() {
								
			$invoice_meta_fields = array( 
					'invoice_created',
					'invoice_date',
					'invoice_number',
					'invoice_number_display',
					'pdf_company_name',
					'pdf_company_details',
					'pdf_registered_name',
					'pdf_registered_address',
					'pdf_company_number',
					'pdf_tax_number'
			);

			return $invoice_meta_fields;
		}

		/**
		 * [pdf_invoice_delete_invoices description]
		 * Delete all PDF Invoice data from WordPress
		 * @return NULL
		 */
		function pdf_invoice_delete_invoices() {
	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_delete_invoices', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
				// Delete the invoice meta from the order
				if ( isset( $_POST['pdfdelete'] ) && $_POST['pdfdelete'] == '1' && isset( $_POST['pdfdelete-confirmation'] ) && $_POST['pdfdelete-confirmation'] === "confirm" ) {
					
					if ( !isset($_POST['pdf_delete_nonce']) || !wp_verify_nonce($_POST['pdf_delete_nonce'],'pdf_delete_nonce_action') ) {
						die( 'Security check' );
					}

					$invoice_meta = $this->get_invoice_meta();
					foreach( $invoice_meta as $meta ) {
						delete_post_meta_by_key( $meta );
					}

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
		function pdf_invoice_past_orders() {

	        $allowed_user_role 	= apply_filters( 'pdf_invoice_allowed_user_role_pdf_invoice_past_orders', 'administrator' );
			$current_user 		= wp_get_current_user();

			if( in_array( $allowed_user_role, $current_user->roles ) ) {
			
				if ( (isset( $_POST['pdf_past_orders'] ) && $_POST['pdf_past_orders'] == '1' && isset( $_POST['pdf_past_orders-confirmation'] ) && $_POST['pdf_past_orders-confirmation'] === "confirm") ) {
					WC()->queue()->add( 'woocommerce_pdf_invoice_update_past_orders', array(), 'pdf_invoice' );
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
			
			if( isset($results) ) {
                foreach ( $results as $result ) {

                    // Make the invoice if we need one.
                    $order = new WC_Order( $result->ID );

                    // WooCommerce 3.0 compatibility
                    $order_status = $order->get_status();
            
                    if ( sanitize_title( $order_status ) == 'completed' && get_post_meta($result->ID, '_invoice_number', TRUE) == '' ) {
                        WC_pdf_functions::woocommerce_completed_order_create_invoice( $result->ID );
                    }

                }

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
					WC()->queue()->add( 'woocommerce_pdf_invoice_update_email_past_orders', array(), 'pdf_invoice' );
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
                      ORDER BY p.id ASC";

            $results = $wpdb->get_results( $query );
			
			if( isset($results) ) {
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

	} // EOF WC_pdf_functions

	$GLOBALS['WC_pdf_functions'] = new WC_pdf_functions();

	function invoice_column_admin_init( $columns ) {
		global $woocommerce;
			
		$columns = 	array_slice( $columns, 0, 2, true ) +
					array( "pdf_invoice_num" => "Invoice" ) +
					array_slice($columns, 2, count($columns) - 1, true) ;
			
		return $columns;

	}