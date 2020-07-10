<?php

class WC_Booking_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Bookings', 'woocommerce-bookings' ) );

		$this->add_exporter( 'woocommerce-bookings-data', __( 'WooCommerce Bookings Data', 'woocommerce-bookings' ), array( $this, 'bookings_data_exporter' ) );
		$this->add_eraser( 'woocommerce-bookings-data', __( 'WooCommerce Bookings Data', 'woocommerce-bookings' ), array( $this, 'bookings_data_eraser' ) );
	}

	/**
	 * Returns a list of Bookings for the user.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WC_Booking
	 */
	protected function get_bookings( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		if ( ! $user instanceof WP_User ) {
			return array();
		}

		return WC_Booking_Data_Store::get_bookings_for_user( $user->ID );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		/* translators: %s: documentation link */
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-bookings' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-bookings' ) );
	}

	/**
	 * Handle exporting data for Bookings.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function bookings_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$bookings = $this->get_bookings( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_bookings',
					'group_label' => __( 'Bookings', 'woocommerce-bookings' ),
					'item_id'     => 'booking-' . $booking->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'Booking Number', 'woocommerce-bookings' ),
							'value' => $booking->get_id(),
						),
						array(
							'name'  => __( 'Booking start', 'woocommerce-bookings' ),
							'value' => date_i18n( 'Y-m-d H:i:s', $booking->get_start() ),
						),
						array(
							'name'  => __( 'Booking end', 'woocommerce-bookings' ),
							'value' => date_i18n( 'Y-m-d H:i:s', $booking->get_end() ),
						),
						array(
							'name'  => __( 'Bookable product', 'woocommerce-bookings' ),
							'value' => $booking->get_product() ? $booking->get_product()->get_name() : '',
						),
						array(
							'name'  => __( 'Booked order ID', 'woocommerce-bookings' ),
							'value' => $booking->get_order_id(),
						),
					),
				);
			}

			$done = 10 > count( $bookings );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases Bookings data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function bookings_data_eraser( $email_address, $page ) {
		$bookings = $this->get_bookings( $email_address, 1 );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $bookings as $booking ) {
			list( $removed, $retained, $msgs ) = $this->maybe_handle_booking( $booking );
			$items_removed  |= $removed;
			$items_retained |= $retained;
			$messages        = array_merge( $messages, $msgs );
		}

		// Tell core if we have more Bookings to work on still
		$done = count( $bookings ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to Bookings
	 *
	 * @param WC_Booking $booking
	 * @return array
	 */
	protected function maybe_handle_booking( $booking ) {
		$booking->get_data_store()->delete( $booking, array( 'force_delete' => true ) );
		return array( true, false, array( __( 'WooCommerce Bookings Data Erased.', 'woocommerce-bookings' ) ) );
	}
}
