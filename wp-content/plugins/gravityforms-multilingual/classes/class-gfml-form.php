<?php

/**
 * @author OnTheGo Systems
 */
class GFML_Form {
	/**
	 * Used to cache the translations of multi input options as they need to be accessed multiple times when translating conditional fields rules.
	 *
	 * @var array
	 */
	private $multi_input_translations = [];

	/**
	 * It returns an associative array where the first level key is the original value of the choice "text"
	 * and the value is another associative array with the translations of the "text" and "value" attributes.
	 *
	 * This method is very similar to `\Gravity_Forms_Multilingual::maybe_translate_placeholder` and could be used in the future to remove some duplication.
	 *
	 * @param \GF_Field $field
	 * @param string    $st_context
	 *
	 * @return array
	 */
	protected function get_multi_input_translations( $field, $st_context ) {
		$field_id = $field->id;

		if ( ! is_array( $field->choices ) ) {
			return [];
		}

		if ( ! array_key_exists( $field_id, $this->multi_input_translations ) ) {
			$snh        = new GFML_String_Name_Helper();
			$snh->field = $field;

			$translations = [];

			foreach ( $field->choices as $index => $choice ) {
				$snh->field_choice       = $choice;
				$snh->field_choice_index = $index;

				$choice_id = $choice['value']; // We use the 'text' property in the original language as ID for the translations cluster.

				$translations[ $choice_id ] = icl_t( $st_context, $snh->get_field_multi_input_choice_value(), $choice['value'] );
			}

			$this->multi_input_translations[ $field_id ] = $translations;
		}

		return $this->multi_input_translations[ $field_id ];
	}

	/**
	 * It matches the field in the rule with the form's field (if present).
	 *
	 * @param array $form
	 * @param array $rule
	 *
	 * @return \GF_Field|null
	 */
	protected function get_field_from_rule( $form, $rule ) {
		if ( $rule && array_key_exists( 'fieldId', $rule ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( (int) $rule['fieldId'] === $field->id ) {
					return $field;
				}
			}
		}

		return null;
	}

}
