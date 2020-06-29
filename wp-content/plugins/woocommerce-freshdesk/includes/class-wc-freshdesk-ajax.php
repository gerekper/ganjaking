<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Freshdesk Ajax.
 *
 * @package  WC_Freshdesk_Ajax
 * @category Ajax
 * @author   WooThemes
 */
class WC_Freshdesk_Ajax {

	/**
	 * Initialize the ajax actions.
	 */
	public function __construct() {
		// Process tickets via ajax.
		add_action( 'wp_ajax_wc_freshdesk_process_tickets', array( $this, 'process_tickets_logged_in_user' ) );
		add_action( 'wp_ajax_nopriv_wc_freshdesk_process_tickets', array( $this, 'process_tickets_logged_out_user' ) );
	}

	/**
	 * Process the tickets by ajax.
	 *
	 * @return  string JSON data.
	 * @param  boolean $use_user_email If true use user email if false use email from order.
	 */
	public function process_tickets( $use_user_email = false ) {
		check_ajax_referer( 'woocommerce_freshdesk_proccess_ticket', 'security' );

		// Get the integration data.
		$integration = new WC_Freshdesk_Integration();

		// Sets the ticket params.
		$ticket      = new WC_Freshdesk_Tickets( $integration->url, $integration->api_key, $integration->debug );
		$order_id    = isset( $_POST['order_id'] )    ? wc_clean( $_POST['order_id'] ) : '';
		$subject     = isset( $_POST['subject'] )     ? wc_clean( wp_unslash( $_POST['subject'] ) ) : '';
		$description = isset( $_POST['description'] ) ? wc_clean( wp_unslash( $_POST['description'] ) ) : '';

		// Valid the order_id field.
		if ( empty( $order_id ) ) {
			wp_send_json(
				array(
					'status'  => 0,
					'message' => __( 'There was an error in the request, please reload this page and try again.', 'woocommerce-freshdesk' )
				)
			);
		}

		// Valid the subject field.
		if ( empty( $subject ) ) {
			wp_send_json(
				array(
					'status'  => 0,
					'message' => __( 'Subject is a required field.', 'woocommerce-freshdesk' )
				)
			);
		}

		// Valid the description field.
		if ( empty( $description ) ) {
			wp_send_json(
				array(
					'status'  => 0,
					'message' => __( 'Description is a required field.', 'woocommerce-freshdesk' )
				)
			);
		}

		do_action( 'woocommerce_freshdesk_process_tickets' );

		// Try to open the ticket.
		$response = $ticket->open_ticket( $order_id, $subject, $description, $use_user_email );

		wp_send_json( $response );
	}

	/**
	 * Process the tickets by ajax for logged in user request.
	 *
	 * @return  string JSON data.
	 */
	public function process_tickets_logged_in_user( ) {
		$this->process_tickets( true );
	}

	/**
	 * Process the tickets by ajax for logged out user request.
	 *
	 * @return  string JSON data.
	 */
	public function process_tickets_logged_out_user( ) {
		$this->process_tickets( false );
	}

}

new WC_Freshdesk_Ajax();
