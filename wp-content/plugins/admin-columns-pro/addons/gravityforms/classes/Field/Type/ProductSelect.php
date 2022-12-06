<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;
use GFCommon;

class ProductSelect extends GravityForms\Field\Field implements GravityForms\Field\Multiple {

	public function get_options() {
		$options = [];

		foreach ( $this->gf_field->offsetGet( 'choices' ) as $choice ) {
			$options[ sprintf( '%s|%s', $choice['value'], GFCommon::to_number( $choice['price'], GFCommon::get_currency() ) ) ] = sprintf( '%s (%s)', $choice['text'], $choice['price'] );
		}

		return $options;
	}

	public function is_multiple() {
		return false;
	}

}