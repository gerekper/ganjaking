<?php

namespace ACA\MetaBox\Search\Comparison\Relation;

use ACA\MetaBox\Search;
use ACP;

class Post extends Search\Comparison\Relation {

	public function get_values( $search, $page ) {
		$related = $this->relation->get_related_field_settings();

		$args = [];

		if ( isset( $related['post_type'] ) && is_string( $related['post_type'] ) ) {
			$args['post_type'] = $related['post_type'];
		}

		return new ACP\Helper\Select\Paginated\Posts( $search, $page, $args );
	}

}