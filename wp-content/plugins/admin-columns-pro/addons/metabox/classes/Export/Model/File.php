<?php

namespace ACA\MetaBox\Export\Model;

use ACA\MetaBox\Column;

/**
 * @property Column $column
 */
class File extends Raw {

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return '';
		}

		$results = [];
		foreach ( $value as $data ) {
			$results[] = $data['url'];
		}

		return implode( "\r\n", $results );
	}

}