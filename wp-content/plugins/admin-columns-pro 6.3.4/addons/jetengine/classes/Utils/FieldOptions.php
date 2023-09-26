<?php

namespace ACA\JetEngine\Utils;

final class FieldOptions {

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	static function get_checked_options( $options ) {
		if ( ! is_array( $options ) ) {
			return [];
		}

		foreach ( $options as $key => $selected ) {
			if ( $selected !== 'true' ) {
				unset( $options[ $key ] );
			}
		}

		return array_keys( $options );
	}

}