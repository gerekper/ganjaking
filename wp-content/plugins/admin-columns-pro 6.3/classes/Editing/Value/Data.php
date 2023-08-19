<?php
declare( strict_types=1 );

namespace ACP\Editing\Value;

use AC\ArrayIterator;

class Data extends ArrayIterator {

//	public static function create_from_request( $data ): self {
//		if ( is_scalar( $data ) ) {
//			return new self( [ 'value' => $data ] );
//		}
//
//		return new self( $data );
//	}

	public function all() {
		return $this->get_copy();
	}

	/**
	 * @return mixed|null
	 */
	public function get_value() {
		return $this->has_offset( 'value' )
			? $this->get_offset( 'value' )
			: null;
	}

}