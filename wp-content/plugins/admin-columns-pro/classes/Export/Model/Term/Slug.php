<?php

namespace ACP\Export\Model\Term;

use ACP\Export\Model;

/**
 * Name (default column) exportability model
 * @since 4.1
 */
class Slug extends Model {

	public function get_value( $id ) {
		$term = get_term( $id );

		return apply_filters( 'editable_slug', $term->slug, $term );
	}

}