<?php

class GPNF_WC_Product_Addons {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {

		add_action( 'woocommerce_gravityforms_entry_created', array( $this, 'update_child_entries_parent' ), 5, 5 );

		// Check if `woocommerce-process-checkout-none` is present. This ensures that we process feeds/notifications
		// only once, either after parent feeds are processed or in the WooCommerce special case.
		// The reason for this and the custom methods is that WC manipulates the parent
		// entry and changes its IDs causing feed processing to fail.
		if ( isset( $_POST['woocommerce-process-checkout-nonce'] ) ) {
			add_action( 'woocommerce_gravityforms_entry_created', array( $this, 'process_feeds' ), 10, 5 );
			add_action( 'woocommerce_gravityforms_entry_created', array( $this, 'maybe_send_child_notifications' ), 10, 5 );

			remove_filter( 'gform_entry_post_save', array( gpnf_feed_processing(), 'process_feeds' ), 11 );
			remove_filter( 'gform_entry_post_save', array( gpnf_notification_processing(), 'maybe_send_child_notifications' ), 11 );
		}

	}

	public function process_feeds( $entry_id, $order_id, $order_item, $form_data, $lead_data ) {
		$form = GFAPI::get_form( rgar( $lead_data, 'form_id' ) );

		if ( ! $form ) {
			return;
		}

		gpnf_feed_processing()->process_feeds( $lead_data, $form );
	}

	public function maybe_send_child_notifications( $entry_id, $order_id, $order_item, $form_data, $lead_data ) {
		$form = GFAPI::get_form( rgar( $lead_data, 'form_id' ) );

		if ( ! $form ) {
			return;
		}

		$send_notifications = isset( $form_data['send_notifications'] ) && $form_data['send_notifications'] === 'yes';

		if ( ! apply_filters( 'woocommerce_gravityforms_send_entry_notifications_on_order_submission', $send_notifications, $form ) ) {
			return;
		}

		gpnf_notification_processing()->maybe_send_child_notifications( $lead_data, $form );
	}


	/**
	 * The reason for this is because the WC GF Products Add-Ons plugin creates a temporary entry when adding the
	 * Product to the Cart. At checkout, this entry is duplicated, and the temporary entry is deleted.
	 *
	 * We need to move the child entries over to the entry that's created when the visitor checks out.
	 */
	public function update_child_entries_parent( $entry_id, $order_id, $order_item, $form_data, $lead_data ) {

		$form = GFAPI::get_form( rgar( $lead_data, 'form_id' ) );

		if ( ! $form ) {
			return;
		}

		/* Loop through Nested Form Fields to figure out which meta keys we need to search for child entries. */
		$nested_form_fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( $field->type !== 'form' ) {
				continue;
			}

			$nested_form_fields[] = $field->id;
		}

		foreach ( $nested_form_fields as $nested_form_field ) {
			$nested_entry_ids = gp_nested_forms()->get_child_entry_ids_from_value( rgar( $lead_data, $nested_form_field ) );

			foreach ( $nested_entry_ids as $nested_entry_id ) {
				gform_update_meta( $nested_entry_id, GPNF_Entry::ENTRY_PARENT_KEY, $entry_id );
			}
		}

	}

}

function gpnf_wc_product_addons() {
	return GPNF_WC_Product_Addons::get_instance();
}
