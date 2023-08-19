<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;

class Name extends GravityForms\Field\Field implements GravityForms\Field\Container {

	public function get_sub_fields() {
		$sub_fields = [];

		foreach ( $this->gf_field->inputs as $input ) {
			$sub_fields[ $input['id'] ] = isset( $input['inputType'] ) && 'radio' === $input['inputType']
				? new Select( $this->get_form_id(), $this->get_id(), $this->gf_field, GravityForms\Utils\FormField::formatChoices( $input['choices'] ), false )
				: new Input( $this->get_form_id(), $this->get_id(), $this->gf_field );
		}

		return $sub_fields;
	}

	public function get_sub_field( $id ) {
		$sub_fields = $this->get_sub_fields();

		return array_key_exists( $id, $sub_fields ) ? $sub_fields[ $id ] : null;
	}

}