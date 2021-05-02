<?php

/**
 * Class GPNF_Export
 *
 * Primary purpose: Handle exporting child entries alongside their corresponding parent entry.
 * Secondary purpose: Handle remapping child forms on Nested Form fields when exporting/importing a parent form.
 */
class GPNF_Export {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {

		if ( version_compare( GFForms::$version, '2.4.11.5', '>=' ) ) {
			add_filter( 'gform_export_fields', array( $this, 'set_export_fields' ) );
			add_filter( 'gform_export_line', array( $this, 'append_child_entry_rows' ), 10, 6 );
		}

		add_filter( 'gform_export_form', array( $this, 'export_child_form_title' ) );
		add_action( 'gform_forms_post_import', array( $this, 'set_imported_child_form_id' ) );

	}

	public function set_export_fields( $form ) {

		if ( $this->are_export_fields_disabled() ) {
			return $form;
		}

		for ( $i = count( $form['fields'] ) - 1; $i >= 0; $i-- ) {

			/* @var GF_Field $field */
			$field = $form['fields'][ $i ];
			if ( ! is_a( $field, 'GF_Field' ) || $field->type !== 'form' ) {
				continue;
			}

			$headers = array();

			/* @var GF_Field $child_field */
			foreach ( $this->get_child_fields( $field ) as $child_field ) {

				if ( $child_field->displayOnly ) {
					continue;
				}

				$field->set_context_property( 'use_admin_label', true );
				$child_field->set_context_property( 'use_admin_label', true );

				$header = sprintf( '%s / %s', $field->get_field_label( false, null ), $child_field->get_field_label( false, null ) );
				/**
				 * Modify the header label for the current child field.
				 *
				 * @since 1.0
				 *
				 * @param string    $header      The default header label.
				 * @param array     $form        The current form object.
				 * @param \GF_Field $field       The current Nested Form field.
				 * @param \GF_Field $child_field The current child field.
				 */
				$header = gf_apply_filters( array( 'gpnf_export_child_field_header', $form['id'], $field->id ), $header, $form, $field, $child_field );
				$header = array(
					'id'    => sprintf( '%d.%d', $field->id, $child_field->id ),
					'label' => $header,
				);

				$headers[] = $header;

				add_filter( "gform_entries_field_header_pre_export_{$form['id']}_{$header['id']}", array( $this, 'set_export_header_label' ), 10, 3 );

			}

			array_splice( $form['fields'], $i + 1, 0, $headers );

		}

		return $form;
	}

	public function set_export_header_label( $label, $form, $field ) {

		$id_bits  = explode( '_', current_filter() );
		$field_id = array_pop( $id_bits );

		foreach ( $form['fields'] as $field ) {
			if ( $field->id == $field_id ) {
				$field->set_context_property( 'use_admin_label', true );
				return $field->get_field_label( false, null );
			}
		}

		return $label;
	}

	public function append_child_entry_rows( $line, $form, $columns, $field_rows, $entry, $separator ) {

		$parent_entry       = new GPNF_Entry( $entry );
		$nested_form_fields = GFCommon::get_fields_by_type( $form, array( 'form' ) );
		$lines              = array();

		foreach ( $nested_form_fields as $nested_form_field ) {
			foreach ( $parent_entry->get_child_entries( $nested_form_field->id ) as $child_entry ) {

				$_line           = array();
				$has_child_field = false;

				foreach ( $columns as $column ) {
					if ( intval( $column ) == $nested_form_field->id && $column != $nested_form_field->id ) {
						$has_child_field     = true;
						$id_bits             = explode( '.', $column );
						$child_form_field_id = array_pop( $id_bits );
						$value               = self::get_nested_field_value( $nested_form_field->gpnfForm, $child_entry, $child_form_field_id );
						$_line[]             = self::escape_value( $value );
					} elseif ( ! is_numeric( $column ) ) {
						$field   = GFAPI::get_field( $form, $column );
						$_line[] = self::escape_value( $field->get_value_export( $child_entry, $column, false, true ) );
					} else {
						$counter = rgar( $field_rows, $column, 1 );
						while ( $counter > 0 ) {
							$_line[] = '""';
							$counter--;
						}
					}
				}

				if ( $has_child_field ) {
					$lines[] = implode( $separator, $_line );
				}
			}
		}

		return empty( $lines ) ? $line : sprintf( "%s\n%s", $line, implode( "\n", $lines ) );
	}

