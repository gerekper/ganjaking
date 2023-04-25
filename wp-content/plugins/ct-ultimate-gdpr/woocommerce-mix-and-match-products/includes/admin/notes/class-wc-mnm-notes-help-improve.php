<?php
/**
 * WooCommerce Mix and Match: Survey note.
 *
 * Adds a note to ask users to complete feedback survey.
 * 
 * @since 2.4.0
 *
 * @package WooCommerce Mix and Match/Admin/Notes
 */
defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\Notes\Note;
use \Automattic\WooCommerce\Admin\Notes\NoteTraits;

/**
 * Add_First_Product.
 */
class WC_MNM_Notes_Help_Improve {
	/**
	 * Note traits.
	 */
	use NoteTraits;

	/**
	 * Name of the note for use in the database.
	 */
	const NOTE_NAME = 'wc-mnm-admin-help-improve-note';

	/**
	 * Get the note.
	 *
	 * @return Note
	 */
	public static function get_note() {

		if ( WC_MNM_Helpers::is_plugin_active_for( 7 * DAY_IN_SECONDS ) ) {
			return;
		}

		// Show if there is a mix and match product.
		$query    = new \WC_Product_Query(
			array(
				'limit'  => 1,
				'return' => 'ids',
				'status' => array( 'publish' ),
				'type'   => 'mix-and-match',
			)
		);
		$products = $query->get_products();

		if ( 0 === count( $products ) ) {
			return;
		}

		// If you're updating the following please use sprintf to separate HTML tags.
		// https://github.com/woocommerce/woocommerce-admin/pull/6617#discussion_r596889685.
		$content_lines = array(
			esc_html__( 'We\'d love your input to shape the future of Mix and Match. Would you tell us a little about yourself? Feel free to share any feedback or ideas that you have.', 'woocommerce-mix-and-match-products' ),
		);

		$additional_data = array(
			'role' => 'administrator',
		);

		$survey_url = esc_url( 'https://forms.gle/RTz9ZR3USPxhTpw48' );

		$note = new Note();
		$note->set_title( esc_html__( 'Help improve Mix and Match', 'woocommerce-mix-and-match-products' ) );
		$note->set_content( implode( '', $content_lines ) );
		$note->set_content_data( (object) $additional_data );
		$note->set_type( Note::E_WC_ADMIN_NOTE_SURVEY );
		$note->set_name( self::NOTE_NAME );
		$note->set_date_reminder( MONTH_IN_SECONDS );
		$note->set_source( 'woocommerce-mix-and-match-products' );
		$note->add_action( 'wc-mnm-help-improve-survey', esc_html__( 'Share feedback', 'woocommerce-mix-and-match-products' ), $survey_url );
		return $note;
	}
}
