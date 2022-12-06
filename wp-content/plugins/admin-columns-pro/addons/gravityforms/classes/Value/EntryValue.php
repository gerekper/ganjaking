<?php

namespace ACA\GravityForms\Value;

use ACA\GravityForms\Field\Field;
use ACA\GravityForms\Value;
use GFAPI;
use GFFormsModel;

class EntryValue implements Value {

	/**
	 * @var Field
	 */
	private $field;

	public function __construct( Field $field ) {
		$this->field = $field;
	}

	public function get_value( $id ) {
		$form_id = $this->field->get_form_id();
		$field_id = $this->field->get_id();

		$form = GFAPI::get_form( $form_id );
		$field = GFAPI::get_field( $form_id, $field_id );
		$entry = GFAPI::get_entry( $id );
		$value = isset( $entry[ $field_id ] ) ? $entry[ $field_id ] : '';
		$columns = GFFormsModel::get_grid_columns( $form_id );

		return $field->get_value_entry_list( $value, $entry, $field_id, $columns, $form );
	}

}