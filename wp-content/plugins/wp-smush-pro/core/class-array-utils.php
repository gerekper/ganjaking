<?php

namespace Smush\Core;

class Array_Utils {
	public function array_hash( $array, $keys = array() ) {
		$hash = 0;
		if ( is_array( $array ) ) {
			foreach ( $array as $key => $value ) {
				if ( is_array( $value ) ) {
					$value_hash = $this->array_hash(
						$value,
						array_merge( $keys, array( $key ) )
					);
				} else {
					$prefix     = join( '~', $keys );
					$value_hash = crc32( $prefix . $value );
				}

				$hash += $value_hash;
			}
		}

		return $hash;
	}

	public function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}

	public function ensure_array( $array ) {
		return empty( $array ) || ! is_array( $array )
			? array()
			: $array;
	}

	/**
	 * WARNING: This trick works only for arrays in which all the values are valid keys.
	 * @see https://stackoverflow.com/a/8321701
	 *
	 * @param $array scalar[]
	 *
	 * @return array Unique array
	 */
	public function fast_array_unique( $array ) {
		if ( ! is_array( $array ) ) {
			return array();
		}

		return array_keys( array_flip( $array ) );
	}
}