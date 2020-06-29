<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Freshdesk Tickets Integration.
 *
 * @package  WC_Freshdesk_Tickets
 * @category Integration
 * @author   WooThemes
 */
class WC_Freshdesk_Tickets extends WC_Freshdesk_Abstract_Integration {

	/**
	 * Create ticket.
	 *
	 * @param  array $data Ticket data.
	 *
	 * @return array       Status (1 to success and 0 when failed) and ticket ID.
	 */
	protected function create_ticket( $data ) {
		$url = esc_url( $this->url ) . 'tickets';
		$params = array(
			'method'  => 'POST',
			'body'    => json_encode( $data ),
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':X' )
			)
		);

		$response = wp_safe_remote_post( $url, $params );

		if ( ! is_wp_error( $response ) && ( $response['response']['code'] === 200 || $response['response']['code'] === 201 ) && ( strcmp( $response['response']['message'], 'Created' ) == 0 ) ) {
			$ticket    = json_decode( $response['body'] );
			$ticket_id = $ticket->id;

			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, sprintf( 'Ticket #%s created successfully!', $ticket_id ) );
			}

			return array(
				'id'     => $ticket_id,
				'status' => 1 // success.
			);
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Failed to create the ticket: ' . print_r( $response, true ) );
			}

			return array(
				'id'     => 0,
				'status' => 0 // fail.
			);
		}
	}

	/**
	 * Convert special characters to UTF-8.
	 *
	 * @param  string $value
	 *
	 * @return string
	 */
	protected function to_utf8( $value ) {
		$encoding = mb_detect_encoding( $value, mb_detect_order(), true );
		$value    = mb_convert_encoding( $value, 'UTF-8', $encoding ? $encoding : 'HTML-ENTITIES' );

		return $value;
	}

	/**
	 * Generate the order data.
	 *
	 * @param  WC_Order $order Order object.
	 *
	 * @return string          Order data.
	 */
	protected function order_data( $order ) {
		ob_start();

		// Break two lines to distance the user message.
		echo PHP_EOL . PHP_EOL;
		echo '****************************************************';
		echo PHP_EOL . PHP_EOL;
		echo __( 'Order data:', 'woocommerce-freshdesk' );
		echo PHP_EOL . PHP_EOL;

		do_action( 'woocommerce_freshdesk_ticket_data_before', $order );

		// Order meta.
		echo sprintf( __( 'Order number: %s', 'woocommerce-freshdesk' ), $order->get_order_number() );
		echo PHP_EOL;
		echo sprintf( __( 'Order date: %s', 'woocommerce-freshdesk' ), date_i18n( wc_date_format(), strtotime( $order->get_date_paid() ) ) );
		echo PHP_EOL . PHP_EOL;

		do_action( 'woocommerce_freshdesk_ticket_data_meta', $order );

		if ( function_exists( 'wc_get_email_order_items' ) ) {
			$order_items_table = wc_get_email_order_items( $order );
		} else {
			$order_items_table = $order->email_order_items_table();
		}

		// Products list.
		echo $this->to_utf8( wp_kses( $order_items_table, array() ) );
		echo '----------' . PHP_EOL . PHP_EOL;

		if ( $totals = $order->get_order_item_totals() ) {
			foreach ( $totals as $total ) {
				echo $this->to_utf8( sanitize_text_field( $total['label'] . "\t " . $total['value'] ) ) . PHP_EOL;
			}
		}

		do_action( 'woocommerce_freshdesk_ticket_data_after', $order );

		$html = ob_get_clean();

		return nl2br( $html );
	}

	/**
	 * Process and open a new ticket.
	 *
	 * @param  int     $order_id       Order ID.
	 * @param  string  $subject        Ticket subject.
	 * @param  string  $description    Ticket description.
	 * @param  boolean $use_user_email If true use user email if false use email from order
	 *
	 * @return array               Success status (1 to success and 0 when failed) and ticket ID.
	 */
	public function open_ticket( $order_id, $subject, $description, $use_user_email = false ) {
		$order = new WC_Order( intval( $order_id ) );
		$formated_subject = sanitize_text_field( $subject ) . ' - ' . __( 'Order', 'woocommerce-freshdesk' ) . ' ' . $order->get_order_number();

		$email = '';
		if( $use_user_email ) { 
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		} else {
			$email = $order->billing_email;
		}


		$data = apply_filters( 'woocommerce_freshdesk_ticket_data', array(
			'email'       => $email,
			'subject'     => $formated_subject,
			'description' => wp_kses( $description, array() ) . $this->order_data( $order ),
			'priority'    => 2, // Medium.
			'status'      => 2 // Open.
		), $order_id );

		$ticket = $this->create_ticket( $data );
		return array(
			'id'      => $ticket['id'],
			'status'  => $ticket['status']
		);
	}

	/**
	 * Process and open a new ticket from a comment.
	 *
	 * @param  string $email       Email from the ticket owner.
	 * @param  string $subject     Ticket subject.
	 * @param  string $description Ticket description.
	 * @param  bool   $is_order    Checks with is an order.
	 * @param  mixed  $order       Order data.
	 *
	 * @return array               Success status (1 to success and 0 when failed) and ticket ID.
	 */
	public function open_ticket_from_comment( $email, $subject, $description, $is_order = false, $order = null ) {
		$formated_subject     = sanitize_text_field( $subject );
		$formated_description = wp_kses( $description, array() );
		if ( $is_order ) {
			$formated_subject     .= ' - ' . __( 'Order', 'woocommerce-freshdesk' ) . ' ' . $order->get_order_number();
			$formated_description .= $this->order_data( $order );
		}

		$data = apply_filters( 'woocommerce_freshdesk_ticket_data_from_comment', array(
			'email'       => $email,
			'subject'     => $formated_subject,
			'description' => $formated_description,
			'priority'    => 2, // Medium.
			'status'      => 2 // Open.
		), $is_order, $order );

		$ticket = $this->create_ticket( $data );
		return array(
			'id'      => $ticket['id'],
			'status'  => $ticket['status']
		);
	}

	/**
	 * Get user tickets.
	 *
	 * @param  string $email User email.
	 * @param  int    $page  Ticket list page.
	 *
	 * @return array
	 */
	protected function get_user_tickets( $email, $page = 1 ) {
		$url = esc_url( add_query_arg( array( 'email' => urlencode( $email ), 'page' => intval( $page ) ), $this->url . 'tickets' ) );
		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':X' )
			)
		);

		$response = wp_safe_remote_get(  $url, $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 && ( strcmp( $response['response']['message'], 'OK' ) == 0 ) ) {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, 'Tickets listed successfully!' );
			}

			return json_decode( $response['body'], true );
		} else {
			if ( 'yes' === $this->debug ) {
				$this->log->add( $this->id, sprintf( 'Failed to list the tickets for customer with email %s : %s', $email, print_r( $response, true ) ) );
			}

			return array();
		}
	}

	/**
	 * Ticket status ID to text.
	 *
	 * @param  int    $id Ticket status ID.
	 *
	 * @return string     Ticket status.
	 */
	protected function ticket_status( $id ) {
		$status = apply_filters( 'woocommerce_freshdesk_ticket_status', array(
			2 => __( 'Open', 'woocommerce-freshdesk' ),
			3 => __( 'Pending', 'woocommerce-freshdesk' ),
			4 => __( 'Resolved', 'woocommerce-freshdesk' ),
			5 => __( 'Closed', 'woocommerce-freshdesk' ),
		) );

		if ( ! isset( $status[ $id ] ) ) {
			return $status[5];
		}

		return $status[ $id ];
	}

	/**
	 * Display a table with user tickets.
	 *
	 * @param  string $email User email.
	 *
	 * @return string        Tickets table.
	 */
	public function tickets_table( $email ) {
		$settings      = get_option( 'woocommerce_freshdesk_settings', false );
		$freshdesk_url = '';
		$html          = '';
		$current_page  = ( isset( $_GET['support_page'] ) && $_GET['support_page'] > 0 ) ? intval( $_GET['support_page'] ) : 1;
		$tickets       = $this->get_user_tickets( $email, $current_page );
		$count         = 0;

		if ( $settings ) {
			$freshdesk_url = 'https://' . $settings['url'] . '.freshdesk.com';
		}

		// Navigation.
		$myaccount_url = get_permalink( wc_get_page_id( 'myaccount' ) );
		$next_page     = add_query_arg( array( 'support_page' => $current_page + 1 ), $myaccount_url );
		$last_page     = add_query_arg( array( 'support_page' => $current_page - 1 ), $myaccount_url );

		if ( ! empty( $tickets ) ) {

			$html .= '<table id="support-tickets-table" class="shop_table">';
				$html .= '<thead>';
					$html .= '<tr>';
						$html .= '<th class="ticket-number"><span class="nobr">' . esc_html__( 'Number', 'woocommerce-freshdesk' ) . '</span></th>';
						$html .= '<th class="ticket-subject"><span class="nobr">' . esc_html__( 'Subject', 'woocommerce-freshdesk' ) . '</span></th>';
						$html .= '<th class="ticket-date"><span class="nobr">' . esc_html__( 'Date', 'woocommerce-freshdesk' ) . '</span></th>';
						$html .= '<th class="ticket-status"><span class="nobr">' . esc_html__( 'Status', 'woocommerce-freshdesk' ) . '</span></th>';
						$html .= '<th class="ticket-actions">&nbsp;</th>';
					$html .= '</tr>';
				$html .= '</thead>';

				$html .= '<form method="post">';
				$html .= '<tbody>';
					foreach ( $tickets as $ticket ) {
						$url = esc_url( $freshdesk_url . '/helpdesk/tickets/' . intval( $ticket['id'] ) );

						$html .= '<tr class="ticket">';
							$html .= '<td><a href="' . $url . '">#' . intval( $ticket['id'] ) . '</a></td>';
							$html .= '<td>' . esc_html( $ticket['subject'] ) . '</td>';
							$html .= '<td>' . date_i18n( wc_date_format(), strtotime( $ticket['updated_at'] ) ) . '</td>';
							$html .= '<td>' . esc_html( $this->ticket_status( intval( $ticket['status'] ) ) ) . '</td>';
							$html .= '<td style="text-align: right;"><button type="submit" class="button" name="reply-ticket-freshdesk" value="' . intval( $ticket['id'] ) . '">' . esc_html__( 'Reply', 'woocommerce-freshdesk' ) . '</button></td>';
						$html .= '</tr>';
						$count++;
					}

				$html .= '</tbody>';
				$html .= '</form>';
			$html .= '</table>';
			$html .= '<div id="support-tickets-navigation">';
				$html .= ( 1 != $current_page ) ? '<a class="button previous" href="' . esc_url( $last_page ) . '">' . esc_html__( 'Previous', 'woocommerce-freshdesk' ) . '</a>' : '';
				$html .= ( 30 <= $count ) ? '<a class="button next" href="' . esc_url( $next_page ) . '">' . esc_html__( 'Next', 'woocommerce-freshdesk' ) . '</a>' : '';
			$html .= '</div>';
		} else {
			if ( 1 != $current_page ) {
				$html .= '<p>' . esc_html__( 'Oops, you\'ve seen all their support tickets, please return to the previous page.', 'woocommerce-freshdesk' ) . '</p>';
				$html .= '<div id="support-tickets-navigation">';
					$html .= '<a class="button previous" href="' . esc_url( $last_page ) . '">' . esc_html__( 'Previous', 'woocommerce-freshdesk' ) . '</a>';
				$html .= '</div>';
			} else {
				$html .= '<p>' . esc_html__( 'You have no support tickets.', 'woocommerce-freshdesk' ) . '</p>';
			}
		}

		return $html;
	}
}
