<?php

class GPNF_Easy_Passthrough {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		add_filter( 'gpnf_can_user_edit_entry', array( $this, 'can_user_edit_entry' ), 10, 3 );

	}

	/**
	 * Check if the child entry that is about to be edited belongs to the parent entry that was populated by GPEP.
	 * Currently only supports entries populated via an EP token as this is the only use case where a user may not own
	 * the entry and be populating entries from a different session.
	 *
	 * @param $can_user_edit_entry
	 * @param $entry
	 * @param $current_user
	 *
	 * @return bool|mixed
	 */
	public function can_user_edit_entry( $can_user_edit_entry, $entry, $current_user ) {

		if ( ! is_callable( 'gp_easy_passthrough' ) ) {
			return $can_user_edit_entry;
		}

		$session = new GPNF_Session( $entry[ GPNF_Entry::ENTRY_PARENT_FORM_KEY ] );

		$cookie = $session->get_cookie();
		if ( ! $cookie ) {
			return $can_user_edit_entry;
		}

		$ep_token = rgars( $cookie, 'request/ep_token' );

		if ( $ep_token ) {
			$ep_entry             = gp_easy_passthrough()->get_entry_for_token( $ep_token );
			$nested_form_field_id = gp_nested_forms()->get_posted_nested_form_field_id();
			$child_entry_ids      = gp_nested_forms()->get_child_entry_ids_from_value( rgar( $ep_entry, $nested_form_field_id ) );
			if ( in_array( $entry['id'], $child_entry_ids ) ) {
				$can_user_edit_entry = true;
			}
		}

		return $can_user_edit_entry;
	}

}

function gpnf_easy_passthrough() {
	return GPNF_Easy_Passthrough::get_instance();
}
