<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Process ticket input/scan.
		add_action( 'wp_ajax_scan_ticket', array( $this, 'scan_ticket' ) );
		add_action( 'wp_ajax_nopriv_scan_ticket', array( $this, 'scan_ticket' ) );

		// Save barcode from ticket edit screen.
		add_action( 'wp_ajax_save_ticket_barcode', array( $this, 'save_ticket_barcode' ) );
		add_action( 'wp_ajax_nopriv_save_ticket_barcode', array( $this, 'save_ticket_barcode' ) );
	}

	/**
	 * Process scanning/input of barcode.
	 *
	 * @return void
	 */
	public function scan_ticket () {
		// Security check.
		$do_nonce_check = apply_filters( 'woocommerce_box_office_do_nonce_check', true );
		if ( $do_nonce_check && ! wp_verify_nonce( $_POST[ 'woocommerce_box_office_scan_nonce' ], 'scan-barcode' ) ) {
			WC_Order_Barcodes()->display_notice( __( 'Permission denied: Security check failed', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		// Retrieve ticket ID from barcode.
		$ticket_id = WCBO()->components->ticket_barcode->get_ticket_id_from_barcode_text( $_POST['barcode_input'] );
		if ( ! $ticket_id ) {
			WC_Order_Barcodes()->display_notice( __( 'Invalid ticket', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		// Check if user has barcode scanning permissions
		$can_scan = apply_filters( 'woocommerce_box_office_scan_permission', current_user_can( 'manage_woocommerce' ), $ticket_id );
		if ( ! $can_scan ) {
			WC_Order_Barcodes()->display_notice( __( 'Permission denied: You do not have sufficient permissions to scan tickets', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		$attended_response = apply_filters( 'woocommerce_box_office_attended_response', sprintf( __( 'Ticket has been marked as %1$sattended%2$s.', 'woocommerce-box-office' ), '<b>', '</b>' ) );

		// Get current attended status
		$attended_status = get_post_meta( $ticket_id, '_attended', true );

		// Get selected action and process accordingly
		$action = esc_attr( $_POST['scan_action'] );
		$response = '';
		$response_type = 'success';
		switch ( $action ) {
			case 'attended':
				if ( $attended_status ) {
					$response = apply_filters( 'woocommerce_box_office_already_attended_response', __( 'Ticket has already been marked as attended.', 'woocommerce-box-office' ) );
					$response_type = 'error';
				} else {
					update_post_meta( $ticket_id, '_attended', 'yes' );
					$response = $attended_response;
				}
			break;

			case 'lookup':
				if ( $attended_status ) {
					$response = $attended_response;
				}
			break;
		}

		// Display response notice
		if ( $response ) {
			WC_Order_Barcodes()->display_notice( $response, $response_type );
		}

		$ticket = wc_box_office_get_ticket( $ticket_id );

		$template_vars = array(
			'ticket_description'   => wc_box_office_get_ticket_description( $ticket_id, 'table' ),
			'edit_ticket_url'      => wcbo_get_my_ticket_url( $ticket_id ),
			'print_ticket_enabled' => get_post_meta( $ticket->product_id, '_print_tickets', true ),
			'print_ticket_url'     => wcbo_get_my_ticket_url( $ticket_id, true ),
		);

		wc_get_template( 'my-ticket-read-only.php', $template_vars, 'woocommerce-box-office', WCBO()->dir . 'templates/' );

		// Exit function to prevent '0' displaying at the end of ajax request
		exit;
	}

	/**
	 * Save barcode via ajax.
	 *
	 * @return  void
	 */
	public function save_ticket_barcode() {
		if ( ! isset( $_POST['ticket_id'] ) ) {
			exit;
		}

		$ticket_id = $_POST['ticket_id'];

		// Add encoded barcode image to ticket
		if ( isset( $_POST['ticket_barcode_image'] ) && ! empty( $_POST['ticket_barcode_image'] ) ) {
			update_post_meta( $ticket_id, '_barcode_image', $_POST['ticket_barcode_image'] );
		}

		// Add barcode text to ticket
		if ( isset( $_POST['ticket_barcode_text'] ) && ! empty( $_POST['ticket_barcode_text'] ) ) {
			update_post_meta( $ticket_id, '_barcode_text', $_POST['ticket_barcode_text'] );
		}

		exit;
	}

}
