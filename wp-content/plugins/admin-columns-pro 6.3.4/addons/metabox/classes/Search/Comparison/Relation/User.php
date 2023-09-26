<?php

namespace ACA\MetaBox\Search\Comparison\Relation;

use AC;
use ACA\MetaBox\Search;
use ACP;

class User extends Search\Comparison\Relation {

	public function get_values( $search, $paged ) {
		$args = compact( 'search', 'paged' );
		$entities = new ACP\Helper\Select\Entities\User( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\UserRole(
				new ACP\Helper\Select\Formatter\UserName( $entities )
			)
		);
	}

}