<?php

namespace ACA\Types\Filtering;

use ACA\Types\Column;
use ACA\Types\Filtering;

/**
 * @property Column $column
 */
class Image extends Filtering {

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = basename( $value );
		}

		// Add dir name for duplicate file names
		foreach ( ac_helper()->array->get_duplicates( $options ) as $value => $basename ) {
			$path = str_replace( '/' . $basename, '', parse_url( $value, PHP_URL_PATH ) );

			$options[ $value ] .= ' (' . $path . ')';
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}