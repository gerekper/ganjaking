<?php

class GPLS_Compatibility_Gravity_Flow {

	public function __construct() {
		if ( ! function_exists( 'gravity_flow' ) ) {
			return;
		}

		$this->add_filters();
	}

	public function add_filters() {
		add_filter( 'gpls_limit_field_value', array( $this, 'use_entry_value' ), 10, 3 );
	}

	/**
	 * Use the value from the current entry during the User Input step as some fields may not be editable thus preventing their values from being submitted.
	 *
	 * @param $value
	 * @param $field
	 * @param $form
	 *
	 * @return void
	 */
	public function use_entry_value( $value, $field, $form ) {
		if ( rgar( $_REQUEST, 'page' ) === 'gravityflow-inbox' && wp_verify_nonce( rgar( $_REQUEST, 'gforms_save_entry' ), 'gforms_save_entry' ) ) {
			$gflow_lid          = rgar( $_REQUEST, 'lid' );
			$current_step_entry = GFAPI::get_entry( $gflow_lid );
			$input_name         = 'input_' . str_replace( '.', '_', $field->id );

			if ( rgblank( $value ) && ! isset( $_POST[ $input_name ] ) ) {
				return GFFormsModel::get_lead_field_value( $current_step_entry, $field );
			}
		}

		return $value;
	}


}
