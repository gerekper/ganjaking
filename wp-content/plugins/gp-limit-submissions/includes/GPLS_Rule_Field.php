<?php

class GPLS_Rule_Field extends GPLS_Rule {

	private $field;

	public static function load( $ruleData, $form_id = false ) {
		$rule               = new self;
		$rule->field        = $ruleData['rule_field'];
		$rule->form_id      = $form_id;
		$rule->field_values = GPLS_Enforce::$field_values;

		return $rule;
	}

	public function context() {

		// If this is a submission, make sure we only validate fields on pages that have been submitted.
		if ( rgpost( 'gform_submit' ) == $this->form_id ) {
			$field = GFFormsModel::get_field( GFAPI::get_form( $this->form_id ), $this->get_field() );
			// Ensure field wasn't deleted before continuing.
			if ( $field ) {
				$submitted_page = GFFormDisplay::get_source_page( $this->form_id );
				return $submitted_page >= $field->pageNumber;
			}
		}

		// For all other cases, field values are always in context.
		return true;
	}

	public function query() {
		global $wpdb;

		// load form and field
		$form  = GFAPI::get_form( $this->form_id );
		$field = GFFormsModel::get_field( $form, $this->get_field() );
		// Fail if field was deleted after the rule group was added.
		if ( ! $field ) {
			return;
		}

		// loop over subfields, getting value if not hidden
		$joins  = array();
		$wheres = array();

		// Exempt entry being currently edited from field value rules.
		if ( is_callable( 'gp_nested_forms' ) && gp_nested_forms()->get_posted_entry_id() ) {
			$wheres[] = sprintf( 'e.id != %d', gp_nested_forms()->get_posted_entry_id() );
		}

		/*
		 * Add a clause for each input in multi-input fields.
		 * Email fields with Confirm Email enabled will have multiple inputs; treat this fields as single-input fields.
		 * Date fields also need to be treated as a single input instead of MM, DD, YYYY.
		 */
		$field_get_input_type = $field->get_input_type();
		if ( ! empty( $field['inputs'] ) && $field_get_input_type != 'email' && $field_get_input_type != 'date' ) {

			foreach ( $field['inputs'] as $subfield ) {

				// check if this is the input we're currently processing
				if ( $subfield['id'] != $this->get_field() && intval( $subfield['id'] ) != $this->get_field() ) {
					continue;
				}
				// input hidden
				if ( ! empty( $subfield['isHidden'] ) ) {
					continue;
				}
				$table_slug = sprintf( 'em%s', str_replace( '.', '_', $subfield['id'] ) );
				$value      = $this->get_limit_field_value( $subfield['id'] );
				// input has no value
				if ( empty( $value ) ) {
					continue;
				}
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$joins[] = $wpdb->prepare( "INNER JOIN {$wpdb->prefix}gf_entry_meta {$table_slug} ON ({$table_slug}.entry_id = e.id AND {$table_slug}.meta_key = %s)", $subfield['id'] );

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wheres[] = $wpdb->prepare( "\n( {$table_slug}.meta_value = %s )", $value );
			}

			if ( ! empty( $joins ) ) {
				return array(
					'join'  => $joins,
					'where' => $wheres,
				);
			} else {
				return array();
			}
		}
		// singular field
		else {
			$value      = $this->get_limit_field_value( $this->get_field() );
			$table_slug = sprintf( 'em%s', str_replace( '.', '_', $this->get_field() ) );

			if ( ! rgblank( $value ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$joins[] = $wpdb->prepare( "INNER JOIN {$wpdb->prefix}gf_entry_meta {$table_slug} ON ({$table_slug}.entry_id = e.id AND {$table_slug}.meta_key = %s)", $this->get_field() );

				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wheres[] = $wpdb->prepare( "\n( {$table_slug}.meta_value = %s )", $value );
			}

			return array(
				'join'  => $joins,
				'where' => $wheres,
			);
		}
	}

	public function get_limit_field_value( $field_id ) {

		$form  = GFAPI::get_form( $this->form_id );
		$field = GFFormsModel::get_field( $form, $field_id );

		if ( ! $field ) {
			return false;
		}
		$input_name = 'input_' . str_replace( '.', '_', $field_id );

		// When a [gpls] shortcode is added to the admin dashboard GFFormDisplay
		// seems to not be loaded at this point. Check if it hasn't and force load it. HS#24109
		if ( ! class_exists( 'GFFormDisplay' ) ) {
			require_once( GFCommon::get_base_path() . '/form_display.php' );
		}
		if ( GFFormDisplay::is_submit_form_id_valid() ) {
			$value = GFFormsModel::prepare_value( $form, $field, rgpost( $input_name ), $input_name, null );
		} else {
			$value = GFFormsModel::get_field_value( $field, $this->field_values );
		}

		/**
		 * Filter the value used by Limit Submissions when comparing the value to already submitted values.
		 *
		 * @param mixed    $value The field value.
		 * @param GF_Field $field The field associated with the rule.
		 * @param array    $form  The current form.
		 *
		 * @since 1.0.9
		 */
		return gf_apply_filters( array( 'gpls_limit_field_value', $form['id'], $field_id ), $value, $field, $form );
	}

	public function render_option_fields( $gfaddon ) {
		$gfaddon->settings_select(
			array(
				'label'   => __( 'User ID', 'gp-limit-submissions' ),
				'name'    => 'rule_field_{i}',
				'class'   => 'rule_value_selector rule_field rule_field_{i} gpls-secondary-field',
				'choices' => $this->get_field_list(),
			)
		);
	}

	public function get_field() {
		return $this->field;
	}

	public function get_field_list() {
		$choices             = array();
		$form                = GFAPI::get_form( $this->get_form_id() );
		$invalid_field_types = array( 'section', 'html', 'page' );
		foreach ( $form['fields'] as $field ) {
			if ( in_array( $field['type'], $invalid_field_types, true ) ) {
				continue;
			}
			// main field
			$choice    = array(
				'label' => $field['label'],
				'value' => $field['id'],
			);
			$choices[] = $choice;
			// add subfields
			$field_get_input_type = $field->get_input_type();
			if ( ! empty( $field['inputs'] ) && $field_get_input_type != 'email' && $field_get_input_type != 'date' ) {

				foreach ( $field['inputs'] as $subfield ) {

					if ( ! empty( $subfield['isHidden'] ) ) {
						continue;
					}
					$choice    = array(
						'label' => $field['label'] . ' (' . $subfield['label'] . ')',
						'value' => $subfield['id'],
					);
					$choices[] = $choice;
				}
			}
		}

		return $choices;
	}

	public function get_form_id() {
		if ( $_GET && isset( $_GET['id'] ) ) {
			return $_GET['id'];
		}

		return 0;
	}

	public function get_type() {
		return 'field';
	}
}
