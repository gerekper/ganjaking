<?php

/**
 * Cast Gravity Forms input ID to a number (either integer or float).
 *
 * @param $input_id mixed
 *
 * @return float|int|null
 */
function gpi_cast_to_input_id( $input_id ) {
	if ( ! is_numeric( $input_id ) ) {
		return null;
	}

	return ( is_float( $input_id ) ) ? (float) $input_id : (int) $input_id;
}

/**
 * Get the Save & Continue values attached to a specific token.
 *
 * From GFFormDisplay::get_form()
 *
 * @param string $token
 *
 * @return array
 */
function gpi_get_save_and_continue_values( $token ) {
	$incomplete_submission_info = GFFormsModel::get_draft_submission_values( $token );

	if ( $incomplete_submission_info ) {
		$submission_details_json = $incomplete_submission_info['submission'];
		$submission_details      = json_decode( $submission_details_json, true );

		return $submission_details['submitted_values'];
	}

	return array();
}

/**
 * Get the selected choice's (or the first choice's) value from a choice-based field. This is needed as a choice-based field's defaultValue property is not
 * set the same way as other fields.
 *
 * @param GF_Field $field
 *
 * @return null|string
 */
function gpi_get_prelected_choice_value( $field ) {
	if ( empty( $field->choices ) ) {
		return null;
	}

	$preselected_choice_value = null;

	/**
	 * If there's a value pre-selected, use it as the preselected choice value.
	 */
	foreach ( $field->choices as $choice_index => $choice ) {
		if ( ! rgar( $choice, 'isSelected' ) ) {
			continue;
		}

		if ( ! rgblank( $choice['value'] ) ) {
			// Choice-based fields with inputs (e.g. checkboxes) use individual input values rather than
			// an array for the checked values.
			if ( $field->inputs ) {
				if ( ! $preselected_choice_value ) {
					$preselected_choice_value = array();
				}

				$preselected_choice_value[ $field->inputs[ $choice_index ]['id'] ] = $choice['value'];
				// If there are multiple pre-selections, make sure we capture them all in an array
			} else {
				if ( $preselected_choice_value ) {
					$preselected_choice_value   = ( is_array( $preselected_choice_value ) ) ? $preselected_choice_value : array( $preselected_choice_value );
					$preselected_choice_value[] = $choice['value'];
				} else {
					$preselected_choice_value = $choice['value'];
				}
			}
		}
	}

	/**
	 * Set preselected choice value to first choice if there is not a placeholder and there isn't a pre-selected
	 * choice above.
	 */
	if ( ! $preselected_choice_value && $field->get_input_type() === 'select' && count( $field->choices ) && ! rgblank( $field->choices[0]['value'] ) && ! $field->placeholder ) {
		$preselected_choice_value = $field->choices[0]['value'];
	}

	return $preselected_choice_value;
}
