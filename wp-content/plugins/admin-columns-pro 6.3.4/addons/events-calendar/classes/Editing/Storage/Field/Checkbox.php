<?php

namespace ACA\EC\Editing\Storage\Field;

use ACP;

class Checkbox extends ACP\Editing\Storage\Post\Meta {

	public function get( int $id ) {
		$value = parent::get( $id );

		return explode( '|', $value );
	}

	public function update( int $id, $data ): bool {
		if ( is_array( $data ) ) {
			$data = implode( '|', $data );
		}

		return parent::update( $id, $data );
	}

}