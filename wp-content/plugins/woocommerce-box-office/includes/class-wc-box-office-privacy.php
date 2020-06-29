<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class WC_Box_Office_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Box Office', 'woocommerce-box-office' ) );

		$this->add_exporter( 'woocommerce-box-office-data', __( 'WooCommerce Box Office Data', 'woocommerce-box-office' ), array( $this, 'ticket_data_exporter' ) );
		$this->add_eraser( 'woocommerce-box-office-data', __( 'WooCommerce Box Office Data', 'woocommerce-box-office' ), array( $this, 'ticket_data_eraser' ) );
	}

	/**
	 * Returns a list of tickets.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_tickets( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		if ( ! $user instanceof WP_User ) {
			return array();
		}

		return wc_box_office_get_tickets_by_user( $user->ID, 10, $page );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-box-office' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-box-office' ) );
	}

	/**
	 * Handle exporting data for tickets.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function ticket_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$tickets = $this->get_tickets( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $tickets ) ) {
			foreach ( $tickets as $ticket ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_tickets',
					'group_label' => __( 'Tickets', 'woocommerce-box-office' ),
					'item_id'     => 'ticket-' . $ticket->ID,
					'data'        => array(
						array(
							'name'  => __( 'Ticket ID', 'woocommerce-box-office' ),
							'value' => $ticket->ID,
						),
						array(
							'name'  => __( 'Ticket data', 'woocommerce-box-office' ),
							'value' => wc_box_office_get_ticket_description( $ticket->ID ),
						),
					),
				);
			}

			$done = 10 > count( $tickets );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases ticket data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function ticket_data_eraser( $email_address, $page ) {
		$tickets = $this->get_tickets( $email_address, 1 );

		$items_removed  = false;
		$messages       = array();

		foreach ( (array) $tickets as $ticket ) {
			wp_delete_post( $ticket->ID, true );

			$items_removed = true;
		}

		if ( $items_removed ) {
			$messages[] = __( 'Box office personal data erased.', 'woocommerce-box-office' );
		}

		// Tell core if we have more tickets to work on still
		$done = count( $tickets ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => $done,
		);
	}
}

new WC_Box_Office_Privacy();
