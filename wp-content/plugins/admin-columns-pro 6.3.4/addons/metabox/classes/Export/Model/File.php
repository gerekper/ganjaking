<?php

namespace ACA\MetaBox\Export\Model;

class File extends Raw {

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) || ! is_array( $value ) ) {
			return '';
		}

		$results = [];
		foreach ( $value as $data ) {
			$results[] = $data['url'];
		}

		return implode( "\r\n", $results );
	}

}