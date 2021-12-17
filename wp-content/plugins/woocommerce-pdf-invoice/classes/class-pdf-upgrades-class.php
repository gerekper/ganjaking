<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class WC_pdf_upgrades {

	    public function __construct() {

	    	// Use Action Scheduler to update Order Meta with pdf_creation
			add_action( 'woocommerce_pdf_invoice_upgrade_order_meta_invoice_creation_date', array( __CLASS__, 'action_scheduler_upgrade_order_meta_invoice_creation_date' ), 10, 2 );

		}

		/**
		 * [pdf_invoice_upgrade_order_meta_invoice_creation_date description]
		 * @return NULL
		 */
		public static function pdf_invoice_upgrade_order_meta_invoice_creation_date() {
			global $wpdb;

			$query = "SELECT COUNT(post_id) FROM {$wpdb->prefix}postmeta 
						WHERE meta_key = '_invoice_number'
					 ";

            $results = $wpdb->get_var( $query );

            if ( $results && $results > 0 ) {
				WC()->queue()->schedule_single( time()+30, 'woocommerce_pdf_invoice_upgrade_order_meta_invoice_creation_date' );
	        }
			 
		}

		/**
		 * [action_scheduler_update_past_orders description]
		 * Setup schedule to create invoices for orders placed before PDF Invoices was installed.
		 * @param  [type] $args  [description]
		 * @param  string $group [description]
		 * @return [type]        [description]
		 */
		public static function action_scheduler_upgrade_order_meta_invoice_creation_date( $args = NULL, $group = '' ) {
            global $wpdb;

            $query = "SELECT post_id FROM {$wpdb->prefix}postmeta 
						WHERE meta_key = '_invoice_number'
						ORDER BY post_id ASC
					 ";

            $results = $wpdb->get_results( $query );

            if( $results ) {

            	foreach( $results AS $result ) {

            		$invoice_created = get_post_meta( $result->post_id, '_invoice_created', TRUE );

            		if( is_object( $invoice_created ) && isset( $invoice_created->date ) ) {
            			add_post_meta( $result->post_id, '_invoice_created_mysql', $invoice_created->date, TRUE );
            		}

            	}

            }

		}
		
	} // EOF WC_pdf_upgrades

	$GLOBALS['WC_pdf_upgrades'] = new WC_pdf_upgrades();