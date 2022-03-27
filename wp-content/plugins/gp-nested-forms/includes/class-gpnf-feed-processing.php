<?php

class GPNF_Feed_Processing {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		// Only pre-process feeds when feeds are about to be processed; all originally applicable feeds should still be validated, etc.
		add_filter( 'gform_entry_post_save', array( $this, 'pre_process_feed_listener' ), 9 );

		// Child feeds should be processed *after* parent feeds.
		add_filter( 'gform_entry_post_save', array( $this, 'process_feeds' ), 11, 2 );

		add_filter( 'gform_trigger_payment_delayed_feeds', array( $this, 'process_delayed_feeds' ), 10, 3 );
		add_filter( 'gform_paypal_fulfillment', array( $this, 'process_delayed_feeds_for_paypal' ), 10, 3 );

	}

	public function pre_process_feed_listener( $return ) {
		add_filter( 'gform_addon_pre_process_feeds', array( $this, 'pre_process_feeds' ), 10, 3 );
		return $return;
	}

	public function pre_process_feeds( $feeds, $entry, $form ) {

		$addon_slug = rgars( $feeds, '0/addon_slug' );
		if ( ! $addon_slug ) {
			return $feeds;
		}

		$is_filtered = false;
		$filtered    = array();

		// Check if this is a nested form submission.
		if ( gp_nested_forms()->is_nested_form_submission() ) {

			$is_filtered       = true;
			$parent_form       = GFAPI::get_form( gp_nested_forms()->get_parent_form_id() );
			$nested_form_field = gp_nested_forms()->get_posted_nested_form_field( $parent_form );

			// Allow each feed's processing to be determined individually.
			foreach ( $feeds as $feed ) {
				if ( $this->should_process_feed( 'child', $feed, $parent_form, $nested_form_field, $addon_slug, $entry, $form ) ) {
					$filtered[] = $feed;
				}
			}
		}
		// Check if we are pre-processing feeds from a parent form submission for a nested entry.
		elseif ( ! empty( $this->_parent_form_data ) ) {

			$is_filtered       = true;
			$parent_form       = $this->_parent_form_data['form'];
			$nested_form_field = $this->_parent_form_data['field'];

			// Allow each feed's processing to be determined individually.
			foreach ( $feeds as $feed ) {
				if ( $this->should_process_feed( 'parent', $feed, $parent_form, $nested_form_field, $addon_slug, $entry, $form ) ) {
					$filtered[] = $feed;
				}
			}
		}

		return $is_filtered ? $filtered : $feeds;
	}

	public function process_feeds( $entry, $form ) {

		// No need to process feeds for nested form submissions; GF will handle that for us.
		if ( gp_nested_forms()->is_nested_form_submission() ) {
			return $entry;
		}

		// If this form has any Nested Form fields, let's find them and *maybe* process feeds for each entry in each field.
		if ( gp_nested_forms()->has_nested_form_field( $form ) ) {

			foreach ( $form['fields'] as $field ) {

				if ( $field->type != 'form' ) {
					continue;
				}

				$nested_entry_ids = gp_nested_forms()->get_field_value( $form, $entry, $field->id );
				if ( empty( $nested_entry_ids ) ) {
					continue;
				}

				$addons = GFAddon::get_registered_addons();

				$nested_entries = apply_filters( 'gpnf_process_feeds_nested_entries', gp_nested_forms()->get_entries( $nested_entry_ids ), $form, $field );
				$nested_form    = apply_filters( 'gpnf_process_feeds_nested_form', gp_nested_forms()->get_nested_form( $field->gpnfForm ), $form, $field );

				foreach ( $addons as $addon ) {
					$addon = call_user_func( array( $addon, 'get_instance' ) );
					if ( $addon instanceof GFFeedAddOn ) {
						foreach ( $nested_entries as $nested_entry ) {
							$this->_parent_form_data = compact( 'form', 'field' );
							$addon->maybe_process_feed( $nested_entry, $nested_form );
							$this->_parent_form_data = false;
						}
					}
				}
			}
		}

		return $entry;
	}

	public function should_process_feed( $context, $feed, $parent_form, $nested_form_field, $addon_slug, $entry, $form ) {

		// Use the field setting by default (passed as $context); allow overriding with the filter below.
		$field_setting       = empty( $nested_form_field->gpnfFeedProcessing ) ? 'parent' : $nested_form_field->gpnfFeedProcessing;
		$should_process_feed = $field_setting == $context;

		/**
		 * Indicate whether a feed should be processed by context (parent or child submission).
		 *
		 * @since 1.0
		 *
		 * @param bool   $should_process_feed Whether the feed should processed for the given context. Compares the context with the $field->gpnfFeedProcessing setting for default evaluation.
		 * @param array  $feed                The current feed.
		 * @param string $context             The current context for which feeds are being processed; 'parent' is a parent form submission; 'child' is a nested form submission.
		 * @param array  $parent_form         The parent form object.
		 * @param array  $nested_form_field   The field object of the Nested Form field.
		 * @param array  $entry               The current entry for which feeds are being processed.
		 */
		$should_process_feed = gf_apply_filters( array( 'gpnf_should_process_feed', $parent_form['id'], $nested_form_field->id ), $should_process_feed, $feed, $context, $parent_form, $nested_form_field, $entry );
		$should_process_feed = gf_apply_filters( array( "gpnf_should_process_{$addon_slug}_feed", $parent_form['id'], $nested_form_field->id ), $should_process_feed, $feed, $context, $parent_form, $nested_form_field, $entry );

		return $should_process_feed;
	}

	/**
	 * Process all delayed feeds for child entries when their parent entry is marked as paid.
	 *
	 * @since 1.0-beta-8.22
	 *
	 * @param      $transaction_id
	 * @param      $payment_feed
	 * @param      $parent_entry
	 * @param null $form
	 */
	public function process_delayed_feeds( $transaction_id, $payment_feed, $parent_entry, $form = null ) {

		if ( $form === null ) {
			$form = GFAPI::get_form( $parent_entry['form_id'] );
		}

		if ( gp_nested_forms()->has_nested_form_field( $form ) ) {

			foreach ( $form['fields'] as $field ) {

				if ( $field->type != 'form' ) {
					continue;
				}

				$nested_entry_ids = gp_nested_forms()->get_field_value( $form, $parent_entry, $field->id );
				if ( empty( $nested_entry_ids ) ) {
					continue;
				}

				$addons         = GFAddon::get_registered_addons();
				$nested_entries = apply_filters( 'gpnf_process_feeds_nested_entries', gp_nested_forms()->get_entries( $nested_entry_ids ), $form, $field );
				$nested_form    = apply_filters( 'gpnf_process_feeds_nested_form', gp_nested_forms()->get_nested_form( $field->gpnfForm ), $form, $field );

				foreach ( $addons as $addon ) {
					$addon = call_user_func( array( $addon, 'get_instance' ) );
					if ( $addon instanceof GFFeedAddOn ) {
						foreach ( $nested_entries as $nested_entry ) {
							$addon->action_trigger_payment_delayed_feeds( $transaction_id, $payment_feed, $nested_entry, $nested_form );
						}
					}
				}
			}
		}

	}

	public function process_delayed_feeds_for_paypal( $parent_entry, $payment_feed, $transaction_id ) {
		$this->process_delayed_feeds( $transaction_id, $payment_feed, $parent_entry );
	}

}

function gpnf_feed_processing() {
	return GPNF_Feed_Processing::get_instance();
}