	public function are_export_fields_disabled() {
		/**
		 * Disable the addition of child form fields to the parent form export settings.
		 *
		 * @since 1.0-beta-8
		 *
		 * @param bool $disable_export_fields Indicate whether child fields of a Nested Form field should be available to export when exporting parent form entries. Defaults to `false`.
		 */
		return apply_filters( 'gpnf_disable_export_fields', false );
	}

	public function get_nested_field_value( $nested_form_id, $nested_form_entry, $nested_field_id ) {
		if ( is_numeric( $nested_field_id ) ) {
			$field      = GFFormsModel::get_field( $nested_form_id, $nested_field_id );
			$input_type = $field->get_input_type();

			if ( $input_type === 'list' ) {

				$value = $field->get_value_export( $nested_form_entry, $nested_field_id, false, true );
				if ( ! $value ) {
					return $value;
				}

				// Handle multi-column List fields.
				array_walk( $value, function( &$_value ) {
					if ( is_array( $_value ) ) {
						$_value = implode( ',', $_value );
					}
				} );

				return implode( '|', $value );
			} elseif ( $input_type === 'chainedselect' ) {
				// ChainedSelects has multiple inputs, combine them as we do with lists
				$values = array();
				foreach ( $field->inputs as $input ) {
					$values[] = $field->get_value_export( $nested_form_entry, $input['id'], false, true );
				}

				return implode( '|', $values );
			}
		}

		return gp_nested_forms()->get_field_value( GFAPI::get_form( $nested_form_id ), $nested_form_entry, $nested_field_id );
	}

	public function escape_value( $value ) {

		$value = str_replace( '"', '""', $value );

		if ( strpos( $value, '=' ) === 0 ) {
			// Prevent Excel formulas
			$value = "'" . $value;
		}

		return '"' . $value . '"';

	}

	public function get_child_fields( $field ) {
		$child_form = GFAPI::get_form( $field->gpnfForm );
		return $child_form['fields'];
	}

	public function export_child_form_title( $form ) {

		foreach ( $form['fields'] as &$field ) {
			if ( $field->type == 'form' ) {
				$child_form           = GFAPI::get_form( $field->gpnfForm );
				$field->gpnfFormTitle = $child_form['title'];
			}
		}

		return $form;
	}

	public function set_imported_child_form_id( $forms ) {

		$update_forms = array();

		foreach ( $forms as $form ) {
			foreach ( $form['fields'] as &$field ) {
				if ( $field->type == 'form' && $field->gpnfFormTitle ) {
					$child_form_id = $this->get_imported_form_child_id( $forms, $field->gpnfFormTitle );
					if ( ! $child_form_id ) {
						$all_forms = GFCache::get( 'gpnf_all_forms' );
						if ( empty( $all_forms ) ) {
							$all_forms = GFFormsModel::get_forms();
						}
						$child_form_id = $this->get_imported_form_child_id( $all_forms, $field->gpnfFormTitle );
						GFCache::set( 'gpnf_all_forms', $all_forms );
					}
					$field->gpnfForm             = $child_form_id;
					$update_forms[ $form['id'] ] = $form;
				}
			}
		}

		GFAPI::update_forms( $update_forms );

	}

	public function get_imported_form_child_id( $forms, $child_form_title ) {

		$form_titles = wp_list_pluck( $forms, 'title' );

		$matches = preg_grep( sprintf( '/%s(\([0-9]+\))?/i', preg_quote( $child_form_title ) ), $form_titles );
		if ( ! empty( $matches ) ) {
			$match = array_keys( $matches );
			$index = array_shift( $match );
			return is_array( $forms[ $index ] ) ? $forms[ $index ]['id'] : $forms[ $index ]->id;
		}

		return false;
	}

}

function gpnf_export() {
	return GPNF_Export::get_instance();
}
