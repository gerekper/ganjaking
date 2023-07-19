<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Barcode {

	/**
	 * Checks if barcode functionality is available or not.
	 *
	 * @since 1.1.1
	 *
	 * @return bool Returns true if barcode functionality is available
	 */
	public function is_available() {
		if ( ! function_exists( 'WC_Order_Barcodes' ) ) {
			return;
		}

		return 'yes' === WC_Order_Barcodes()->barcode_enable;
	}

	/**
	 * Display ticket barcode of a given ticket.
	 *
	 * The barcode for this ticket is displayed in ticket meta box (in admin
	 * screen) and printed page (for customer). If current ticket doesn't have
	 * barcode (for example the order barcodes extension just installed recently),
	 * it will generate new barcode text and image.
	 *
	 * @param integer $ticket Ticket ID
	 * @param array   $args {
	 *     Optional. Arguments to display ticket barcode.
	 *
	 *     @type bool $auto_generate Automatically generate barcode when barcode
	 *                               meta is not present in ticket post. Default
	 *                               to `true`. In case enqueuing JS is not possible
	 *                               (e.g. in printed layout) then set this to
	 *                               `false`
	 * }
	 *
	 * @return void
	 */
	public function display_ticket_barcode( $ticket_id = 0, $args = array() ) {
		if ( ! $this->is_available() ) {
			return;
		}

		if ( ! $ticket_id ) {
			return;
		}

		if ( is_object( $ticket_id ) ) {
			$ticket_id = $ticket_id->ID;
		}

		$args = wp_parse_args(
			$args,
			array(
				'auto_generate' => true,
			)
		);

		$barcode_text = get_post_meta( $ticket_id, '_barcode_text', true );
		$barcode      = '<div class="woocommerce-order-barcodes-container" style="text-align:center;">';

		// Ensure barcode exists
		if ( empty( $barcode_text ) ) {
			$this->generate_ticket_barcode( $ticket_id );
		}

		// Passing true as second variable creates the barcode an an image, this fixes issue #300
		// Second variable does not exist before Order Barcode v1.3.21
		if ( defined( 'WC_ORDER_BARCODES_VERSION' ) && version_compare( WC_ORDER_BARCODES_VERSION, '1.3.21', '>=' ) ) {
			$barcode .= WC_Order_Barcodes()->display_barcode( $ticket_id, true );
		} else {
			$barcode .= WC_Order_Barcodes()->display_barcode( $ticket_id );
		}

		$barcode .= '</div>';
		echo $barcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Generate unique barcode for ticket.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_id Ticket ID
	 *
	 * @return  void
	 */
	public function generate_ticket_barcode( $ticket_id = 0 ) {
		if ( ! $this->is_available() ) {
			return;
		}

		if ( ! $ticket_id ) {
			return;
		}

		if ( isset( get_post()->ID ) ) {
			$barcode_string = $this->generate_barcode_text_for_ticket();
			update_post_meta( $ticket_id, '_barcode_text', $barcode_string );
		}
	}

	/**
	 * Generate a unique barcode text for a ticket.
	 *
	 * @since 1.1.1
	 *
	 * @return string Generated barcode text
	 */
	public function generate_barcode_text_for_ticket() {
		// Use PHP's uniqid() for the barcode
		$barcode_string = uniqid();

		// Check if this barcode already exists and add increment if so
		$existing_ticket_id = $this->get_ticket_id_from_barcode_text( $barcode_string );
		$orig_string        = $barcode_string;

		$i = 1;
		while ( $existing_ticket_id != 0 ) {
			$barcode_string     = $orig_string . $i;
			$existing_ticket_id = $this->get_ticket_id_from_barcode_text( $barcode_string );
			++$i;
		}

		// Return unique barcode.
		return apply_filters( 'woocommerce_box_office_barcode_string', $barcode_string );
	}

	/**
	 * @deprecated
	 */
	public function get_ticket_barcode_string() {
		_deprecated_function( __METHOD__, '1.1.1', 'WC_Box_Office_Ticket_Barcode::generate_barcode_text_for_ticket' );
		return $this->generate_barcode_text_for_ticket();
	}

	/**
	 * Retrieve ticket ID from a given barcode text.
	 *
	 * @since 1.1.1
	 *
	 * @param  string  $barcode Barcode text
	 * @return integer          Ticket ID
	 */
	public function get_ticket_id_from_barcode_text( $barcode = '' ) {
		if ( ! $barcode ) {
			return 0;
		}

		global $wpdb;

		return absint( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_barcode_text', $barcode ) ) );
	}

	/**
	 * @deprecated
	 */
	public function get_barcode_ticket( $barcode = '' ) {
		_deprecated_function( __METHOD__, '1.1.1', 'WC_Box_Office_Ticket_Barcode::get_ticket_id_from_barcode_text' );
		return $this->get_ticket_id_from_barcode_text( $barcode );
	}
}
