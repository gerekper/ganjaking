<?php
/**
 * WC Box Office Ticket Ajax
 *
 * @package woocommerce-box-office
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Box_Office_Ticket_Ajax class.
 *
 * Handles ajax requests for ticket scanning.
 */
class WC_Box_Office_Ticket_Ajax {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Process ticket input/scan.
		add_action( 'wp_ajax_scan_ticket', array( $this, 'scan_ticket' ) );
		add_action( 'wp_ajax_nopriv_scan_ticket', array( $this, 'scan_ticket' ) );
	}

	/**
	 * Process scanning/input of barcode.
	 *
	 * @return void
	 */
	public function scan_ticket() {
		// Security check.
		/**
		 * Filter whether to perform nonce check when scanning tickets.
		 *
		 * @param bool $do_nonce_check Whether to perform nonce check. Default true.
		 * @since 1.0.0
		 */
		$do_nonce_check = apply_filters( 'woocommerce_box_office_do_nonce_check', true );
		if ( $do_nonce_check && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_box_office_scan_nonce'] ?? '' ) ), 'scan-barcode' ) ) {
			WC_Order_Barcodes()->display_notice( esc_html__( 'Permission denied: Security check failed', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		// Retrieve ticket ID from barcode.
		$barcode_input = sanitize_text_field( wp_unslash( $_POST['barcode_input'] ?? '' ) );
		$ticket_id     = WCBO()->components->ticket_barcode->get_ticket_id_from_barcode_text( $barcode_input );
		if ( ! $ticket_id ) {
			WC_Order_Barcodes()->display_notice( esc_html__( 'Invalid ticket', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		// Check if user has barcode scanning permissions.
		/**
		 * Filter whether user has permission to scan tickets.
		 * By default, only users with manage_woocommerce capability can scan tickets.
		 *
		 * @param bool $can_scan Whether user can scan tickets. Default true.
		 * @param int  $ticket_id Ticket ID.
		 * @since 1.0.0
		 */
		$can_scan = apply_filters( 'woocommerce_box_office_scan_permission', current_user_can( 'manage_woocommerce' ), $ticket_id );
		if ( ! $can_scan ) {
			WC_Order_Barcodes()->display_notice( esc_html__( 'Permission denied: You do not have sufficient permissions to scan tickets', 'woocommerce-box-office' ), 'error' );
			exit;
		}

		// Show scanned barcode number.
		WC_Order_Barcodes()->display_notice(
			sprintf(
				// translators: %s: barcode input.
				esc_html__( 'Showing results for barcode: %s', 'woocommerce-box-office' ),
				'<strong>' . esc_attr( $barcode_input ) . '</strong>'
			),
			'success'
		);

		/**
		 * Filter response when ticket has been marked as attended.
		 *
		 * @param string $response Response message.
		 * @since 1.0.0
		 */
		$attended_response = apply_filters(
			'woocommerce_box_office_attended_response',
			// translators: %%1$s: Start bold text, %%2$s: End bold text.
			sprintf( esc_html__( 'Ticket has been marked as %1$sattended%2$s.', 'woocommerce-box-office' ), '<b>', '</b>' )
		);

		// Get current attended status.
		$attended_status = get_post_meta( $ticket_id, '_attended', true );

		// Get selected action and process accordingly.
		$action        = sanitize_text_field( wp_unslash( $_POST['scan_action'] ?? '' ) );
		$response      = '';
		$response_type = 'success';
		switch ( $action ) {
			case 'attended':
				if ( $attended_status ) {
					/**
					 * Filter response when ticket has already been marked as attended.
					 *
					 * @param string $response Response message.
					 * @since 1.0.0
					 */
					$response      = apply_filters( 'woocommerce_box_office_already_attended_response', esc_html__( 'Ticket has already been marked as attended.', 'woocommerce-box-office' ) );
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

		// Display response notice.
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

		// Exit function to prevent '0' displaying at the end of ajax request.
		exit;
	}
}
