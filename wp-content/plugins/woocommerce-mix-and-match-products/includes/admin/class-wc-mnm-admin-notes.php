<?php
/**
 * WooCommerce Mix and Match - WooCommerce Admin Notices.
 *
 * Adds relevant information via the WooCommerce Inbox.
 *
 * @since   2.4.0
 * @version 2.4.1
 *
 * @package WooCommerce Mix and Match/Admin/Notes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_MNM_Admin_Notes {

	/**
	 * Attach hooks and filters
	 */
	public static function init() {
		add_action( 'wc_admin_daily', array( __CLASS__, 'possibly_add_notes' ), 15 );
	}

	/**
	 * Include the notes to create.
	 *
	 * @since 2.4.1
	 */
	public static function possibly_add_notes() {

		// Start adding our notes/messages.
		WC_MNM_Notes_Get_Support::possibly_add_note();
		WC_MNM_Notes_Help_Improve::possibly_add_note();
	}

	/**
	 * Include the notes to create.
	 *
	 * @since 2.4.1 - renamed already.
	 */
	public static function initialize_notes() {
		return self::possibly_add_notes();
	}
} // END class

return WC_MNM_Admin_Notes::init();
