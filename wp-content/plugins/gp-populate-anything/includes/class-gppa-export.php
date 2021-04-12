<?php

/**
 * @class GPPA_Export
 *
 * Remap Gravity Forms Entries object types during import when multiple forms are included in the same JSON file.
 */
class GPPA_Export {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {

		add_filter( 'gform_export_form', array( $this, 'export_gf_entry_form_title' ) );
		add_action( 'gform_forms_post_import', array( $this, 'set_imported_gf_entry_primary_property' ) );

	}

	public function export_gf_entry_form_title( $form ) {
		foreach ( $form['fields'] as &$field ) {
			foreach ( array( 'choices', 'values' ) as $populate ) {
				$prefix = 'gppa-' . $populate;

				if ( rgar( $field, $prefix . '-object-type' ) === 'gf_entry' ) {
					$gf_entry_form = GFAPI::get_form( rgar( $field, $prefix . '-primary-property' ) );

					if ( ! $gf_entry_form ) {
						continue;
					}

					$field->{$prefix . '-gf-entry-form-title'} = $gf_entry_form['title'];
				}
			}
		}

		return $form;
	}

	public function set_imported_gf_entry_primary_property( $forms ) {
		$update_forms = array();

		foreach ( $forms as $form ) {
			foreach ( $form['fields'] as &$field ) {
				foreach ( array( 'choices', 'values' ) as $populate ) {
					$prefix = 'gppa-' . $populate;

					if ( rgar( $field, $prefix . '-object-type' ) === 'gf_entry' && rgar( $field, $prefix . '-gf-entry-form-title' ) ) {
						$form_id = $this->get_form_from_title( $forms, rgar( $field, $prefix . '-gf-entry-form-title' ) );

						if ( ! $form_id ) {
							continue;
						}

						$field->{$prefix . '-primary-property'} = $form_id;
						unset( $field->{$prefix . '-gf-entry-form-title'} );

						$update_forms[ $form['id'] ] = $form;
					}
				}
			}
		}

		GFAPI::update_forms( $update_forms );
	}

	public function get_form_from_title( $forms, $form_title ) {
		$form_titles = wp_list_pluck( $forms, 'title' );

		$matches = preg_grep( sprintf( '/%s(\([0-9]+\))?/i', preg_quote( $form_title ) ), $form_titles );
		if ( ! empty( $matches ) ) {
			$match = array_keys( $matches );
			$index = array_shift( $match );

			return is_array( $forms[ $index ] ) ? $forms[ $index ]['id'] : $forms[ $index ]->id;
		}

		return false;
	}

}

function gppa_export() {
	return GPPA_Export::get_instance();
}
