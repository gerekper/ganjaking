<?php

namespace ACP\Helper\Select\Paginated;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;

class Terms extends Paginated {

	public function __construct( $search_term, $page, array $taxonomy = [], array $query_args = [] ) {
		$entities = new Select\Entities\Taxonomy( array_merge( $query_args, [
			'search'   => $search_term,
			'page'     => $page,
			'taxonomy' => $taxonomy,
		] ) );

		parent::__construct(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

}