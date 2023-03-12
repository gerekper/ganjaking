<?php

use WPML\FP\Obj;

/**
 * @author OnTheGo Systems
 */
class GFML_Conditional_Logic extends GFML_Form {

	const RULES_PATH = [ 'conditionalLogic', 'rules' ];

	/**
	 * It translates the attributes of the conditional logic, before translating the fields.
	 * This is necessary, or it would not be possible to adjust the conditional logic based on values from "choices" fields.
	 *
	 * @param array  $form
	 * @param string $st_context
	 *
	 * @return array
	 */
	public function translate_conditional_logic( $form, $st_context ) {
		$form = $this->translate_rules_for_section( $form, $st_context, 'fields' );
		$form = $this->translate_rules_for_section( $form, $st_context, 'notifications' );

		return $form;
	}

	/**
	 * Translate conditional rules for the different sections in the form.
	 *
	 * @param array  $form
	 * @param string $st_context
	 * @param string $section_key fields|notifications|confirmations.
	 *
	 * @return array $form
	 */
	private function translate_rules_for_section( $form, $st_context, $section_key ) {
		if ( isset( $form[ $section_key ] ) && is_array( $form[ $section_key ] ) ) {
			foreach ( $form[ $section_key ] as &$form_item ) {
				$rules            = $this->get_item_rules( $form_item );
				$translated_rules = $this->translate_rules( $form, $st_context, $rules, $form_item );
				$form_item        = $this->set_item_rules( $form_item, $translated_rules );
			}
		}
		return $form;
	}

	/**
	 * Get the item's conditionalLogic rules.
	 * $form_item in different sections may either be an object or array.
	 *
	 * @param object|array $form_item
	 *
	 * @return object|array $form_item
	 */
	private function get_item_rules( $form_item ) {
		return Obj::pathOr( [], self::RULES_PATH, $form_item );
	}

	/**
	 * Set the item's conditionalLogic rules.
	 * $form_item in different sections may either be an object or array.
	 *
	 * @param object|array $form_item
	 * @param array        $translated_rules
	 *
	 * @return object|array $form_item
	 */
	private function set_item_rules( $form_item, $translated_rules ) {
		return $translated_rules
			? Obj::assocPath( self::RULES_PATH, $translated_rules, $form_item )
			: $form_item;
	}

	/**
	 * @param array          $form
	 * @param string         $st_context
	 * @param object|array   $rules
	 * @param GF_Field|array $current_field The field who's rules are being translated.
	 *
	 * @return object|array
	 */
	private function translate_rules( $form, $st_context, $rules, $current_field ) {
		if ( is_array( $rules ) ) {
			foreach ( $rules as $key => &$rule ) {
				$rule_field = $this->get_field_from_rule( $form, $rule );

				if ( ! $rule_field ) {
					continue;
				}

				if ( ! empty( $rule_field->choices ) ) {
					$translations = $this->get_multi_input_translations( $rule_field, $st_context );
					if ( isset( $rule['value'] ) && isset( $translations[ $rule['value'] ] ) ) {
						$rule['value'] = $translations[ $rule['value'] ];
					} elseif ( isset( $rule['value'] ) && $this->is_rule_translatable( $rule ) ) {
						$rule['value'] = $this->translate_rule_value( $rule_field, $st_context, $key, $rule );
					}
				} elseif ( isset( $rule['value'] ) && $this->is_rule_translatable( $rule ) ) {
					$rule_field = $current_field instanceof GF_Field ? $current_field : $rule_field;

					$rule['value'] = $this->translate_rule_value( $rule_field, $st_context, $key, $rule );
				}
			}
		}
		return $rules;
	}

	/**
	 * Register values from conditional logic for translation.
	 *
	 * @param GFML_TM_API $gfml_tm_api
	 * @param object      $package
	 * @param GF_Field    $field
	 */
	public function register_conditional_logic( GFML_TM_API $gfml_tm_api, $package, GF_Field $field ) {
		if ( isset( $field->conditionalLogic['rules'] ) ) {
			$string_name_helper        = new GFML_String_Name_Helper();
			$string_name_helper->field = $field;
			foreach ( $field->conditionalLogic['rules'] as $key => $rule ) {
				if ( $this->is_rule_translatable( $rule ) ) {
					$gfml_tm_api->register_gf_string(
						$rule['value'],
						$string_name_helper->get_conditional_rule( $key, $rule ),
						$package,
						$this->build_string_title( $field, $key, $rule ),
						'LINE'
					);
				}
			}
		}
	}

	/**
	 * Return string title limited to 160 characters.
	 *
	 * @param GF_Field $field
	 * @param int      $key
	 * @param array    $rule
	 * @return string
	 */
	private function build_string_title( GF_Field $field, $key, $rule ) {
		$id       = $field['id'];
		$type     = $field['type'];
		$operator = $rule['operator'];
		$slen     = mb_strlen( $id . $type . $operator ) + 10;
		$flen     = mb_strlen( $field->label );
		if ( $slen + $flen < 160 ) {
			$label = $field->label;
		} else {
			$label = mb_substr( $field->label, 0, 160 - $slen - 3 ) . '...';
		}
		return sprintf( '{%s:%s} %s[rule][%s][%s]', $id, $type, $label, $key, $operator );
	}

	/**
	 * Checks whether rule value should be registered with strings for translation.
	 *
	 * @param array $rule
	 *
	 * @return bool
	 */
	private function is_rule_translatable( $rule ) {
		switch ( $rule ['operator'] ) {
			case '>':
			case '<':
			case 'contains':
			case 'starts_with':
			case 'ends_with':
			case 'is':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Translate a rule value.
	 *
	 * @param GF_Field $field
	 * @param array    $st_context
	 * @param int      $key
	 * @param array    $rule
	 *
	 * @return string
	 */
	private function translate_rule_value( GF_Field $field, $st_context, $key, $rule ) {
		if ( ! empty( $rule['value'] ) ) {
			$string_name_helper        = new GFML_String_Name_Helper();
			$string_name_helper->field = $field;
			$rule['value']             = icl_t( $st_context, $string_name_helper->get_conditional_rule( $key, $rule ), $rule['value'] );
		}

		return $rule['value'];
	}
}
