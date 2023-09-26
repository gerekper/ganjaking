<?php

namespace ACA\MetaBox\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Taxonomy implements FormatValue {

	public function format_value( $term_ids ) {
		$term_ids = maybe_unserialize( $term_ids );

		if ( empty( $term_ids ) ) {
			return null;
		}

		if ( strpos( $term_ids, ',' ) ) {
			$term_ids = explode( ',', $term_ids );
		}

		$term_id = is_array( $term_ids ) ? $term_ids[0] : $term_ids;

		$term = get_term( $term_id );

		return $term->name ?? null;
	}

}