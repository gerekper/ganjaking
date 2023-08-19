<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;

class CheckboxGroup extends GravityForms\Field\Field
	implements GravityForms\Field\Options, GravityForms\Field\Container {

	public function get_options() {
		return GravityForms\Utils\FormField::formatChoices( $this->gf_field->choices );
	}

	/**
	 * @return Checkbox[]
	 */
	public function get_sub_fields() {
		$fields = [];

		foreach ( $this->gf_field->inputs as $key => $input ) {
			$fields[ $input['id'] ] = new Checkbox( $this->get_form_id(), $this->get_id(), $this->gf_field, $this->gf_field->choices[ $key ]['value'], $input['label'] );
		}

		return $fields;
	}

	public function get_sub_field( $id ) {
		$fields = $this->get_sub_fields();

		return array_key_exists( $id, $fields ) ? $fields[ $id ] : null;
	}

}