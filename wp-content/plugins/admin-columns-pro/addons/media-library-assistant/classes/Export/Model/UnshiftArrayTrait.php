<?php

namespace ACA\MLA\Export\Model;

trait UnshiftArrayTrait {

	private function shift_element_to_top( array $array, int $key ): array {
		if ( isset( $array[ $key ] ) ) {
			$current = $array[ $key ];

			unset( $array[ $key ] );

			array_unshift( $array, $current );
		}

		return $array;
	}

}