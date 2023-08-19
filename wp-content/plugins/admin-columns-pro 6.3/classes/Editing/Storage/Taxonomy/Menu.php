<?php

namespace ACP\Editing\Storage\Taxonomy;

use ACP\Editing\Storage;
use WP_Term;

class Menu extends Storage\Menu {

	protected function get_title( int $id ): string {
		$term = get_term_by( 'id', $id, $this->object_type );

		if ( ! $term instanceof WP_Term ) {
			return false;
		}

		return $term->name;
	}

}