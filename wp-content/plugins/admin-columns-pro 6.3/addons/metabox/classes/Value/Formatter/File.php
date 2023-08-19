<?php

namespace ACA\MetaBox\Value\Formatter;

use ACA\MetaBox\Value\Formatter;

class File implements Formatter {

	public function format( $value, $id = null ) {
		if ( is_array( $value ) ) {
			$value = array_map( [ $this, 'get_attachment_url' ], $value );

			return array_filter( $value );
		}

		return $this->get_attachment_url( (int) $value );
	}

	private function get_attachment_url( $id ) {
		return is_numeric( $id )
			? wp_get_attachment_url( $id )
			: null;
	}

}