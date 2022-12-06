<?php

namespace ACA\MetaBox\Search\Comparison\Relation;

use ACA\MetaBox\Search;
use ACP;

class Term extends Search\Comparison\Relation {

	public function get_values( $search, $page ) {
		$related = $this->relation->get_related_field_settings();

		$taxonomies = [];

		if ( isset( $related['taxonomy'] ) && is_string( $related['taxonomy'] ) ) {
			$taxonomies = [ $related['taxonomy'] ];
		}

		return new ACP\Helper\Select\Paginated\Terms( $search, $page, $taxonomies );
	}

}