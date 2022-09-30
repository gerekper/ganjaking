<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

    class WC_pdf_database {

        public function __construct() {

        	global $woocommerce, $wpdb;

        	// Get PDF Invoice Options
        	$settings = get_option('woocommerce_pdf_invoice_settings');

        }

        public static function create_invoice( $order_id ) {

        	$table_name 	 = $wpdb->prefix . "co_wc_pdf_invoices";
        	$invoice_created = current_time('mysql');

        	$sql = "insert into $table_name (
					order_id,
					invoice_created
				) VALUES (
					$order_id,
					$invoice_created
				);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

        }

        public static function update_invoice( $order_id ) {

        	// Get the invoice options
			$settings 		 = get_option( 'woocommerce_pdf_invoice_settings' );

			// Get WC_pdf_functions 
			include_once( 'class-pdf-functions-class.php' );

        	$table_name 	 = $wpdb->prefix . "co_wc_pdf_invoices";

			$invoice_date 				= WC_pdf_functions::set_invoice_date( $order_id );
			$invoice_number_display 	= WC_pdf_functions::create_display_invoice_number( $order_id );
			$pdf_company_name 			= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_name' );
			$pdf_company_details 		= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_details' );
			$pdf_registered_name 		= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_name' );
			$pdf_registered_address 	= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_registered_address' );
			$pdf_company_number 		= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_company_number' );
			$pdf_tax_number 			= WC_pdf_functions::get_pdf_company_field( $order_id, 'pdf_tax_number' );
			$wc_pdf_invoice_number		= WC_pdf_functions::set_invoice_number( $order_id );
			$pdf_logo_file 				= isset( $settings['logo_file'] ) ? $settings['logo_file'] : '';


        	$sql = "UPDATE $table_name SET
					invoice_date = $invoice_date,
					invoice_number_display = $invoice_number_display,
					pdf_company_name = $pdf_company_name,
					pdf_company_details = $pdf_company_details,
					pdf_registered_name = $pdf_registered_name,
					pdf_registered_address = $pdf_registered_address,
					pdf_company_number = $pdf_company_number,
					pdf_tax_number = $pdf_tax_number,
					wc_pdf_invoice_number = $wc_pdf_invoice_number,
					pdf_logo_file = $pdf_logo_file
					WHERE order_id = $order_id;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

        }

        public static function create_table() {

			$table_name = $wpdb->prefix . "co_wc_pdf_invoices";

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			  order_id int NOT NULL,
			  invoice_created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  invoice_date varchar(255) DEFAULT '' NOT NULL,
			  invoice_number int(9) NOT NULL AUTO_INCREMENT,
			  invoice_number_display varchar(255) DEFAULT '' NOT NULL,
			  pdf_company_name varchar(255) DEFAULT '' NOT NULL,
			  pdf_company_details text DEFAULT '' NOT NULL,
			  pdf_registered_name varchar(255) DEFAULT '' NOT NULL,
			  pdf_registered_address text DEFAULT '' NOT NULL,
			  pdf_company_number varchar(255) DEFAULT '' NOT NULL,
			  pdf_tax_number varchar(255) DEFAULT '' NOT NULL,
			  wc_pdf_invoice_number int(9) NOT NULL AUTO_INCREMENT,
			  pdf_logo_file varchar(255) DEFAULT '' NOT NULL,
			  PRIMARY KEY  (invoice_number)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

        }

        public static function insert_record( $data ) {

        	$table_name = $wpdb->prefix . "co_wc_pdf_invoices";

        	$sql = "insert into $table_name (
					order_id,
					invoice_created,
					invoice_date,
					invoice_number,
					invoice_number_display,
					pdf_company_name,
					pdf_company_details,
					pdf_registered_name,
					pdf_registered_address,
					pdf_company_number,
					pdf_tax_number,
					wc_pdf_invoice_number,
					pdf_logo_file
				) VALUES (
					$data
				);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

        }

        public static function initial_data_insert() {

        	


        }



    }

    $GLOBALS['WC_pdf_database'] = new WC_pdf_database();