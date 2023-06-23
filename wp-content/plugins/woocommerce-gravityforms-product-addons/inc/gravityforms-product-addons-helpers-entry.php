<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_GFPA_Helpers_Entry {

	/**
	 * Helper function to delete an entry, but leave file uploads intact.
	 * See GFAPI::delete_entry() for the original function.
	 *
	 * @param array $entry
	 *
	 * @return bool|int|mysqli_result|resource|WP_Error|null
	 */
	public static function safe_delete_entry( array $entry ) {
		global $wpdb;

		$entry_id = $entry['id'] ?? false;
		GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Deleting entry #{$entry_id}." );

		if ( ! $entry_id ) {
			GFCommon::log_debug( __METHOD__ . '(): [woocommerce-gravityforms-product-addons] Entry ID not passed to safe_delete_entry.');
			return true;
		}

		/**
		 * Fires before an entry is deleted.
		 *
		 * @param $entry_id
		 */
		do_action( 'gform_delete_entry', $entry_id );

		$entry_table           = GFFormsModel::get_entry_table_name();
		$entry_notes_table     = GFFormsModel::get_entry_notes_table_name();
		$entry_meta_table_name = GFFormsModel::get_entry_meta_table_name();

		// Delete from entry meta
		$sql = $wpdb->prepare( "DELETE FROM $entry_meta_table_name WHERE entry_id=%d", $entry_id );
		$wpdb->query( $sql );

		// Delete from lead notes
		$sql = $wpdb->prepare( "DELETE FROM $entry_notes_table WHERE entry_id=%d", $entry_id );
		$wpdb->query( $sql );


		// Delete from entry table
		$sql    = $wpdb->prepare( "DELETE FROM $entry_table WHERE id=%d", $entry_id );
		$result = $wpdb->query( $sql );

		if ( ! $result || ! is_wp_error( $result ) ) {
			GFCommon::log_error( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Failed to delete entry #{$entry_id}." );
			$db_error = $wpdb->last_error;
			if ( $db_error ) {
				GFCommon::log_error( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Database error: {$db_error}" );
			}

			$result = new WP_Error( 'gform_delete_entry_failed', __( 'Failed to delete entry.', 'woocommerce-gravityforms-product-addons' ) );
		}

		return $result;
	}

}
