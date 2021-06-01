<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

 	/**
	 * Admin Notices for SagePay Form
	 */
	class PDF_Invoices_Admin_Notices {
		
		public function __construct() {
			
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );

			// Dismiss the notice
			add_action( 'wp_ajax_dismiss_pdf_invoices_missing_logo_notice', array( $this, 'dismiss_pdf_invoices_missing_logo_notice' ) );

			// Enqueue the jQuery to dismiss the notice
			add_action( 'admin_enqueue_scripts', array( $this, 'pdf_invoices_missing_logo_enqueue_admin_script' ) );

		}

		/**
		 * [admin_notice description]
		 * @return [type] [description]
		 */
		function admin_notice() {
			global $current_user;

			$current_user 	= wp_get_current_user();
			$user_id 		= $current_user->ID;

			if( current_user_can( 'manage_woocommerce' ) ) {

				$show_notice 	= get_user_meta( $user_id, 'pdf-invoices-missing-logo-notice-dismissed', true );

				if( empty( $show_notice ) || $show_notice != '1' ) {

					$settings 		= get_option( 'woocommerce_pdf_invoice_settings' );
					$enable_remote 	= isset( $settings['enable_remote'] ) ? $settings['enable_remote'] : NULL;

					$remote_logo_warning_heading 	 = __('Please check your PDF invoices.', 'woocommerce-pdf-invoice' );
					$remote_logo_warning_body 		 = __('The PDF generator has been updated, this update may mean your logo is not displaying on the PDF invoice.', 'woocommerce-pdf-invoice' );
					$remote_logo_warning_body 		.= sprintf( __('<br /><br />If your logo is missing from the invoice you can set the "Set Resources Folder" option to "Yes" in the <a href="%s">PDF Invoices Settings</a>.', 'woocommerce-pdf-invoice' ), admin_url( 'admin.php?page=woocommerce_pdf', 'https' ) );
					$remote_logo_warning_body 		.= sprintf( __('<br />More information is available in the <a href="%s">PDF Invoices Documentation</a>.', 'woocommerce-pdf-invoice' ), 'https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/#section-24' );
					
					if( $enable_remote == 'true' ) {
						$remote_logo_warning_body 		.= __('<br /><br /><strong>If you have set the Remote Logo option to "Yes" to fix a missing logo then you can revert this option back to "No" once the "Set Resources Folder" option is set to "Yes"</strong>', 'woocommerce-pdf-invoice' );
					}

					echo '<div class="notice notice-warning pdf-invoices-missing-logo-notice is-dismissible"><h3>' . $remote_logo_warning_heading . '</h3><h4>' . $remote_logo_warning_body . '</h4></div>';
				}

			}

		}

		/**
		 * [dismiss_pdf_invoices_missing_logo_notice description]
		 * @return [type] [description]
		 */
		function dismiss_pdf_invoices_missing_logo_notice() {
			global $current_user;

			$current_user 	= wp_get_current_user();
			$user_id 		= $current_user->ID;

			update_user_meta( $user_id, 'pdf-invoices-missing-logo-notice-dismissed', 1 );
	    }

	    /**
	     * [pdf_invoices_missing_logo_enqueue_admin_script description]
	     * @param  [type] $hook [description]
	     * @return [type]       [description]
	     */
	    function pdf_invoices_missing_logo_enqueue_admin_script( $hook ) {
		    wp_enqueue_script( 'dismiss_pdf_invoices_missing_logo_notice_script', PDFPLUGINURL . '/assets/js/dismiss.js', array('jquery' ), PDFVERSION, true );
		}

	} // End class
	
	$PDF_Invoices_Admin_Notices = new PDF_Invoices_Admin_Notices;
   