<?php

namespace ACP\Helper\Select\Paginated;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;

class Posts extends Paginated {

	public function __construct( $search_term, $page, array $args = [] ) {
		$entities = new Select\Entities\Post( array_merge( $args, [
			's'     => $search_term,
			'paged' => $page,
		] ) );

		parent::__construct(
			$entities,
			new Select\Group\PostType(
				new Select\Formatter\PostTitle( $entities )
			)
		);
	}

}