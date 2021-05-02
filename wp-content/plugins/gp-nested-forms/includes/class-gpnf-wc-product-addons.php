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
