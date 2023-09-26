<?php

namespace ACP\Helper\Select\Paginated;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select;

class Users extends Paginated {

	public function __construct( $search_term, $page, $args = [] ) {
		$entities = new Select\Entities\User( array_merge( [
			'search' => $search_term,
			'paged'  => $page,
		], $args ) );

		parent::__construct(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

}