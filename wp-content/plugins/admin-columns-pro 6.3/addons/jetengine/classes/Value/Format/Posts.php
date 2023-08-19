<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Value\Formatter;

class Posts extends Formatter {

	public function format( $raw_value ): ?string {
		if ( empty( $raw_value ) ) {
			return $this->column->get_empty_char();
		}

		$post_ids = is_array( $raw_value )
			? $raw_value
			: [ $raw_value ];

		return implode( ', ', array_map( [ $this, 'format_post' ], $post_ids ) );
	}

	private function format_post( $post_id ) {
		return $this->column->get_formatted_value( $post_id, $post_id );
	}

}