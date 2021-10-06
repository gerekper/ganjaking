<?php

class GPNF_Notification_Processing {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {

		add_filter( 'gform_disable_notification', array( $this, 'should_disable_notification' ), 10, 4 );
		add_filter( 'gform_entry_post_save', array( $this, 'maybe_send_child_notifications' ), 11, 2 );
		add_filter( 'gform_notification', array( $this, 'add_notification_filters' ), 10 );
		add_filter( 'gform_notification', array( $this, 'complicate_parent_merge_tag' ), 10 );

	}

	public function add_notification_filters( $notification ) {
		remove_filter( 'gform_replace_merge_tags', array( gpnf_parent_merge_tag(), 'parse_parent_merge_tag' ), 5 );
		add_filter( 'gform_replace_merge_tags', array( gpnf_parent_merge_tag(), 'parse_parent_merge_tag' ), 5, 7 );

		return $notification;
	}

	/**
	 * @param $notification
	 *
	 * This changes the merge tag so it won't be caught by the default Gravity Forms {FIELD_LABEL:FIELD_ID} pattern.
	 *
	 * @return mixed
	 */
	public function complicate_parent_merge_tag( $object ) {

		foreach ( $object as $prop => $value ) {
			if ( is_array( $value ) ) {
				$object[ $prop ] = $this->complicate_parent_merge_tag( $value );
			} elseif ( is_string( $value ) ) {
				$object[ $prop ] = preg_replace( '/\{Parent:(.*?)\}/i', '{%GPNF:Parent:$1%}', $value );
			}
		}

		return $object;
	}

	public function should_disable_notification( $value, $notification, $form, $entry ) {

		if ( $notification['event'] != 'form_submission' ) {
			return $value;
		}

		if ( gp_nested_forms()->is_nested_form_submission() ) {
			$parent_form       = GFAPI::get_form( gp_nested_forms()->get_parent_form_id() );
			$nested_form_field = gp_nested_forms()->get_posted_nested_form_field( $parent_form );

			return ! $this->should_send_notification( 'child', $notification, $parent_form, $nested_form_field, $entry, $form );
		} elseif ( $parent_form_id = rgar( $entry, GPNF_Entry::ENTRY_PARENT_FORM_KEY ) ) {
			$parent_form       = GFAPI::get_form( $parent_form_id );
			$nested_form_field = GFFormsModel::get_field( $parent_form, rgar( $entry, GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY ) );

			return ! $this->should_send_notification( 'parent', $notification, $parent_form, $nested_form_field, $entry, $form );
		}

		return $value;

	}

	public function maybe_send_child_notifications( $entry, $form ) {

		if ( ! gp_nested_forms()->has_nested_form_field( $form ) ) {
			return $entry;
		}

		$parent_entry = new GPNF_Entry( $entry );
		if ( ! $parent_entry->has_children() ) {
			return $entry;
		}

		$child_entries = $parent_entry->get_child_entries();
		if ( ! $child_entries ) {
			return $entry;
		}

		foreach ( $child_entries as $child_entry ) {
			$child_form = gp_nested_forms()->get_nested_form( $child_entry['form_id'] );

			GFCommon::send_form_submission_notifications( $child_form, $child_entry );
		}

		return $entry;

	}

	public function should_send_notification( $context, $notification, $parent_form, $nested_form_field, $entry, $child_form ) {

		$should_send_notification = $context === 'parent';

		/**
		 * Indicate whether a notification should be sent by context (parent or child submission).
		 *
		 * @since 1.0-beta-4.10
		 *
		 * @param bool   $should_send_notification Whether the notification should be sent for the given context.
		 * @param array  $notification             The notification object.
		 * @param string $context                  The current context for which notifications are being processed; 'parent' is a parent form submission; 'child' is a nested form submission.
		 * @param array  $parent_form              The parent form object.
		 * @param array  $nested_form_field        The field object of the Nested Form field.
		 * @param array  $entry                    The current entry for which feeds are being processed.
		 * @param array  $child_form               The child form object.
		 */
		$should_send_notification = gf_apply_filters( array(
			'gpnf_should_send_notification',
			$parent_form['id'],
			$nested_form_field->id,
		), $should_send_notification, $notification, $context, $parent_form, $nested_form_field, $entry, $child_form );

		return $should_send_notification;
	}

}

function gpnf_notification_processing() {
	return GPNF_Notification_Processing::get_instance();
}
