<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;
use GF_Field_Address;

class Address extends GravityForms\Field\Field implements GravityForms\Field\Container {

	/**
	 * @return GravityForms\Field[]
	 */
	public function get_sub_fields() {
		$sub_fields = [];

		foreach ( $this->gf_field->offsetGet( 'inputs' ) as $input ) {
			$sub_id = (int) explode( '.', $input['id'] )[1];

			if ( 6 === $sub_id ) {
				$countries = $this->gf_field instanceof GF_Field_Address ? $this->gf_field->get_countries() : [];

				$sub_fields[ $input['id'] ] = new Select( $this->get_form_id(), $this->get_id(), $this->gf_field, array_combine( $countries, $countries ), false );
			} else {
				$sub_fields[ $input['id'] ] = new Input( $this->get_form_id(), $this->get_id(), $this->gf_field );
			}
		}

		return $sub_fields;
	}

	public function get_sub_field( $id ) {
		$sub_fields = $this->get_sub_fields();

		return array_key_exists( $id, $sub_fields ) ? $sub_fields[ $id ] : null;
	}

}