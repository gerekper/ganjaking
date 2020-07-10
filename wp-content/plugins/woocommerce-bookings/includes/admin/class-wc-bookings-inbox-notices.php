<?php
class WC_Bookings_Inbox_Notice {
	const NOTE_NAME = 'woocommerce-bookings-welcome-note';

	/**
	 * Adds a note to the merchant' inbox.
	 */
	public static function add_activity_panel_inbox_welcome_note() {
		if ( ! class_exists( 'WC_Admin_Notes' ) ) {
			return;
		}

		if ( ! class_exists( 'WC_Data_Store' ) ) {
			return;
		}

		$data_store = WC_Data_Store::load( 'admin-note' );

		// First, see if we've already created this kind of note so we don't do it again.
		$note_ids = $data_store->get_notes_with_name( self::NOTE_NAME );

		if ( 0 < count( $note_ids ) ) {
			return;
		}

		// Otherwise, add the note
		$note = new WC_Admin_Note();
		$note->set_title( __( 'You\'re almost ready to take bookings!', 'woocommerce-bookings' ) );
		$note->set_content(
			__( 'To get set up, you\'ll need to set your availability or add a bookable product.', 'woocommerce-bookings' )
		);
		$note->set_content_data( (object) array(
			'bookings_welcome'     => true,
		) );

		$note->set_type( WC_Admin_Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
		// See https://automattic.github.io/gridicons/ for icon names.
		// Don't include the gridicons- part of the name.
		$note->set_icon( 'info' );
		$note->set_name( self::NOTE_NAME );
		$note->set_source( 'woocommerce-bookings' );
		$note->add_action(
			'settings',
			__( 'Set my availability', 'woocommerce-bookings' ),
			'edit.php?post_type=wc_booking&page=wc_bookings_settings&tab=availability'
		);
		$note->add_action(
			'google-integration',
			__( 'Add a bookable product', 'woocommerce-bookings' ),
			'post-new.php?post_type=product&bookable_product=1'
		);
		$note->save();
	}

	/**
	 * Removes any notes this plugin created.
	 */
	public static function remove_activity_panel_inbox_notes() {
		if ( ! class_exists( 'WC_Admin_Notes' ) ) {
			return;
		}

		WC_Admin_Notes::delete_notes_with_name( self::NOTE_NAME );
	}
}
