<?php

namespace ACA\JetEngine\Value;

trait DefaultValueFormatterTrait {

	public function get_value( $id ) {
		$raw_value = $this->get_raw_value( $id );

		if ( ! $raw_value ) {
			return $this->get_empty_char();
		}

		return ( new ValueFormatterFactory() )->create( $this, $this->field )->format( $raw_value );
	}
}