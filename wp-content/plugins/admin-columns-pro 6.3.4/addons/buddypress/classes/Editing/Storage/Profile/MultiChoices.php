<?php

namespace ACA\BP\Editing\Storage\Profile;

use ACA\BP\Editing\Storage;

class MultiChoices extends Storage\Profile {

	public function get( int $id ) {
		$value = parent::get( $id );

		if ( ! is_array( $value ) ) {
			return $value;
		}

		return array_values( $value );
	}

}