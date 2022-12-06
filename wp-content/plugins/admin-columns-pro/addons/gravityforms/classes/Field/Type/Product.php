<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;

class Product extends GravityForms\Field\Field implements GravityForms\Field\Container {

	public function get_sub_fields() {
		$sub_fields = [];

		foreach ( $this->gf_field->inputs as $input ) {
			$sub_field_number = explode( '.', $input['id'] )[1];

			$sub_fields[ $input['id'] ] = $sub_field_number == 3
				? new Number( $this->get_form_id(), $this->get_id(), $this->gf_field )
				: new Input( $this->get_form_id(), $this->get_id(), $this->gf_field );
		}

		return $sub_fields;
	}

	public function get_sub_field( $id ) {
		$sub_fields = $this->get_sub_fields();

		return array_key_exists( $id, $sub_fields ) ? $sub_fields[ $id ] : null;
	}

}