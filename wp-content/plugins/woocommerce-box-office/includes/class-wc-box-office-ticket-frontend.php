<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Process updated ticket from front-end ticket page.
		add_action( 'wp', array( $this, 'maybe_process_updated_ticket' ) );
		add_action( 'wp', array( $this, 'maybe_print_ticket' ) );

		// My tickets page.
		add_action( 'plugins_loaded', array( $this, 'my_tickets' ) );

		// Display tickets list in PDF invoice.
		add_filter( 'pdf_template_line_output', array( $this, 'pdf_invoice_ticket_list' ), 10, 2 );
	}

	/**
	 * Display tickets list in my account page.
	 *
	 * @since 1.0.2
	 */
	public function my_tickets() {
		$this->my_tickets_query();
	}

	/**
	 * My tickets page query.
	 *
	 * @since 1.0.2
	 */
	public function my_tickets_query() {
		require_once( WCBO()->dir . 'includes/class-wc-box-office-ticket-frontend-query.php' );
		new WC_Box_Office_Ticket_Frontend_Query();
	}

	/**
	 * Process updated ticket from ticket edit page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function maybe_process_updated_ticket() {
		if ( ! $this->_is_valid_ticket_page_request( 'edit' ) ) {
			return;
		}

		try {
			if ( empty( $_POST['_wpnonce'] ) ) {
				throw new Exception( __( 'Missing nonce to update the ticket.', 'woocommerce-box-office' ) );
			}

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-box-office_update_ticket' ) ) {
				throw new Exception( __( 'We were unable to update your ticket, please try again.', 'woocommerce-box-office' ) );
			}

			$ticket = wc_box_office_get_ticket_by_token( $_GET['token'] );
			if ( ! $ticket ) {
				throw new Exception( __( 'Invalid ticket.', 'woocommerce-box-office' ) );
			}

			if ( ! is_ticket_editable( $ticket ) ) {
				throw new Exception( __( 'Ticket is not editable.', 'woocommerce-box-office' ) );
			}

			$ticket_form = new WC_Box_Office_Ticket_Form( $ticket->product );
			$ticket_form->validate( $_POST );

			// In case email contact is changed, we need to re-send the email.
			$old_fields       = $ticket->fields;

			$ticket->update( $ticket_form->get_clean_data() );

			// Update order item meta for this ticket when ticket is updated.
			WCBO()->components->order->update_item_meta_from_ticket( $ticket->id );

			$new_email_fields = $ticket->get_ticket_fields_by_type( 'email' );
			$new_fields       = $ticket->fields;

			if ( $old_fields !== $new_fields ) {
				$send_to = array();
				foreach ( $new_email_fields as $key => $field ) {
					if ( 'yes' === $field['email_contact'] ) {
						$send_to[] = $field['value'];
					}
				}

				if ( ! empty( $send_to ) ) {
					$subject = get_post_meta( $ticket->product_id, '_email_ticket_subject', true );
					$message = get_post_meta( $ticket->product_id, '_ticket_email_html', true );
					wc_box_office_send_ticket_email( $ticket->id, $send_to, $subject, $message );
				}
			}

			wc_add_notice( __( 'Ticket updated.', 'woocommerce-box-office' ), 'success' );

		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
	}

	/**
	 * Maybe print ticket request.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function maybe_print_ticket() {
		if ( ! $this->_is_valid_ticket_page_request( 'print' ) ) {
			return;
		}

		add_action( 'template_redirect', array( $this, 'print_ticket' ) );
	}

	/**
	 * Hijacks the template to render when printing the ticket.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function print_ticket() {
		$ticket = wc_box_office_get_ticket_by_token( $_GET['token'] );
		if ( ! $ticket ) {
			// @TODO(gedex) Probably send it 404 page?
			return;
		}

		$template_vars = array(
			'printed_content' => $ticket->get_printed_content(),
			'print_barcode'   => ( 'yes' === get_post_meta( $ticket->product_id, '_print_barcode', true ) ),
			'ticket_id'       => $ticket->id,
		);

		wc_get_template( 'my-ticket-printed.php', $template_vars, 'woocommerce-box-office', WCBO()->dir . 'templates/' );
		exit();
	}

	/**
	 * Validate whether current request comes with expected request vars (POST
	 * and GET) and from my-ticket page.
	 *
	 * @since 1.1.0
	 *
	 * @param string $mode  Either 'edit' or 'print'. Default to 'edit'.
	 *
	 * @return bool
	 */
	protected function _is_valid_ticket_page_request( $mode = 'edit' ) {
		global $wp_query;

		if ( ! $wp_query->is_page( get_option( 'box_office_my_ticket_page_id' ) ) ) {
			return false;
		}

		if ( ! in_array( $mode, array( 'edit', 'print' ) ) ) {
			return false;
		}

		if ( empty( $_GET['token'] ) ) {
			return false;
		}

		if ( 'edit' === $mode ) {
			if ( ! isset( $_POST['action'] ) ) {
				return false;
			}

			if ( 'update_ticket' !== $_POST['action'] ) {
				return false;
			}

			if ( empty( $_POST['ticket_fields'] ) ) {
				return false;
			}

			if ( ! is_array( $_POST['ticket_fields'] ) ) {
				return false;
			}
		} elseif ( 'print' === $mode && empty( $_GET['print'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Display user tickets on My Account page.
	 *
	 * @return void
	 */
	public function account_ticket_list() {
		echo do_shortcode( '[user_tickets fields_format="list"]' );
	}

	/**
	 * Add order tickets to PDF invoice.
	 *
	 * @param  string  $pdflines Existing PDF content
	 * @param  integer $order_id Order ID
	 * @return string            Updated PDF content
	 */
	public function pdf_invoice_ticket_list( $pdflines = '', $order_id = 0 ) {
		if ( ! $pdflines || ! $order_id ) {
			return $pdflines;
		}

		$tickets = WCBO()->components->order->get_tickets_by_order( $order_id );

		if ( 0 === count( $tickets ) ) {
			return $pdflines;
		}

		$html = '';

		$description = apply_filters( 'woocommerce_box_office_pdf_invoice_tickets_description', __( 'Log into your acount to edit each of your purchased tickets.', 'woocommerce-box-office' ) );

		if ( $description ) {
			$html .= '<p class="ticket-list-description">' . $description . '</p>' . "\n";
		}

		$html .= '<dl class="purchased-tickets">' . "\n";

		foreach ( $tickets as $ticket ) {

			// Get ticket product ID
			$product_id = get_post_meta( $ticket->ID, '_product', true );

			// Get available fields from ticket product
			$ticket_fields      = get_post_meta( $product_id, '_ticket_fields', true );
			$ticket_description = '';
			foreach ( $ticket_fields as $field_key => $field ) {

				$ticket_meta = get_post_meta( $ticket->ID, $field_key, true );

				if ( $ticket_meta && ! is_array( $ticket_meta ) ) {
					if ( $ticket_description ) {
						$ticket_description .= ' | ';
					}
					$ticket_description .= $field['label'] . ': ' . $ticket_meta;
				}
			}

			// Output HTML
			$html .= '<dt>' . $ticket->post_title . '</dt>' . "\n";
			$html .= '<dd class="description">' . $ticket_description . '</dd>' . "\n";
		}

		$html .= '</dl>' . "\n";

		// Format markup for PDF invoice
		$output = '<table class="shop_table ticketdetails" width="100%">' .
					'<thead>' .
					'<tr><th align="left"><h2>' . apply_filters( 'woocommerce_box_office_order_tickets_title', esc_html__( 'Order Tickets', 'woocommerce-box-office' ) ) . '</h2></th></tr>' .
					'</thead>' .
					'<tbody>' .
					'<tr>' .
					'<td>' .
					$html .
					'</td>' .
					'</tr>' .
					'</tbody>' .
					'</table>';

		// Add ticket details to PDF invoice
		$pdflines .= $output;

		return $pdflines;
	}

}
